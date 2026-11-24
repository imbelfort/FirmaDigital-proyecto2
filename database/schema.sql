-- ============================================================
-- BASE DE DATOS: db_grupo26sa
-- SISTEMA DE FIRMA DIGITAL DEL VICERRECTORADO
-- UNIVERSIDAD AUTONOMA GABRIEL RENE MORENO (UAGRM)
-- GRUPO 26 SA - TECNOLOGIA WEB
-- ============================================================
-- REGLA DE CREDENCIALES Y CODIGOS (ESTRICTAMENTE NUMERICOS, SIN LETRAS):
-- * Alumnos: Registro Universitario numérico (ej. 219064748). Su C.I. es su contraseña.
-- * Docentes: Código de Docente numérico de 4 dígitos (ej. 1045). Su C.I. es su contraseña.
-- * Vicerrector / Encargado / Personal Administrativo: Código numérico de 4 dígitos (ej. 1001, 1002). Su C.I. es su contraseña.
-- ============================================================

-- ============================================================
-- ELIMINACION DE TABLAS (ORDEN INVERSO DE DEPENDENCIAS)
-- ============================================================
DROP TABLE IF EXISTS reporte_estadistica CASCADE;
DROP TABLE IF EXISTS alerta_sla CASCADE;
DROP TABLE IF EXISTS monitoreo_sla CASCADE;
DROP TABLE IF EXISTS portal_seguimiento CASCADE;
DROP TABLE IF EXISTS certificado_oficial CASCADE;
DROP TABLE IF EXISTS despacho_legajo CASCADE;
DROP TABLE IF EXISTS firma_digital CASCADE;
DROP TABLE IF EXISTS bitacora_tramite CASCADE;
DROP TABLE IF EXISTS observacion_tramite CASCADE;
DROP TABLE IF EXISTS flujo_tramite CASCADE;
DROP TABLE IF EXISTS guia_procesal CASCADE;
DROP TABLE IF EXISTS documento_expediente CASCADE;
DROP TABLE IF EXISTS requisito CASCADE;
DROP TABLE IF EXISTS tramite CASCADE;
DROP TABLE IF EXISTS tipo_tramite CASCADE;
DROP TABLE IF EXISTS indicador_gestion CASCADE;
DROP TABLE IF EXISTS delegacion_autoridad CASCADE;
DROP TABLE IF EXISTS autoridad CASCADE;
DROP TABLE IF EXISTS docente CASCADE;
DROP TABLE IF EXISTS alumno CASCADE;
DROP TABLE IF EXISTS usuario CASCADE;

-- ============================================================
-- CU1. GESTION DE USUARIOS (Vicerrector, Encargado, Docentes, Alumnos)
-- ============================================================

CREATE TABLE usuario (
    id_usuario SERIAL PRIMARY KEY,
    registro VARCHAR(50) UNIQUE NOT NULL,      -- Registro universitario o código de funcionario
    ci VARCHAR(30) UNIQUE NOT NULL,            -- Cédula de Identidad (actúa por defecto como contraseña)
    password VARCHAR(255) NOT NULL,            -- Contraseña (por defecto idéntica al CI)
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telefono VARCHAR(30),
    rol VARCHAR(50) NOT NULL,                  -- 'VICERRECTOR', 'ENCARGADO', 'DOCENTE', 'ALUMNO', 'ADMINISTRATIVO'
    unidad_academica VARCHAR(150),             -- Facultad / Carrera / Dirección
    cargo VARCHAR(100),                        -- Cargo institucional
    estado VARCHAR(30) NOT NULL DEFAULT 'ACTIVO',
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_usuario_rol
        CHECK (rol IN ('VICERRECTOR', 'ENCARGADO', 'DOCENTE', 'ALUMNO', 'ADMINISTRATIVO')),
    CONSTRAINT chk_usuario_estado
        CHECK (estado IN ('ACTIVO', 'INACTIVO', 'SUSPENDIDO'))
);

