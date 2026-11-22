<template>
  <div class="space-y-6">
    <!-- Card de Bienvenida M3 Estilizada y Limpia -->
    <div class="m3-hero-gradient rounded-3xl p-6 sm:p-7 text-white shadow-lg relative overflow-hidden transition-all duration-300">
      <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center space-x-2.5">
            <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white shadow-xs">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold heading-font tracking-tight">
              Hola, {{ state.user.nombre }} {{ state.user.apellidos }}
            </h1>
          </div>
          <p class="text-white/85 text-xs sm:text-sm mt-1.5 font-medium">
            {{ state.user.rol === 'DOCENTE' ? 'Código Docente' : 'RU' }}: <span class="font-mono font-bold">{{ state.user.registro }}</span> | C.I.: <span class="font-mono">{{ state.user.ci }}</span> | {{ state.user.rol === 'DOCENTE' ? 'Facultad' : 'Carrera' }}: {{ state.user.alumno ? state.user.alumno.carrera : (state.user.unidad_academica || 'UAGRM') }}
          </p>
        </div>
        <div class="flex items-center space-x-2 self-start sm:self-center">
          <span class="text-[11px] font-bold tracking-wider uppercase px-3.5 py-1.5 rounded-full bg-white/20 border border-white/25 backdrop-blur-md shadow-xs flex items-center space-x-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
            <span>{{ state.user.rol === 'DOCENTE' ? 'Portal Docente / Académico' : 'Portal del Estudiante' }}</span>
          </span>
        </div>
      </div>
      <!-- Decoración sutil de fondo que acompaña el gradiente del tema -->
      <div class="absolute right-0 bottom-0 opacity-10 translate-x-12 translate-y-12 pointer-events-none">
        <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a10.97 10.97 0 00-.25 2.449c0 4.148 2.5 7.9 6 9.4 3.5-1.5 6-5.252 6-9.4 0-.847-.09-1.67-.25-2.45l2.644-1.13a1 1 0 000-1.84l-7-3z"/></svg>
      </div>
    </div>

    <!-- Pestañas M3 limpias para el Alumno -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2 gap-3">
      <div class="flex items-center space-x-2">
        <button
          @click="activeSubTab = 'mis_tramites'"
          :class="activeSubTab === 'mis_tramites' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-extrabold' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
          class="pb-2.5 px-3.5 text-xs sm:text-sm transition cursor-pointer flex items-center space-x-1.5"
        >
          <span>Mi Historial de Trámites</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 dark:bg-slate-800 font-mono">{{ misTramites.length }}</span>
        </button>
        <button
          @click="activeSubTab = 'disponibles'"
          :class="activeSubTab === 'disponibles' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-extrabold' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
          class="pb-2.5 px-3.5 text-xs sm:text-sm transition cursor-pointer"
        >
          Trámites Disponibles para Solicitar
        </button>
      </div>

      <!-- Controles de Filtros: Switch de Gastos y Filtro de Estado de Trámite -->
      <div v-if="activeSubTab === 'mis_tramites'" class="flex flex-wrap items-center gap-2">
        <!-- Filtro por Estado de la DB -->
        <div v-if="!verSoloPagados" class="flex items-center space-x-1.5">
          <label class="text-[11px] font-bold text-slate-500">Estado:</label>
          <select
            v-model="filtroEstado"
            class="px-2.5 py-1 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 cursor-pointer focus:ring-2 focus:ring-blue-500"
          >
            <option value="TODOS">Todos los Estados</option>
            <option value="TERMINADOS">✓ Terminados (Firmado / Despachado)</option>
            <option value="INICIADO">Iniciado / Recepción</option>
            <option value="REQUISITOS_VALIDADOS">Requisitos Validados</option>
            <option value="EXPEDIENTE_COMPILADO">Expediente Compilado</option>
            <option value="EN_FIRMA">En Firma VR</option>
            <option value="FIRMADO">Firmado Oficial</option>
            <option value="DESPACHADO">Despachado</option>
            <option value="OBSERVADO">Observado</option>
          </select>
        </div>

        <!-- Switch M3 para ver Lista Limpia de Pagos Realizados / Gastos -->
        <div class="flex items-center space-x-2 bg-slate-50 dark:bg-slate-800/80 px-3 py-1.5 rounded-full border border-slate-200 dark:border-slate-700 shadow-xs">
          <label class="text-[11px] font-bold cursor-pointer flex items-center space-x-1 select-none" for="switch-pagos">
            <span :class="verSoloPagados ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-600 dark:text-slate-400'">
              Ver Lista de Pagos (Gastos)
            </span>
            <span class="px-1.5 py-0.2 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 text-[10px] font-mono">
              Bs. {{ totalGastado.toFixed(2) }}
            </span>
          </label>
          <!-- Toggle Switch -->
          <button
            id="switch-pagos"
            type="button"
            @click="verSoloPagados = !verSoloPagados"
            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden"
            :class="verSoloPagados ? 'bg-emerald-600' : 'bg-slate-300 dark:bg-slate-600'"
            title="Activar para ver desglose de pagos y gastos"
          >
            <span
              class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out"
              :class="verSoloPagados ? 'translate-x-4' : 'translate-x-0'"
            />
          </button>
        </div>
      </div>
    </div>

    <!-- Pestaña 1: HISTORIAL DE TRÁMITES (Con diseño M3, estados legibles sin guiones bajos, pago y seguimiento) -->
    <div v-if="activeSubTab === 'mis_tramites'" class="space-y-4">
      <!-- MODO 1: VISTA LIMPIA DE HISTORIAL DE PAGOS / GASTOS (Sin etapas procesales ni detalles de trámite) -->
      <div v-if="verSoloPagados" class="space-y-4">
        <!-- Resumen de Gasto Total -->
        <div class="m3-surface p-5 bg-gradient-to-r from-emerald-800 to-teal-900 text-white shadow-md flex items-center justify-between">
          <div>
            <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-200">Resumen de Inversión Académica</span>
            <h2 class="text-xl sm:text-2xl font-black mt-0.5">Historial de Pagos y Aranceles Cancelados</h2>
            <p class="text-xs text-emerald-100">Desglose oficial de todos los pagos realizados mediante PagoFácil QR.</p>
          </div>
          <div class="text-right">
            <span class="text-[10px] text-emerald-200 uppercase font-semibold">Total Cancelado</span>
            <p class="text-2xl sm:text-3xl font-black text-white">Bs. {{ totalGastado.toFixed(2) }}</p>
            <span class="text-[11px] text-emerald-200">{{ pagosRealizadosList.length }} transacciones</span>
          </div>
        </div>

        <div v-if="pagosRealizadosList.length === 0" class="m3-card p-10 text-center border border-slate-200 dark:border-slate-800">
          <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          <h3 class="mt-3 text-sm font-bold text-slate-800 dark:text-white">No tienes pagos registrados todavía</h3>
          <p class="text-xs text-slate-500 mt-1">Los trámites que canceles con QR aparecerán listados aquí con su comprobante.</p>
        </div>

        <!-- Tabla / Lista Limpia de Pagos Realizados -->
        <div v-else class="m3-card border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-bold border-b border-slate-200 dark:border-slate-800">
                <tr>
                  <th class="p-3.5">Código Trámite</th>
                  <th class="p-3.5">Concepto / Trámite</th>
                  <th class="p-3.5">Ref. PagoFácil</th>
                  <th class="p-3.5">Fecha de Pago</th>
                  <th class="p-3.5">Método</th>
                  <th class="p-3.5 text-right">Monto (Bs.)</th>
                  <th class="p-3.5 text-center">Estado</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <tr v-for="t in pagosRealizadosList" :key="t.id" class="hover:bg-slate-50/70 dark:hover:bg-slate-800/50 transition">
                  <td class="p-3.5 font-mono font-bold text-blue-600 dark:text-blue-400">{{ t.codigo_tramite }}</td>
                  <td class="p-3.5 font-semibold text-slate-800 dark:text-slate-200">{{ t.tipo_tramite ? t.tipo_tramite.nombre : t.asunto }}</td>
                  <td class="p-3.5 font-mono text-slate-500 text-[11px]">{{ t.pago ? t.pago.payment_number : 'PAGO-' + t.codigo_tramite }}</td>
                  <td class="p-3.5 text-slate-500">{{ formatFecha(t.pago && t.pago.fecha_pago ? t.pago.fecha_pago : t.fecha_solicitud) }}</td>
                  <td class="p-3.5"><span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 font-mono text-[10px] font-bold text-slate-600 dark:text-slate-300">PAGOFACIL QR</span></td>
                  <td class="p-3.5 text-right font-black text-emerald-600 dark:text-emerald-400 text-sm">
                    Bs. {{ t.pago && t.pago.monto ? Number(t.pago.monto).toFixed(2) : (t.tipo_tramite ? Number(t.tipo_tramite.costo_bs).toFixed(2) : '0.00') }}
                  </td>
                  <td class="p-3.5 text-center">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                      ✓ PAGADO
                    </span>
                  </td>
                </tr>
              </tbody>
              <tfoot class="bg-slate-50 dark:bg-slate-800/80 font-bold border-t border-slate-200 dark:border-slate-800">
                <tr>
                  <td colspan="5" class="p-3.5 text-right uppercase text-slate-600 dark:text-slate-300">Total Acumulado en Pagos:</td>
                  <td class="p-3.5 text-right text-emerald-600 dark:text-emerald-400 font-black text-base">Bs. {{ totalGastado.toFixed(2) }}</td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <!-- MODO 2: BANDEJA DE TRÁMITES CON SEGUIMIENTO PROCESAL Y FILTRO POR ESTADO DE LA DB -->
      <div v-else class="space-y-4">
        <div v-if="loading" class="p-12 text-center text-slate-500">Cargando trámites...</div>
        
        <div v-else-if="tramitesFiltrados.length === 0" class="m3-card p-10 text-center border border-slate-200 dark:border-slate-800">
          <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          <h3 class="mt-3 text-sm font-bold text-slate-800 dark:text-white">
            {{ filtroEstado !== 'TODOS' ? 'No se encontraron trámites con el estado seleccionado' : 'Aún no has solicitado ningún trámite' }}
          </h3>
          <p class="text-xs text-slate-500 mt-1">
            {{ filtroEstado !== 'TODOS' ? 'Prueba cambiando el filtro de estado arriba.' : 'Revisa los trámites disponibles con sus requisitos y costos para iniciar tu solicitud.' }}
          </p>
          <button
            v-if="filtroEstado !== 'TODOS'"
            @click="filtroEstado = 'TODOS'"
            class="mt-4 px-5 py-2.5 rounded-full bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs shadow-md transition cursor-pointer"
          >
            Ver Todos los Estados
          </button>
          <button
            v-else
            @click="activeSubTab = 'disponibles'"
            class="mt-4 px-5 py-2.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition cursor-pointer"
          >
            Ver Trámites Disponibles
          </button>
        </div>

        <div v-else class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
              v-for="t in tramitesPaginados"
              :key="t.id"
              class="m3-card p-5 border border-slate-200/80 dark:border-slate-800 space-y-4 hover:border-blue-400/50 transition"
            >
          <!-- Encabezado con código y fecha legible -->
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <span class="font-mono text-xs font-black text-blue-600 dark:text-blue-400 px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-950/40">
                {{ t.codigo_tramite }}
              </span>
              <span class="text-[11px] text-slate-400">
                {{ formatFecha(t.fecha_solicitud) }}
              </span>
            </div>
            <!-- Estado formateado sin guiones bajos (ej. "En Firma", "Requisitos Validados") -->
            <span class="m3-badge uppercase" :class="getEstadoBadge(t.estado)">
              {{ formatEstadoLegible(t.estado) }}
            </span>
          </div>

          <div>
            <h3 class="font-bold text-slate-900 dark:text-white text-base">{{ t.asunto }}</h3>
            <p class="text-xs text-slate-500">{{ t.tipo_tramite ? t.tipo_tramite.nombre : 'Trámite Académico' }}</p>
          </div>

          <!-- SECCIÓN DE SEGUIMIENTO EN TIEMPO REAL (En qué etapa se encuentra) -->
          <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-xs">
              <span class="font-bold text-slate-700 dark:text-slate-300">Etapa Actual del Proceso:</span>
              <button @click="verDetalleSeguimiento(t)" class="text-blue-600 dark:text-blue-400 font-bold hover:underline cursor-pointer text-[11px] flex items-center space-x-1">
                <span>Ver Seguimiento Detallado</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </button>
            </div>
            
            <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
              <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" :style="`width: ${getPorcentajeProgreso(t.estado)}%`"></div>
            </div>

            <div class="flex items-center justify-between text-[11px] text-slate-500">
              <span :class="t.estado === 'INICIADO' ? 'font-bold text-blue-600' : ''">Recepción</span>
              <span :class="t.estado === 'REQUISITOS_VALIDADOS' ? 'font-bold text-blue-600' : ''">Revisión</span>
              <span :class="t.estado === 'EXPEDIENTE_COMPILADO' ? 'font-bold text-blue-600' : ''">Compilación</span>
              <span :class="t.estado === 'EN_FIRMA' ? 'font-bold text-purple-600' : ''">Firma Digital</span>
              <span :class="t.estado === 'DESPACHADO' ? 'font-bold text-emerald-600' : ''">Despacho</span>
            </div>

            <!-- PIN Secreto directo en la tarjeta para consulta externa -->
            <div class="pt-1 flex items-center justify-between text-[11px] text-slate-400 border-t border-slate-200/50 dark:border-slate-700/50">
              <span>PIN de Consulta Pública: <strong class="font-mono text-slate-700 dark:text-slate-300">{{ t.portal_seguimiento ? t.portal_seguimiento.pin_seguimiento : 'N/A' }}</strong></span>
              <span class="text-blue-600 font-medium">Plazo SLA: 72h hábiles</span>
            </div>
          </div>

          <!-- MÓDULO DE PAGO CON PRECIO EN BS (TIPO_TRAMITE.COSTO_BS) -->
          <div class="p-3.5 rounded-2xl border flex items-center justify-between text-xs" :class="isPagado(t) ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800' : 'bg-amber-50/50 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800'">
            <div>
              <span class="block text-[10px] uppercase font-bold text-slate-500">
                Arancel del Trámite:
              </span>
              <p class="text-base font-black text-slate-900 dark:text-white">
                Bs. {{ t.tipo_tramite ? t.tipo_tramite.costo_bs : '0.00' }}
              </p>
              <div class="flex items-center space-x-1.5 mt-0.5">
                <span class="w-2 h-2 rounded-full" :class="isPagado(t) ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse'"></span>
                <span class="font-bold text-[11px]" :class="isPagado(t) ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-700 dark:text-amber-300'">
                  {{ isPagado(t) ? 'PAGO CONFIRMADO' : 'PAGO PENDIENTE' }}
                </span>
              </div>
            </div>

            <div>
              <button
                v-if="!isPagado(t)"
                @click="abrirModalPago(t)"
                class="px-3.5 py-2 bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold text-xs rounded-full shadow-sm transition flex items-center space-x-1.5 cursor-pointer"
              >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                <span>Pagar con QR</span>
              </button>
              <span v-else class="text-[11px] font-mono font-bold text-emerald-700 dark:text-emerald-300 bg-white dark:bg-slate-900 px-2.5 py-1 rounded-full border border-emerald-300 dark:border-emerald-700 inline-block">
                ✓ Recibo Pagado
              </span>
            </div>
          </div>

          <!-- Documentos del Expediente (Solo se habilita adjuntar si el trámite requiere recaudos y está en RECEPCION/INICIADO o en OBSERVADO para subsanar) -->
          <div v-if="t.tipo_tramite && t.tipo_tramite.requisitos && t.tipo_tramite.requisitos.length > 0" class="pt-2 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center justify-between text-xs mb-2">
              <span class="font-bold text-slate-700 dark:text-slate-300">
                Expediente Digital ({{ t.documentos ? t.documentos.length : 0 }} recaudos)
              </span>
              <!-- Botón condicional según etapa y estado procesal -->
              <button
                v-if="puedeAdjuntar(t)"
                @click="openSubirDoc(t)"
                class="px-2.5 py-1 rounded-full text-xs font-bold transition flex items-center space-x-1 cursor-pointer"
                :class="t.estado === 'OBSERVADO' ? 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 hover:bg-amber-200' : 'bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 hover:bg-blue-100'"
              >
                <span>{{ t.estado === 'OBSERVADO' ? 'Subsanar / Reemplazar Recaudo' : '+ Adjuntar Recaudo' }}</span>
              </button>
              <span v-else class="text-[10px] text-slate-400 italic">
                {{ getMotivoBloqueoAdjunto(t) }}
              </span>
            </div>

            <!-- Alerta informativa si está observado -->
            <div v-if="t.estado === 'OBSERVADO'" class="p-2.5 mb-2 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-[11px] text-amber-900 dark:text-amber-200 flex items-start space-x-2">
              <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
              <div>
                <p class="font-bold">El expediente tiene observaciones del Encargado.</p>
                <p>Por favor adjunta el documento corregido para subsanar el requisito observado.</p>
              </div>
            </div>

            <div class="space-y-1.5 max-h-36 overflow-y-auto">
              <div
                v-for="doc in t.documentos"
                :key="doc.id"
                class="flex items-center justify-between p-2 rounded-xl bg-slate-50 dark:bg-slate-800 text-xs"
              >
                <div class="truncate max-w-[200px]">
                  <span class="font-medium text-slate-700 dark:text-slate-200 block truncate">{{ doc.nombre_documento }}</span>
                  <span class="font-mono text-[10px] text-slate-400 truncate block">Hash: {{ doc.hash_documento ? doc.hash_documento.substring(0, 16) + '...' : 'Generado' }}</span>
                </div>
                <div class="flex items-center space-x-1.5">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="getDocBadge(doc.estado_validacion)">
                    {{ formatEstadoLegible(doc.estado_validacion) }}
                  </span>
                  <!-- Botón directo de corregir si este documento específico fue observado -->
                  <button
                    v-if="doc.estado_validacion === 'OBSERVADO'"
                    @click="corregirDocObservado(t, doc)"
                    class="px-2 py-0.5 text-[10px] font-bold bg-amber-600 hover:bg-amber-700 text-white rounded cursor-pointer"
                    title="Subir archivo corregido para este documento"
                  >
                    Corregir
                  </button>
                </div>
              </div>
            </div>
          </div>
          <!-- Si el trámite no requiere recaudos -->
          <div v-else class="pt-2 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400 italic">
            ✓ Este trámite no requiere adjuntar archivos adicionales.
          </div>

          <!-- Certificado con QR si ya fue firmado por el Vicerrector -->
          <div v-if="t.certificado" class="p-3.5 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-950/30 dark:to-teal-950/30 rounded-2xl border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
            <div>
              <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300">Certificado Oficial Emitido</p>
              <p class="text-[10px] font-mono text-slate-500">UUID: {{ t.certificado.codigo_verificacion }}</p>
            </div>
            <button
              @click="verCertificadoModal(t.certificado)"
              class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-full shadow-sm transition cursor-pointer"
            >
              Ver QR Oficial
            </button>
          </div>

        </div>
      </div>

      <!-- Barra de Paginación para Bandeja de Trámites Alumno -->
      <div v-if="totalPaginas > 1" class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-3 border-t border-slate-200 dark:border-slate-800 text-xs">
        <div class="text-slate-500">
          Mostrando <span class="font-bold text-slate-700 dark:text-slate-300">{{ (paginaActual - 1) * itemsPorPagina + 1 }}</span> a 
          <span class="font-bold text-slate-700 dark:text-slate-300">{{ Math.min(paginaActual * itemsPorPagina, tramitesFiltrados.length) }}</span> de 
          <span class="font-bold text-slate-700 dark:text-slate-300">{{ tramitesFiltrados.length }}</span> trámites
        </div>
        <div class="flex items-center space-x-2">
          <button
            @click="paginaActual--"
            :disabled="paginaActual <= 1"
            class="px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer transition"
          >
            ← Anterior
          </button>
          <span class="font-mono font-bold text-slate-800 dark:text-slate-200 px-2">
            Página {{ paginaActual }} de {{ totalPaginas }}
          </span>
          <button
            @click="paginaActual++"
            :disabled="paginaActual >= totalPaginas"
            class="px-3.5 py-1.5 rounded-full bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer transition"
          >
            Siguiente →
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

    <!-- Pestaña 2: TRÁMITES DISPONIBLES PARA SOLICITAR (Listado Oficial con Precios, Requisitos y Botón Directo) -->
    <div v-if="activeSubTab === 'disponibles'" class="space-y-4">
      <div class="m3-card p-4 bg-blue-50/60 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-900 text-xs text-blue-900 dark:text-blue-200">
        <p class="font-bold flex items-center space-x-1.5 text-sm mb-1">
          <svg class="w-4 h-4 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          Trámites Oficiales Disponibles
        </p>
        Selecciona el trámite académico que necesitas. Cada trámite cuenta con su arancel oficial en Bolivianos (Bs.) y sus requisitos indispensables antes de iniciar la solicitud.
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="tipo in tiposTramite"
          :key="tipo.id"
          class="m3-card p-6 border border-slate-200 dark:border-slate-800 space-y-4 hover:border-blue-400/50 transition flex flex-col justify-between"
        >
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="font-mono text-xs px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 font-bold text-blue-600 dark:text-blue-400">{{ tipo.codigo }}</span>
              <div class="text-right">
                <span class="text-[10px] text-slate-400 block uppercase font-bold">Precio Oficial</span>
                <span class="text-xl font-black text-emerald-600 dark:text-emerald-400">Bs. {{ tipo.costo_bs }}</span>
              </div>
            </div>

            <div>
              <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ tipo.nombre }}</h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">{{ tipo.descripcion }}</p>
            </div>

            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 flex items-center justify-between text-xs text-slate-600 dark:text-slate-300">
              <span>⏱️ Tiempo SLA de Atención:</span>
              <span class="font-bold font-mono text-blue-600 dark:text-blue-400">{{ tipo.tiempo_sla_horas }} Horas Hábiles</span>
            </div>

            <!-- Requisitos que debe adjuntar -->
            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/40 rounded-2xl space-y-2 text-xs border border-slate-100 dark:border-slate-800">
              <p class="font-bold text-slate-800 dark:text-slate-200 flex items-center justify-between">
                <span>Requisitos Indispensables:</span>
                <span class="text-[10px] text-slate-400 font-normal">({{ tipo.requisitos ? tipo.requisitos.length : 0 }} recaudos)</span>
              </p>
              <ul class="space-y-1.5 text-slate-600 dark:text-slate-400">
                <li v-for="r in tipo.requisitos" :key="r.id" class="flex items-start space-x-2">
                  <span class="text-emerald-500 font-bold">✓</span>
                  <span class="flex-1">
                    {{ r.nombre }}
                    <span v-if="r.es_obligatorio" class="text-red-500 font-bold" title="Obligatorio">*</span>
                  </span>
                  <span class="font-mono text-[10px] px-1.5 py-0.5 rounded-md bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300">{{ r.formato_permitido || 'PDF' }}</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- Botón Directo para Solicitar este trámite específico sin formularios duplicados -->
          <button
            @click="solicitarTramiteDirecto(tipo)"
            class="w-full mt-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-full transition shadow-sm flex items-center justify-center space-x-2 cursor-pointer"
          >
            <span>Solicitar este Trámite (Bs. {{ tipo.costo_bs }})</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL CONFIRMAR SOLICITUD DE TRÁMITE (Flujo ágil y directo) -->
    <div v-if="modalNuevoTramite" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="m3-card p-6 sm:p-8 max-w-lg w-full space-y-5 border border-slate-200 dark:border-slate-800 shadow-2xl">
        <div>
          <h3 class="text-lg font-bold text-slate-900 dark:text-white">Confirmar Solicitud de Trámite</h3>
          <p class="text-xs text-slate-500">Se registrará en tu historial de trámites con arancel oficial.</p>
        </div>
        
        <div v-if="tipoSeleccionadoInfo" class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 space-y-3 text-xs">
          <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-2">
            <div>
              <span class="text-[10px] uppercase font-bold text-slate-400">Trámite Seleccionado:</span>
              <p class="text-sm font-bold text-slate-900 dark:text-white">{{ tipoSeleccionadoInfo.nombre }}</p>
            </div>
            <div class="text-right">
              <span class="text-[10px] uppercase font-bold text-slate-400">Precio Oficial:</span>
              <p class="text-base font-black text-emerald-600 dark:text-emerald-400">Bs. {{ tipoSeleccionadoInfo.costo_bs }}</p>
            </div>
          </div>

          <!-- Indicador Claro de Requisitos / Si requiere subir archivos o no -->
          <div>
            <p class="font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
              <span>Requisitos y Documentación Requerida:</span>
              <span class="text-[11px] font-semibold" :class="tipoSeleccionadoInfo.requisitos && tipoSeleccionadoInfo.requisitos.length > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'">
                {{ tipoSeleccionadoInfo.requisitos && tipoSeleccionadoInfo.requisitos.length > 0 ? 'Requiere adjuntos (' + tipoSeleccionadoInfo.requisitos.length + ')' : 'Sin requisitos documentales previos' }}
              </span>
            </p>

            <div v-if="!tipoSeleccionadoInfo.requisitos || tipoSeleccionadoInfo.requisitos.length === 0" class="p-3 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/50 text-emerald-800 dark:text-emerald-300">
              <p class="font-medium flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Este trámite no exige subir archivos previos. Se iniciará directamente.</span>
              </p>
            </div>

            <ul v-else class="space-y-1.5">
              <li
                v-for="r in tipoSeleccionadoInfo.requisitos"
                :key="r.id"
                class="flex items-center justify-between p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-[11px]"
              >
                <div class="flex items-center space-x-2">
                  <span class="w-2 h-2 rounded-full" :class="r.es_obligatorio ? 'bg-red-500' : 'bg-slate-400'"></span>
                  <span class="font-medium text-slate-800 dark:text-slate-200">{{ r.nombre }}</span>
                  <span v-if="r.es_obligatorio" class="text-[10px] text-red-500 font-bold">(Obligatorio)</span>
                </div>
                <span class="font-mono text-[10px] px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500">
                  Formato {{ r.formato_permitido || 'PDF' }}
                </span>
              </li>
            </ul>
            <p v-if="tipoSeleccionadoInfo.requisitos && tipoSeleccionadoInfo.requisitos.length > 0" class="text-[10px] text-slate-400 mt-1">
              Podrás adjuntar tus archivos en PDF inmediatamente al confirmar o desde tu historial de trámites.
            </p>
          </div>
        </div>

        <!-- Botón Directo para Solicitar este trámite específico sin formularios duplicados -->
        <div class="flex justify-end space-x-2 pt-2">
          <button
            @click="modalNuevoTramite = false"
            :disabled="creandoTramite"
            class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-700 cursor-pointer disabled:opacity-50"
          >
            Cancelar
          </button>
          <button
            @click="crearTramite"
            :disabled="creandoTramite"
            class="px-5 py-2.5 text-xs font-bold bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-full shadow cursor-pointer flex items-center space-x-1.5"
          >
            <svg v-if="creandoTramite" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            <span>{{ creandoTramite ? 'Iniciando trámite...' : 'Confirmar e Iniciar Trámite' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL DETALLE DE SEGUIMIENTO (Trazabilidad Procesal Paso a Paso) -->
    <div v-if="modalSeguimientoDetalle" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="m3-card p-6 sm:p-8 max-w-lg w-full space-y-5 border border-slate-200 dark:border-slate-800 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
          <div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Seguimiento en Vivo del Trámite</h3>
            <p class="text-xs text-slate-500 font-mono">{{ tramiteEnDetalle.codigo_tramite }}</p>
          </div>
          <button @click="modalSeguimientoDetalle = false" class="p-1 rounded-full text-slate-400 hover:text-slate-600 dark:hover:text-white cursor-pointer">✕</button>
        </div>

        <!-- Resumen -->
        <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl text-xs space-y-1.5">
          <p class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ tramiteEnDetalle.asunto }}</p>
          <div class="flex justify-between text-slate-500 pt-1">
            <span>Arancel: <strong class="text-emerald-600 font-bold">Bs. {{ tramiteEnDetalle.tipo_tramite ? tramiteEnDetalle.tipo_tramite.costo_bs : '0.00' }}</strong></span>
            <span>Estado: <strong class="text-blue-600 font-bold">{{ formatEstadoLegible(tramiteEnDetalle.estado) }}</strong></span>
          </div>
          <p class="text-slate-400 text-[11px]">PIN para consulta externa: <span class="font-mono font-bold text-slate-700 dark:text-slate-300">{{ tramiteEnDetalle.portal_seguimiento ? tramiteEnDetalle.portal_seguimiento.pin_seguimiento : 'N/A' }}</span></p>
        </div>

        <!-- Línea de Tiempo de Etapas Procesales -->
        <div class="space-y-3">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Historial de Etapas Procesales</h4>
          <div class="relative pl-6 border-l-2 border-blue-500 space-y-4 text-xs">
            <div v-for="f in tramiteEnDetalle.flujos" :key="f.id" class="relative">
              <span class="absolute -left-[31px] top-0 w-3.5 h-3.5 rounded-full bg-blue-600 ring-4 ring-white dark:ring-slate-900"></span>
              <p class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ f.etapa_actual }}</p>
              <p class="text-[11px] text-slate-500">Iniciado: {{ formatFecha(f.fecha_inicio_etapa) }}</p>
              <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold" :class="f.estado_etapa === 'COMPLETADO' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800'">
                {{ formatEstadoLegible(f.estado_etapa) }}
              </span>
            </div>
          </div>
        </div>

        <button @click="modalSeguimientoDetalle = false" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-full transition cursor-pointer">
          Cerrar Detalle
        </button>
      </div>
    </div>

    <!-- MODAL DE PAGO PAGOFACIL QR -->
    <div v-if="modalPagoActivo" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs">
      <div class="m3-card p-6 sm:p-8 max-w-md w-full space-y-5 border border-slate-200 dark:border-slate-800 shadow-2xl text-center">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-orange-500/20">
          <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
        </div>

        <div>
          <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-orange-100 text-orange-800 dark:bg-orange-950 dark:text-orange-300">
            PagoFácil QR Bancario
          </span>
          <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">Pago de Arancel Universitario</h3>
          <p class="text-xs text-slate-500 font-mono">{{ tramiteAPagar.codigo_tramite }}</p>
        </div>

        <div class="py-2 px-4 rounded-xl bg-slate-50 dark:bg-slate-800 inline-block border border-slate-200 dark:border-slate-700">
          <span class="text-xs text-slate-400 block font-semibold">Total a pagar</span>
          <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">Bs. {{ tramiteAPagar.tipo_tramite ? tramiteAPagar.tipo_tramite.costo_bs : '0.00' }}</span>
        </div>

        <!-- Contenedor del QR o Error de Configuración -->
        <div v-if="pagoError" class="p-4 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-center space-y-2">
          <div class="w-10 h-10 mx-auto rounded-full bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
          </div>
          <p class="text-xs font-bold text-red-700 dark:text-red-300">Pasarela PagoFácil no disponible</p>
          <p class="text-[11px] text-red-600 dark:text-red-400">{{ pagoError }}</p>
        </div>

        <div v-else class="space-y-3">
          <div class="p-3 bg-white rounded-2xl inline-block shadow-inner border border-slate-200">
            <img
              v-if="pagoData.qr_image_url || pagoData.qr_base64"
              :src="pagoData.qr_base64 ? `data:image/png;base64,${pagoData.qr_base64}` : pagoData.qr_image_url"
              alt="Código QR PagoFácil"
              class="w-48 h-48 mx-auto object-contain"
            />
            <div v-else class="w-48 h-48 flex items-center justify-center text-slate-400 text-xs">
              Cargando código QR...
            </div>
          </div>

          <!-- Botón para Descargar / Guardar QR en Galería o Dispositivo -->
          <div v-if="pagoData.qr_image_url || pagoData.qr_base64">
            <button
              @click="descargarQrPago"
              class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-full text-xs font-bold transition flex items-center justify-center space-x-1.5 mx-auto border border-slate-200 dark:border-slate-700 cursor-pointer shadow-2xs"
            >
              <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              <span>Descargar / Guardar QR</span>
            </button>
          </div>
        </div>

        <div v-if="!pagoError" class="text-xs text-slate-500 space-y-1">
          <p class="font-mono font-bold text-slate-700 dark:text-slate-300">Ref: {{ pagoData.payment_number }}</p>
          <p class="text-[11px]">Escanee el código QR desde su aplicación bancaria o guárdelo para pagar después.</p>
          <!-- Aviso de Vigencia del QR -->
          <p class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold flex items-center justify-center space-x-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Válido por 24 horas. Si vence, el sistema te generará uno nuevo automáticamente.</span>
          </p>
        </div>

        <div class="space-y-2 pt-2">
          <button
            v-if="!pagoError"
            @click="verificarPagoConPagoFacil"
            :disabled="loadingPago || verificandoPago"
            class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-xs rounded-full shadow transition cursor-pointer flex items-center justify-center space-x-1.5"
          >
            <svg v-if="verificandoPago" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ verificandoPago ? 'Consultando estado en PagoFácil...' : 'Verificar Estado del Pago en PagoFácil' }}</span>
          </button>
          <button
            @click="modalPagoActivo = false"
            :disabled="verificandoPago"
            class="w-full py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold text-xs rounded-full transition cursor-pointer disabled:opacity-50"
          >
            Cerrar ventana (Pagar más tarde)
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Adjuntar Documento con Selector de Archivo Real o URL -->
    <div v-if="modalAdjuntar" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="m3-card p-6 sm:p-8 max-w-md w-full space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-bold text-slate-900 dark:text-white">Adjuntar Archivo al Expediente</h3>
          <span class="text-xs font-mono text-blue-600 dark:text-blue-400 font-bold">{{ selectedTramite ? selectedTramite.codigo_tramite : '' }}</span>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nombre o Tipo del Documento</label>
          <input
            v-model="formDoc.nombre_documento"
            type="text"
            class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm"
            placeholder="Ej: Fotocopia de Cédula de Identidad o Certificado"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Seleccionar Archivo (PDF / Imagen)</label>
          <input
            type="file"
            @change="onFileSelected"
            accept=".pdf,.png,.jpg,.jpeg"
            class="w-full px-3 py-2 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-600 dark:text-slate-300 file:mr-3 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 dark:file:bg-blue-950 dark:file:text-blue-300 cursor-pointer"
          />
          <p v-if="archivoSeleccionado" class="text-[11px] text-emerald-600 font-semibold mt-1">
            Archivo listo para subir: {{ archivoSeleccionado.name }} ({{ (archivoSeleccionado.size / 1024).toFixed(1) }} KB)
          </p>
        </div>

        <p class="text-[11px] text-slate-500">
          Al subirlo, el servidor genera automáticamente el enlace de almacenamiento permanente y calcula el hash SHA-256 para garantizar la autenticidad jurídica.
        </p>

        <div class="flex justify-end space-x-2 pt-4">
          <button @click="cerrarModalAdjuntar" :disabled="subiendoDoc" class="px-4 py-2 text-xs font-semibold text-slate-500 cursor-pointer disabled:opacity-50">Cancelar</button>
          <button @click="guardarDocumento" :disabled="subiendoDoc || !formDoc.nombre_documento" class="px-5 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow cursor-pointer disabled:opacity-50 flex items-center space-x-1">
            <svg v-if="subiendoDoc" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            <span>{{ subiendoDoc ? 'Subiendo y computando hash...' : 'Subir Archivo' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Certificado Oficial con QR -->
    <div v-if="certModalData" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs">
      <div class="m3-card p-8 max-w-lg w-full space-y-5 border-4 border-emerald-500/30 text-center shadow-2xl">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center">
          <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white heading-font uppercase tracking-wide">
          Certificación Oficial UAGRM
        </h2>
        <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ certModalData.titulo_documento }}</p>
        
        <div class="p-4 bg-white rounded-2xl inline-block shadow-inner border border-slate-200">
          <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(certModalData.qr_data)}`" alt="QR Verificación" class="w-44 h-44 mx-auto" />
        </div>

        <div class="text-xs space-y-1 text-slate-500">
          <p class="font-mono font-bold text-slate-800 dark:text-slate-200 text-xs">UUID: {{ certModalData.codigo_verificacion }}</p>
          <p>Firma Digital Oficial con Token Criptográfico ADSIB / UAGRM</p>
          <p class="text-[10px] text-emerald-600 font-bold">Estado: DOCUMENTO VIGENTE Y VALIDADOR HABILITADO</p>
        </div>

        <button @click="certModalData = null" class="w-full py-2.5 bg-slate-800 text-white font-bold rounded-full text-xs hover:bg-slate-700 transition cursor-pointer">
          Cerrar Visualizador
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { state, actions } from '../stores/appState';
import axios from 'axios';

const activeSubTab = ref('mis_tramites');
const misTramites = ref([]);
const tiposTramite = ref([]);
const verSoloPagados = ref(false);
const filtroEstado = ref('TODOS');
const loading = ref(false);

const pagosRealizadosList = computed(() => {
  return misTramites.value.filter(t => t.pago && t.pago.estado === 'COMPLETADO');
});

const totalGastado = computed(() => {
  return pagosRealizadosList.value.reduce((acc, t) => {
    const monto = t.pago?.monto ? Number(t.pago.monto) : (t.tipo_tramite ? Number(t.tipo_tramite.costo_bs) : 0);
    return acc + monto;
  }, 0);
});

const tramitesFiltrados = computed(() => {
  if (filtroEstado.value === 'TODOS') {
    return misTramites.value;
  }
  if (filtroEstado.value === 'TERMINADOS') {
    return misTramites.value.filter(t => t.estado === 'FIRMADO' || t.estado === 'DESPACHADO');
  }
  return misTramites.value.filter(t => t.estado === filtroEstado.value);
});

const paginaActual = ref(1);
const itemsPorPagina = ref(6);

const totalPaginas = computed(() => {
  const tot = Math.ceil(tramitesFiltrados.value.length / itemsPorPagina.value);
  return tot > 0 ? tot : 1;
});

const tramitesPaginados = computed(() => {
  const inicio = (paginaActual.value - 1) * itemsPorPagina.value;
  return tramitesFiltrados.value.slice(inicio, inicio + itemsPorPagina.value);
});
const creandoTramite = ref(false);
const verificandoPago = ref(false);
const loadingPago = ref(false);
const subiendoDoc = ref(false);

const modalNuevoTramite = ref(false);
const formNuevo = ref({
  id_tipo_tramite: 1,
  asunto: '',
});

const tipoSeleccionadoInfo = computed(() => {
  return tiposTramite.value.find(t => t.id === formNuevo.value.id_tipo_tramite);
});

// Modal de Seguimiento Detallado
const modalSeguimientoDetalle = ref(false);
const tramiteEnDetalle = ref({});

function verDetalleSeguimiento(t) {
  tramiteEnDetalle.value = t;
  modalSeguimientoDetalle.value = true;
}

function getPorcentajeProgreso(estado) {
  switch (estado) {
    case 'INICIADO': return 25;
    case 'REQUISITOS_VALIDADOS': return 45;
    case 'EXPEDIENTE_COMPILADO': return 65;
    case 'EN_FIRMA': return 85;
    case 'FIRMADO':
    case 'DESPACHADO': return 100;
    default: return 30;
  }
}

// Formateador limpio de estados sin guiones bajos
function formatEstadoLegible(texto) {
  if (!texto) return '';
  return texto.split('_').map(palabra => palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase()).join(' ');
}

// Modal de Pago
const modalPagoActivo = ref(false);
const tramiteAPagar = ref({});
const pagoData = ref({});
const pagoError = ref(null);

const modalAdjuntar = ref(false);
const selectedTramite = ref(null);
const formDoc = ref({
  nombre_documento: '',
});
const archivoSeleccionado = ref(null);

function onFileSelected(event) {
  const file = event.target.files[0];
  if (file) {
    archivoSeleccionado.value = file;
    if (!formDoc.value.nombre_documento) {
      // Usar el nombre del archivo sin extensión por defecto
      formDoc.value.nombre_documento = file.name.replace(/\.[^/.]+$/, "");
    }
  }
}

function cerrarModalAdjuntar() {
  modalAdjuntar.value = false;
  archivoSeleccionado.value = null;
  formDoc.value.nombre_documento = '';
}

const certModalData = ref(null);

function formatFecha(f) {
  if (!f) return '';
  return new Date(f).toLocaleDateString('es-BO', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function isPagado(t) {
  return t.pago && t.pago.estado === 'COMPLETADO';
}

async function loadData() {
  loading.value = true;
  try {
    const userId = state.user?.id_usuario;
    const [resTramites, resTipos] = await Promise.all([
      axios.get('/api/tramites', { params: userId ? { solicitante_id: userId } : {} }),
      axios.get('/api/tramites/tipos'),
    ]);
    misTramites.value = resTramites.data?.data || [];
    tiposTramite.value = resTipos.data?.data || [];
  } catch (e) {
    console.error('Error cargando datos del estudiante:', e);
  } finally {
    loading.value = false;
  }
}

// Solicitar trámite directo desde el listado de trámites disponibles
function solicitarTramiteDirecto(tipo) {
  formNuevo.value.id_tipo_tramite = tipo.id;
  const reg = state.user?.registro ? ` - ${state.user.registro}` : '';
  formNuevo.value.asunto = `Solicitud de ${tipo.nombre}${reg}`;
  modalNuevoTramite.value = true;
}

// Escuchar si viene seleccionado un trámite desde el buscador global
watch(() => state.tramiteSeleccionadoParaSolicitar, (nuevoTipo) => {
  if (nuevoTipo) {
    solicitarTramiteDirecto(nuevoTipo);
    state.tramiteSeleccionadoParaSolicitar = null;
  }
}, { immediate: true });

async function crearTramite() {
  if (creandoTramite.value) return;
  creandoTramite.value = true;
  // Si por alguna razón el asunto estuviera vacío, se llena automáticamente con el formato: Solicitud de [Nombre] - [Registro]
  if (!formNuevo.value.asunto || formNuevo.value.asunto.trim() === '') {
    const tSel = tipoSeleccionadoInfo.value;
    const reg = state.user?.registro ? ` - ${state.user.registro}` : '';
    formNuevo.value.asunto = tSel ? `Solicitud de ${tSel.nombre}${reg}` : `Solicitud de Trámite Académico${reg}`;
  }
  try {
    const res = await axios.post('/api/tramites', {
      ...formNuevo.value,
      id_usuario_solicitante: state.user.id_usuario,
    });
    modalNuevoTramite.value = false;
    formNuevo.value.asunto = '';
    await loadData();
    activeSubTab.value = 'mis_tramites';

    if (res.data.data) {
      actions.toast('Trámite iniciado con éxito. Procede con el pago o adjunto de requisitos.', 'success', 'Solicitud Registrada');
      abrirModalPago(res.data.data);
    }
  } catch (e) {
    actions.toast(e.response?.data?.message || 'Error al iniciar trámite. Por favor revise sus datos.', 'error', 'No se pudo iniciar');
  } finally {
    creandoTramite.value = false;
  }
}

// Descargar imagen QR del Pago
function descargarQrPago() {
  const imgSrc = pagoData.value.qr_base64
    ? `data:image/png;base64,${pagoData.value.qr_base64}`
    : pagoData.value.qr_image_url;

  if (!imgSrc) return;

  const a = document.createElement('a');
  a.href = imgSrc;
  a.download = `QR_Pago_${tramiteAPagar.value.codigo_tramite || 'UAGRM'}.png`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  actions.toast('Código QR descargado. Puedes compartirlo o pagarlo desde tu galería bancaria.', 'success', 'QR Guardado');
}

async function abrirModalPago(t) {
  if (loadingPago.value) return;
  loadingPago.value = true;
  tramiteAPagar.value = t;
  pagoData.value = {};
  pagoError.value = null;
  modalPagoActivo.value = true;

  try {
    const res = await axios.post(`/api/pagos/tramite/${t.id}/generar-qr`);
    pagoData.value = {
      ...res.data.pago,
      qr_image_url: res.data.qr_image_url,
    };
  } catch (e) {
    pagoError.value = e.response?.data?.message || 'Error al conectar con la pasarela PagoFácil.';
  } finally {
    loadingPago.value = false;
  }
}

async function verificarPagoConPagoFacil() {
  if (verificandoPago.value) return;
  verificandoPago.value = true;
  try {
    const res = await axios.post(`/api/pagos/tramite/${tramiteAPagar.value.id}/verificar`);
    if (res.data.pagado) {
      actions.toast(res.data.message || 'Pago confirmado y acreditado con éxito en PagoFácil.', 'success', 'Pago Acreditado');
      modalPagoActivo.value = false;
      await loadData();
    } else {
      actions.toast(res.data.message || 'El pago aún figura como pendiente en PagoFácil.', 'warning', 'Pago Pendiente');
    }
  } catch (e) {
    actions.toast(e.response?.data?.message || 'Error al verificar estado de pago en PagoFácil', 'error', 'Error Pasarela');
  } finally {
    verificandoPago.value = false;
  }
}

function puedeAdjuntar(t) {
  // Solo se puede adjuntar si el trámite está en etapa inicial (RECEPCION / INICIADO)
  // o si el trámite fue OBSERVADO por el Encargado para subsanación.
  // En 'REQUISITOS_VALIDADOS', 'EXPEDIENTE_COMPILADO', 'EN_FIRMA', 'FIRMADO' o 'DESPACHADO' queda cerrado.
  return t.estado === 'INICIADO' || t.estado === 'OBSERVADO';
}

function getMotivoBloqueoAdjunto(t) {
  if (t.estado === 'REQUISITOS_VALIDADOS' || t.estado === 'EXPEDIENTE_COMPILADO') {
    return 'Revisión cerrada: requisitos cotejados';
  }
  if (t.estado === 'EN_FIRMA') {
    return 'En firma vicerrectoral (cerrado)';
  }
  if (t.estado === 'FIRMADO' || t.estado === 'DESPACHADO') {
    return 'Trámite finalizado';
  }
  return 'Expediente no modificable';
}

function corregirDocObservado(t, doc) {
  selectedTramite.value = t;
  formDoc.value.nombre_documento = doc.nombre_documento + ' (Subsanado)';
  archivoSeleccionado.value = null;
  modalAdjuntar.value = true;
}

function openSubirDoc(t) {
  selectedTramite.value = t;
  formDoc.value.nombre_documento = '';
  archivoSeleccionado.value = null;
  modalAdjuntar.value = true;
}

async function guardarDocumento() {
  if (!formDoc.value.nombre_documento || subiendoDoc.value) return;
  subiendoDoc.value = true;
  try {
    const formData = new FormData();
    formData.append('id_tramite', selectedTramite.value.id);
    formData.append('nombre_documento', formDoc.value.nombre_documento);
    if (archivoSeleccionado.value) {
      formData.append('archivo', archivoSeleccionado.value);
    }

    await axios.post('/api/documentos', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });

    cerrarModalAdjuntar();
    await loadData();
    actions.toast('Documento adjuntado exitosamente con cálculo de Hash SHA-256.', 'success', 'Archivo Guardado');
  } catch (e) {
    actions.toast(e.response?.data?.message || 'Error al subir documento', 'error', 'Error');
  } finally {
    subiendoDoc.value = false;
  }
}

function verCertificadoModal(cert) {
  certModalData.value = cert;
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
