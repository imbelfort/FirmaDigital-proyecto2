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
        <div class="inline-block animate-spin rounded-full h-6 w-6 border-2 border-blue-600 border-t-transparent"></div>
        <p class="mt-2 text-sm font-medium">Buscando en todo el sistema...</p>
      </div>

      <div v-else-if="hasResults" class="max-h-[75vh] overflow-y-auto p-3 space-y-4">
        
        <!-- Acciones Directas y Temas (Ej. Activar Modo Oscuro, Temas, Navegación Rápida) -->
        <div v-if="results.acciones_sistema.length > 0">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-3 mb-1.5 flex items-center justify-between">
            <span>Acciones Rápidas & Ajustes del Sistema</span>
            <span class="text-[10px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-500">{{ results.acciones_sistema.length }}</span>
          </h3>
          <div class="space-y-1">
            <button
              v-for="opc in results.acciones_sistema"
              :key="opc.titulo"
              @click="ejecutarAccion(opc)"
              class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl hover:bg-blue-50/70 dark:hover:bg-slate-800 text-left transition cursor-pointer group"
            >
              <div class="flex items-center space-x-3">
                <span class="p-2 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 group-hover:scale-105 transition">
                  <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
                <div>
                  <span class="font-bold text-slate-800 dark:text-slate-100 text-sm block">{{ opc.titulo }}</span>
                  <span class="text-[11px] text-slate-400">Clic para aplicar inmediatamente</span>
                </div>
              </div>
              <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                {{ opc.categoria }}
              </span>
            </button>
          </div>
        </div>

        <!-- Tipos de Trámites Oficiales (Catálogo para Iniciar - Solo visible para Alumno o Docente) -->
        <div v-if="(state.user?.rol === 'ALUMNO' || state.user?.rol === 'DOCENTE') && results.tipos_tramite && results.tipos_tramite.length > 0">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-3 mb-1.5 flex items-center justify-between">
            <span>Trámites Académicos Disponibles para Solicitar</span>
            <span class="text-[10px] bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 px-1.5 py-0.5 rounded font-bold">{{ results.tipos_tramite.length }}</span>
          </h3>
          <div class="space-y-1">
            <button
              v-for="tipo in results.tipos_tramite"
              :key="tipo.id"
              @click="seleccionarTipoTramite(tipo)"
              class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl hover:bg-emerald-50/60 dark:hover:bg-slate-800 text-left transition cursor-pointer border border-transparent hover:border-emerald-300 dark:hover:border-emerald-800"
            >
              <div>
                <div class="flex items-center space-x-2">
                  <span class="font-mono text-xs font-bold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-blue-600 dark:text-blue-400">{{ tipo.codigo }}</span>
                  <span class="font-bold text-slate-900 dark:text-white text-sm">{{ tipo.nombre }}</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ tipo.descripcion }}</p>
              </div>
              <div class="text-right pl-3 shrink-0">
                <span class="text-xs font-black text-emerald-600 dark:text-emerald-400">Bs. {{ tipo.costo_bs }}</span>
                <span class="block text-[10px] text-slate-400">Solicitar →</span>
              </div>
            </button>
          </div>
        </div>

        <!-- Trámites y Expedientes (Nuevos, en Proceso o Terminados) -->
        <div v-if="results.tramites.length > 0">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-3 mb-1.5 flex items-center justify-between">
            <span>Expedientes y Trámites (Historial Completo)</span>
            <span class="text-[10px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-500">{{ results.tramites.length }}</span>
          </h3>
          <div class="space-y-1">
            <button
              v-for="tr in results.tramites"
              :key="tr.id"
              @click="selectTramite(tr)"
              class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-left transition cursor-pointer"
            >
              <div>
                <div class="flex items-center space-x-2">
                  <span class="font-mono text-xs font-black text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2 py-0.5 rounded-md">{{ tr.codigo_tramite }}</span>
                  <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ tr.asunto }}</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                  Solicitante: <strong class="text-slate-700 dark:text-slate-300">{{ tr.solicitante ? tr.solicitante.nombre + ' ' + tr.solicitante.apellidos : 'N/A' }}</strong> 
                  <span v-if="tr.solicitante" class="font-mono text-[11px] text-slate-400"> (Reg: {{ tr.solicitante.registro }} | CI: {{ tr.solicitante.ci }})</span>
                </p>
              </div>
              <div class="text-right shrink-0 pl-2">
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full uppercase inline-block" :class="getEstadoBadge(tr.estado)">
                  {{ formatEstado(tr.estado) }}
                </span>
                <span class="block text-[10px] font-mono text-slate-400 mt-1">
                  {{ tr.pago && tr.pago.estado === 'COMPLETADO' ? 'Bs. ' + (tr.tipo_tramite ? tr.tipo_tramite.costo_bs : '0') + ' (Pagado)' : 'Pendiente Pago' }}
                </span>
              </div>
            </button>
          </div>
        </div>

        <!-- Usuarios con sus Trámites Asociados (Para Alumnos, Docentes, Encargados) -->
        <div v-if="results.usuarios.length > 0">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-3 mb-1.5 flex items-center justify-between">
            <span>Personas (Alumnos, Docentes, Personal)</span>
            <span class="text-[10px] bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-slate-500">{{ results.usuarios.length }}</span>
          </h3>
          <div class="space-y-1.5">
            <div
              v-for="u in results.usuarios"
              :key="u.id_usuario"
              class="p-3 rounded-xl bg-slate-50/70 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 space-y-2"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-bold text-slate-900 dark:text-white">
                    {{ u.nombre }} {{ u.apellidos }}
                  </p>
                  <p class="text-xs text-slate-500">
                    Reg/Código: <span class="font-mono font-bold text-slate-700 dark:text-slate-300">{{ u.registro }}</span> | C.I.: <span class="font-mono">{{ u.ci }}</span> | {{ u.unidad_academica || u.cargo || 'UAGRM' }}
                  </p>
                </div>
                <span class="text-xs px-2.5 py-0.5 rounded-full font-bold font-mono bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                  {{ u.rol }}
                </span>
              </div>

              <!-- Trámites Recientes de este Usuario (si los tiene) -->
              <div v-if="u.tramites && u.tramites.length > 0" class="pt-2 border-t border-slate-200/50 dark:border-slate-700/50 space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Trámites iniciados por este usuario:</span>
                <div v-for="ut in u.tramites" :key="ut.id" class="flex items-center justify-between text-xs p-1.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700">
                  <div class="truncate max-w-[320px]">
                    <span class="font-mono text-[10px] font-bold text-blue-600 dark:text-blue-400 mr-1.5">{{ ut.codigo_tramite }}</span>
                    <span class="font-medium text-slate-800 dark:text-slate-200">{{ ut.asunto }}</span>
                  </div>
                  <span class="text-[10px] font-bold px-2 py-0.2 rounded-full" :class="getEstadoBadge(ut.estado)">{{ formatEstado(ut.estado) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Certificados Oficiales con QR -->
        <div v-if="results.certificados.length > 0">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 px-3 mb-1.5 flex items-center justify-between">
            <span>Certificados Oficiales con Firma Digital ADSIB / QR</span>
            <span class="text-[10px] bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-300 px-1.5 py-0.5 rounded font-bold">{{ results.certificados.length }}</span>
          </h3>
          <div class="space-y-1">
            <div
              v-for="c in results.certificados"
              :key="c.id"
              class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-800/60 text-left"
            >
              <div>
                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ c.titulo_documento }}</p>
                <p class="text-xs font-mono text-emerald-700 dark:text-emerald-400">UUID: {{ c.codigo_verificacion }}</p>
              </div>
              <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-600 text-white shadow-xs">
                DOCUMENTO FIRMADO Y VIGENTE
              </span>
            </div>
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
  acciones_sistema: [],
  tipos_tramite: [],
  tramites: [],
  usuarios: [],
  certificados: [],
});

