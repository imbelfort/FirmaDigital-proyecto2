<template>
  <div v-if="state.globalSearchOpen" class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-20" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="actions.closeGlobalSearch()"></div>

    <div class="relative mx-auto max-w-2xl transform divide-y divide-slate-100 dark:divide-slate-800 rounded-2xl bg-white dark:bg-slate-900 shadow-2xl ring-1 ring-black/5 dark:ring-white/10 transition-all overflow-hidden">
      <!-- Barra de input -->
      <div class="relative flex items-center px-4 py-3">
        <svg class="h-6 w-6 text-slate-400 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 1114 0z" />
        </svg>
        <input
          ref="searchInput"
          v-model="query"
          @input="handleInput"
          type="text"
          class="w-full bg-transparent text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none text-base sm:text-lg"
          placeholder="Buscar trámites, personas, opciones del sistema, certificados..."
        />
        <button @click="actions.closeGlobalSearch()" class="p-1 rounded-md text-slate-400 hover:text-slate-600 dark:hover:text-white">
          <kbd class="text-xs bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700">ESC</kbd>
        </button>
      </div>

      <!-- Resultados -->
      <div v-if="loading" class="p-8 text-center text-slate-500">
        <div class="inline-block animate-spin rounded-full h-6 w-6 border-2 border-primary-500 border-t-transparent"></div>
        <p class="mt-2 text-sm">Buscando en tiempo real...</p>
      </div>

      <div v-else-if="hasResults" class="max-h-96 overflow-y-auto p-3 space-y-4">
        <!-- Opciones del Sistema -->
        <div v-if="results.opciones_sistema.length > 0">
          <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-3 mb-1">Opciones y Módulos</h3>
          <div class="space-y-1">
            <button
              v-for="opc in results.opciones_sistema"
              :key="opc.ruta"
              @click="selectOption(opc.ruta)"
              class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-left transition"
            >
              <div class="flex items-center space-x-3">
                <span class="p-1.5 rounded-md bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400">
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
                <span class="font-medium text-slate-800 dark:text-slate-200 text-sm">{{ opc.titulo }}</span>
              </div>
              <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500">{{ opc.categoria }}</span>
            </button>
          </div>
        </div>

        <!-- Trámites -->
        <div v-if="results.tramites.length > 0">
          <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-3 mb-1">Trámites Registrados</h3>
          <div class="space-y-1">
            <button
              v-for="tr in results.tramites"
              :key="tr.id"
              @click="selectTramite(tr)"
              class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-left transition"
            >
              <div>
                <div class="flex items-center space-x-2">
                  <span class="font-mono text-xs font-bold text-blue-600 dark:text-blue-400">{{ tr.codigo_tramite }}</span>
                  <span class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ tr.asunto }}</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Solicitante: {{ tr.solicitante ? tr.solicitante.nombre + ' ' + tr.solicitante.apellidos : 'N/A' }}</p>
              </div>
              <span class="text-xs font-semibold px-2 py-0.5 rounded-full" :class="getEstadoBadge(tr.estado)">{{ tr.estado }}</span>
            </button>
          </div>
        </div>

        <!-- Usuarios -->
        <div v-if="results.usuarios.length > 0">
          <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-3 mb-1">Usuarios y Autoridades</h3>
          <div class="space-y-1">
            <button
              v-for="u in results.usuarios"
              :key="u.id_usuario"
              @click="selectUsuario(u)"
              class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-left transition"
            >
              <div>
                <p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ u.nombre }} {{ u.apellidos }}</p>
                <p class="text-xs text-slate-500">Reg: {{ u.registro }} | CI: {{ u.ci }} | {{ u.unidad_academica || u.cargo || 'UAGRM' }}</p>
              </div>
              <span class="text-xs px-2 py-0.5 rounded font-mono bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300">{{ u.rol }}</span>
            </button>
          </div>
        </div>

        <!-- Certificados Oficiales -->
        <div v-if="results.certificados.length > 0">
          <h3 class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-3 mb-1">Certificados con Firma Digital QR</h3>
          <div class="space-y-1">
            <button
              v-for="c in results.certificados"
              :key="c.id"
              @click="selectCertificado(c)"
              class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-left transition"
            >
              <div>
                <p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ c.titulo_documento }}</p>
                <p class="text-xs font-mono text-emerald-600 dark:text-emerald-400">UUID: {{ c.codigo_verificacion }}</p>
              </div>
              <span class="text-xs px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">VALIDADO</span>
            </button>
          </div>
        </div>
      </div>

      <div v-else-if="query.length >= 2" class="p-8 text-center text-slate-500">
        <p class="text-sm">No se encontraron resultados para "<span class="font-semibold">{{ query }}</span>"</p>
        <p class="text-xs text-slate-400 mt-1">Pruebe buscando por código TR, número de carnet, registro o nombre de trámite.</p>
      </div>

      <div v-else class="p-6 text-center text-slate-400 text-sm">
        Escriba al menos 2 caracteres para buscar en todo el sistema.
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import { state, actions } from '../stores/appState';
import axios from 'axios';

const query = ref('');
const loading = ref(false);
const searchInput = ref(null);
const results = ref({
  opciones_sistema: [],
  tramites: [],
  usuarios: [],
  certificados: [],
});

let debounceTimer = null;

function handleInput() {
  clearTimeout(debounceTimer);
  if (query.value.trim().length < 2) {
    results.value = { opciones_sistema: [], tramites: [], usuarios: [], certificados: [] };
    return;
  }
  loading.value = true;
  debounceTimer = setTimeout(async () => {
    try {
      const res = await axios.get('/api/search', { params: { q: query.value } });
      results.value = res.data.results;
    } catch (e) {
      console.error(e);
    } finally {
      loading.value = false;
    }
  }, 250);
}

const hasResults = computed(() => {
  return results.value.opciones_sistema.length > 0 ||
         results.value.tramites.length > 0 ||
         results.value.usuarios.length > 0 ||
         results.value.certificados.length > 0;
});

function selectOption(ruta) {
  actions.setActiveTab(ruta);
  actions.closeGlobalSearch();
}

function selectTramite(tr) {
  actions.setActiveTab('tramites');
  actions.closeGlobalSearch();
}

function selectUsuario(u) {
  actions.setActiveTab('usuarios');
  actions.closeGlobalSearch();
}

function selectCertificado(c) {
  actions.setActiveTab('monitoreo-sla');
  actions.closeGlobalSearch();
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

// Atajo de teclado Ctrl+K o Cmd+K
function onKeydown(e) {
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault();
    if (state.globalSearchOpen) {
      actions.closeGlobalSearch();
    } else {
      actions.openGlobalSearch();
      nextTick(() => searchInput.value?.focus());
    }
  }
  if (e.key === 'Escape' && state.globalSearchOpen) {
    actions.closeGlobalSearch();
  }
}

onMounted(() => {
  window.addEventListener('keydown', onKeydown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', onKeydown);
});
</script>
