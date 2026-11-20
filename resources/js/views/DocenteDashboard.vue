<template>
  <div class="space-y-6">
    <!-- Encabezado Docente -->
    <div class="bg-gradient-to-r from-amber-600 via-orange-700 to-red-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
      <div class="relative z-10">
        <span class="text-xs font-semibold tracking-wider uppercase px-3 py-1 rounded-full bg-white/20 backdrop-blur-xs">
          Portal Docente & Revisor Académico
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold mt-2 heading-font">
          Docente: {{ state.user.nombre }} {{ state.user.apellidos }}
        </h1>
        <p class="text-amber-100 text-sm mt-1">
          Facultad: <span class="font-bold text-white">{{ state.user.unidad_academica || 'FICCT' }}</span> | Código Docente: <span class="font-mono">{{ state.user.registro }}</span> | C.I.: <span class="font-mono">{{ state.user.ci }}</span>
        </p>
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
