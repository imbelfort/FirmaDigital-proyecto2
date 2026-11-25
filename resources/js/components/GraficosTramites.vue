<template>
  <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
    <!-- Trámites por Estado -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-4">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <h3 class="font-bold text-base text-slate-900 dark:text-white">Trámites por estado</h3>
        <div role="group" aria-label="Tipo de gráfico por estado" class="flex gap-1 rounded-lg bg-slate-100 dark:bg-slate-800 p-1">
          <button
            v-for="tipo in ['Dona', 'Torta', 'Barras']"
            :key="tipo"
            type="button"
            :aria-pressed="graficoEstado === tipo"
            class="rounded-md px-3 py-1.5 text-xs font-semibold"
            :class="graficoEstado === tipo ? 'bg-white text-blue-700 shadow-sm dark:bg-slate-700 dark:text-blue-300' : 'text-slate-600 dark:text-slate-300'"
            @click="graficoEstado = tipo"
          >{{ tipo }}</button>
        </div>
      </div>
      <p v-if="!totalEstados" class="text-sm text-slate-500">No hay trámites registrados.</p>
      <div v-else class="flex flex-wrap items-center gap-6">
        <div
          v-if="graficoEstado !== 'Barras'"
          aria-hidden="true"
          class="mx-auto grid size-40 shrink-0 place-items-center rounded-full"
          :style="{ background: fondoCircular }"
        >
          <div v-if="graficoEstado === 'Dona'" class="grid size-28 content-center rounded-full bg-white text-center dark:bg-slate-900">
            <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ totalEstados }}</span>
            <span class="text-xs text-slate-500">Trámites</span>
          </div>
        </div>
        <ul class="min-w-48 flex-1 space-y-3">
          <li v-for="item in estados" :key="item.estado" class="space-y-1">
            <div class="flex items-center gap-2 text-xs">
              <span aria-hidden="true" class="size-2.5 shrink-0 rounded-full" :style="{ backgroundColor: item.color }"></span>
              <span class="flex-1 text-slate-700 dark:text-slate-300">{{ item.etiqueta }}</span>
              <span class="font-mono text-slate-600 dark:text-slate-300">{{ item.total }} ({{ formatoPorcentaje.format(item.proporcion) }})</span>
            </div>
            <div
              v-if="graficoEstado === 'Barras'"
              aria-hidden="true"
              class="h-2 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800"
            >
              <div class="h-full rounded-full" :style="{ width: `${item.proporcion * 100}%`, backgroundColor: item.color }"></div>
            </div>
          </li>
        </ul>
      </div>
    </div>

    <div class="min-w-0 space-y-4 rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
      <div>
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Solicitudes por mes</h3>
        <p class="text-xs text-slate-500">Últimos seis meses, incluido el mes actual</p>
      </div>
      <p v-if="!meses.some(mes => mes.total > 0)" class="text-sm text-slate-500">No hay solicitudes en este período.</p>
      <div v-if="meses.length" class="overflow-x-auto">
        <svg role="img" :aria-label="resumenMensual" viewBox="0 0 420 180" class="w-full min-w-72 text-slate-600 dark:text-slate-300">
          <line x1="20" y1="140" x2="400" y2="140" stroke="currentColor" stroke-opacity=".25" />
          <polyline :points="meses.map(mes => `${mes.x},${mes.y}`).join(' ')" fill="none" stroke="#3b82f6" stroke-width="3" stroke-linejoin="round" />
          <g v-for="mes in meses" :key="mes.mes">
            <circle :cx="mes.x" :cy="mes.y" r="4" fill="#3b82f6" />
            <text :x="mes.x" :y="mes.y - 10" text-anchor="middle" fill="currentColor" font-size="12" font-weight="600">{{ mes.total }}</text>
            <text :x="mes.x" y="164" text-anchor="middle" fill="currentColor" font-size="11">{{ mes.etiqueta }}</text>
          </g>
        </svg>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  porEstado: { type: Array, required: true },
  porMes: { type: Array, required: true },
});

const graficoEstado = ref('Dona');
const colores = ['#2563eb', '#0d9488', '#7c3aed', '#d97706', '#e11d48', '#0891b2', '#4f46e5', '#65a30d'];
const formatoPorcentaje = new Intl.NumberFormat('es-BO', { style: 'percent', maximumFractionDigits: 1 });
const formatoMes = new Intl.DateTimeFormat('es-BO', { month: 'short', year: '2-digit', timeZone: 'UTC' });

const totalEstados = computed(() =>
  props.porEstado.reduce((total, item) => total + Number(item.total), 0)
);
const estados = computed(() => props.porEstado.map((item, index) => {
  const nombre = item.estado.replaceAll('_', ' ').toLowerCase();
  return {
    ...item,
    etiqueta: nombre.charAt(0).toUpperCase() + nombre.slice(1),
    color: colores[index % colores.length],
    proporcion: totalEstados.value ? Number(item.total) / totalEstados.value : 0,
  };
}));
const fondoCircular = computed(() => {
  let inicio = 0;
  const segmentos = estados.value.map((item, index) => {
    const fin = index === estados.value.length - 1 ? 100 : inicio + item.proporcion * 100;
    const segmento = `${item.color} ${inicio}% ${fin}%`;
    inicio = fin;
    return segmento;
  });
  return segmentos.length === 1 ? estados.value[0].color : `conic-gradient(${segmentos.join(', ')})`;
});

const meses = computed(() => {
  const serie = props.porMes;
  const maximo = Math.max(1, ...serie.map(mes => Number(mes.total)));
  return serie.map((mes, index) => ({
    ...mes,
    etiqueta: formatoMes.format(new Date(`${mes.mes}-01T00:00:00Z`)),
    x: 35 + index * 70,
    y: 140 - (Number(mes.total) / maximo) * 108,
  }));
});
const resumenMensual = computed(() => meses.value.map(mes => `${mes.etiqueta}, ${mes.total} solicitudes`).join('. '));
</script>
