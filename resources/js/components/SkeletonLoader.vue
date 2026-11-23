<template>
  <div class="space-y-4 animate-pulse w-full">
    <!-- Variante 1: Tarjetas de Trámites (Grid 2 columnas) -->
    <div v-if="tipo === 'cards'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div
        v-for="i in cantidad"
        :key="i"
        class="m3-card p-5 border border-slate-200 dark:border-slate-800 space-y-4 bg-white/60 dark:bg-slate-900/40"
      >
        <div class="flex items-center justify-between">
          <div class="h-6 w-24 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
          <div class="h-5 w-28 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
        </div>
        <div class="space-y-2">
          <div class="h-5 w-3/4 bg-slate-200 dark:bg-slate-800 rounded-md"></div>
          <div class="h-3 w-1/2 bg-slate-100 dark:bg-slate-800/60 rounded-md"></div>
        </div>
        <div class="p-3.5 bg-slate-100/70 dark:bg-slate-800/40 rounded-2xl space-y-2">
          <div class="h-3 w-1/3 bg-slate-200 dark:bg-slate-700 rounded"></div>
          <div class="h-2 w-full bg-slate-200 dark:bg-slate-700 rounded-full"></div>
        </div>
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex justify-between">
          <div class="h-8 w-24 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
          <div class="h-8 w-28 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
        </div>
      </div>
    </div>

    <!-- Variante 2: Filas de Tabla / Lista -->
    <div v-else-if="tipo === 'table'" class="m3-card border border-slate-200 dark:border-slate-800 overflow-hidden divide-y divide-slate-100 dark:divide-slate-800">
      <div v-for="i in cantidad" :key="i" class="p-4 flex items-center justify-between space-x-4">
        <div class="space-y-2 flex-1">
          <div class="h-4 w-1/3 bg-slate-200 dark:bg-slate-800 rounded"></div>
          <div class="h-3 w-1/2 bg-slate-100 dark:bg-slate-800/60 rounded"></div>
        </div>
        <div class="h-6 w-20 bg-slate-200 dark:bg-slate-800 rounded-full"></div>
        <div class="h-6 w-16 bg-slate-200 dark:bg-slate-800 rounded-md"></div>
      </div>
    </div>

    <!-- Variante 3: KPIs / Resumen Estadístico -->
    <div v-else-if="tipo === 'kpis'" class="grid grid-cols-2 sm:grid-cols-4 gap-3.5">
      <div v-for="i in 4" :key="i" class="m3-card p-4 border border-slate-200 dark:border-slate-800 space-y-2">
        <div class="h-3 w-1/2 bg-slate-200 dark:bg-slate-800 rounded"></div>
        <div class="h-7 w-16 bg-slate-300 dark:bg-slate-700 rounded"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  tipo: {
    type: String,
    default: 'cards', // 'cards' | 'table' | 'kpis'
  },
  cantidad: {
    type: Number,
    default: 4,
  },
});
</script>