let debounceTimer = null;

function handleInput() {
  clearTimeout(debounceTimer);
  if (query.value.trim().length < 2) {
    results.value = { acciones_sistema: [], tipos_tramite: [], tramites: [], usuarios: [], certificados: [] };
    return;
  }
  loading.value = true;
  debounceTimer = setTimeout(async () => {
    try {
      const res = await axios.get('/api/search', {
        params: {
          q: query.value,
          usuario_id: state.user?.id_usuario
        }
      });
      results.value = res.data.results;
    } catch (e) {
      console.error(e);
    } finally {
      loading.value = false;
    }
  }, 200);
}

const hasResults = computed(() => {
  return (results.value.acciones_sistema && results.value.acciones_sistema.length > 0) ||
         (results.value.tipos_tramite && results.value.tipos_tramite.length > 0) ||
         (results.value.tramites && results.value.tramites.length > 0) ||
         (results.value.usuarios && results.value.usuarios.length > 0) ||
         (results.value.certificados && results.value.certificados.length > 0);
});

function ejecutarAccion(opc) {
  actions.closeGlobalSearch();
  switch (opc.tipo) {
    case 'ACCION_DARK':
      actions.toggleDark();
      actions.toast('Modo de pantalla actualizado', 'info');
      break;
    case 'ACCION_CONTRASTE':
      actions.toggleContrast();
      actions.toast('Modo alto contraste cambiado', 'info');
      break;
    case 'TEMA_UAGRM':
      actions.setTheme('uagrm');
      actions.toast('Tema UAGRM activado', 'success');
      break;
    case 'TEMA_EMERALD':
      actions.setTheme('emerald');
      actions.toast('Tema Esmeralda activado', 'success');
      break;
    case 'TEMA_INDIGO':
      actions.setTheme('indigo');
      actions.toast('Tema Índigo activado', 'success');
      break;
    case 'FONT_NORMAL':
      actions.setFontSize('font-normal');
      actions.toast('Tamaño de fuente estándar', 'info');
      break;
    case 'FONT_LG':
      actions.setFontSize('font-lg');
      actions.toast('Tamaño de fuente aumentado', 'info');
      break;
    case 'RUTA_REPORTES':
      actions.setActiveTab('reportes');
      break;
    default:
      actions.setActiveTab('dashboard');
      break;
  }
}

function seleccionarTipoTramite(tipo) {
  if (state.user?.rol === 'ALUMNO' || state.user?.rol === 'DOCENTE') {
    actions.solicitarTramiteDesdeBuscador(tipo);
  } else {
    actions.closeGlobalSearch();
    actions.setActiveTab('dashboard');
    actions.toast(`Trámite ${tipo.nombre} disponible en aranceles (Bs. ${tipo.costo_bs})`, 'info', tipo.codigo);
  }
}

function selectTramite(tr) {
  actions.closeGlobalSearch();
  actions.setActiveTab('dashboard');
  actions.toast(`Expediente ${tr.codigo_tramite} (${formatEstado(tr.estado)}) seleccionado`, 'info', tr.codigo_tramite);
}

function formatEstado(texto) {
  if (!texto) return '';
  return texto.split('_').map(p => p.charAt(0).toUpperCase() + p.slice(1).toLowerCase()).join(' ');
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
