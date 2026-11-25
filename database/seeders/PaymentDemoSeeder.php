<?php

namespace Database\Seeders;

use App\Models\TipoTramite;
use App\Models\Tramite;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PaymentDemoSeeder extends Seeder
{
    public function run(): void
    {
        $alumnos = Usuario::where('rol', 'ALUMNO')->orderBy('id_usuario')->limit(4)->pluck('id_usuario');
        $tipos = TipoTramite::where('estado', 'ACTIVO')->where('costo_bs', '>', 0)->orderBy('id')->limit(4)->get();
        if ($alumnos->isEmpty() || $tipos->isEmpty()) {
            throw new RuntimeException('Los datos de pagos requieren al menos un alumno y un tipo de trámite activo con arancel.');
        }

        $escenarios = [
            ['PAGOFACIL_QR', 'COMPLETADO', 24],
            ['STRIPE', 'COMPLETADO', 38],
            ['STRIPE', 'COMPLETADO', 60],
            ['MANUAL_VENTANILLA', 'COMPLETADO', 72],
            ['PAGOFACIL_QR', 'COMPLETADO', 120],
            ['STRIPE', 'COMPLETADO', 144],
            ['PAGOFACIL_QR', 'PENDIENTE', 1],
            ['STRIPE', 'PENDIENTE', 2],
            ['STRIPE', 'PROCESANDO', 3],
            ['STRIPE', 'RECHAZADO', 4],
            ['STRIPE', 'CANCELADO', 6],
            ['STRIPE', 'FALLIDO', 11],
            ['STRIPE', 'EXPIRADO', 52],
            ['PAGOFACIL_QR', 'EXPIRADO', 28],
            [null, null, 2],
        ];

        DB::transaction(function () use ($escenarios, $alumnos, $tipos) {
            foreach ($escenarios as $indice => [$metodo, $estado, $horas]) {
                $tipo = $tipos[$indice % $tipos->count()];
                $fechaSolicitud = now()->subHours($horas);
                $codigo = sprintf('TR-DEMO-PAGO-%02d', $indice + 1);
                $tramite = Tramite::firstOrCreate(['codigo_tramite' => $codigo], [
                    'id_usuario_solicitante' => $alumnos[$indice % $alumnos->count()],
                    'id_tipo_tramite' => $tipo->id,
                    'asunto' => "Demostración de pagos · {$tipo->nombre}",
                    'estado' => 'INICIADO',
                    'prioridad' => 'NORMAL',
                    'fecha_solicitud' => $fechaSolicitud,
                ]);

                if (!$metodo || !$tramite->wasRecentlyCreated) {
                    continue;
                }

                $tramite->pago()->create([
                    'payment_number' => "PAGO-{$codigo}",
                    'transaction_id' => $metodo === 'MANUAL_VENTANILLA' ? null : "demo_{$codigo}",
                    'monto' => $tipo->costo_bs,
                    'moneda' => 'BOB',
                    'metodo_pago' => $metodo,
                    'estado' => $estado,
                    'fecha_expiracion' => $metodo === 'MANUAL_VENTANILLA' ? null : $fechaSolicitud->copy()->addDay(),
                    'fecha_pago' => $estado === 'COMPLETADO' ? $fechaSolicitud->copy()->addMinutes(20) : null,
                ]);
            }
        });
    }
}
