<template>
  <div class="fixed bottom-5 right-5 z-[9999] flex flex-col space-y-2 pointer-events-none max-w-sm w-full px-4 sm:px-0">
    <transition-group
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-for="toast in state.toasts"
        :key="toast.id"
        class="pointer-events-auto w-full p-4 rounded-2xl shadow-xl border flex items-start space-x-3 transition-all backdrop-blur-md"
        :class="getToastClasses(toast.tipo)"
      >
        <!-- Icono según tipo -->
        <div class="shrink-0 mt-0.5">
          <!-- Éxito -->
          <svg v-if="toast.tipo === 'success'" class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <!-- Error -->
          <svg v-else-if="toast.tipo === 'error'" class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <!-- Advertencia -->
          <svg v-else-if="toast.tipo === 'warning'" class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <!-- Info -->
          <svg v-else class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>

        <!-- Mensaje -->
        <div class="flex-1 min-w-0">
          <p v-if="toast.titulo" class="text-xs font-bold text-slate-900 dark:text-white leading-tight">
            {{ toast.titulo }}
          </p>
          <p class="text-xs text-slate-600 dark:text-slate-300 mt-0.5 leading-snug">
            {{ toast.mensaje }}
          </p>
        </div>

        <!-- Botón cerrar -->
        <button
          @click="actions.removeToast(toast.id)"
          class="shrink-0 p-1 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-lg transition cursor-pointer"
        >
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { state, actions } from '../stores/appState';

function getToastClasses(tipo) {
  switch (tipo) {
    case 'success':
      return 'bg-white/95 dark:bg-slate-900/95 border-emerald-500/40 shadow-emerald-500/10';
    case 'error':
      return 'bg-white/95 dark:bg-slate-900/95 border-red-500/40 shadow-red-500/10';
    case 'warning':
      return 'bg-white/95 dark:bg-slate-900/95 border-amber-500/40 shadow-amber-500/10';
    default:
      return 'bg-white/95 dark:bg-slate-900/95 border-blue-500/40 shadow-blue-500/10';
  }
}
</script>