CREATE TABLE alumno (
    id_alumno INTEGER PRIMARY KEY,
    carrera VARCHAR(120) NOT NULL,
    facultad VARCHAR(120) NOT NULL,
    semestre INTEGER DEFAULT 1,
    estado_academico VARCHAR(50) DEFAULT 'REGULAR',
    CONSTRAINT fk_alumno_usuario
        FOREIGN KEY (id_alumno) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

CREATE TABLE docente (
    id_docente INTEGER PRIMARY KEY,
    facultad VARCHAR(120) NOT NULL,
    departamento VARCHAR(120),
    categoria_docente VARCHAR(50) DEFAULT 'TIEMPO_COMPLETO', -- 'TITULAR', 'ADJUNTO', 'TIEMPO_COMPLETO'
    CONSTRAINT fk_docente_usuario
        FOREIGN KEY (id_docente) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

CREATE TABLE autoridad (
    id_autoridad INTEGER PRIMARY KEY,
    tipo_autoridad VARCHAR(50) NOT NULL,       -- 'VICERRECTOR', 'ENCARGADO_DESPACHO'
    resolucion_nombramiento VARCHAR(100),
    fecha_posesion DATE,
    CONSTRAINT fk_autoridad_usuario
        FOREIGN KEY (id_autoridad) REFERENCES usuario(id_usuario) ON DELETE CASCADE
);

-- ============================================================
-- CU2. GESTION DE DELEGACION DE AUTORIDAD E INDICADORES DE GESTION
-- ============================================================

CREATE TABLE delegacion_autoridad (
    id SERIAL PRIMARY KEY,
    id_autoridad_titular INTEGER NOT NULL,     -- Vicerrector Titular que delega
    id_autoridad_delegada INTEGER NOT NULL,    -- Encargado o autoridad que asume
    resolucion VARCHAR(100) NOT NULL,          -- Nro. de Resolución Vicerrectoral
    motivo TEXT NOT NULL,                      -- Motivo de la delegación (ej: Viaje oficial, licencia)
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    alcance_firma VARCHAR(100) DEFAULT 'TOTAL',-- 'TOTAL', 'SOLO_TITULOS', 'CERTIFICACIONES'
    estado VARCHAR(30) NOT NULL DEFAULT 'ACTIVA',
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_delegacion_titular
        FOREIGN KEY (id_autoridad_titular) REFERENCES usuario(id_usuario),
    CONSTRAINT fk_delegacion_delegado
        FOREIGN KEY (id_autoridad_delegada) REFERENCES usuario(id_usuario),
    CONSTRAINT chk_delegacion_estado
        CHECK (estado IN ('ACTIVA', 'FINALIZADA', 'REVOCADA'))
);

CREATE TABLE indicador_gestion (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    meta_esperada NUMERIC(10,2) NOT NULL,      -- Ej: 72.00 horas, 95.00%
    valor_actual NUMERIC(10,2) DEFAULT 0,
    unidad_medida VARCHAR(30) NOT NULL,        -- 'HORAS', 'PORCENTAJE', 'CANTIDAD'
    periodo VARCHAR(50) DEFAULT 'MENSUAL',     -- 'DIARIO', 'SEMANAL', 'MENSUAL', 'ANUAL'
    estado VARCHAR(30) NOT NULL DEFAULT 'ACTIVO',
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- CU3. GESTION DE TRAMITES, SELECCIONAR DOCUMENTOS, VALIDAR REQUISITOS Y COMPILAR EXPEDIENTE
-- ============================================================

CREATE TABLE tipo_tramite (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,        -- 'DIPLOMA_ACAD', 'TIT_PROV_NAC', 'CERT_NOTAS', 'LEGALIZACION'
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    tiempo_sla_horas INTEGER NOT NULL DEFAULT 72, -- Tiempo estándar SLA: 72 horas hábiles
    costo_bs NUMERIC(10,2) DEFAULT 0.00,
    estado VARCHAR(30) NOT NULL DEFAULT 'ACTIVO'
);

CREATE TABLE requisito (
    id SERIAL PRIMARY KEY,
    id_tipo_tramite INTEGER NOT NULL,
    nombre VARCHAR(150) NOT NULL,              -- Ej: 'Fotocopia Legalizada CI', 'Certificado de Notas Original'
    descripcion TEXT,
    es_obligatorio BOOLEAN NOT NULL DEFAULT TRUE,
    formato_permitido VARCHAR(50) DEFAULT 'PDF',
    CONSTRAINT fk_requisito_tipo_tramite
        FOREIGN KEY (id_tipo_tramite) REFERENCES tipo_tramite(id) ON DELETE CASCADE
);

CREATE TABLE tramite (
    id SERIAL PRIMARY KEY,
    codigo_tramite VARCHAR(50) UNIQUE NOT NULL, -- Ej: 'TR-2026-0001'
    id_usuario_solicitante INTEGER NOT NULL,    -- Alumno o Docente que solicita
    id_tipo_tramite INTEGER NOT NULL,
    asunto VARCHAR(200) NOT NULL,
    estado VARCHAR(50) NOT NULL DEFAULT 'INICIADO',
    -- Estados: INICIADO, REQUISITOS_VALIDADOS, EXPEDIENTE_COMPILADO, EN_FIRMA, FIRMADO, DESPACHADO, OBSERVADO, RECHAZADO
    prioridad VARCHAR(30) NOT NULL DEFAULT 'NORMAL', -- 'NORMAL', 'URGENTE'
    fecha_solicitud TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_finalizacion TIMESTAMP,

    CONSTRAINT fk_tramite_solicitante
        FOREIGN KEY (id_usuario_solicitante) REFERENCES usuario(id_usuario),
    CONSTRAINT fk_tramite_tipo
        FOREIGN KEY (id_tipo_tramite) REFERENCES tipo_tramite(id)
);

CREATE TABLE documento_expediente (
    id SERIAL PRIMARY KEY,
    id_tramite INTEGER NOT NULL,
    id_requisito INTEGER,
    nombre_documento VARCHAR(200) NOT NULL,
    tipo_documento VARCHAR(50) DEFAULT 'PDF',
    archivo_url TEXT,
    hash_documento VARCHAR(128),               -- Hash SHA-256 del documento para integridad
    estado_validacion VARCHAR(30) NOT NULL DEFAULT 'PENDIENTE', -- 'PENDIENTE', 'APROBADO', 'OBSERVADO', 'RECHAZADO'
    observacion_validacion TEXT,
    fecha_validacion TIMESTAMP,

    CONSTRAINT fk_doc_tramite
        FOREIGN KEY (id_tramite) REFERENCES tramite(id) ON DELETE CASCADE,
    CONSTRAINT fk_doc_requisito
        FOREIGN KEY (id_requisito) REFERENCES requisito(id)
);

-- ============================================================
-- CU4. GESTION DE ENROSTRAR FLUJO, VISUALIZAR GUIA PROCESAL, GESTIONAR OBSERVACIONES Y BITACORA
-- ============================================================

CREATE TABLE guia_procesal (
    id SERIAL PRIMARY KEY,
    id_tipo_tramite INTEGER NOT NULL,
    paso_numero INTEGER NOT NULL,
    nombre_etapa VARCHAR(150) NOT NULL,        -- 'Recepción', 'Revisión y Cotejo', 'Compilación', 'Firma Vicerrectoral', 'Despacho'
    descripcion_instrucciones TEXT NOT NULL,
    rol_responsable VARCHAR(50) NOT NULL,      -- 'ENCARGADO', 'VICERRECTOR'
    tiempo_estimado_horas INTEGER DEFAULT 24,

    CONSTRAINT fk_guia_tipo_tramite
        FOREIGN KEY (id_tipo_tramite) REFERENCES tipo_tramite(id) ON DELETE CASCADE
);

CREATE TABLE flujo_tramite (
    id SERIAL PRIMARY KEY,
    id_tramite INTEGER NOT NULL,
    etapa_actual VARCHAR(100) NOT NULL,
    id_usuario_responsable INTEGER,
    fecha_inicio_etapa TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_fin_etapa TIMESTAMP,
    estado_etapa VARCHAR(30) NOT NULL DEFAULT 'EN_PROCESO', -- 'EN_PROCESO', 'COMPLETADO', 'OBSERVADO'

    CONSTRAINT fk_flujo_tramite
        FOREIGN KEY (id_tramite) REFERENCES tramite(id) ON DELETE CASCADE,
    CONSTRAINT fk_flujo_responsable
        FOREIGN KEY (id_usuario_responsable) REFERENCES usuario(id_usuario)
);

CREATE TABLE observacion_tramite (
    id SERIAL PRIMARY KEY,
    id_tramite INTEGER NOT NULL,
    id_usuario INTEGER NOT NULL,               -- Encargado o Vicerrector que emite la observación
    tipo_observacion VARCHAR(50) NOT NULL,     -- 'FALTA_REQUISITO', 'DOCUMENTO_ILEGIBLE', 'ERROR_DATOS', 'OTRO'
    descripcion TEXT NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'ABIERTA', -- 'ABIERTA', 'SUBSANADA', 'CERRADA'
    fecha_observacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_subsanacion TIMESTAMP,

    CONSTRAINT fk_obs_tramite
        FOREIGN KEY (id_tramite) REFERENCES tramite(id) ON DELETE CASCADE,
    CONSTRAINT fk_obs_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

CREATE TABLE bitacora_tramite (
    id SERIAL PRIMARY KEY,
    id_tramite INTEGER NOT NULL,
    id_usuario INTEGER,
    accion VARCHAR(100) NOT NULL,              -- 'CREACION', 'VALIDO_REQUISITO', 'OBSERVO', 'AVANZO_FLUJO', 'FIRMO_DIGITALMENTE', 'DESPACHO'
    detalle TEXT,
    ip_origen VARCHAR(45),
    fecha_evento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_bitacora_tramite
        FOREIGN KEY (id_tramite) REFERENCES tramite(id) ON DELETE CASCADE,
    CONSTRAINT fk_bitacora_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

-- ============================================================
-- CU5. GESTION DE DESPACHAR, PREVISUALIZAR LEGAJOS Y EJECUTAR FIRMA DIGITAL CONSOLIDADA O REMOTA
-- ============================================================

CREATE TABLE firma_digital (
    id SERIAL PRIMARY KEY,
    id_tramite INTEGER NOT NULL,
    id_firmante INTEGER NOT NULL,              -- Vicerrector o Delegado
    tipo_firma VARCHAR(50) NOT NULL,           -- 'CONSOLIDADA' o 'REMOTA'
    algoritmo VARCHAR(50) NOT NULL DEFAULT 'SHA256withRSA',
    hash_documento VARCHAR(128) NOT NULL,      -- Hash criptográfico del expediente compilado
    firma_digital_valor TEXT NOT NULL,         -- Firma electrónica generada (Base64)
    numero_certificado_digital VARCHAR(100) NOT NULL, -- Nro. de Certificado digital ADSIB/UAGRM
    fecha_firma TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    valido BOOLEAN NOT NULL DEFAULT TRUE,

    CONSTRAINT fk_firma_tramite
        FOREIGN KEY (id_tramite) REFERENCES tramite(id) ON DELETE CASCADE,
    CONSTRAINT fk_firma_firmante
        FOREIGN KEY (id_firmante) REFERENCES usuario(id_usuario),
    CONSTRAINT chk_tipo_firma
        CHECK (tipo_firma IN ('CONSOLIDADA', 'REMOTA'))
);

CREATE TABLE despacho_legajo (
    id SERIAL PRIMARY KEY,
    id_tramite INTEGER NOT NULL,
    id_usuario_despachador INTEGER NOT NULL,
    numero_despacho VARCHAR(50) UNIQUE NOT NULL,
    legajo_previsualizado_url TEXT,
    tipo_entrega VARCHAR(50) NOT NULL DEFAULT 'DIGITAL_CORREO', -- 'DIGITAL_CORREO', 'PORTAL_DESCARGA', 'VENTANILLA'
    fecha_despacho TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(30) NOT NULL DEFAULT 'DESPACHADO',

    CONSTRAINT fk_despacho_tramite
        FOREIGN KEY (id_tramite) REFERENCES tramite(id) ON DELETE CASCADE,
    CONSTRAINT fk_despacho_usuario
        FOREIGN KEY (id_usuario_despachador) REFERENCES usuario(id_usuario)
);

-- ============================================================
-- CU6. GESTION DE CERTIFICADO OFICIAL CON QR Y HABILITAR PORTAL DE SEGUIMIENTO ESTUDIANTIL
-- ============================================================

CREATE TABLE certificado_oficial (
    id SERIAL PRIMARY KEY,
    id_tramite INTEGER NOT NULL,
    id_firma_digital INTEGER NOT NULL,
    codigo_verificacion VARCHAR(64) UNIQUE NOT NULL, -- Código UUID único de validación
    qr_data TEXT NOT NULL,                           -- Contenido o URL codificada en el QR
    titulo_documento VARCHAR(200) NOT NULL,          -- Ej: 'DIPLOMA ACADEMICO DE LICENCIATURA EN ING. INFORMATICA'
    fecha_emision TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion DATE,
    portal_url VARCHAR(255) NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'EMITIDO',   -- 'EMITIDO', 'REVOCADO'

    CONSTRAINT fk_cert_tramite
        FOREIGN KEY (id_tramite) REFERENCES tramite(id) ON DELETE CASCADE,
    CONSTRAINT fk_cert_firma
        FOREIGN KEY (id_firma_digital) REFERENCES firma_digital(id)
);

CREATE TABLE portal_seguimiento (
    id SERIAL PRIMARY KEY,
    id_tramite INTEGER UNIQUE NOT NULL,
    pin_seguimiento VARCHAR(20) NOT NULL,            -- PIN secreto de consulta para el alumno
    portal_activo BOOLEAN NOT NULL DEFAULT TRUE,
    total_visitas INTEGER DEFAULT 0,
    ultimo_acceso TIMESTAMP,

    CONSTRAINT fk_portal_tramite
        FOREIGN KEY (id_tramite) REFERENCES tramite(id) ON DELETE CASCADE
);

-- ============================================================
-- CU7. GESTION DE MONITOREO Y TIEMPOS DE ATENCION Y ALERTAS SLA (72 HORAS HABILES)
-- ============================================================

CREATE TABLE monitoreo_sla (
    id SERIAL PRIMARY KEY,
    id_tramite INTEGER UNIQUE NOT NULL,
    horas_sla_limite INTEGER NOT NULL DEFAULT 72,     -- Límite estricto: 72 horas hábiles
    fecha_inicio_sla TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_limite_sla TIMESTAMP NOT NULL,             -- Calculada: inicio + 72 horas hábiles
    horas_transcurridas NUMERIC(6,2) DEFAULT 0,
    estado_sla VARCHAR(30) NOT NULL DEFAULT 'EN_TIEMPO',
    -- Estados: 'EN_TIEMPO', 'POR_VENCER', 'VENCIDO', 'CUMPLIDO_EN_TIEMPO', 'CUMPLIDO_FUERA_DE_TIEMPO'
    alertas_emitidas INTEGER DEFAULT 0,
    ultima_alerta TIMESTAMP,

    CONSTRAINT fk_sla_tramite
        FOREIGN KEY (id_tramite) REFERENCES tramite(id) ON DELETE CASCADE,
    CONSTRAINT chk_estado_sla
        CHECK (estado_sla IN ('EN_TIEMPO', 'POR_VENCER', 'VENCIDO', 'CUMPLIDO_EN_TIEMPO', 'CUMPLIDO_FUERA_DE_TIEMPO'))
);

CREATE TABLE alerta_sla (
    id SERIAL PRIMARY KEY,
    id_monitoreo INTEGER NOT NULL,
    id_tramite INTEGER NOT NULL,
    tipo_alerta VARCHAR(50) NOT NULL,                -- 'ADVERTENCIA_24H', 'ADVERTENCIA_12H', 'VENCIMIENTO_SLA'
    mensaje TEXT NOT NULL,
    fecha_alerta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    notificado_a VARCHAR(150),                       -- Correo del responsable o Vicerrector notificado

    CONSTRAINT fk_alerta_monitoreo
        FOREIGN KEY (id_monitoreo) REFERENCES monitoreo_sla(id) ON DELETE CASCADE,
    CONSTRAINT fk_alerta_tramite
        FOREIGN KEY (id_tramite) REFERENCES tramite(id) ON DELETE CASCADE
);

-- ============================================================
-- CU8. REPORTES Y ESTADISTICAS
-- ============================================================

CREATE TABLE reporte_estadistica (
    id SERIAL PRIMARY KEY,
    id_usuario INTEGER NOT NULL,                     -- Usuario que generó el reporte
    tipo_reporte VARCHAR(100) NOT NULL,
    -- 'TRAMITES_POR_ESTADO', 'CUMPLIMIENTO_SLA', 'FIRMAS_POR_VICERRECTOR', 'TRAMITES_POR_CARRERA', 'OBSERVACIONES_FRECUENTES'
    parametros TEXT,
    resultado_resumen TEXT,
    fecha_generacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_reporte_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);