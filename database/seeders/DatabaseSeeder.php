<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Limpia la base de datos por completo y siembra todos los datos maestros,
     * incluyendo el Administrador General (1000 / 1111111), roles, trámites y bitácoras.
     */
    public function run(): void
    {
        // 1. Limpieza total de la base de datos previa (DROP CASCADE / TRUNCATE)
        DB::statement("
            TRUNCATE TABLE 
                pago_tramite,
                reporte_estadistica,
                alerta_sla,
                monitoreo_sla,
                portal_seguimiento,
                certificado_oficial,
                despacho_legajo,
                firma_digital,
                bitacora_tramite,
                observacion_tramite,
                flujo_tramite,
                documento_expediente,
                requisito,
                tramite,
                tipo_tramite,
                guia_procesal,
                indicador_gestion,
                delegacion_autoridad,
                autoridad,
                docente,
                alumno,
                usuario
            RESTART IDENTITY CASCADE;
        ");

        // 2. Sembrando Usuarios Oficiales (incluyendo Admin 1000)
        DB::table('usuario')->insert([
            [
                'id_usuario' => 1,
                'registro' => '1001',
                'ci' => '1234567',
                'password' => '1234567',
                'nombre' => 'Reinerio',
                'apellidos' => 'Vargas',
                'email' => 'vicerrector@uagrm.edu.bo',
                'telefono' => '71000001',
                'rol' => 'VICERRECTOR',
                'unidad_academica' => 'Vicerrectorado',
                'cargo' => 'Vicerrector General',
                'estado' => 'ACTIVO',
                'fecha_creacion' => now(),
                'fecha_modificacion' => now(),
            ],
            [
                'id_usuario' => 2,
                'registro' => '1002',
                'ci' => '2345678',
                'password' => '2345678',
                'nombre' => 'Carlos',
                'apellidos' => 'Mendoza',
                'email' => 'encargado.firma@uagrm.edu.bo',
                'telefono' => '71000002',
                'rol' => 'ENCARGADO',
                'unidad_academica' => 'Despacho Vicerrectorado',
                'cargo' => 'Encargado de Despacho y Firma',
                'estado' => 'ACTIVO',
                'fecha_creacion' => now(),
                'fecha_modificacion' => now(),
            ],
            [
                'id_usuario' => 3,
                'registro' => '1045',
                'ci' => '3456789',
                'password' => '3456789',
                'nombre' => 'Alberto',
                'apellidos' => 'Guzman',
                'email' => 'aguzman@uagrm.edu.bo',
                'telefono' => '71000003',
                'rol' => 'DOCENTE',
                'unidad_academica' => 'Facultad de Ingenieria en Ciencias de la Computacion',
                'cargo' => 'Docente Titular',
                'estado' => 'ACTIVO',
                'fecha_creacion' => now(),
                'fecha_modificacion' => now(),
            ],
            [
                'id_usuario' => 4,
                'registro' => '219064748',
                'ci' => '4567890',
                'password' => '4567890',
                'nombre' => 'Juan',
                'apellidos' => 'Perez Rodriguez',
                'email' => 'juan.perez@alu.uagrm.edu.bo',
                'telefono' => '71000004',
                'rol' => 'ALUMNO',
                'unidad_academica' => 'Facultad de Ingenieria en Ciencias de la Computacion',
                'cargo' => 'Ingenieria Informatica',
                'estado' => 'ACTIVO',
                'fecha_creacion' => now(),
                'fecha_modificacion' => now(),
            ],
            [
                'id_usuario' => 5,
                'registro' => '220015482',
                'ci' => '5678901',
                'password' => '5678901',
                'nombre' => 'Maria',
                'apellidos' => 'Lopez Flores',
                'email' => 'maria.lopez@alu.uagrm.edu.bo',
                'telefono' => '71000005',
                'rol' => 'ALUMNO',
                'unidad_academica' => 'Facultad de Ingenieria en Ciencias de la Computacion',
                'cargo' => 'Ingenieria en Sistemas',
                'estado' => 'ACTIVO',
                'fecha_creacion' => now(),
                'fecha_modificacion' => now(),
            ],
            [
                'id_usuario' => 8,
                'registro' => '1000',
                'ci' => '1111111',
                'password' => '1111111',
                'nombre' => 'Administrador',
                'apellidos' => 'General UAGRM',
                'email' => 'admin@uagrm.edu.bo',
                'telefono' => '70000000',
                'rol' => 'ADMINISTRATIVO',
                'unidad_academica' => 'Rectorado',
                'cargo' => 'Administrador de Plataforma',
                'estado' => 'ACTIVO',
                'fecha_creacion' => now(),
                'fecha_modificacion' => now(),
            ],
            [
                'id_usuario' => 9,
                'registro' => '221045891',
                'ci' => '7891234',
                'password' => '7891234',
                'nombre' => 'Lucas',
                'apellidos' => 'Vargas Justiniano',
                'email' => 'lucas.vargas@alu.uagrm.edu.bo',
                'telefono' => '72000009',
                'rol' => 'ALUMNO',
                'unidad_academica' => 'Facultad de Ingenieria en Ciencias de la Computacion',
                'cargo' => 'Ingenieria Informatica',
                'estado' => 'ACTIVO',
                'fecha_creacion' => now(),
                'fecha_modificacion' => now(),
            ],
            [
                'id_usuario' => 10,
                'registro' => '221078452',
                'ci' => '8912345',
                'password' => '8912345',
                'nombre' => 'Daniela',
                'apellidos' => 'Roca Gutierrez',
                'email' => 'daniela.roca@alu.uagrm.edu.bo',
                'telefono' => '72000010',
                'rol' => 'ALUMNO',
                'unidad_academica' => 'Facultad de Ciencias Economicas',
                'cargo' => 'Ingenieria Comercial',
                'estado' => 'ACTIVO',
                'fecha_creacion' => now(),
                'fecha_modificacion' => now(),
            ],
        ]);

        // Sincronizar secuencia de usuario
        DB::statement("SELECT setval('usuario_id_usuario_seq', (SELECT MAX(id_usuario) FROM usuario));");

        // 3. Sembrando Entidades Derivadas (Autoridad, Alumno, Docente)...
        DB::table('autoridad')->insert([
            ['id_autoridad' => 1, 'tipo_autoridad' => 'VICERRECTOR', 'resolucion_nombramiento' => 'RES-VR-2022-001', 'fecha_posesion' => '2022-01-10'],
            ['id_autoridad' => 2, 'tipo_autoridad' => 'ENCARGADO_DESPACHO', 'resolucion_nombramiento' => 'RES-VR-2024-045', 'fecha_posesion' => '2024-02-01'],
        ]);

        DB::table('docente')->insert([
            ['id_docente' => 3, 'facultad' => 'Facultad de Ingenieria en Ciencias de la Computacion', 'departamento' => 'Computacion e Informatica', 'categoria_docente' => 'TITULAR'],
        ]);

        DB::table('alumno')->insert([
            ['id_alumno' => 4, 'carrera' => 'Ingenieria Informatica', 'facultad' => 'Facultad de Ingenieria en Ciencias de la Computacion', 'semestre' => 10, 'estado_academico' => 'EGRESADO'],
            ['id_alumno' => 5, 'carrera' => 'Ingenieria en Sistemas', 'facultad' => 'Facultad de Ingenieria en Ciencias de la Computacion', 'semestre' => 9, 'estado_academico' => 'REGULAR'],
            ['id_alumno' => 9, 'carrera' => 'Ingenieria Informatica', 'facultad' => 'Facultad de Ingenieria en Ciencias de la Computacion', 'semestre' => 8, 'estado_academico' => 'REGULAR'],
            ['id_alumno' => 10, 'carrera' => 'Ingenieria Comercial', 'facultad' => 'Facultad de Ciencias Economicas', 'semestre' => 6, 'estado_academico' => 'REGULAR'],
        ]);

        DB::table('delegacion_autoridad')->insert([
            [
                'id_autoridad_titular' => 1,
                'id_autoridad_delegada' => 2,
                'resolucion' => 'RES-VR-DEL-01/2026',
                'motivo' => 'Comision oficial en reunion de Vicerrectores CEUB Sucre',
                'fecha_inicio' => now()->toDateString(),
                'fecha_fin' => now()->addDays(10)->toDateString(),
                'alcance_firma' => 'TOTAL',
                'estado' => 'ACTIVA',
            ],
        ]);

        DB::table('indicador_gestion')->insert([
            ['codigo' => 'IND-SLA-72H', 'nombre' => 'Tasa Cumplimiento SLA 72 Horas', 'descripcion' => 'Porcentaje de tramites despachados en 72 horas habiles', 'meta_esperada' => 95.00, 'valor_actual' => 92.50, 'unidad_medida' => 'PORCENTAJE', 'periodo' => 'MENSUAL'],
            ['codigo' => 'IND-TIEMPO-FIRMA', 'nombre' => 'Tiempo Promedio de Firma Digital', 'descripcion' => 'Horas promedio desde compilacion hasta firma vicerrectoral', 'meta_esperada' => 24.00, 'valor_actual' => 18.50, 'unidad_medida' => 'HORAS', 'periodo' => 'MENSUAL'],
            ['codigo' => 'IND-TRAMITES-MES', 'nombre' => 'Volumen Mensual de Tramites', 'descripcion' => 'Cantidad total de tramites atendidos', 'meta_esperada' => 500.00, 'valor_actual' => 340.00, 'unidad_medida' => 'CANTIDAD', 'periodo' => 'MENSUAL'],
        ]);

        // 4. Catálogo de Trámites Oficiales y Requisitos
        DB::table('tipo_tramite')->insert([
            ['id' => 1, 'codigo' => 'DIPLOMA_ACAD', 'nombre' => 'Diploma Academico', 'descripcion' => 'Emision y firma digital de Diploma Academico de Grado', 'tiempo_sla_horas' => 72, 'costo_bs' => 350.00, 'estado' => 'ACTIVO'],
            ['id' => 2, 'codigo' => 'TIT_PROV_NAC', 'nombre' => 'Titulo en Provision Nacional', 'descripcion' => 'Emision y firma vicerrectoral de Titulo en Provision Nacional', 'tiempo_sla_horas' => 72, 'costo_bs' => 450.00, 'estado' => 'ACTIVO'],
            ['id' => 3, 'codigo' => 'CERT_NOTAS', 'nombre' => 'Certificado de Notas Legalizado', 'descripcion' => 'Legalizacion con firma digital de historial academico completo', 'tiempo_sla_horas' => 72, 'costo_bs' => 50.00, 'estado' => 'ACTIVO'],
            ['id' => 4, 'codigo' => 'CONVALIDACION', 'nombre' => 'Convalidacion de Materias', 'descripcion' => 'Dictamen y resolucion de convalidacion academica', 'tiempo_sla_horas' => 72, 'costo_bs' => 120.00, 'estado' => 'ACTIVO'],
        ]);
        DB::statement("SELECT setval('tipo_tramite_id_seq', (SELECT MAX(id) FROM tipo_tramite));");

        DB::table('requisito')->insert([
            ['id' => 1, 'id_tipo_tramite' => 1, 'nombre' => 'Fotocopia Legalizada CI', 'descripcion' => 'Cedula de Identidad anverso y reverso legible', 'es_obligatorio' => true, 'formato_permitido' => 'PDF'],
            ['id' => 2, 'id_tipo_tramite' => 1, 'nombre' => 'Certificado de Calificaciones Completo', 'descripcion' => 'Historial oficial emitido por DPA', 'es_obligatorio' => true, 'formato_permitido' => 'PDF'],
            ['id' => 3, 'id_tipo_tramite' => 1, 'nombre' => 'Acta de Defensa de Grado', 'descripcion' => 'Acta formal firmada por el tribunal calificador', 'es_obligatorio' => true, 'formato_permitido' => 'PDF'],
            ['id' => 4, 'id_tipo_tramite' => 2, 'nombre' => 'Fotocopia de Diploma Academico', 'descripcion' => 'Diploma academico emitido previamente', 'es_obligatorio' => true, 'formato_permitido' => 'PDF'],
            ['id' => 5, 'id_tipo_tramite' => 2, 'nombre' => 'Certificado de Nacimiento Computarizado', 'descripcion' => 'Emitido por SERECI no mayor a 1 año', 'es_obligatorio' => true, 'formato_permitido' => 'PDF'],
            ['id' => 6, 'id_tipo_tramite' => 3, 'nombre' => 'Formulario de Solicitud de Notas', 'descripcion' => 'Formulario oficial de DPA firmado', 'es_obligatorio' => true, 'formato_permitido' => 'PDF'],
        ]);
        DB::statement("SELECT setval('requisito_id_seq', (SELECT MAX(id) FROM requisito));");

        DB::table('guia_procesal')->insert([
            ['id_tipo_tramite' => 1, 'paso_numero' => 1, 'nombre_etapa' => 'Recepcion y Cotejo', 'descripcion_instrucciones' => 'Verificar registro del alumno, CI y requisitos obligatorios', 'rol_responsable' => 'ENCARGADO', 'tiempo_estimado_horas' => 12],
            ['id_tipo_tramite' => 1, 'paso_numero' => 2, 'nombre_etapa' => 'Compilacion de Expediente', 'descripcion_instrucciones' => 'Compilar legajo unico digital y generar hash SHA-256', 'rol_responsable' => 'ENCARGADO', 'tiempo_estimado_horas' => 12],
            ['id_tipo_tramite' => 1, 'paso_numero' => 3, 'nombre_etapa' => 'Firma Vicerrectoral', 'descripcion_instrucciones' => 'Ejecutar firma digital consolidada o remota con token', 'rol_responsable' => 'VICERRECTOR', 'tiempo_estimado_horas' => 24],
            ['id_tipo_tramite' => 1, 'paso_numero' => 4, 'nombre_etapa' => 'Despacho y Notificacion', 'descripcion_instrucciones' => 'Generar Certificado QR, habilitar portal de seguimiento y notificar', 'rol_responsable' => 'ENCARGADO', 'tiempo_estimado_horas' => 24],
        ]);

        // 5. Trámites Iniciales de Demostración
        // Trámite 1: En Firma
        DB::table('tramite')->insert([
            'id' => 1,
            'codigo_tramite' => 'TR-2026-0001',
            'id_usuario_solicitante' => 4,
            'id_tipo_tramite' => 1,
            'asunto' => 'Solicitud Diploma Academico de Licenciatura en Informatica',
            'estado' => 'EN_FIRMA',
            'prioridad' => 'NORMAL',
            'fecha_solicitud' => now()->subHours(15),
        ]);

        DB::table('pago_tramite')->insert([
            'id_tramite' => 1,
            'payment_number' => 'PAGO-TR-2026-0001',
            'transaction_id' => 'TX-PAGOFACIL-9001',
            'monto' => 350.00,
            'moneda' => 'BOB',
            'metodo_pago' => 'PAGOFACIL_QR',
            'estado' => 'COMPLETADO',
            'fecha_pago' => now()->subHours(14),
            'fecha_expiracion' => now()->addDay(),
            'created_at' => now()->subHours(15),
            'updated_at' => now()->subHours(14),
        ]);

        DB::table('documento_expediente')->insert([
            [
                'id_tramite' => 1,
                'id_requisito' => 1,
                'nombre_documento' => 'CI_JuanPerez.pdf',
                'tipo_documento' => 'PDF',
                'archivo_url' => '/storage/expedientes/ci_4567890.pdf',
                'hash_documento' => 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855',
                'estado_validacion' => 'APROBADO',
                'fecha_validacion' => now()->subHours(12),
            ],
            [
                'id_tramite' => 1,
                'id_requisito' => 2,
                'nombre_documento' => 'Historial_JuanPerez.pdf',
                'tipo_documento' => 'PDF',
                'archivo_url' => '/storage/expedientes/historial_4567890.pdf',
                'hash_documento' => 'ba7816bf8f01cfea414140de5dae2223b00361a396177a9cb410ff61f20015ad',
                'estado_validacion' => 'APROBADO',
                'fecha_validacion' => now()->subHours(12),
            ],
        ]);

        DB::table('flujo_tramite')->insert([
            ['id_tramite' => 1, 'etapa_actual' => 'Firma Vicerrectoral', 'id_usuario_responsable' => 1, 'estado_etapa' => 'EN_PROCESO', 'fecha_inicio_etapa' => now()->subHours(5)],
        ]);

        DB::table('bitacora_tramite')->insert([
            ['id_tramite' => 1, 'id_usuario' => 4, 'accion' => 'INICIO_TRAMITE', 'detalle' => 'El estudiante inicio la solicitud de Diploma Academico', 'ip_origen' => '127.0.0.1', 'fecha_evento' => now()->subHours(15)],
            ['id_tramite' => 1, 'id_usuario' => 4, 'accion' => 'PAGO_CONFIRMADO', 'detalle' => 'Pago confirmado por Bs. 350.00 via PagoFacil QR', 'ip_origen' => '127.0.0.1', 'fecha_evento' => now()->subHours(14)],
            ['id_tramite' => 1, 'id_usuario' => 2, 'accion' => 'VALIDACION_DOCUMENTOS', 'detalle' => 'Encargado aprobo los requisitos presentados', 'ip_origen' => '127.0.0.1', 'fecha_evento' => now()->subHours(12)],
            ['id_tramite' => 1, 'id_usuario' => 2, 'accion' => 'COMPILACION_EXPEDIENTE', 'detalle' => 'Expediente compilado listo para firma', 'ip_origen' => '127.0.0.1', 'fecha_evento' => now()->subHours(5)],
        ]);

        DB::table('monitoreo_sla')->insert([
            'id' => 1,
            'id_tramite' => 1,
            'horas_sla_limite' => 72,
            'fecha_inicio_sla' => now()->subHours(15),
            'fecha_limite_sla' => now()->subHours(15)->addHours(72),
            'horas_transcurridas' => 15.00,
            'estado_sla' => 'EN_TIEMPO',
            'alertas_emitidas' => 0,
        ]);
        DB::statement("SELECT setval('monitoreo_sla_id_seq', (SELECT MAX(id) FROM monitoreo_sla));");

        DB::table('portal_seguimiento')->insert([
            'id_tramite' => 1,
            'pin_seguimiento' => 'PIN-9482',
            'portal_activo' => true,
            'total_visitas' => 3,
            'ultimo_acceso' => now(),
        ]);

        DB::statement("SELECT setval('tramite_id_seq', (SELECT MAX(id) FROM tramite));");
    }
}
