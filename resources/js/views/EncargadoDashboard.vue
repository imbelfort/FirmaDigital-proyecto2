<template>
  <div class="space-y-6">
    <!-- Card de Bienvenida M3 Estilizada y Limpia -->
    <div class="m3-hero-gradient rounded-3xl p-6 sm:p-7 text-white shadow-xl relative overflow-hidden transition-all duration-300">
      <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center space-x-2.5">
            <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white shadow-xs">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold heading-font tracking-tight">
              Hola, {{ state.user.nombre }} {{ state.user.apellidos }}
            </h1>
          </div>
          <p class="text-white/85 text-xs sm:text-sm mt-1.5 font-medium">
            Rol: <span class="font-bold text-white">{{ state.user.rol }}</span> | Código / Registro: <span class="font-mono text-white font-bold">{{ state.user.registro }}</span> | C.I.: <span class="font-mono text-white font-bold">{{ state.user.ci }}</span>
          </p>
        </div>
        <div class="flex items-center space-x-2 self-start sm:self-center">
          <span class="text-[11px] font-bold tracking-wider uppercase px-3.5 py-1.5 rounded-full bg-white/20 border border-white/25 backdrop-blur-md shadow-xs flex items-center space-x-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            <span>Mesa de Entrada & Revisión de Requisitos</span>
          </span>
        </div>
      </div>
      <!-- Decoración sutil de fondo -->
      <div class="absolute right-0 bottom-0 opacity-10 translate-x-12 translate-y-12 pointer-events-none">
        <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a10.97 10.97 0 00-.25 2.449c0 4.148 2.5 7.9 6 9.4 3.5-1.5 6-5.252 6-9.4 0-.847-.09-1.67-.25-2.45l2.644-1.13a1 1 0 000-1.84l-7-3z"/></svg>
      </div>
    </div>

    <!-- Navegación de Pestañas M3 para el Encargado de Mesa de Entrada -->
    <div class="flex items-center space-x-2 border-b border-slate-200 dark:border-slate-800 pb-2 overflow-x-auto">
      <button
        @click="activeTab = 'todos_tramites'"
        :class="activeTab === 'todos_tramites' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
        class="pb-2.5 px-3.5 text-xs sm:text-sm transition cursor-pointer shrink-0"
      >
        Trámites de Estudiantes ({{ tramites.length }})
      </button>
      <button
        @click="activeTab = 'despacho'"
        :class="activeTab === 'despacho' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
        class="pb-2.5 px-3.5 text-xs sm:text-sm transition cursor-pointer shrink-0"
      >
        Despacho de Legajos
      </button>
    </div>

    <!-- TAB 1: GESTIÓN DE TRÁMITES DE TODOS LOS ESTUDIANTES (Mover estados, cotejar, observar) -->
    <div v-if="activeTab === 'todos_tramites'" class="space-y-4">
      <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
        <h2 class="text-base font-bold text-slate-900 dark:text-white">Bandeja de Trámites Solicitados por Alumnos</h2>
        <div class="flex min-w-0 flex-wrap items-center gap-2" role="group" aria-label="Filtros de trámites">
          <select v-model="filtroEstadoTramites" aria-label="Estado del trámite" class="max-w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2.5 py-1.5 text-xs">
            <option value="">Todos los estados</option>
            <option v-for="estado in estadosTramites" :key="estado" :value="estado">{{ formatEstadoLegible(estado) }}</option>
          </select>
          <select v-model="filtroTipoTramites" aria-label="Tipo de trámite" class="max-w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2.5 py-1.5 text-xs">
            <option value="">Todos los tipos</option>
            <option v-for="[id, nombre] in tiposDeLaBandeja" :key="id" :value="id">{{ nombre }}</option>
          </select>
          <button v-if="codigoTramiteSeleccionado || filtroEstadoTramites || filtroTipoTramites" type="button"
                  @click="limpiarFiltrosTramites" class="px-2 py-1.5 text-xs font-semibold text-blue-700 dark:text-blue-300 cursor-pointer">
            Limpiar filtros
          </button>
        </div>
      </div>

      <!-- Skeleton Loader mientras recupera los trámites en el Encargado -->
      <SkeletonLoader v-if="loading" tipo="cards" :cantidad="4" />

      <div v-else-if="tramitesFiltrados.length === 0" class="m3-card p-12 text-center text-slate-500 border border-slate-200 dark:border-slate-800">
        {{ tramites.length ? 'No hay trámites que coincidan con los filtros.' : 'No hay trámites registrados en la bandeja.' }}
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="t in tramitesPaginados"
          :key="t.id"
          :id="`bandeja-${t.codigo_tramite}`"
          tabindex="-1"
          class="m3-card p-5 border border-slate-200 dark:border-slate-800 space-y-4 hover:border-blue-400/50 transition"
        >
          <!-- Encabezado con código, solicitante y estado -->
          <div class="flex items-center justify-between">
            <div>
              <span class="font-mono text-xs font-black text-blue-600 dark:text-blue-400 px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-950/40">
                {{ t.codigo_tramite }}
              </span>
              <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1">
                Alumno: {{ t.solicitante ? t.solicitante.nombre + ' ' + t.solicitante.apellidos : 'N/A' }}
              </p>
              <p class="text-[11px] text-slate-400 font-mono">Reg: {{ t.solicitante ? t.solicitante.registro : '' }} | CI: {{ t.solicitante ? t.solicitante.ci : '' }}</p>
            </div>
            <div class="text-right">
              <span class="m3-badge uppercase" :class="getEstadoBadge(t.estado)">
                {{ formatEstadoLegible(t.estado) }}
              </span>
              <div class="flex items-center justify-end space-x-1.5 mt-1">
                <span class="text-[10px] font-bold" :class="isPagado(t) ? 'text-emerald-600' : 'text-amber-600'">
                  {{ isPagado(t) ? 'PAGADO' : 'PAGO PENDIENTE' }} (Bs. {{ t.tipo_tramite ? t.tipo_tramite.costo_bs : '0.00' }})
                </span>
                <!-- Botón rápido para el Encargado: Eximir o Validar Pago en Ventanilla si PagoFácil no está activo -->
                <button
                  v-if="!isPagado(t) && !['FIRMADO', 'DESPACHADO'].includes(t.estado)"
                  @click="validarPagoEnVentanilla(t)"
                  class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-100 hover:bg-emerald-200 dark:bg-emerald-950 dark:hover:bg-emerald-900 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700 cursor-pointer transition shadow-2xs"
                  title="Permite al encargado validar el arancel en ventanilla o eximirlo sin depender de la pasarela QR"
                >
                  Omitir / Validar Pago
                </button>
              </div>
            </div>
          </div>

          <h3 class="font-bold text-slate-900 dark:text-white text-sm">{{ t.asunto }}</h3>

          <!-- Semáforo SLA de Control de Tiempos para el Encargado -->
          <div v-if="t.monitoreo_sla" class="p-3 rounded-2xl border text-xs space-y-1.5" :class="getSlaContainerClass(t.monitoreo_sla)">
            <div class="flex items-center justify-between">
              <span class="font-bold flex items-center space-x-1.5" :class="getSlaTextClass(t.monitoreo_sla)">
                <span class="w-2.5 h-2.5 rounded-full" :class="getSlaDotClass(t.monitoreo_sla)"></span>
                <span>{{ getSlaBadgeLabel(t.monitoreo_sla) }}</span>
              </span>
              <span class="font-mono text-[11px] text-slate-500 font-semibold">
                {{ formatSlaTiempoRestante(t.monitoreo_sla) }}
              </span>
            </div>
            <!-- Barra de progreso SLA -->
            <div class="w-full bg-slate-200 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
              <div
                class="h-full rounded-full transition-all duration-500"
                :class="getSlaBarClass(t.monitoreo_sla)"
                :style="{ width: getSlaPorcentaje(t.monitoreo_sla) + '%' }"
              ></div>
            </div>
            <div class="flex justify-between text-[10px] text-slate-400 font-mono">
              <span>Límite: {{ t.monitoreo_sla.horas_sla_limite }}h reglamentarias</span>
              <span v-if="t.monitoreo_sla.alertas && t.monitoreo_sla.alertas.length > 0" class="text-red-500 font-bold flex items-center space-x-1">
                <svg class="w-3 h-3 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ t.monitoreo_sla.alertas.length }} alerta(s) emitida(s)</span>
              </span>
            </div>
          </div>

          <!-- Cotejo de Recaudos -->
          <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
            <button type="button" @click="abrirAdjuntos(t)" class="px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 text-xs font-bold cursor-pointer">
              Ver adjuntos ({{ t.documentos?.length ?? 0 }})
            </button>
            <div v-for="d in t.documentos" :key="d.id" class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 text-xs space-y-2">
              <div class="flex items-center justify-between">
                <div>
                  <p class="font-semibold text-slate-800 dark:text-slate-200">{{ d.nombre_documento }}</p>
                  <p class="text-[10px] font-mono text-slate-400 truncate max-w-[200px]">SHA-256: {{ d.hash_documento ? d.hash_documento.substring(0, 16) + '...' : '' }}</p>
                </div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="getDocBadge(d.estado_validacion)">{{ d.estado_validacion === 'PENDIENTE' && d.observacion_validacion ? 'Corrección en revisión' : formatEstadoLegible(d.estado_validacion) }}</span>
              </div>

              <!-- Si el recaudo está observado, mostrar la nota explicativa -->
              <div v-if="d.estado_validacion === 'OBSERVADO'" class="p-2 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-[11px] text-amber-800 dark:text-amber-300 flex items-start space-x-1.5">
                <svg class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                  <p class="font-bold">{{ motivosObservacion[d.tipo_observacion] || 'Observación' }}</p>
                  <p>{{ d.observacion_validacion || 'Documento requiere corrección por parte del estudiante.' }}</p>
                </div>
              </div>

              <!-- Acciones de validación para el Encargado (Solo si el trámite NO ha concluido ni está en firma) -->
              <div v-if="['INICIADO', 'OBSERVADO'].includes(t.estado)" class="flex items-center space-x-2 pt-1 border-t border-slate-200/60 dark:border-slate-700/60">
                <button
                  @click="aprobarDocDirecto(d)"
                  class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[10px] font-bold cursor-pointer transition flex items-center space-x-1"
                  title="Aprobar Recaudo"
                >
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                  <span>Aprobar</span>
                </button>
                <button
                  @click="abrirModalObservarDoc(d)"
                  class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-[10px] font-bold cursor-pointer transition flex items-center space-x-1"
                  title="Observar este documento específico con motivo obligatorio"
                >
                  <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                  <span>Observar Recaudo</span>
                </button>
                <button
                  v-if="d.archivo_url"
                  type="button" @click="abrirAdjuntos(t, d)"
                  class="px-2 py-1 text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 text-[10px] underline ml-auto cursor-pointer"
                >
                  Ver archivo
                </button>
              </div>
              <div v-else class="flex items-center justify-between pt-1 border-t border-slate-200/60 dark:border-slate-700/60 text-[10px] text-slate-400">
                <span class="italic font-medium">Revisión cerrada (Trámite en {{ formatEstadoLegible(t.estado) }})</span>
                <button
                  v-if="d.archivo_url"
                  type="button" @click="abrirAdjuntos(t, d)"
                  class="text-blue-600 dark:text-blue-400 underline cursor-pointer"
                >
                  Ver archivo
                </button>
              </div>
            </div>
          </div>

          <!-- BOTONES PARA MOVER DE ESTADO EL TRÁMITE (Flujo Progresivo Inteligente) -->
          <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Mover Estado Procesal:</span>
            <p v-if="tieneAdjuntosBloqueantes(t)" class="text-xs text-amber-800 dark:text-amber-300">
              El trámite puede avanzar cuando se corrijan los adjuntos observados y se aprueben las correcciones.
            </p>
            
            <!-- Trámite ya en etapas finales o de firma (Informativo) -->
            <div v-if="t.estado === 'FIRMADO' || t.estado === 'DESPACHADO'" class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 font-semibold flex items-center space-x-1.5">
              <span class="flex items-center space-x-1.5">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Trámite finalizado procesalmente ({{ formatEstadoLegible(t.estado) }}).</span>
              </span>
            </div>

            <div v-else-if="t.estado === 'EN_FIRMA'" class="p-2.5 rounded-xl bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800 text-xs text-purple-800 dark:text-purple-300 font-semibold">
              <span>⏳ En Despacho del Vicerrector para firma digital criptográfica.</span>
            </div>

            <!-- Botones dinámicos según el estado actual -->
            <div v-else class="grid grid-cols-2 gap-2 text-xs">
              <!-- Paso 1: Validar Requisitos (Solo habilitado si está INICIADO u OBSERVADO) -->
              <button
                v-if="t.estado === 'INICIADO' || t.estado === 'OBSERVADO'"
                @click="solicitarConfirmacionAvance(t, 'Revisión y Cotejo', 'REQUISITOS_VALIDADOS')"
                :disabled="procesandoAccion || tieneAdjuntosBloqueantes(t)"
                class="py-2 px-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition cursor-pointer disabled:opacity-50 text-left shadow-xs flex items-center justify-between"
              >
                <span>1. Validar Requisitos</span>
                <span class="text-[10px] font-mono">→</span>
              </button>
              <div
                v-else-if="t.estado !== 'INICIADO' && t.estado !== 'OBSERVADO'"
                class="py-2 px-2.5 bg-slate-50 dark:bg-slate-800/50 text-slate-400 dark:text-slate-500 rounded-xl text-left font-semibold border border-dashed border-slate-200 dark:border-slate-700 flex items-center space-x-1"
              >
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Requisitos ya validados</span>
              </div>

              <!-- Paso 2: Compilar Legajo (Habilitado si los requisitos están validados) -->
              <button
                v-if="t.estado === 'REQUISITOS_VALIDADOS'"
                @click="solicitarConfirmacionAvance(t, 'Compilación de Expediente', 'EXPEDIENTE_COMPILADO')"
                :disabled="procesandoAccion || tieneAdjuntosBloqueantes(t)"
                class="py-2 px-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition cursor-pointer disabled:opacity-50 text-left shadow-xs flex items-center justify-between"
              >
                <span>2. Compilar Legajo</span>
                <span class="text-[10px] font-mono">→</span>
              </button>
              <div
                v-else-if="t.estado === 'EXPEDIENTE_COMPILADO'"
                class="py-2 px-2.5 bg-slate-50 dark:bg-slate-800/50 text-slate-400 dark:text-slate-500 rounded-xl text-left font-semibold border border-dashed border-slate-200 dark:border-slate-700 flex items-center space-x-1"
              >
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Expediente ya compilado</span>
              </div>

              <!-- Paso 3: Pasar a Firma Vicerrectoral (Habilitado cuando el expediente está compilado) -->
              <button
                v-if="t.estado === 'EXPEDIENTE_COMPILADO'"
                @click="solicitarConfirmacionAvance(t, 'Firma Vicerrectoral', 'EN_FIRMA')"
                :disabled="procesandoAccion || tieneAdjuntosBloqueantes(t)"
                class="py-2 px-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl transition cursor-pointer shadow-sm disabled:opacity-50 text-left flex items-center justify-between"
              >
                <span>3. Pasar a Firma VR</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
              </button>

              <button
                v-if="t.estado === 'INICIADO'"
                @click="openObservacion(t)"
                :disabled="procesandoAccion"
                class="py-2 px-2.5 bg-red-100 hover:bg-red-200 dark:bg-red-950/40 text-red-700 dark:text-red-300 font-bold rounded-xl transition cursor-pointer disabled:opacity-50 text-left"
              >
                ! Observar Trámite
              </button>
            </div>
          </div>

        </div>
      </div>
      <nav v-if="!loading && tramitesFiltrados.length" class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500" aria-label="Páginas de trámites">
        <span aria-live="polite">{{ (paginaVisible - 1) * tramitesPorPagina + 1 }}–{{ Math.min(paginaVisible * tramitesPorPagina, tramitesFiltrados.length) }} de {{ tramitesFiltrados.length }} trámites</span>
        <div v-if="totalPaginasTramites > 1" class="flex items-center gap-2">
          <button type="button" :disabled="paginaVisible === 1" @click="paginaTramites = paginaVisible - 1" class="rounded-lg border border-slate-300 dark:border-slate-700 px-3 py-1.5 cursor-pointer disabled:opacity-40">Anterior</button>
          <button type="button" :disabled="paginaVisible === totalPaginasTramites" @click="paginaTramites = paginaVisible + 1" class="rounded-lg border border-slate-300 dark:border-slate-700 px-3 py-1.5 cursor-pointer disabled:opacity-40">Siguiente</button>
        </div>
      </nav>
    </div>

    <!-- TAB 2: GESTIÓN DE TIPOS DE TRÁMITES (Crear, Editar aranceles Bs y SLAs) -->
    <div v-if="activeTab === 'catalogo_gestion'" class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-base font-bold text-slate-900 dark:text-white">Tipos de Trámites Oficiales (tipo_tramite)</h2>
          <p class="text-xs text-slate-500">Crea nuevos trámites o modifica los precios oficiales en Bs y plazos de atención.</p>
        </div>
        <button
          @click="abrirModalTipoTramite(null)"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-full shadow-sm cursor-pointer"
        >
          + Crear Nuevo Tipo de Trámite
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="tipo in tiposTramite"
          :key="tipo.id"
          class="m3-card p-5 border border-slate-200 dark:border-slate-800 space-y-3"
        >
          <div class="flex items-center justify-between">
            <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-blue-600 dark:text-blue-400">{{ tipo.codigo }}</span>
            <div class="text-right">
              <span class="text-[10px] text-slate-400 block font-bold">Precio Oficial</span>
              <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">Bs. {{ tipo.costo_bs }}</span>
            </div>
          </div>

          <h3 class="font-bold text-slate-900 dark:text-white text-base">{{ tipo.nombre }}</h3>
          <p class="text-xs text-slate-500">{{ tipo.descripcion }}</p>
          <p class="text-xs text-slate-600 dark:text-slate-300">⏱️ Plazo SLA: <strong class="font-mono text-blue-600">{{ tipo.tiempo_sla_horas }} horas</strong></p>

          <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex justify-end space-x-2">
            <button
              @click="abrirModalTipoTramite(tipo)"
              class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs rounded-full transition cursor-pointer"
            >
              Editar Arancel / Datos
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 3: GESTIÓN DE USUARIOS (Alumnos, Docentes, Personal) -->
    <div v-if="activeTab === 'usuarios'" class="space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-900 dark:text-white">Usuarios del Sistema</h2>
        <button @click="modalNuevoUsuario = true" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-full shadow-sm cursor-pointer">
          + Agregar Usuario
        </button>
      </div>

      <div class="m3-card border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-400 uppercase font-semibold">
              <tr>
                <th class="p-3">Nombre</th>
                <th class="p-3">Código/Registro</th>
                <th class="p-3">C.I.</th>
                <th class="p-3">Rol</th>
                <th class="p-3">Unidad / Carrera</th>
                <th class="p-3">Estado</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-for="u in usuarios" :key="u.id_usuario" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                <td class="p-3 font-bold text-slate-800 dark:text-slate-200">{{ u.nombre }} {{ u.apellidos }}</td>
                <td class="p-3 font-mono">{{ u.registro }}</td>
                <td class="p-3 font-mono">{{ u.ci }}</td>
                <td class="p-3 font-bold text-blue-600 dark:text-blue-400">{{ u.rol }}</td>
                <td class="p-3 text-slate-500">{{ u.unidad_academica || u.cargo || 'UAGRM' }}</td>
                <td class="p-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">{{ u.estado }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 4: DESPACHO DE LEGAJOS -->
    <div v-if="activeTab === 'despacho'" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="t in tramitesFirmados"
          :key="t.id"
          class="m3-card p-5 border border-slate-200 dark:border-slate-800 space-y-3"
        >
          <div class="flex items-center justify-between">
            <span class="font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ t.codigo_tramite }}</span>
            <span class="m3-badge bg-emerald-100 text-emerald-800">FIRMADO POR VR</span>
          </div>
          <h3 class="font-bold text-slate-900 dark:text-white">{{ t.asunto }}</h3>
          <p class="text-xs text-slate-500">Alumno: {{ t.solicitante ? t.solicitante.nombre + ' ' + t.solicitante.apellidos : '' }}</p>
          <button type="button" @click="abrirAdjuntos(t)" class="px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 text-xs font-bold cursor-pointer">
            Ver adjuntos ({{ t.documentos?.length ?? 0 }})
          </button>

          <div v-if="t.despacho" class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-xs space-y-1">
            <p class="font-bold text-emerald-600">Despachado Oficialmente</p>
            <p>Nro. Despacho: <span class="font-mono font-bold">{{ t.despacho.numero_despacho }}</span></p>
          </div>
          <div v-else class="flex space-x-2 pt-2">
            <button
              @click="ejecutarDespacho(t, 'DIGITAL_CORREO')"
              class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-full transition shadow-sm cursor-pointer"
            >
              Despachar por Correo
            </button>
            <button
              @click="ejecutarDespacho(t, 'PORTAL_DESCARGA')"
              class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-full transition shadow-sm cursor-pointer"
            >
              Habilitar Descarga Portal
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 5: BITÁCORA DE AUDITORÍA Y SEGURIDAD (Exclusivo Administrador) -->
    <div v-if="activeTab === 'auditoria'" class="space-y-5">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
            <svg class="w-4 h-4 text-slate-600 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span>Bitácora de Auditoría del Sistema</span>
            <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300 font-bold">Confidencial</span>
          </h2>
          <p class="text-xs text-slate-500">Registro inmutable de inicios de sesión, accesos fallidos, tráfico y bloqueos de seguridad.</p>
        </div>
        <div class="flex items-center space-x-2">
          <select
            v-model="filtroTipoAuditoria"
            @change="cargarAuditoria"
            class="px-3 py-1.5 rounded-full border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-200"
          >
            <option value="TODOS">Todos los Eventos</option>
            <option value="LOGIN_EXITOSO">Logins Exitosos</option>
            <option value="LOGIN_FALLIDO">Logins Fallidos</option>
            <option value="TRAFICO_OPERATIVO">Tráfico Operativo</option>
            <option value="ACCESO_NO_AUTORIZADO">Bloqueos de Seguridad</option>
          </select>
          <button
            @click="cargarAuditoria"
            class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-full shadow-xs cursor-pointer"
          >
            ↻ Actualizar
          </button>
        </div>
      </div>

      <!-- KPIs Rápidos de Auditoría -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
        <div class="m3-card p-4 border border-slate-200 dark:border-slate-800">
          <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Logins Exitosos</span>
          <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ auditoriaKpis.logins_exitosos }}</p>
        </div>
        <div class="m3-card p-4 border border-slate-200 dark:border-slate-800">
          <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block">Logins Fallidos</span>
          <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ auditoriaKpis.logins_fallidos }}</p>
        </div>
        <div class="m3-card p-4 border border-slate-200 dark:border-slate-800">
          <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 block">Tráfico Operativo</span>
          <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ auditoriaKpis.trafico_operativo }}</p>
        </div>
        <div class="m3-card p-4 border border-slate-200 dark:border-slate-800">
          <span class="text-[10px] font-bold uppercase tracking-wider text-red-600 block">Bloqueos RBAC</span>
          <p class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ auditoriaKpis.bloqueos_seguridad }}</p>
        </div>
      </div>

      <!-- Tabla de Eventos Registrados -->
      <div class="m3-card border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-bold border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="p-3">ID / Fecha</th>
                <th class="p-3">Tipo Evento</th>
                <th class="p-3">Usuario / Reg</th>
                <th class="p-3">Descripción</th>
                <th class="p-3">Ruta & Método</th>
                <th class="p-3">IP Origen</th>
                <th class="p-3 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-if="auditoriaLogs.length === 0">
                <td colspan="7" class="p-6 text-center text-slate-400">No hay eventos registrados con el filtro actual.</td>
              </tr>
              <tr v-for="log in auditoriaLogs" :key="log.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                <td class="p-3 font-mono text-[11px] text-slate-500">
                  <span class="font-bold text-slate-700 dark:text-slate-300">#{{ log.id }}</span>
                  <br />{{ formatFecha(log.fecha_evento) }}
                </td>
                <td class="p-3">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="getBadgeAuditoria(log.tipo_evento)">
                    {{ log.tipo_evento }}
                  </span>
                </td>
                <td class="p-3 font-mono text-[11px]">
                  {{ log.registro || 'N/A' }}
                  <span v-if="log.id_usuario" class="text-slate-400 block text-[10px]">UID: {{ log.id_usuario }}</span>
                </td>
                <td class="p-3 text-slate-800 dark:text-slate-200 max-w-xs truncate" :title="log.descripcion">
                  {{ log.descripcion }}
                </td>
                <td class="p-3 font-mono text-[11px] text-slate-500">
                  <span class="font-bold text-blue-600 dark:text-blue-400">{{ log.metodo_http || 'GET' }}</span> /{{ log.ruta_accedida }}
                </td>
                <td class="p-3 font-mono text-[11px] text-slate-500">{{ log.ip_origen || '127.0.0.1' }}</td>
                <td class="p-3 text-center">
                  <span class="px-2 py-0.5 rounded font-mono text-[10px] font-bold" :class="log.status_code >= 400 ? 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300'">
                    {{ log.status_code || 200 }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL EDITAR / CREAR TIPO DE TRÁMITE -->
    <ModalAdjuntos ref="modalAdjuntos" :formato-estado="formatEstadoLegible" />

    <div v-if="modalTipoTramite" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="m3-card p-6 sm:p-8 max-w-md w-full space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
          {{ editandoTipoId ? 'Editar Tipo de Trámite' : 'Crear Nuevo Tipo de Trámite' }}
        </h3>
        
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Código del Trámite</label>
          <input
            v-model="formTipo.codigo"
            :disabled="!!editandoTipoId"
            type="text"
            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono uppercase"
            placeholder="EJ: DIPLOMA_ACAD"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nombre del Trámite</label>
          <input
            v-model="formTipo.nombre"
            type="text"
            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
            placeholder="Ej: Diploma Académico de Grado"
          />
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Precio Oficial (Bs.)</label>
            <input
              v-model="formTipo.costo_bs"
              type="number"
              step="0.50"
              class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tiempo SLA (Horas)</label>
            <input
              v-model="formTipo.tiempo_sla_horas"
              type="number"
              class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold"
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Descripción</label>
          <textarea
            v-model="formTipo.descripcion"
            rows="2"
            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
          ></textarea>
        </div>

        <div class="flex justify-end space-x-2 pt-2">
          <button @click="modalTipoTramite = false" class="px-4 py-2 text-xs font-semibold text-slate-500 cursor-pointer">Cancelar</button>
          <button @click="guardarTipoTramite" class="px-5 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow cursor-pointer">Guardar</button>
        </div>
      </div>
    </div>

    <!-- Modal Observación a Nivel de Trámite -->
    <div v-if="modalObs" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="m3-card p-6 sm:p-8 max-w-md w-full space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Emitir Observación al Trámite</h3>
        <p class="text-xs text-slate-500">El trámite pasará a estado <strong>OBSERVADO</strong> y se notificará al estudiante para su subsanación.</p>
        
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipo de Observación</label>
          <select v-model="formObs.tipo_observacion" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm">
            <option value="FALTA_REQUISITO">Falta de Requisito Obligatorio</option>
            <option value="DOCUMENTO_ILEGIBLE">Documento Ilegible o Incompleto</option>
            <option value="ERROR_DATOS">Error en los Datos Personales</option>
          </select>
        </div>
        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
              Descripción de la Observación <span class="text-red-500 font-bold">*</span>
            </label>
            <span class="text-[10px] text-amber-600 font-bold">Obligatorio</span>
          </div>
          <textarea
            v-model="formObs.descripcion"
            rows="3"
            class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-red-500 focus:outline-hidden"
            placeholder="Especifique detalladamente qué debe corregir o presentar el estudiante..."
          ></textarea>
          <p v-if="errorObsTramite" class="text-xs font-semibold text-red-600 dark:text-red-400 mt-1">
            {{ errorObsTramite }}
          </p>
        </div>
        <div class="flex justify-end space-x-2 pt-4">
          <button @click="modalObs = false" class="px-4 py-2 text-xs font-semibold text-slate-500 cursor-pointer">Cancelar</button>
          <button @click="guardarObservacion" class="px-5 py-2 text-xs font-bold bg-red-600 hover:bg-red-700 text-white rounded-full shadow cursor-pointer">Guardar Observación</button>
        </div>
      </div>
    </div>

    <!-- Modal Específico para Observar un Recaudo/Documento -->
    <div v-if="modalDocObs" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="m3-card p-6 sm:p-8 max-w-md w-full space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl">
        <div>
          <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
            Cotejo Documental
          </span>
          <h3 class="text-lg font-bold text-slate-900 dark:text-white mt-1">Observar Recaudo Específico</h3>
          <p class="text-xs text-slate-500 mt-0.5">
            Documento: <strong class="text-slate-800 dark:text-slate-200">{{ docEnObservacion ? docEnObservacion.nombre_documento : '' }}</strong>
          </p>
        </div>

        <div>
          <label for="tipo-observacion-adjunto" class="block text-xs font-semibold mb-1">Motivo de la observación</label>
          <select id="tipo-observacion-adjunto" v-model="formDocObs.tipo_observacion" required
                  class="w-full p-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-sm">
            <option disabled value="">Selecciona un motivo</option>
            <option v-for="(motivo, codigo) in motivosObservacion" :key="codigo" :value="codigo">{{ motivo }}</option>
          </select>
        </div>
        <div>
          <div class="flex items-center justify-between mb-1">
            <label for="detalle-observacion-adjunto" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
              Detalle para el alumno <span class="text-red-500 font-bold">*</span>
            </label>
            <span class="text-[10px] text-amber-600 font-bold">Campo Obligatorio</span>
          </div>
          <textarea
            id="detalle-observacion-adjunto"
            v-model="formDocObs.observacion_validacion"
            rows="3"
            class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-amber-500 focus:outline-hidden"
            placeholder="Ej: El archivo PDF adjuntado está borroso, incompleto o no coincide con los sellos oficiales..."
          ></textarea>
          <p v-if="errorDocObs" class="text-xs font-semibold text-red-600 dark:text-red-400 mt-1">
            {{ errorDocObs }}
          </p>
        </div>

        <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100 dark:border-slate-800">
          <button @click="modalDocObs = false" class="px-4 py-2 text-xs font-semibold text-slate-500 cursor-pointer">
            Cancelar
          </button>
          <button
            @click="confirmarObservarDoc"
            :disabled="procesandoAccion"
            class="px-5 py-2 text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white rounded-full shadow cursor-pointer disabled:opacity-50"
          >
            Confirmar Observación de Recaudo
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmación para Mover Estado Procesal -->
    <div v-if="modalConfirmarAvance" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="m3-card p-6 sm:p-7 max-w-md w-full space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 rounded-2xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Confirmar Transición Procesal</h3>
            <p class="text-xs text-slate-500">Trámite: <span class="font-mono font-bold">{{ tramiteAvanzarInfo ? tramiteAvanzarInfo.codigo_tramite : '' }}</span></p>
          </div>
        </div>

        <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 space-y-2 text-xs">
          <div class="flex justify-between">
            <span class="text-slate-500">Solicitante:</span>
            <span class="font-bold text-slate-800 dark:text-slate-200">{{ tramiteAvanzarInfo?.solicitante ? tramiteAvanzarInfo.solicitante.nombre + ' ' + tramiteAvanzarInfo.solicitante.apellidos : 'N/A' }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500">Etapa Destino:</span>
            <span class="font-bold text-blue-600 dark:text-blue-400">{{ etapaAvanzarDestino }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500">Nuevo Estado DB:</span>
            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ estadoAvanzarDestino }}</span>
          </div>
        </div>

        <p class="text-xs text-slate-600 dark:text-slate-400">
          ¿Está seguro de avanzar este expediente? Esta acción cerrará la etapa previa, registrará la firma de auditoría del encargado y notificará el avance.
        </p>

        <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100 dark:border-slate-800">
          <button
            @click="modalConfirmarAvance = false"
            :disabled="procesandoAccion"
            class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-700 cursor-pointer disabled:opacity-50"
          >
            Cancelar
          </button>
          <button
            @click="ejecutarAvanceConfirmado"
            :disabled="procesandoAccion"
            class="px-5 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-md cursor-pointer transition flex items-center space-x-1.5 disabled:opacity-50"
          >
            <span v-if="procesandoAccion" class="animate-spin rounded-full h-3.5 w-3.5 border-2 border-white border-t-transparent"></span>
            <span>{{ procesandoAccion ? 'Avanzando...' : 'Sí, Confirmar Avance' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Nuevo Usuario -->
    <div v-if="modalNuevoUsuario" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="m3-card p-6 sm:p-8 max-w-md w-full space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Registrar Nuevo Usuario</h3>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nombre</label>
            <input v-model="formUser.nombre" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Apellidos</label>
            <input v-model="formUser.apellidos" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Código / Registro</label>
            <input v-model="formUser.registro" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono" placeholder="219000000" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">C.I.</label>
            <input v-model="formUser.ci" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono" placeholder="4567890" />
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Correo</label>
          <input v-model="formUser.email" type="email" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs" placeholder="usuario@uagrm.edu.bo" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Rol</label>
          <select v-model="formUser.rol" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs">
            <option value="ALUMNO">ALUMNO</option>
            <option value="DOCENTE">DOCENTE</option>
            <option value="ENCARGADO">ENCARGADO</option>
            <option value="ADMINISTRATIVO">ADMINISTRATIVO</option>
            <option value="VICERRECTOR">VICERRECTOR</option>
          </select>
        </div>

        <!-- Selector de Carrera si el usuario es ALUMNO -->
        <div v-if="formUser.rol === 'ALUMNO'" class="space-y-1 p-3 bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-900/40 rounded-2xl">
          <label class="block text-xs font-bold text-blue-900 dark:text-blue-300">Carrera Universitaria</label>
          <select v-model="formUser.carrera" class="w-full px-3 py-2 rounded-xl border border-blue-300 dark:border-blue-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200">
            <option value="Ingenieria Informatica">Ingeniería Informática</option>
            <option value="Ingenieria en Sistemas">Ingeniería en Sistemas</option>
            <option value="Ingenieria en Redes y Telecomunicaciones">Ingeniería en Redes y Telecomunicaciones</option>
            <option value="Ingenieria Comercial">Ingeniería Comercial</option>
            <option value="Administracion de Empresas">Administración de Empresas</option>
            <option value="Contaduria Publica">Contaduría Pública</option>
            <option value="Derecho">Derecho</option>
            <option value="Medicina">Medicina</option>
            <option value="Arquitectura">Arquitectura</option>
          </select>
        </div>

        <div class="flex justify-end space-x-2 pt-4">
          <button @click="modalNuevoUsuario = false" class="px-4 py-2 text-xs font-semibold text-slate-500 cursor-pointer">Cancelar</button>
          <button @click="guardarUsuario" class="px-5 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow cursor-pointer">Crear Usuario</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { formatFecha } from '../fechas';
import { motivosObservacion } from '../motivosObservacion';
import { ref, computed, onMounted, watch } from 'vue';
import { state, actions } from '../stores/appState';
import axios from 'axios';
import ModalAdjuntos from '../components/ModalAdjuntos.vue';
import SkeletonLoader from '../components/SkeletonLoader.vue';

const activeTab = ref('todos_tramites');
const modalAdjuntos = ref(null);

function abrirAdjuntos(tramite, documento) {
  modalAdjuntos.value.abrir(tramite, documento);
}

// Helpers visuales para el Semáforo SLA
function getSlaBadgeLabel(sla) {
  if (!sla) return 'Sin SLA';
  if (['CUMPLIDO_EN_TIEMPO', 'CUMPLIDO_FUERA_DE_TIEMPO'].includes(sla.estado_sla)) {
    return 'Cumplido / Concluido';
  }
  if (sla.estado_sla === 'VENCIDO') return 'SLA Vencido';
  if (sla.estado_sla === 'POR_VENCER') return 'Por Vencer (<24h)';
  return 'En Tiempo';
}

function getSlaContainerClass(sla) {
  if (!sla) return 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700';
  if (['CUMPLIDO_EN_TIEMPO', 'CUMPLIDO_FUERA_DE_TIEMPO'].includes(sla.estado_sla)) {
    return 'bg-blue-50/60 dark:bg-blue-950/20 border-blue-200 dark:border-blue-900/40';
  }
  if (sla.estado_sla === 'VENCIDO') {
    return 'bg-red-50/60 dark:bg-red-950/30 border-red-200 dark:border-red-900/50';
  }
  if (sla.estado_sla === 'POR_VENCER') {
    return 'bg-amber-50/60 dark:bg-amber-950/30 border-amber-200 dark:border-amber-900/50';
  }
  return 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-900/40';
}

function getSlaTextClass(sla) {
  if (!sla) return 'text-slate-500';
  if (['CUMPLIDO_EN_TIEMPO', 'CUMPLIDO_FUERA_DE_TIEMPO'].includes(sla.estado_sla)) return 'text-blue-600 dark:text-blue-400';
  if (sla.estado_sla === 'VENCIDO') return 'text-red-600 dark:text-red-400';
  if (sla.estado_sla === 'POR_VENCER') return 'text-amber-600 dark:text-amber-400';
  return 'text-emerald-600 dark:text-emerald-400';
}

function getSlaDotClass(sla) {
  if (!sla) return 'bg-slate-400';
  if (['CUMPLIDO_EN_TIEMPO', 'CUMPLIDO_FUERA_DE_TIEMPO'].includes(sla.estado_sla)) return 'bg-blue-500';
  if (sla.estado_sla === 'VENCIDO') return 'bg-red-500 animate-pulse';
  if (sla.estado_sla === 'POR_VENCER') return 'bg-amber-500 animate-bounce';
  return 'bg-emerald-500';
}

function getSlaBarClass(sla) {
  if (!sla) return 'bg-slate-400';
  if (['CUMPLIDO_EN_TIEMPO', 'CUMPLIDO_FUERA_DE_TIEMPO'].includes(sla.estado_sla)) return 'bg-blue-500';
  if (sla.estado_sla === 'VENCIDO') return 'bg-red-500';
  if (sla.estado_sla === 'POR_VENCER') return 'bg-amber-500';
  return 'bg-emerald-500';
}

function getSlaPorcentaje(sla) {
  if (!sla || !sla.horas_sla_limite) return 0;
  if (['CUMPLIDO_EN_TIEMPO', 'CUMPLIDO_FUERA_DE_TIEMPO'].includes(sla.estado_sla)) return 100;
  const transcurridas = parseFloat(sla.horas_transcurridas) || 0;
  const limite = parseFloat(sla.horas_sla_limite) || 72;
  const pct = Math.min(Math.round((transcurridas / limite) * 100), 100);
  return pct;
}

function formatSlaTiempoRestante(sla) {
  if (!sla) return '';
  if (['CUMPLIDO_EN_TIEMPO', 'CUMPLIDO_FUERA_DE_TIEMPO'].includes(sla.estado_sla)) {
    return 'Trámite Concluido';
  }
  const transcurridas = parseFloat(sla.horas_transcurridas) || 0;
  const limite = parseFloat(sla.horas_sla_limite) || 72;
  const restantes = Math.round(limite - transcurridas);
  if (restantes <= 0) {
    return `Excedido por ${Math.abs(restantes)}h`;
  }
  return `Quedan ${restantes}h (${transcurridas}h usadas)`;
}

const loading = ref(false);
const tramites = ref([]);
const codigoTramiteSeleccionado = ref(null);
const filtroEstadoTramites = ref('');
const filtroTipoTramites = ref('');
const paginaTramites = ref(1);
const tramitePorEnfocar = ref(null);
const tramitesPorPagina = 8;

const estadosTramites = computed(() => [...new Set(tramites.value.map(tramite => tramite.estado))]);
const tiposDeLaBandeja = computed(() => [...new Map(tramites.value
  .filter(tramite => tramite.tipo_tramite)
  .map(tramite => [tramite.id_tipo_tramite, tramite.tipo_tramite.nombre]))]
  .sort((a, b) => a[1].localeCompare(b[1], 'es-BO')));

const tramitesFiltrados = computed(() => {
  return tramites.value.filter(tramite => {
    if (filtroEstadoTramites.value && tramite.estado !== filtroEstadoTramites.value) return false;
    if (filtroTipoTramites.value && tramite.id_tipo_tramite !== filtroTipoTramites.value) return false;
    return !codigoTramiteSeleccionado.value || tramite.codigo_tramite === codigoTramiteSeleccionado.value;
  });
});

const totalPaginasTramites = computed(() => Math.max(1, Math.ceil(tramitesFiltrados.value.length / tramitesPorPagina)));
const paginaVisible = computed(() => Math.min(paginaTramites.value, totalPaginasTramites.value));
const tramitesPaginados = computed(() => {
  const inicio = (paginaVisible.value - 1) * tramitesPorPagina;
  return tramitesFiltrados.value.slice(inicio, inicio + tramitesPorPagina);
});

function limpiarFiltrosTramites() {
  codigoTramiteSeleccionado.value = null;
  filtroEstadoTramites.value = '';
  filtroTipoTramites.value = '';
  paginaTramites.value = 1;
}

watch([filtroEstadoTramites, filtroTipoTramites], () => {
  paginaTramites.value = 1;
});

watch(() => state.tramiteParaUbicar, codigo => {
  if (!codigo) return;
  activeTab.value = 'todos_tramites';
  limpiarFiltrosTramites();
  codigoTramiteSeleccionado.value = codigo;
  tramitePorEnfocar.value = codigo;
  state.tramiteParaUbicar = null;
}, { immediate: true });

watch([tramitePorEnfocar, loading], ([codigo, cargando]) => {
  if (!codigo || cargando) return;
  const tarjeta = document.getElementById(`bandeja-${codigo}`);
  if (!tarjeta) return;
  tarjeta.focus({ preventScroll: true });
  tarjeta.scrollIntoView({ block: 'center' });
  tramitePorEnfocar.value = null;
}, { flush: 'post' });
const tiposTramite = ref([]);
const usuarios = ref([]);

// Estado para Bitácora de Auditoría del Administrador
const filtroTipoAuditoria = ref('TODOS');
const auditoriaLogs = ref([]);
const auditoriaKpis = ref({
  logins_exitosos: 0,
  logins_fallidos: 0,
  trafico_operativo: 0,
  bloqueos_seguridad: 0,
});

async function cargarAuditoria() {
  try {
    const res = await axios.get('/api/auditoria', {
      params: { tipo: filtroTipoAuditoria.value }
    });
    if (res.data.success) {
      auditoriaLogs.value = res.data.data || [];
      auditoriaKpis.value = res.data.kpis || auditoriaKpis.value;
    }
  } catch (e) {
    console.error('Error al cargar bitácora:', e);
  }
}

function getBadgeAuditoria(tipo) {
  switch (tipo) {
    case 'LOGIN_EXITOSO': return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
    case 'LOGIN_FALLIDO': return 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300';
    case 'ACCESO_NO_AUTORIZADO': return 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300';
    default: return 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300';
  }
}

const modalObs = ref(false);
const tramiteSeleccionado = ref(null);
const formObs = ref({ tipo_observacion: 'FALTA_REQUISITO', descripcion: '' });
const errorObsTramite = ref('');

// Estado y modal para Observación Específica de Recaudo
const modalDocObs = ref(false);
const docEnObservacion = ref(null);
const formDocObs = ref({ tipo_observacion: '', observacion_validacion: '' });
const errorDocObs = ref('');

function abrirModalObservarDoc(doc) {
  docEnObservacion.value = doc;
  formDocObs.value = {
    tipo_observacion: doc.estado_validacion === 'OBSERVADO' ? (doc.tipo_observacion ?? '') : '',
    observacion_validacion: doc.estado_validacion === 'OBSERVADO' ? (doc.observacion_validacion ?? '') : '',
  };
  errorDocObs.value = '';
  modalDocObs.value = true;
}

async function aprobarDocDirecto(doc) {
  if (procesandoAccion.value) return;
  procesandoAccion.value = true;
  try {
    await axios.post(`/api/documentos/${doc.id}/validar`, {
      estado_validacion: 'APROBADO',
      hash_documento: doc.hash_documento,
      observacion_validacion: 'Documento aprobado conforme a requisitos oficiales.',
    });
    actions.toast(`Documento "${doc.nombre_documento}" verificado y aprobado.`, 'success', 'Recaudo Aprobado');
    await loadData();
  } catch (e) {
    actions.toast(e.response?.data?.message || 'Error al aprobar el documento.', 'error', 'Error Recaudo');
  } finally {
    procesandoAccion.value = false;
  }
}

async function confirmarObservarDoc() {
  if (!formDocObs.value.tipo_observacion) {
    errorDocObs.value = 'Selecciona un motivo para la observación.';
    return;
  }
  if (!formDocObs.value.observacion_validacion || formDocObs.value.observacion_validacion.trim() === '') {
    errorDocObs.value = 'Atención: Debe escribir el motivo de la observación para que el estudiante sepa qué subsanar.';
    return;
  }
  errorDocObs.value = '';
  if (procesandoAccion.value) return;
  procesandoAccion.value = true;

  try {
    await axios.post(`/api/documentos/${docEnObservacion.value.id}/validar`, {
      estado_validacion: 'OBSERVADO',
      tipo_observacion: formDocObs.value.tipo_observacion,
      hash_documento: docEnObservacion.value.hash_documento,
      observacion_validacion: formDocObs.value.observacion_validacion.trim(),
    });

    modalDocObs.value = false;
    actions.toast(`Documento "${docEnObservacion.value.nombre_documento}" observado. Trámite notificado al estudiante.`, 'warning', 'Recaudo Observado');
    await loadData();
  } catch (e) {
    errorDocObs.value = e.response?.data?.message || 'Error al procesar la observación del documento.';
    actions.toast(errorDocObs.value, 'error', 'Error al Observar');
  } finally {
    procesandoAccion.value = false;
  }
}

const procesandoAccion = ref(false);

const modalTipoTramite = ref(false);
const editandoTipoId = ref(null);
const formTipo = ref({
  codigo: '',
  nombre: '',
  descripcion: '',
  costo_bs: 0,
  tiempo_sla_horas: 72,
});

const modalNuevoUsuario = ref(false);
const formUser = ref({
  nombre: '',
  apellidos: '',
  registro: '',
  ci: '',
  email: '',
  rol: 'ALUMNO',
  carrera: 'Ingenieria Informatica',
});

const tramitesFirmados = computed(() => {
  return tramites.value.filter(t => t.estado === 'FIRMADO' || t.estado === 'DESPACHADO');
});

function formatEstadoLegible(texto) {
  if (!texto) return '';
  return texto.split('_').map(palabra => palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase()).join(' ');
}

function isPagado(t) {
  return t.pago && t.pago.estado === 'COMPLETADO';
}

async function validarPagoEnVentanilla(t) {
  if (procesandoAccion.value) return;
  procesandoAccion.value = true;
  try {
    const { data } = await axios.post(`/api/pagos/tramite/${t.id}/confirmar`);
    actions.toast(data.message, 'success', 'Arancel acreditado');
    await loadData();
  } catch (e) {
    actions.toast(e.response?.data?.message || 'Error al validar el pago en ventanilla.', 'error', 'Error Pago');
  } finally {
    procesandoAccion.value = false;
  }
}

async function loadData() {
  loading.value = true;
  try {
    const [resTramites, resTipos, resUsers] = await Promise.all([
      axios.get('/api/tramites'),
      axios.get('/api/tramites/tipos'),
      axios.get('/api/usuarios'),
    ]);
    tramites.value = resTramites.data?.data || [];
    tiposTramite.value = resTipos.data?.data || [];
    usuarios.value = resUsers.data?.data || [];
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

async function validarDoc(idDoc, estado) {
  if (procesandoAccion.value) return;
  procesandoAccion.value = true;
  try {
    await axios.post(`/api/documentos/${idDoc}/validar`, {
      estado_validacion: estado,
      observacion_validacion: `Validado: ${estado}`,
    });
    await loadData();
  } catch (e) {
    alert('Error al validar documento');
  } finally {
    procesandoAccion.value = false;
  }
}

// Modal de Confirmación de Transición de Estados
const modalConfirmarAvance = ref(false);
const tramiteAvanzarInfo = ref(null);
const etapaAvanzarDestino = ref('');
const estadoAvanzarDestino = ref('');

function tieneAdjuntosBloqueantes(tramite) {
  return tramite.documentos?.some(doc => doc.estado_validacion === 'OBSERVADO'
    || (doc.estado_validacion === 'PENDIENTE' && doc.observacion_validacion));
}

function solicitarConfirmacionAvance(t, etapa, nuevoEstado) {
  tramiteAvanzarInfo.value = t;
  etapaAvanzarDestino.value = etapa;
  estadoAvanzarDestino.value = nuevoEstado;
  modalConfirmarAvance.value = true;
}

async function ejecutarAvanceConfirmado() {
  if (procesandoAccion.value || !tramiteAvanzarInfo.value) return;
  procesandoAccion.value = true;
  try {
    const res = await axios.post(`/api/tramites/${tramiteAvanzarInfo.value.id}/flujo`, {
      etapa_actual: etapaAvanzarDestino.value,
      nuevo_estado_tramite: estadoAvanzarDestino.value,
    });
    modalConfirmarAvance.value = false;
    actions.toast(`Trámite ${tramiteAvanzarInfo.value.codigo_tramite} avanzado a '${etapaAvanzarDestino.value}'.`, 'success', 'Etapa Actualizada');
    await loadData();
  } catch (e) {
    actions.toast(e.response?.data?.message || 'Error al avanzar el flujo procesal.', 'error', 'Error Flujo');
  } finally {
    procesandoAccion.value = false;
  }
}

async function avanzar(t, etapa, nuevoEstado) {
  solicitarConfirmacionAvance(t, etapa, nuevoEstado);
}

function openObservacion(t) {
  tramiteSeleccionado.value = t;
  formObs.value.descripcion = '';
  errorObsTramite.value = '';
  modalObs.value = true;
}

async function guardarObservacion() {
  if (!formObs.value.descripcion || formObs.value.descripcion.trim() === '') {
    errorObsTramite.value = 'Atención: Debe escribir una descripción detallada de la observación para el estudiante.';
    return;
  }
  errorObsTramite.value = '';

  if (procesandoAccion.value) return;
  procesandoAccion.value = true;
  try {
    await axios.post(`/api/tramites/${tramiteSeleccionado.value.id}/observaciones`, {
      tipo_observacion: formObs.value.tipo_observacion,
      descripcion: formObs.value.descripcion.trim(),
    });
    modalObs.value = false;
    actions.toast('Observación procesal registrada y trámite notificado al estudiante.', 'warning', 'Trámite Observado');
    await loadData();
  } catch (e) {
    errorObsTramite.value = e.response?.data?.message || 'Error al emitir la observación procesal.';
    actions.toast(errorObsTramite.value, 'error', 'Error');
  } finally {
    procesandoAccion.value = false;
  }
}

function abrirModalTipoTramite(tipo) {
  if (tipo) {
    editandoTipoId.value = tipo.id;
    formTipo.value = { ...tipo };
  } else {
    editandoTipoId.value = null;
    formTipo.value = { codigo: '', nombre: '', descripcion: '', costo_bs: 100, tiempo_sla_horas: 72 };
  }
  modalTipoTramite.value = true;
}

async function guardarTipoTramite() {
  if (procesandoAccion.value) return;
  procesandoAccion.value = true;
  try {
    if (editandoTipoId.value) {
      await axios.put(`/api/tramites/tipos/${editandoTipoId.value}`, formTipo.value);
    } else {
      await axios.post('/api/tramites/tipos', formTipo.value);
    }
    modalTipoTramite.value = false;
    actions.toast('Tipo de trámite guardado exitosamente.', 'success', 'Catálogo Actualizado');
    await loadData();
  } catch (e) {
    actions.toast(e.response?.data?.message || 'Error al guardar tipo de trámite', 'error', 'Error');
  } finally {
    procesandoAccion.value = false;
  }
}

async function ejecutarDespacho(t, tipoEntrega) {
  if (procesandoAccion.value) return;
  procesandoAccion.value = true;
  try {
    await axios.post(`/api/firma/despacho/${t.id}`, { tipo_entrega: tipoEntrega });
    actions.toast(`Legajo del trámite ${t.codigo_tramite} despachado exitosamente. SLA cerrado.`, 'success', 'Trámite Finalizado');
    await loadData();
  } catch (e) {
    actions.toast(e.response?.data?.message || 'Error al despachar legajo', 'error', 'Error Despacho');
  } finally {
    procesandoAccion.value = false;
  }
}

async function guardarUsuario() {
  if (procesandoAccion.value) return;
  procesandoAccion.value = true;
  try {
    await axios.post('/api/usuarios', formUser.value);
    modalNuevoUsuario.value = false;
    actions.toast(`Usuario ${formUser.value.nombre} creado con éxito`, 'success', 'Usuario Creado');
    await loadData();
  } catch (e) {
    actions.toast(e.response?.data?.message || 'Error al crear usuario', 'error', 'Error');
  } finally {
    procesandoAccion.value = false;
  }
}

function getEstadoBadge(estado) {
  switch (estado) {
    case 'FIRMADO':
    case 'DESPACHADO': return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
    case 'OBSERVADO': return 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300';
    case 'EN_FIRMA': return 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300';
    default: return 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300';
  }
}

function getDocBadge(val) {
  switch (val) {
    case 'APROBADO': return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
    case 'OBSERVADO': return 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300';
    case 'RECHAZADO': return 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300';
    default: return 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300';
  }
}

onMounted(() => {
  loadData();
});
</script>
