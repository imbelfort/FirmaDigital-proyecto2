<?php

namespace App\Features\Tramites;

use App\Models\Tramite;
use App\Models\TipoTramite;
use App\Models\MonitoreoSla;
use App\Models\PortalSeguimiento;
use App\Models\FlujoTramite;
use App\Models\BitacoraTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TramiteController
{
    public function index(Request $request)
    {
        $query = Tramite::with([
            'solicitante',
            'tipoTramite',
            'documentos',
            'flujos.responsable',
            'observaciones.usuario',
            'firmaDigital.firmante',
            'despacho',
            'certificado',
            'portalSeguimiento',
            'monitoreoSla'
        ])->orderBy('id', 'desc');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('solicitante_id')) {
            $query->where('id_usuario_solicitante', $request->solicitante_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('codigo_tramite', 'ilike', "%{$s}%")
                  ->orWhere('asunto', 'ilike', "%{$s}%")
                  ->orWhereHas('solicitante', function ($sub) use ($s) {
                      $sub->where('nombre', 'ilike', "%{$s}%")
                          ->orWhere('apellidos', 'ilike', "%{$s}%")
                          ->orWhere('registro', 'ilike', "%{$s}%");
                  });
            });
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    public function tiposTramite()
    {
        return response()->json([
            'success' => true,
            'data' => TipoTramite::with(['requisitos', 'guiasProcesales'])->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_tipo_tramite' => 'required|exists:tipo_tramite,id',
            'asunto' => 'required|string|max:200',
        ]);

        $solicitanteId = $request->id_usuario_solicitante ?? session('usuario')->id_usuario ?? 4;
        $tipo = TipoTramite::findOrFail($request->id_tipo_tramite);

        return DB::transaction(function () use ($request, $solicitanteId, $tipo) {
            $totalCount = Tramite::count() + 1;
            $codigo = 'TR-' . date('Y') . '-' . str_pad($totalCount, 4, '0', STR_PAD_LEFT);

            $tramite = Tramite::create([
                'codigo_tramite' => $codigo,
                'id_usuario_solicitante' => $solicitanteId,
                'id_tipo_tramite' => $tipo->id,
                'asunto' => $request->asunto,
                'estado' => 'INICIADO',
                'prioridad' => $request->prioridad ?? 'NORMAL',
                'fecha_solicitud' => now(),
            ]);

            // Inicializar Monitoreo SLA de 72 Horas Hábiles (Falla 2 corregida de LOGICA_DE_NEGOCIO.md)
            $slaHoras = $tipo->tiempo_sla_horas ?? 72;
            MonitoreoSla::create([
                'id_tramite' => $tramite->id,
                'horas_sla_limite' => $slaHoras,
                'fecha_inicio_sla' => now(),
                'fecha_limite_sla' => now()->addHours($slaHoras),
                'horas_transcurridas' => 0,
                'estado_sla' => 'EN_TIEMPO',
                'alertas_emitidas' => 0,
            ]);

            // Generar Portal de Seguimiento Estudiantil con PIN Único
            $pin = 'PIN-' . rand(1000, 9999);
            PortalSeguimiento::create([
                'id_tramite' => $tramite->id,
                'pin_seguimiento' => $pin,
                'portal_activo' => true,
                'total_visitas' => 0,
            ]);

            // Flujo Inicial
            FlujoTramite::create([
                'id_tramite' => $tramite->id,
                'etapa_actual' => 'Recepción y Cotejo',
                'id_usuario_responsable' => 2, // Encargado
                'fecha_inicio_etapa' => now(),
                'estado_etapa' => 'EN_PROCESO',
            ]);

            // Bitácora Inicial
            BitacoraTramite::create([
                'id_tramite' => $tramite->id,
                'id_usuario' => $solicitanteId,
                'accion' => 'CREACION',
                'detalle' => "Inicio de solicitud de trámite: {$tipo->nombre}",
                'ip_origen' => request()->ip(),
                'fecha_evento' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Trámite {$codigo} iniciado exitosamente. PIN de seguimiento: {$pin}",
                'data' => $tramite->load(['tipoTramite', 'portalSeguimiento', 'monitoreoSla']),
            ]);
        });
    }

    public function avanzarFlujo(Request $request, $id)
    {
        $request->validate([
            'etapa_actual' => 'required|string',
            'id_usuario_responsable' => 'nullable|integer',
        ]);

        $tramite = Tramite::findOrFail($id);

        return DB::transaction(function () use ($tramite, $request) {
            // Cerrar etapa previa
            FlujoTramite::where('id_tramite', $tramite->id)
                ->where('estado_etapa', 'EN_PROCESO')
                ->update([
                    'estado_etapa' => 'COMPLETADO',
                    'fecha_fin_etapa' => now(),
                ]);

            // Crear nueva etapa
            FlujoTramite::create([
                'id_tramite' => $tramite->id,
                'etapa_actual' => $request->etapa_actual,
                'id_usuario_responsable' => $request->id_usuario_responsable ?? session('usuario')->id_usuario ?? 2,
                'fecha_inicio_etapa' => now(),
                'estado_etapa' => 'EN_PROCESO',
            ]);

            // Actualizar estado del tramite si aplica
            if ($request->has('nuevo_estado_tramite')) {
                $tramite->estado = $request->nuevo_estado_tramite;
                $tramite->save();
            }

            // Registrar Bitácora
            BitacoraTramite::create([
                'id_tramite' => $tramite->id,
                'id_usuario' => session('usuario')->id_usuario ?? 2,
                'accion' => 'AVANZO_FLUJO',
                'detalle' => "Etapa avanzada a: {$request->etapa_actual}",
                'ip_origen' => request()->ip(),
                'fecha_evento' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Flujo procesal avanzado con éxito',
                'data' => $tramite->load('flujos.responsable'),
            ]);
        });
    }

    public function agregarObservacion(Request $request, $id)
    {
        $request->validate([
            'tipo_observacion' => 'required|string',
            'descripcion' => 'required|string',
        ]);

        $tramite = Tramite::findOrFail($id);
        $tramite->estado = 'OBSERVADO';
        $tramite->save();

        $obs = \App\Models\ObservacionTramite::create([
            'id_tramite' => $tramite->id,
            'id_usuario' => session('usuario')->id_usuario ?? 2,
            'tipo_observacion' => $request->tipo_observacion,
            'descripcion' => $request->descripcion,
            'estado' => 'ABIERTA',
            'fecha_observacion' => now(),
        ]);

        BitacoraTramite::create([
            'id_tramite' => $tramite->id,
            'id_usuario' => session('usuario')->id_usuario ?? 2,
            'accion' => 'OBSERVO',
            'detalle' => "Observación emitida: {$request->tipo_observacion} - {$request->descripcion}",
            'ip_origen' => request()->ip(),
            'fecha_evento' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Observación agregada al expediente',
            'data' => $obs,
        ]);
    }

    public function subsanarObservacion(Request $request, $id)
    {
        $obs = \App\Models\ObservacionTramite::findOrFail($id);
        $obs->estado = 'SUBSANADA';
        $obs->fecha_subsanacion = now();
        $obs->save();

        $tramite = $obs->tramite;
        // Si no hay más abiertas
        $abiertas = \App\Models\ObservacionTramite::where('id_tramite', $tramite->id)->where('estado', 'ABIERTA')->count();
        if ($abiertas === 0) {
            $tramite->estado = 'INICIADO';
            $tramite->save();
        }

        BitacoraTramite::create([
            'id_tramite' => $tramite->id,
            'id_usuario' => session('usuario')->id_usuario ?? $tramite->id_usuario_solicitante,
            'accion' => 'SUBSANO_OBSERVACION',
            'detalle' => "Observación subsanada: {$request->detalle_subsanacion}",
            'ip_origen' => request()->ip(),
            'fecha_evento' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Observación subsanada',
            'data' => $obs,
        ]);
    }
}
