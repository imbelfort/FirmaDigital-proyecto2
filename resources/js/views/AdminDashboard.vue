<template>
  <div class="space-y-6">
    <!-- Card de Bienvenida M3 para Administrador General -->
    <div class="m3-hero-gradient rounded-3xl p-6 sm:p-7 text-white shadow-xl relative overflow-hidden transition-all duration-300">
      <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center space-x-2.5">
            <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white shadow-xs">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold heading-font tracking-tight">
              Panel del Administrador: {{ state.user.nombre }} {{ state.user.apellidos }}
            </h1>
          </div>
          <p class="text-white/85 text-xs sm:text-sm mt-1.5 font-medium">
            Rol: <span class="font-bold text-white">ADMINISTRADOR GENERAL</span> | Código / Registro: <span class="font-mono text-white font-bold">{{ state.user.registro }}</span> | C.I.: <span class="font-mono text-white font-bold">{{ state.user.ci }}</span>
          </p>
        </div>
        <div class="flex items-center space-x-2 self-start sm:self-center">
          <span class="text-[11px] font-bold tracking-wider uppercase px-3.5 py-1.5 rounded-full bg-white/20 border border-white/25 backdrop-blur-md shadow-xs flex items-center space-x-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Control Global del Sistema</span>
          </span>
        </div>
      </div>
      <div class="absolute right-0 bottom-0 opacity-10 translate-x-12 translate-y-12 pointer-events-none">
        <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a10.97 10.97 0 00-.25 2.449c0 4.148 2.5 7.9 6 9.4 3.5-1.5 6-5.252 6-9.4 0-.847-.09-1.67-.25-2.45l2.644-1.13a1 1 0 000-1.84l-7-3z"/></svg>
      </div>
    </div>

    <!-- Pestañas Exclusivas de la Vista del Administrador -->
    <div class="flex items-center space-x-2 border-b border-slate-200 dark:border-slate-800 pb-2 overflow-x-auto">
      <button
        @click="activeTab = 'metricas'"
        :class="activeTab === 'metricas' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
        class="pb-2.5 px-3.5 text-xs sm:text-sm transition cursor-pointer shrink-0 flex items-center space-x-1.5"
      >
        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <span>Métricas Estadísticas (Trámites & Pagos)</span>
      </button>

      <button
        @click="activeTab = 'tipos_tramites'"
        :class="activeTab === 'tipos_tramites' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
        class="pb-2.5 px-3.5 text-xs sm:text-sm transition cursor-pointer shrink-0 flex items-center space-x-1.5"
      >
        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        <span>Gestionar Trámites ({{ tiposTramite.length }})</span>
      </button>

      <button
        @click="activeTab = 'usuarios'"
        :class="activeTab === 'usuarios' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
        class="pb-2.5 px-3.5 text-xs sm:text-sm transition cursor-pointer shrink-0 flex items-center space-x-1.5"
      >
        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <span>Gestión de Usuarios ({{ usuarios.length }})</span>
      </button>

      <button
        @click="activeTab = 'auditoria'; cargarAuditoria()"
        :class="activeTab === 'auditoria' ? 'border-b-2 border-red-600 text-red-600 dark:text-red-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
        class="pb-2.5 px-3.5 text-xs sm:text-sm transition cursor-pointer shrink-0 flex items-center space-x-1.5"
      >
        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        <span>Bitácora de Acceso & Seguridad</span>
        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300 font-bold">Auditoría</span>
      </button>
    </div>

    <!-- PESTAÑA 1: MÉTRICAS ESTADÍSTICAS TOTALES (Trámites & Pagos en Bs) -->
    <div v-if="activeTab === 'metricas'" class="space-y-6">
      <!-- KPIs de Trámites Totales -->
      <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Estadísticas Totales de Trámites</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <div class="m3-card p-5 border border-slate-200 dark:border-slate-800">
            <span class="text-xs font-semibold text-slate-500 block">Trámites Registrados</span>
            <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">{{ totalTramites }}</p>
            <span class="text-[10px] text-blue-600 font-bold">100% Expedientes</span>
          </div>
          <div class="m3-card p-5 border border-slate-200 dark:border-slate-800">
            <span class="text-xs font-semibold text-emerald-600 block">Finalizados Oficiales</span>
            <p class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ tramitesFinalizados }}</p>
            <span class="text-[10px] text-emerald-600 font-bold">Firmados y Despachados</span>
          </div>
          <div class="m3-card p-5 border border-slate-200 dark:border-slate-800">
            <span class="text-xs font-semibold text-purple-600 block">En Proceso / Firma</span>
            <p class="text-2xl sm:text-3xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ tramitesEnProceso }}</p>
            <span class="text-[10px] text-purple-600 font-bold">En Revisión o Vicerrectoría</span>
          </div>
          <div class="m3-card p-5 border border-slate-200 dark:border-slate-800">
            <span class="text-xs font-semibold text-red-600 block">Observados</span>
            <p class="text-2xl sm:text-3xl font-black text-red-600 dark:text-red-400 mt-1">{{ tramitesObservados }}</p>
            <span class="text-[10px] text-red-600 font-bold">En Subsanación</span>
          </div>
        </div>
      </div>

      <GraficosTramites v-if="reporteTramites" :por-estado="reporteTramites.tramites_por_estado" :por-mes="reporteTramites.tramites_por_mes" />
      <p v-else-if="errorGraficos" role="alert" class="text-sm text-red-700 dark:text-red-300">
        No se pudieron cargar los gráficos de trámites.
        <button type="button" class="font-semibold underline" @click="cargarGraficos">Reintentar</button>
      </p>

      <!-- KPIs de Pagos Estadísticos -->
      <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Estadísticas Totales de Pagos (Aranceles Universita­rios)</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="m3-card p-5 border border-slate-200 dark:border-slate-800 bg-gradient-to-tr from-emerald-50/60 to-teal-50/30 dark:from-emerald-950/20 dark:to-teal-950/10">
            <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300 block">Recaudación Total (Bs.)</span>
            <p class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ formatoBs(totalRecaudadoBs) }}</p>
            <span class="text-[11px] text-slate-500">Todos los medios de pago</span>
          </div>
          <div class="m3-card p-5 border border-slate-200 dark:border-slate-800">
            <span class="text-xs font-semibold text-slate-500 block">Transacciones Completadas</span>
            <p class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-1">{{ totalPagosCompletados }}</p>
            <span class="text-[11px] text-emerald-600 font-bold">Pagos Verificados</span>
          </div>
          <div class="m3-card p-5 border border-slate-200 dark:border-slate-800">
            <span class="text-xs font-semibold text-amber-600 block">Aranceles sin acreditar</span>
            <p class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ totalPagosPendientes }}</p>
            <span class="text-[11px] text-amber-600 font-bold">Incluye trámites sin intento de pago</span>
          </div>
        </div>
      </div>

      <section class="m3-card p-5 border border-slate-200 dark:border-slate-800 space-y-5">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Recaudación por método</h3>
        <div class="grid gap-3 sm:grid-cols-3">
          <div v-for="medio in resumenMedios" :key="medio.id" class="rounded-xl border border-slate-200 dark:border-slate-700 p-4">
            <div class="flex items-center justify-between gap-2">
              <span class="text-sm font-bold">{{ medio.nombre }}</span>
              <span class="text-xs text-slate-500">{{ medio.acreditados }} acreditados</span>
            </div>
            <p class="mt-2 text-xl font-black text-emerald-600 dark:text-emerald-400">{{ formatoBs(medio.recaudado) }}</p>
            <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700" aria-hidden="true">
              <div class="h-full rounded-full bg-emerald-500" :style="{ width: (totalRecaudadoBs ? medio.recaudado / totalRecaudadoBs * 100 : 0) + '%' }"></div>
            </div>
            <p class="mt-2 text-xs text-slate-500">{{ medio.activos }} pendientes o procesando ? {{ medio.incidencias }} con incidencia</p>
          </div>
        </div>

        <div class="flex flex-wrap items-end justify-between gap-3">
          <h3 class="text-sm font-bold text-slate-900 dark:text-white">Pagos de trámites <span class="text-xs font-normal text-slate-500">({{ pagosFiltrados.length }})</span></h3>
          <div class="flex flex-wrap gap-2">
            <select v-model="filtroMedioPago" aria-label="Método de pago" class="rounded-lg border border-slate-300 bg-transparent px-2 py-1.5 text-xs dark:border-slate-700 dark:bg-slate-900">
              <option value="">Todos los métodos</option>
              <option v-for="(nombre, id) in paymentMethods" :key="id" :value="id">{{ nombre }}</option>
            </select>
            <select v-model="filtroEstadoPago" aria-label="Estado del pago" class="rounded-lg border border-slate-300 bg-transparent px-2 py-1.5 text-xs dark:border-slate-700 dark:bg-slate-900">
              <option value="">Todos los estados</option>
              <option v-for="(nombre, id) in paymentStatuses" :key="id" :value="id">{{ nombre }}</option>
            </select>
          </div>
        </div>
        <div class="max-h-96 overflow-auto">
          <table class="w-full text-left text-xs">
            <thead class="sticky top-0 bg-slate-50 text-slate-500 dark:bg-slate-800">
              <tr>
                <th scope="col" class="p-3">Trámite</th>
                <th scope="col" class="p-3">Solicitante</th>
                <th scope="col" class="p-3">Método</th>
                <th scope="col" class="p-3">Estado</th>
                <th scope="col" class="p-3 text-right">Monto</th>
                <th scope="col" class="p-3">Fecha de pago</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-for="t in pagosFiltrados" :key="t.id">
                <td class="p-3 font-mono whitespace-nowrap">{{ t.codigo_tramite }}</td>
                <td class="p-3">{{ t.solicitante?.nombre }} {{ t.solicitante?.apellidos }}</td>
                <td class="p-3 whitespace-nowrap">{{ paymentMethods[t.pago?.metodo_pago] || t.pago?.metodo_pago || '?' }}</td>
                <td class="p-3">
                  <span class="whitespace-nowrap rounded-full px-2 py-1 font-semibold" :class="claseEstadoPago(t.pago)">
                    {{ paymentStatuses[paymentStatus(t.pago)] || paymentStatus(t.pago) }}
                  </span>
                </td>
                <td class="p-3 text-right tabular-nums whitespace-nowrap">{{ formatoBs(t.pago?.monto ?? t.tipo_tramite?.costo_bs ?? 0) }}</td>
                <td class="p-3 whitespace-nowrap">{{ t.pago?.fecha_pago ? new Date(t.pago.fecha_pago).toLocaleDateString('es-BO') : '?' }}</td>
              </tr>
              <tr v-if="!pagosFiltrados.length">
                <td colspan="6" class="p-6 text-center text-slate-500">No hay pagos para estos filtros.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Distribución por Tipo de Trámite Oficial -->
      <div class="m3-card p-6 border border-slate-200 dark:border-slate-800 space-y-4">
        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Aranceles y Plazos SLA Configurados</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-bold">
              <tr>
                <th class="p-3">Código</th>
                <th class="p-3">Nombre del Trámite</th>
                <th class="p-3">Precio Arancel</th>
                <th class="p-3">Plazo SLA</th>
                <th class="p-3">Trámites Activos</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-for="tipo in tiposTramite" :key="tipo.id">
                <td class="p-3 font-mono font-bold text-blue-600">{{ tipo.codigo }}</td>
                <td class="p-3 font-semibold text-slate-800 dark:text-slate-200">{{ tipo.nombre }}</td>
                <td class="p-3 font-black text-emerald-600">Bs. {{ tipo.costo_bs }}</td>
                <td class="p-3 font-mono text-slate-500">{{ tipo.tiempo_sla_horas }} Horas</td>
                <td class="p-3 font-bold text-slate-700 dark:text-slate-300">{{ contarPorTipo(tipo.id) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Widget de Monitoreo y Cumplimiento de Plazos SLA -->
      <div class="m3-card p-6 border border-slate-200 dark:border-slate-800 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center space-x-2">
              <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              <span>Monitoreo de Plazos Reglamentarios (SLA)</span>
            </h3>
            <p class="text-xs text-slate-500">Supervisión en tiempo real del cumplimiento de horas límite por trámite.</p>
          </div>
          <span class="text-xs font-mono font-bold px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-full">
            Total Monitoreados: {{ slaStats.total }}
          </span>
        </div>

        <!-- KPIs de Monitoreo SLA -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <div class="p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40">
            <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300 block">En Tiempo Normal</span>
            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ slaStats.enTiempo }}</p>
            <span class="text-[10px] text-emerald-600 font-semibold">Dentro del plazo</span>
          </div>
          <div class="p-4 rounded-2xl bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/40">
            <span class="text-[11px] font-bold text-amber-700 dark:text-amber-300 block">Por Vencer (&lt;24h)</span>
            <p class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ slaStats.porVencer }}</p>
            <span class="text-[10px] text-amber-600 font-semibold">Alerta preventiva</span>
          </div>
          <div class="p-4 rounded-2xl bg-red-50/70 dark:bg-red-950/20 border border-red-200 dark:border-red-800/40">
            <span class="text-[11px] font-bold text-red-700 dark:text-red-300 block">SLA Vencidos</span>
            <p class="text-2xl font-black text-red-600 dark:text-red-400 mt-1">{{ slaStats.vencidos }}</p>
            <span class="text-[10px] text-red-600 font-semibold">Fuera de tiempo límite</span>
          </div>
          <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-950/20 border border-blue-200 dark:border-blue-800/40">
            <span class="text-[11px] font-bold text-blue-700 dark:text-blue-300 block">Concluidos</span>
            <p class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ slaStats.cumplidos }}</p>
            <span class="text-[10px] text-blue-600 font-semibold">Despachados</span>
          </div>
        </div>

        <!-- Tabla resumen de trámites con alerta activa -->
        <div v-if="slaTramitesCriticos.length > 0" class="overflow-x-auto pt-2 border-t border-slate-100 dark:border-slate-800">
          <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">Trámites en Atención Prioritaria (Por Vencer o Vencidos):</p>
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 uppercase font-bold">
              <tr>
                <th class="p-2.5">Código</th>
                <th class="p-2.5">Trámite</th>
                <th class="p-2.5">Solicitante</th>
                <th class="p-2.5">Estado SLA</th>
                <th class="p-2.5">Horas Transcurridas</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-for="t in slaTramitesCriticos" :key="t.id">
                <td class="p-2.5 font-mono font-bold text-blue-600">{{ t.codigo_tramite }}</td>
                <td class="p-2.5 font-semibold text-slate-800 dark:text-slate-200">{{ t.asunto }}</td>
                <td class="p-2.5 text-slate-600 dark:text-slate-300">{{ t.solicitante ? t.solicitante.nombre + ' ' + t.solicitante.apellidos : 'N/A' }}</td>
                <td class="p-2.5">
                  <span
                    class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                    :class="t.monitoreo_sla?.estado_sla === 'VENCIDO' ? 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'"
                  >
                    {{ t.monitoreo_sla?.estado_sla === 'VENCIDO' ? 'VENCIDO' : 'POR VENCER' }}
                  </span>
                </td>
                <td class="p-2.5 font-mono text-slate-500">
                  {{ t.monitoreo_sla?.horas_transcurridas }}h / {{ t.monitoreo_sla?.horas_sla_limite }}h
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- PESTAÑA 2: GESTIONAR TRÁMITES (Crear, Editar, Activar y Desactivar) -->
    <div v-if="activeTab === 'tipos_tramites'" class="space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <span>Gestionar Trámites</span>
          </h2>
          <p class="text-xs text-slate-500">Configura aranceles, plazos SLA y disponibilidad de trámites universitarios.</p>
        </div>
        <div class="flex items-center space-x-2">
          <button
            @click="abrirModalTipoTramite(null)"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-full shadow-sm cursor-pointer transition flex items-center space-x-1.5"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Nuevo Trámite</span>
          </button>
        </div>
      </div>

      <div class="m3-card border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-400 uppercase font-semibold">
              <tr>
                <th class="p-3.5">Código</th>
                <th class="p-3.5">Nombre del Trámite</th>
                <th class="p-3.5">Arancel Oficial</th>
                <th class="p-3.5">Plazo SLA</th>
                <th class="p-3.5">Expedientes Registrados</th>
                <th class="p-3.5">Estado</th>
                <th class="p-3.5 text-center">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-for="t in tiposTramite" :key="t.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                <td class="p-3.5 font-mono font-bold text-blue-600 dark:text-blue-400">{{ t.codigo }}</td>
                <td class="p-3.5 font-bold text-slate-800 dark:text-slate-200">
                  {{ t.nombre }}
                  <span v-if="t.descripcion" class="block text-[11px] text-slate-400 font-normal line-clamp-1">{{ t.descripcion }}</span>
                </td>
                <td class="p-3.5 font-black text-emerald-600 dark:text-emerald-400 text-sm">Bs. {{ t.costo_bs }}</td>
                <td class="p-3.5 font-mono text-slate-500">{{ t.tiempo_sla_horas }} Horas</td>
                <td class="p-3.5">
                  <span class="px-2.5 py-0.5 rounded-full font-bold text-[10px]" :class="contarPorTipo(t.id) > 0 ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-400'">
                    {{ contarPorTipo(t.id) }} trámite(s)
                  </span>
                </td>
                <td class="p-3.5">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="t.estado === 'ACTIVO' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'">
                    {{ t.estado }}
                  </span>
                </td>
                <td class="p-3.5 text-center">
                  <div class="flex items-center justify-center space-x-1.5">
                    <button
                      @click="abrirModalTipoTramite(t)"
                      class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-lg text-xs cursor-pointer transition shadow-2xs"
                    >
                      Editar
                    </button>
                    
                    <!-- Botón Desactivar / Reactivar -->
                    <button
                      @click="alternarEstadoTipo(t)"
                      :class="t.estado === 'ACTIVO' ? 'bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200 dark:border-amber-800' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800'"
                      class="px-2.5 py-1 rounded-lg text-xs font-bold cursor-pointer transition border"
                      :title="t.estado === 'ACTIVO' ? 'Desactivar trámite' : 'Reactivar trámite'"
                    >
                      {{ t.estado === 'ACTIVO' ? 'Desactivar' : 'Reactivar' }}
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- PESTAÑA 3: GESTIÓN DE USUARIOS (Administrar y Editar Usuarios, Excluyendo su propia cuenta) -->
    <div v-if="activeTab === 'usuarios'" class="space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>Gestión de Usuarios</span>
          </h2>
          <p class="text-xs text-slate-500">Administra cuentas, roles y carreras. Por seguridad, la cuenta del Administrador en sesión está protegida.</p>
        </div>
        <div class="flex items-center space-x-2">
          <button
            @click="abrirModalCrearUsuario"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-full shadow-sm cursor-pointer transition flex items-center space-x-1"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Nuevo Usuario</span>
          </button>
        </div>
      </div>

      <!-- Barra de Filtros Avanzados (Texto, Carrera / Unidad Académica, Rol y Estado) -->
      <div class="m3-card p-4 border border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-900/60 flex flex-wrap items-center gap-3">
        <!-- Búsqueda textual -->
        <div class="flex-1 min-w-[200px]">
          <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Buscar por Texto</label>
          <div class="relative">
            <input
              v-model="filtroUsuario"
              type="text"
              placeholder="Buscar por nombre, registro o CI..."
              class="w-full pl-9 pr-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 1114 0z"/></svg>
          </div>
        </div>

        <!-- Filtro por Carrera / Unidad Académica -->
        <div class="min-w-[190px]">
          <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Filtrar por Carrera / Unidad</label>
          <select
            v-model="filtroCarrera"
            class="w-full px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 cursor-pointer"
          >
            <option value="TODAS">Todas las Carreras / Unidades</option>
            <option v-for="car in listaCarrerasDisponibles" :key="car" :value="car">{{ car }}</option>
          </select>
        </div>

        <!-- Filtro por Rol -->
        <div class="min-w-[140px]">
          <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Filtrar por Rol</label>
          <select
            v-model="filtroRol"
            class="w-full px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 cursor-pointer"
          >
            <option value="TODOS">Todos los Roles</option>
            <option value="ALUMNO">ALUMNO</option>
            <option value="DOCENTE">DOCENTE</option>
            <option value="ENCARGADO">ENCARGADO</option>
            <option value="VICERRECTOR">VICERRECTOR</option>
            <option value="ADMINISTRATIVO">ADMINISTRATIVO</option>
          </select>
        </div>

        <!-- Filtro por Estado -->
        <div class="min-w-[120px]">
          <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">Estado</label>
          <select
            v-model="filtroEstadoUsuario"
            class="w-full px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-semibold text-slate-800 dark:text-slate-200 cursor-pointer"
          >
            <option value="TODOS">Todos</option>
            <option value="ACTIVO">Activos</option>
            <option value="INACTIVO">Inactivos</option>
          </select>
        </div>

        <!-- Botón Limpiar Filtros -->
        <div class="self-end pb-0.5">
          <button
            v-if="filtroUsuario || filtroCarrera !== 'TODAS' || filtroRol !== 'TODOS' || filtroEstadoUsuario !== 'TODOS'"
            @click="limpiarFiltrosUsuario"
            class="px-3 py-1.5 text-xs text-blue-600 dark:text-blue-400 hover:underline cursor-pointer font-bold"
          >
            Limpiar filtros
          </button>
        </div>
      </div>

      <div class="m3-card border border-slate-200 dark:border-slate-800 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-400 uppercase font-semibold">
              <tr>
                <th class="p-3.5">Nombre y Apellidos</th>
                <th class="p-3.5">Código / Reg.</th>
                <th class="p-3.5">C.I.</th>
                <th class="p-3.5">Rol</th>
                <th class="p-3.5">Unidad / Carrera</th>
                <th class="p-3.5">Estado</th>
                <th class="p-3.5 text-center">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-for="u in usuariosFiltrados" :key="u.id_usuario" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                <td class="p-3.5 font-bold text-slate-800 dark:text-slate-200">
                  {{ u.nombre }} {{ u.apellidos }}
                  <span v-if="esMismoUsuario(u)" class="ml-1 text-[10px] font-mono px-1.5 py-0.2 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 font-bold">(Tu Cuenta Actual)</span>
                </td>
                <td class="p-3.5 font-mono">{{ u.registro }}</td>
                <td class="p-3.5 font-mono">{{ u.ci }}</td>
                <td class="p-3.5 font-bold" :class="getRolColor(u.rol)">{{ u.rol }}</td>
                <td class="p-3.5 text-slate-500">{{ u.alumno ? u.alumno.carrera : (u.unidad_academica || u.cargo || 'UAGRM') }}</td>
                <td class="p-3.5">
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="u.estado === 'ACTIVO' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300'">
                    {{ u.estado }}
                  </span>
                </td>
                <td class="p-3.5 text-center">
                  <!-- Si es la cuenta del mismo Admin, se deshabilita para evitar auto-bloqueos -->
                  <button
                    v-if="!esMismoUsuario(u)"
                    @click="abrirEditarUsuario(u)"
                    class="px-3 py-1 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-lg text-xs cursor-pointer transition shadow-2xs"
                  >
                    Editar
                  </button>
                  <span v-else class="text-[10px] text-slate-400 font-semibold italic">Protegido</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- PESTAÑA 3: BITÁCORA DE ACCESO Y SEGURIDAD -->
    <div v-if="activeTab === 'auditoria'" class="space-y-5">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
          <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center space-x-2">
            <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span>Bitácora de Auditoría del Sistema</span>
            <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300 font-bold">Confidencial</span>
          </h2>
          <p class="text-xs text-slate-500">Registro de visitas, inicios de sesión y operaciones del sistema.</p>
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
            <option value="VISITA_PAGINA">Visitas a páginas</option>
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

      <p v-if="errorAuditoria" role="alert" class="text-sm text-red-700 dark:text-red-300">{{ errorAuditoria }}</p>

      <!-- KPIs de Auditoría -->
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

      <div v-if="!errorAuditoria" class="grid gap-4 lg:grid-cols-2">
        <section v-for="ranking in rankingsAuditoria" :key="ranking.titulo" class="m3-card space-y-4 border border-slate-200 p-5 dark:border-slate-800">
          <div>
            <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ ranking.titulo }}</h3>
            <p class="text-xs text-slate-500">{{ ranking.resumen }}</p>
          </div>
          <p v-if="!ranking.filas.length" class="text-sm text-slate-500">Sin registros.</p>
          <ol v-else class="space-y-3">
            <li v-for="fila in ranking.filas" :key="fila.nombre" class="space-y-1">
              <div class="flex justify-between gap-2 text-xs text-slate-700 dark:text-slate-300">
                <span class="min-w-0 break-all">{{ fila.nombre }}</span>
                <span class="shrink-0 font-mono font-bold">{{ fila.total }}</span>
              </div>
              <div aria-hidden="true" class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                <div class="h-full rounded-full bg-blue-600 dark:bg-blue-400" :style="{ width: `${fila.total / Math.max(1, ranking.filas[0].total) * 100}%` }"></div>
              </div>
            </li>
          </ol>
        </section>
      </div>

      <!-- Tabla de Eventos -->
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
                  <br />{{ log.fecha_evento }}
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
                  <span v-if="log.tipo_evento === 'VISITA_PAGINA'">{{ log.ruta_accedida }}</span>
                  <template v-else>
                    <span class="font-bold text-blue-600 dark:text-blue-400">{{ log.metodo_http || '—' }}</span>
                    {{ log.ruta_accedida ? `/${log.ruta_accedida}` : '—' }}
                  </template>
                </td>
                <td class="p-3 font-mono text-[11px] text-slate-500">{{ log.ip_origen || '—' }}</td>
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

    <!-- MODAL EDITAR USUARIO (Excluye auto-edición) -->
    <div v-if="modalEditarUsuario" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="m3-card p-6 sm:p-8 max-w-md w-full space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Editar Usuario</h3>
        <p class="text-xs text-slate-500">Modificando a: <span class="font-bold text-slate-800 dark:text-slate-200">{{ formEditar.nombre }} {{ formEditar.apellidos }}</span></p>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nombre</label>
            <input v-model="formEditar.nombre" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Apellidos</label>
            <input v-model="formEditar.apellidos" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs" />
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Correo Electrónico</label>
          <input v-model="formEditar.email" type="email" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs" />
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Teléfono</label>
            <input v-model="formEditar.telefono" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Estado</label>
            <select v-model="formEditar.estado" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold">
              <option value="ACTIVO">ACTIVO</option>
              <option value="INACTIVO">INACTIVO</option>
            </select>
          </div>
        </div>

        <p v-if="errorEdicion" class="text-xs font-semibold text-red-600 dark:text-red-400">
          {{ errorEdicion }}
        </p>

        <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100 dark:border-slate-800">
          <button @click="modalEditarUsuario = false" class="px-4 py-2 text-xs font-semibold text-slate-500 cursor-pointer">Cancelar</button>
          <button @click="guardarEdicionUsuario" class="px-5 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow cursor-pointer">Guardar Cambios</button>
        </div>
      </div>
    </div>

    <!-- MODAL NUEVO USUARIO -->
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

        <div class="flex justify-end space-x-2 pt-4">
          <button @click="modalNuevoUsuario = false" class="px-4 py-2 text-xs font-semibold text-slate-500 cursor-pointer">Cancelar</button>
          <button @click="guardarUsuario" class="px-5 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow cursor-pointer">Crear Usuario</button>
        </div>
      </div>
    </div>

    <!-- MODAL CREAR / EDITAR TIPO DE TRÁMITE -->
    <div v-if="modalTipoTramite" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="m3-card p-6 sm:p-8 max-w-lg max-h-[90vh] overflow-y-auto w-full space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
          {{ editandoTipoId ? 'Editar Tipo de Trámite' : 'Crear Nuevo Tipo de Trámite' }}
        </h3>
        <p class="text-xs text-slate-500">Configuración arancelaria oficial y SLA según normativa universitaria.</p>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Código del Trámite</label>
          <input
            v-model="formTipo.codigo"
            :disabled="!!editandoTipoId"
            type="text"
            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono uppercase font-bold disabled:opacity-60"
            placeholder="EJ: DIPLOMA_ACAD"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nombre Oficial del Trámite</label>
          <input
            v-model="formTipo.nombre"
            type="text"
            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
            placeholder="Ej: Diploma Académico de Grado"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Descripción / Finalidad</label>
          <textarea
            v-model="formTipo.descripcion"
            rows="2"
            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
            placeholder="Breve explicación del trámite..."
          ></textarea>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Arancel Oficial (Bs.)</label>
            <input
              v-model="formTipo.costo_bs"
              type="number"
              step="0.50"
              class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold text-emerald-600"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Plazo SLA (Horas)</label>
            <input
              v-model="formTipo.tiempo_sla_horas"
              type="number"
              class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono"
            />
          </div>
        </div>

        <div v-if="editandoTipoId">
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Estado</label>
          <select v-model="formTipo.estado" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold">
            <option value="ACTIVO">ACTIVO</option>
            <option value="INACTIVO">INACTIVO</option>
          </select>
        </div>

        <fieldset class="space-y-2 rounded-xl border border-slate-200 p-3 dark:border-slate-700">
          <legend class="px-1 text-xs font-bold text-slate-800 dark:text-slate-200">Adjuntos por etapa</legend>
          <p class="text-xs text-slate-500">Marca cuándo se pueden subir archivos para este tipo de trámite.</p>
          <label v-for="etapa in etapasAdjuntos" :key="etapa.estado"
            class="flex items-center justify-between gap-2 rounded-lg bg-slate-50 px-3 py-2 text-xs dark:bg-slate-800">
            <span>{{ etapa.nombre }}</span>
            <input v-model="formTipo.etapas_adjuntos" type="checkbox" :value="etapa.estado">
          </label>
          <p class="text-xs text-slate-500">El expediente se cierra para adjuntos al compilarlo y permanece cerrado durante firma y despacho.</p>
        </fieldset>

        <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100 dark:border-slate-800">
          <button @click="modalTipoTramite = false" :disabled="guardandoTipo" class="px-4 py-2 text-xs font-semibold text-slate-500 cursor-pointer disabled:opacity-50">Cancelar</button>
          <button @click="guardarTipoTramite" :disabled="guardandoTipo || !formTipo.nombre" class="px-5 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-full shadow cursor-pointer flex items-center space-x-1">
            <span v-if="guardandoTipo">Guardando...</span>
            <span v-else>{{ editandoTipoId ? 'Guardar Cambios' : 'Crear Tipo de Trámite' }}</span>
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { state, actions } from '../stores/appState';
import axios from 'axios';
import { paymentMethods, paymentStatuses, paymentStatus } from '../paymentLabels';
import GraficosTramites from '../components/GraficosTramites.vue';

