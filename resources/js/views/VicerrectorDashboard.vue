<template>
  <div class="space-y-6">
    <!-- Card de Bienvenida M3 Estilizada y Limpia -->
    <div class="m3-hero-gradient rounded-3xl p-6 sm:p-7 text-white shadow-xl relative overflow-hidden transition-all duration-300">
      <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center space-x-2.5">
            <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white shadow-xs">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold heading-font tracking-tight">
              Hola, {{ state.user.nombre }} {{ state.user.apellidos }}
            </h1>
          </div>
          <p class="text-white/85 text-xs sm:text-sm mt-1.5 font-medium">
            Cargo: <span class="font-bold text-white">{{ state.user.cargo || 'Vicerrector General' }}</span> | Código: <span class="font-mono font-bold">{{ state.user.registro }}</span> | C.I.: <span class="font-mono">{{ state.user.ci }}</span>
          </p>
        </div>
        <div class="flex items-center space-x-2 self-start sm:self-center">
          <span class="text-[11px] font-bold tracking-wider uppercase px-3.5 py-1.5 rounded-full bg-white/20 border border-white/25 backdrop-blur-md shadow-xs flex items-center space-x-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span>Despacho Vicerrectoral & Firma Digital</span>
          </span>
        </div>
      </div>
      <!-- Decoración sutil de fondo -->
      <div class="absolute right-0 bottom-0 opacity-10 translate-x-12 translate-y-12 pointer-events-none">
        <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a10.97 10.97 0 00-.25 2.449c0 4.148 2.5 7.9 6 9.4 3.5-1.5 6-5.252 6-9.4 0-.847-.09-1.67-.25-2.45l2.644-1.13a1 1 0 000-1.84l-7-3z"/></svg>
      </div>
    </div>

    <!-- KPIs Rápidos -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <span class="text-xs font-semibold text-slate-400 uppercase">Trámites Totales</span>
        <p class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ kpis.total_tramites }}</p>
      </div>
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <span class="text-xs font-semibold text-slate-400 uppercase">Firmados Oficiales</span>
        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ kpis.total_firmados }}</p>
      </div>
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <span class="text-xs font-semibold text-slate-400 uppercase">En Proceso de Firma</span>
        <p class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">{{ kpis.total_en_proceso }}</p>
      </div>
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        <span class="text-xs font-semibold text-slate-400 uppercase">Cumplimiento SLA 72h</span>
        <p class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ kpis.tasa_sla }}%</p>
      </div>
    </div>

    <!-- Navegación de Pestañas de Autoridad -->
    <div class="flex space-x-2 border-b border-slate-200 dark:border-slate-800 pb-2">
      <button
        @click="activeSubTab = 'pendientes_firma'"
        :class="activeSubTab === 'pendientes_firma' ? 'border-b-2 border-purple-600 text-purple-600 dark:text-purple-400 font-bold' : 'text-slate-500'"
        class="pb-2 px-3 text-sm transition"
      >
        Bandeja de Firma Digital ({{ tramitesEnFirma.length }})
      </button>
      <button
        @click="activeSubTab = 'delegaciones'"
        :class="activeSubTab === 'delegaciones' ? 'border-b-2 border-purple-600 text-purple-600 dark:text-purple-400 font-bold' : 'text-slate-500'"
        class="pb-2 px-3 text-sm transition"
      >
        Delegación de Autoridad (CU2)
      </button>
      <button
        @click="activeSubTab = 'indicadores'"
        :class="activeSubTab === 'indicadores' ? 'border-b-2 border-purple-600 text-purple-600 dark:text-purple-400 font-bold' : 'text-slate-500'"
        class="pb-2 px-3 text-sm transition"
      >
        Indicadores de Gestión (CU2)
      </button>
    </div>

    <!-- Pestaña 1: Bandeja de Firma -->
    <div v-if="activeSubTab === 'pendientes_firma'" class="space-y-4">
      <div class="flex justify-between items-center">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Expedientes listos para Firma Digital (Consolidada / Remota)</h2>
        <span class="text-xs text-slate-500">Token Algoritmo: SHA256withRSA</span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="t in allTramites"
          :key="t.id"
          class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 space-y-3"
        >
          <div class="flex items-center justify-between">
            <span class="font-mono text-xs font-bold text-purple-600 dark:text-purple-400">{{ t.codigo_tramite }}</span>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full" :class="getEstadoBadge(t.estado)">{{ t.estado }}</span>
          </div>

          <h3 class="font-bold text-slate-900 dark:text-white text-base">{{ t.asunto }}</h3>
          <p class="text-xs text-slate-500">Solicitante: {{ t.solicitante ? t.solicitante.nombre + ' ' + t.solicitante.apellidos : 'N/A' }} | CI: {{ t.solicitante ? t.solicitante.ci : '' }}</p>

          <div class="text-xs p-3 bg-slate-50 dark:bg-slate-800 rounded-xl space-y-1">
            <p class="font-semibold text-slate-700 dark:text-slate-300">Recaudos validados: {{ t.documentos ? t.documentos.length : 0 }}</p>
            <div v-for="d in t.documentos" :key="d.id" class="flex justify-between text-[11px] text-slate-500">
              <span class="truncate max-w-[200px]">{{ d.nombre_documento }}</span>
              <span class="font-mono text-emerald-600">{{ d.estado_validacion }}</span>
            </div>
          </div>

          <!-- Acciones de Firma -->
          <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex space-x-2">
            <button
              v-if="t.estado !== 'FIRMADO' && t.estado !== 'DESPACHADO'"
              @click="openModalFirma(t, 'CONSOLIDADA')"
              class="flex-1 py-2 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl transition shadow"
            >
              Firma Consolidada
            </button>
            <button
              v-if="t.estado !== 'FIRMADO' && t.estado !== 'DESPACHADO'"
              @click="openModalFirma(t, 'REMOTA')"
              class="flex-1 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl transition shadow"
            >
              Firma Remota ADSIB
            </button>
            <div v-else class="w-full text-center py-2 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 font-bold text-xs rounded-xl">
              ✓ Firmado Digitalmente (Cert: {{ t.firma_digital ? t.firma_digital.numero_certificado_digital : 'Oficial' }})
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pestaña 2: Delegación de Autoridad (CU2) -->
    <div v-if="activeSubTab === 'delegaciones'" class="space-y-4">
      <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800">
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Delegaciones de Autoridad Activas (CU2)</h3>
        <p class="text-xs text-slate-500 mb-4">Permite transferir facultades de firma digital vicerrectoral a autoridades encargadas durante ausencias oficiales.</p>
        
        <div class="space-y-3">
          <div
            v-for="d in delegaciones"
            :key="d.id"
            class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs space-y-2"
          >
            <div class="flex items-center justify-between">
              <span class="font-bold text-purple-700 dark:text-purple-300 font-mono text-sm">{{ d.resolucion }}</span>
              <span class="px-2 py-0.5 rounded-full font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">{{ d.estado }}</span>
            </div>
            <p class="text-slate-800 dark:text-slate-200 font-semibold">{{ d.motivo }}</p>
            <div class="flex justify-between text-slate-500 pt-1">
              <span>Titular: {{ d.titular ? d.titular.nombre + ' ' + d.titular.apellidos : 'Vicerrector' }}</span>
              <span>Delegado: {{ d.delegado ? d.delegado.nombre + ' ' + d.delegado.apellidos : 'Encargado' }}</span>
            </div>
            <p class="text-[11px] text-indigo-600 dark:text-indigo-400 font-bold">Alcance de Firma: {{ d.alcance_firma }} | Vigencia: {{ d.fecha_inicio }} al {{ d.fecha_fin }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Pestaña 3: Indicadores de Gestión (CU2) -->
    <div v-if="activeSubTab === 'indicadores'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div
        v-for="ind in indicadores"
        :key="ind.id"
        class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 space-y-3"
      >
        <div class="flex justify-between items-center">
          <span class="font-mono text-xs font-bold text-blue-600 dark:text-blue-400">{{ ind.codigo }}</span>
          <span class="text-xs px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">{{ ind.periodo }}</span>
        </div>
        <h4 class="font-bold text-slate-900 dark:text-white">{{ ind.nombre }}</h4>
        <p class="text-xs text-slate-500">{{ ind.descripcion }}</p>
        
        <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl flex justify-between items-center">
          <div>
            <span class="text-[10px] uppercase text-slate-400 block font-semibold">Meta Esperada</span>
            <span class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ ind.meta_esperada }} {{ ind.unidad_medida }}</span>
          </div>
          <div class="text-right">
            <span class="text-[10px] uppercase text-slate-400 block font-semibold">Valor Actual</span>
            <span class="font-bold text-emerald-600 text-sm">{{ ind.valor_actual }} {{ ind.unidad_medida }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Ejecutar Firma Digital -->
    <div v-if="modalFirma" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-md w-full space-y-4 shadow-2xl border border-slate-100 dark:border-slate-800">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Ejecutar Firma Digital Vicerrectoral</h3>
        <p class="text-xs text-slate-500">Trámite: <span class="font-bold text-purple-600">{{ tramiteAFirmar.codigo_tramite }}</span></p>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipo de Firma</label>
          <select
            v-model="formFirma.tipo_firma"
            class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm"
          >
            <option value="CONSOLIDADA">Firma Consolidada (Token Local)</option>
            <option value="REMOTA">Firma Remota (Servicio Seguro ADSIB)</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Número de Certificado Digital ADSIB / UAGRM</label>
          <input
            v-model="formFirma.numero_certificado_digital"
            type="text"
            class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono text-sm"
          />
        </div>

        <div class="flex justify-end space-x-2 pt-4">
          <button @click="modalFirma = false" class="px-4 py-2 text-xs font-semibold text-slate-500">Cancelar</button>
          <button @click="ejecutarFirma" class="px-5 py-2 text-xs font-bold bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow">Aplicar Firma y Emitir QR</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { state } from '../stores/appState';
import axios from 'axios';

const activeSubTab = ref('pendientes_firma');
const allTramites = ref([]);
const delegaciones = ref([]);
const indicadores = ref([]);
const kpis = ref({
  total_tramites: 0,
  total_firmados: 0,
  total_en_proceso: 0,
  tasa_sla: 95,
});

const modalFirma = ref(false);
const tramiteAFirmar = ref({});
const formFirma = ref({
  tipo_firma: 'CONSOLIDADA',
  numero_certificado_digital: 'CERT-ADSIB-2026-VR001',
});

const tramitesEnFirma = computed(() => {
  return allTramites.value.filter(t => t.estado === 'EN_FIRMA' || t.estado === 'INICIADO');
});

async function loadData() {
  try {
    const [resTramites, resDeleg, resDash] = await Promise.all([
      axios.get('/api/tramites'),
      axios.get('/api/firma/delegaciones'),
      axios.get('/api/reportes/dashboard'),
    ]);
    allTramites.value = resTramites.data.data;
    delegaciones.value = resDeleg.data.data;
    kpis.value = resDash.data.kpis;
    indicadores.value = resDash.data.indicadores;
  } catch (e) {
    console.error(e);
  }
}

function openModalFirma(t, tipo) {
  tramiteAFirmar.value = t;
  formFirma.value.tipo_firma = tipo;
  modalFirma.value = true;
}

async function ejecutarFirma() {
  try {
    await axios.post(`/api/firma/tramite/${tramiteAFirmar.value.id}`, formFirma.value);
    modalFirma.value = false;
    alert('Firma Digital aplicada correctamente y Certificado con QR emitido.');
    await loadData();
  } catch (e) {
    alert('Error al firmar');
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

onMounted(() => {
  loadData();
});
</script>
