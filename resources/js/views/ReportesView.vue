<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white heading-font">Reportes y Estadísticas Oficiales</h1>
        <p class="text-xs text-slate-500">Métricas analíticas institucionales con protección contra división por cero (CU8).</p>
      </div>
      <button @click="loadData" class="px-3.5 py-1.5 text-xs font-semibold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 rounded-lg text-slate-700 dark:text-slate-300 transition">
        Refrescar Métricas
      </button>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800">
        <span class="text-xs text-slate-400 uppercase font-semibold">Total Trámites</span>
        <p class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ data.kpis ? data.kpis.total_tramites : 0 }}</p>
      </div>
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800">
        <span class="text-xs text-slate-400 uppercase font-semibold">Total Firmados</span>
        <p class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ data.kpis ? data.kpis.total_firmados : 0 }}</p>
      </div>
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800">
        <span class="text-xs text-slate-400 uppercase font-semibold">Observados</span>
        <p class="text-3xl font-extrabold text-red-600 dark:text-red-400 mt-1">{{ data.kpis ? data.kpis.total_observados : 0 }}</p>
      </div>
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800">
        <span class="text-xs text-slate-400 uppercase font-semibold">Tasa Cumplimiento SLA</span>
        <p class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ data.kpis ? data.kpis.tasa_sla : 95 }}%</p>
      </div>
    </div>

    <!-- Detalle de Trámites por Estado y Firmas por Autoridad -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- Trámites por Estado -->
      <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-4">
        <h3 class="font-bold text-base text-slate-900 dark:text-white">Distribución de Trámites por Estado</h3>
        <div class="space-y-3">
          <div v-for="item in data.tramites_por_estado" :key="item.estado" class="space-y-1">
            <div class="flex justify-between text-xs font-semibold">
              <span class="text-slate-700 dark:text-slate-300">{{ item.estado }}</span>
              <span class="text-slate-500 font-mono">{{ item.total }}</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
              <div
                class="bg-blue-600 h-2 rounded-full"
                :style="`width: ${data.kpis?.total_tramites ? (item.total / data.kpis.total_tramites) * 100 : 0}%`"
              ></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Firmas Emitidas por Autoridad -->
      <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-4">
        <h3 class="font-bold text-base text-slate-900 dark:text-white">Firmas Digitales por Autoridad</h3>
        <div class="space-y-3">
          <div v-for="aut in data.firmas_por_autoridad" :key="aut.nombre" class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl flex items-center justify-between text-xs">
            <div>
              <p class="font-bold text-slate-900 dark:text-white">{{ aut.nombre }} {{ aut.apellidos }}</p>
              <p class="text-slate-500 text-[11px]">{{ aut.cargo }}</p>
            </div>
            <span class="font-mono font-extrabold text-sm text-purple-600 dark:text-purple-400">{{ aut.total_firmas }} firmas</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const data = ref({});

async function loadData() {
  try {
    const res = await axios.get('/api/reportes/dashboard');
    data.value = res.data;
  } catch (e) {
    console.error(e);
  }
}

onMounted(() => {
  loadData();
});
</script>