const activeTab = ref('metricas');
const tramites = ref([]);
const reporteTramites = ref(null);
const errorGraficos = ref(false);
const tiposTramite = ref([]);
const usuarios = ref([]);
const filtroUsuario = ref('');
const filtroCarrera = ref('TODAS');
const filtroRol = ref('TODOS');
const filtroEstadoUsuario = ref('TODOS');

// Lista única de Carreras y Unidades Académicas para el selector de filtro
const listaCarrerasDisponibles = computed(() => {
  const set = new Set();
  usuarios.value.forEach(u => {
    const carrera = u.alumno?.carrera || u.unidad_academica;
    if (carrera && carrera.trim()) {
      set.add(carrera.trim());
    }
  });
  return Array.from(set).sort();
});

function limpiarFiltrosUsuario() {
  filtroUsuario.value = '';
  filtroCarrera.value = 'TODAS';
  filtroRol.value = 'TODOS';
  filtroEstadoUsuario.value = 'TODOS';
}

// Bitácora de Auditoría
const filtroTipoAuditoria = ref('TODOS');
const auditoriaLogs = ref([]);
const visitasPorPagina = ref([]);
const operacionesPorRuta = ref([]);
const errorAuditoria = ref('');
const auditoriaKpis = ref({
  logins_exitosos: 0,
  logins_fallidos: 0,
  trafico_operativo: 0,
  bloqueos_seguridad: 0,
  visitas_globales: 0,
});
const rankingsAuditoria = computed(() => [
  {
    titulo: 'Páginas más visitadas',
    resumen: `${auditoriaKpis.value.visitas_globales} visitas acumuladas · Hasta 10 páginas`,
    filas: visitasPorPagina.value.map(pagina => ({ nombre: pagina.pagina, total: pagina.total_visitas })),
  },
  {
    titulo: 'Rutas con más operaciones registradas',
    resumen: 'Hasta 10 rutas, agrupadas por método HTTP',
    filas: operacionesPorRuta.value.map(ruta => ({
      nombre: `${ruta.metodo_http || '—'} /${ruta.ruta_accedida}`,
      total: ruta.total,
    })),
  },
]);

