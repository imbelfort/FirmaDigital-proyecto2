<template>
  <div class="space-y-6">
    <!-- Card de Bienvenida M3 Estilizada y Limpia -->
    <div class="m3-hero-gradient rounded-3xl p-6 sm:p-7 text-white shadow-xl relative overflow-hidden transition-all duration-300">
      <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center space-x-2">
            <span class="text-2xl sm:text-3xl animate-bounce">👋</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold heading-font tracking-tight">
              Hola, {{ state.user.nombre }} {{ state.user.apellidos }}
            </h1>
          </div>
          <p class="text-white/85 text-xs sm:text-sm mt-1.5 font-medium">
            Facultad: <span class="font-bold text-white">{{ state.user.unidad_academica || 'FICCT' }}</span> | Código Docente: <span class="font-mono">{{ state.user.registro }}</span> | C.I.: <span class="font-mono">{{ state.user.ci }}</span>
          </p>
        </div>
        <div class="flex items-center space-x-2 self-start sm:self-center">
          <span class="text-[11px] font-bold tracking-wider uppercase px-3.5 py-1.5 rounded-full bg-white/20 border border-white/25 backdrop-blur-md shadow-xs">
            📚 Portal Docente & Revisor Académico
          </span>
        </div>
      </div>
      <!-- Decoración sutil de fondo -->
      <div class="absolute right-0 bottom-0 opacity-10 translate-x-12 translate-y-12 pointer-events-none">
        <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a10.97 10.97 0 00-.25 2.449c0 4.148 2.5 7.9 6 9.4 3.5-1.5 6-5.252 6-9.4 0-.847-.09-1.67-.25-2.45l2.644-1.13a1 1 0 000-1.84l-7-3z"/></svg>
      </div>
    </div>

    <!-- Pestañas de Docente -->
    <div class="flex space-x-2 border-b border-slate-200 dark:border-slate-800 pb-2">
      <button
        @click="activeSubTab = 'mis_tramites'"
        :class="activeSubTab === 'mis_tramites' ? 'border-b-2 border-amber-600 text-amber-600 dark:text-amber-400 font-bold' : 'text-slate-500'"
        class="pb-2 px-3 text-sm transition"
      >
        Mis Trámites de Convalidación / Grado
      </button>
      <button
        @click="activeSubTab = 'todos'"
        :class="activeSubTab === 'todos' ? 'border-b-2 border-amber-600 text-amber-600 dark:text-amber-400 font-bold' : 'text-slate-500'"
        class="pb-2 px-3 text-sm transition"
      >
        Trámites de Estudiantes para Revisión
      </button>
    </div>

    <!-- Lista de Trámites -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div
        v-for="t in tramites"
        :key="t.id"
        class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 space-y-3"
      >
        <div class="flex items-center justify-between">
          <span class="font-mono text-xs font-bold text-amber-600 dark:text-amber-400">{{ t.codigo_tramite }}</span>
          <span class="text-xs font-semibold px-2 py-0.5 rounded-full" :class="getEstadoBadge(t.estado)">{{ t.estado }}</span>
        </div>
        <h3 class="font-bold text-slate-900 dark:text-white">{{ t.asunto }}</h3>
        <p class="text-xs text-slate-500">Solicitante: {{ t.solicitante ? t.solicitante.nombre + ' ' + t.solicitante.apellidos : 'N/A' }}</p>

        <!-- Recaudos -->
        <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl space-y-1 text-xs">
          <p class="font-semibold text-slate-700 dark:text-slate-300">Documentos del Expediente:</p>
          <div v-for="d in t.documentos" :key="d.id" class="flex justify-between text-slate-500">
            <span>{{ d.nombre_documento }}</span>
            <span class="font-bold text-emerald-600">{{ d.estado_validacion }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { state } from '../stores/appState';
import axios from 'axios';

const activeSubTab = ref('mis_tramites');
const tramites = ref([]);

async function loadData() {
  try {
    const res = await axios.get('/api/tramites');
    tramites.value = res.data.data;
  } catch (e) {
    console.error(e);
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