// Modal y Edición de Usuario
const modalEditarUsuario = ref(false);
const formEditar = ref({ id_usuario: null, nombre: '', apellidos: '', email: '', telefono: '', estado: 'ACTIVO' });
const errorEdicion = ref('');

// Modal Nuevo Usuario
const modalNuevoUsuario = ref(false);
const formUser = ref({ nombre: '', apellidos: '', registro: '', ci: '', email: '', rol: 'ALUMNO' });

// Verificación de si la fila es el mismo Admin en sesión
function esMismoUsuario(u) {
  return String(u.id_usuario) === String(state.user?.id_usuario) || String(u.registro) === String(state.user?.registro);
}

// Estadísticas de Trámites
const totalTramites = computed(() => tramites.value.length);
const tramitesFinalizados = computed(() => tramites.value.filter(t => t.estado === 'FIRMADO' || t.estado === 'DESPACHADO').length);
const tramitesEnProceso = computed(() => tramites.value.filter(t => ['INICIADO', 'REQUISITOS_VALIDADOS', 'EXPEDIENTE_COMPILADO', 'EN_FIRMA'].includes(t.estado)).length);
const tramitesObservados = computed(() => tramites.value.filter(t => t.estado === 'OBSERVADO').length);

const filtroMedioPago = ref('');
const filtroEstadoPago = ref('');
const monedaBs = new Intl.NumberFormat('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
const formatoBs = (monto) => `Bs. ${monedaBs.format(Number(monto))}`;
const estadosActivos = ['PENDIENTE', 'PROCESANDO'];

const pagosCompletadosList = computed(() => tramites.value.filter(t => paymentStatus(t.pago) === 'COMPLETADO'));
const totalPagosCompletados = computed(() => pagosCompletadosList.value.length);
const totalPagosPendientes = computed(() => tramites.value.length - totalPagosCompletados.value);
const totalRecaudadoBs = computed(() => pagosCompletadosList.value.reduce((total, t) => total + Number(t.pago.monto), 0));
const resumenMedios = computed(() => Object.entries(paymentMethods).map(([id, nombre]) => {
  const pagos = tramites.value.map(t => t.pago).filter(pago => pago?.metodo_pago === id);
  const acreditados = pagos.filter(pago => paymentStatus(pago) === 'COMPLETADO');
  const activos = pagos.filter(pago => estadosActivos.includes(paymentStatus(pago))).length;
  return {
    id,
    nombre,
    acreditados: acreditados.length,
    activos,
    incidencias: pagos.length - acreditados.length - activos,
    recaudado: acreditados.reduce((total, pago) => total + Number(pago.monto), 0),
  };
}));
const pagosFiltrados = computed(() => tramites.value.filter(t =>
  (!filtroMedioPago.value || t.pago?.metodo_pago === filtroMedioPago.value)
  && (!filtroEstadoPago.value || paymentStatus(t.pago) === filtroEstadoPago.value)
));

function claseEstadoPago(pago) {
  const estado = paymentStatus(pago);
  if (estado === 'COMPLETADO') return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
  if (estadosActivos.includes(estado)) return 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300';
  return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
}

// Estadísticas y Monitoreo SLA
const slaStats = computed(() => {
  let enTiempo = 0;
  let porVencer = 0;
  let vencidos = 0;
  let cumplidos = 0;
  let total = 0;

  tramites.value.forEach(t => {
    if (t.monitoreo_sla) {
      total++;
      const est = t.monitoreo_sla.estado_sla;
      if (['CUMPLIDO_EN_TIEMPO', 'CUMPLIDO_FUERA_DE_TIEMPO'].includes(est)) {
        cumplidos++;
      } else if (est === 'VENCIDO') {
        vencidos++;
      } else if (est === 'POR_VENCER') {
        porVencer++;
      } else {
        enTiempo++;
      }
    }
  });

  return { total, enTiempo, porVencer, vencidos, cumplidos };
});

const slaTramitesCriticos = computed(() => {
  return tramites.value.filter(t => {
    return t.monitoreo_sla && ['POR_VENCER', 'VENCIDO'].includes(t.monitoreo_sla.estado_sla);
  }).slice(0, 5); // Mostrar los primeros 5 más críticos
});

function contarPorTipo(idTipo) {
  return tramites.value.filter(t => t.id_tipo_tramite === idTipo).length;
}

const usuariosFiltrados = computed(() => {
  return usuarios.value.filter(u => {
    // 1. Filtro textual
    if (filtroUsuario.value.trim()) {
      const q = filtroUsuario.value.toLowerCase().trim();
      const nom = `${u.nombre} ${u.apellidos}`.toLowerCase();
      const reg = String(u.registro || '').toLowerCase();
      const ci = String(u.ci || '').toLowerCase();
      const matchText = nom.includes(q) || reg.includes(q) || ci.includes(q);
      if (!matchText) return false;
    }

    // 2. Filtro por Carrera / Unidad Académica
    if (filtroCarrera.value !== 'TODAS') {
      const carreraU = (u.alumno?.carrera || u.unidad_academica || '').trim();
      if (carreraU !== filtroCarrera.value) return false;
    }

    // 3. Filtro por Rol
    if (filtroRol.value !== 'TODOS') {
      if (u.rol !== filtroRol.value) return false;
    }

    // 4. Filtro por Estado
    if (filtroEstadoUsuario.value !== 'TODOS') {
      if (u.estado !== filtroEstadoUsuario.value) return false;
    }

    return true;
  });
});

function getRolColor(rol) {
  switch (rol) {
    case 'ADMINISTRATIVO': return 'text-red-600 dark:text-red-400';
    case 'VICERRECTOR': return 'text-purple-600 dark:text-purple-400';
    case 'ENCARGADO': return 'text-emerald-600 dark:text-emerald-400';
    case 'DOCENTE': return 'text-amber-600 dark:text-amber-400';
    default: return 'text-blue-600 dark:text-blue-400';
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

async function loadData() {
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
  }
}

async function cargarGraficos() {
  errorGraficos.value = false;
  try {
    const res = await axios.get('/api/reportes/dashboard');
    reporteTramites.value = res.data;
  } catch {
    errorGraficos.value = true;
  }
}

async function cargarAuditoria() {
  errorAuditoria.value = '';
  try {
    const res = await axios.get('/api/auditoria', {
      params: { tipo: filtroTipoAuditoria.value }
    });
    if (res.data.success) {
      auditoriaLogs.value = res.data.data || [];
      auditoriaKpis.value = res.data.kpis || auditoriaKpis.value;
      visitasPorPagina.value = res.data.visitas_por_pagina || [];
      operacionesPorRuta.value = res.data.operaciones_por_ruta || [];
    }
  } catch (e) {
    auditoriaLogs.value = [];
    errorAuditoria.value = e.response?.data?.message || 'No se pudo cargar la bitácora.';
  }
}

function abrirModalCrearUsuario() {
  formUser.value = { nombre: '', apellidos: '', registro: '', ci: '', email: '', rol: 'ALUMNO' };
  modalNuevoUsuario.value = true;
}

async function guardarUsuario() {
  try {
    await axios.post('/api/usuarios', formUser.value);
    modalNuevoUsuario.value = false;
    actions.toast(`Usuario ${formUser.value.nombre} ${formUser.value.apellidos} creado exitosamente.`, 'success', 'Usuario Creado');
    await loadData();
  } catch (e) {
    actions.toast(e.response?.data?.message || 'Error al crear usuario', 'error', 'Error al Guardar');
  }
}

function abrirEditarUsuario(u) {
  if (esMismoUsuario(u)) return;
  formEditar.value = {
    id_usuario: u.id_usuario,
    nombre: u.nombre,
    apellidos: u.apellidos,
    email: u.email,
    telefono: u.telefono || '',
    estado: u.estado,
  };
  errorEdicion.value = '';
  modalEditarUsuario.value = true;
}

async function guardarEdicionUsuario() {
  errorEdicion.value = '';
  try {
    await axios.put(`/api/usuarios/${formEditar.value.id_usuario}`, formEditar.value);
    modalEditarUsuario.value = false;
    actions.toast(`Usuario ${formEditar.value.nombre} ${formEditar.value.apellidos} actualizado correctamente.`, 'success', 'Usuario Actualizado');
    await loadData();
  } catch (e) {
    errorEdicion.value = e.response?.data?.message || 'Error al guardar los cambios del usuario.';
    actions.toast(errorEdicion.value, 'error', 'Error al Actualizar');
  }
}

// Gestión de Tipos de Trámites y Aranceles
const modalTipoTramite = ref(false);
const editandoTipoId = ref(null);
const guardandoTipo = ref(false);
const etapasAdjuntos = [
  { estado: 'INICIADO', nombre: 'Recepción' },
  { estado: 'REQUISITOS_VALIDADOS', nombre: 'Después de validar requisitos' },
  { estado: 'OBSERVADO', nombre: 'Subsanación de observaciones' },
];
const adjuntosPredeterminados = ['INICIADO', 'OBSERVADO'];
const formTipo = ref({
  codigo: '',
  nombre: '',
  descripcion: '',
  costo_bs: 50,
  tiempo_sla_horas: 72,
  estado: 'ACTIVO',
});

function abrirModalTipoTramite(tipo) {
  if (tipo) {
    editandoTipoId.value = tipo.id;
    formTipo.value = {
      codigo: tipo.codigo,
      nombre: tipo.nombre,
      descripcion: tipo.descripcion || '',
      costo_bs: tipo.costo_bs,
      tiempo_sla_horas: tipo.tiempo_sla_horas,
      estado: tipo.estado || 'ACTIVO',
      etapas_adjuntos: [...(tipo.etapas_adjuntos ?? adjuntosPredeterminados)],
    };
  } else {
    editandoTipoId.value = null;
    formTipo.value = {
      codigo: '',
      nombre: '',
      descripcion: '',
      costo_bs: 50,
      tiempo_sla_horas: 72,
      estado: 'ACTIVO',
      etapas_adjuntos: [...adjuntosPredeterminados],
    };
  }
  modalTipoTramite.value = true;
}

async function guardarTipoTramite() {
  if (guardandoTipo.value) return;
  guardandoTipo.value = true;
  try {
    if (editandoTipoId.value) {
      await axios.put(`/api/tramites/tipos/${editandoTipoId.value}`, formTipo.value);
      actions.toast(`Tipo de trámite "${formTipo.value.nombre}" actualizado con éxito.`, 'success', 'Catálogo Actualizado');
    } else {
      await axios.post('/api/tramites/tipos', formTipo.value);
      actions.toast(`Tipo de trámite "${formTipo.value.nombre}" creado exitosamente.`, 'success', 'Nuevo Trámite Registrado');
    }
    modalTipoTramite.value = false;
    await loadData();
  } catch (e) {
    actions.toast(e.response?.data?.message || 'Error al guardar tipo de trámite.', 'error', 'Error');
  } finally {
    guardandoTipo.value = false;
  }
}

async function alternarEstadoTipo(tipo) {
  const nuevoEstado = tipo.estado === 'ACTIVO' ? 'INACTIVO' : 'ACTIVO';
  const accion = nuevoEstado === 'INACTIVO' ? 'desactivar (Borrado Lógico)' : 'reactivar';
  
  if (!confirm(`¿Desea ${accion} el trámite "${tipo.nombre}"?`)) {
    return;
  }

  try {
    const res = await axios.delete(`/api/tramites/tipos/${tipo.id}`);
    actions.toast(res.data.message || `Trámite ${tipo.nombre} actualizado.`, 'success', 'Soft Delete / Estado');
    await loadData();
  } catch (e) {
    actions.toast(e.response?.data?.message || 'Error al cambiar estado del trámite.', 'error', 'Error');
  }
}

onMounted(() => {
  loadData();
  cargarGraficos();
  cargarAuditoria();
});
</script>
