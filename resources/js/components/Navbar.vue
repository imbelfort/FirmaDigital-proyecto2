<template>
  <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        
        <!-- Logo y Título -->
        <div class="flex items-center space-x-3 cursor-pointer" @click="actions.setActiveTab('dashboard')">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 via-blue-600 to-red-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-500/20">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <div>
            <div class="flex items-center space-x-2">
              <span class="text-lg font-extrabold tracking-tight heading-font text-slate-900 dark:text-white">UAGRM</span>
              <span class="text-xs px-2 py-0.5 rounded-full font-semibold uppercase bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-400">Vicerrectorado</span>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Sistema Oficial de Firma Digital</p>
          </div>
        </div>

        <!-- Barra Central: Buscador Global (Trigger) -->
        <div class="flex-1 max-w-md mx-4 hidden md:block">
          <button
            @click="actions.openGlobalSearch()"
            class="w-full flex items-center justify-between px-3 py-2 text-sm text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700/60 rounded-xl border border-transparent dark:border-slate-700/50 transition shadow-inner"
          >
            <div class="flex items-center space-x-2">
              <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 1114 0z" />
              </svg>
              <span>Buscar trámites, personas, opciones...</span>
            </div>
            <kbd class="text-xs bg-white dark:bg-slate-900 px-1.5 py-0.5 rounded text-slate-500 border border-slate-200 dark:border-slate-700 shadow-sm">Ctrl K</kbd>
          </button>
        </div>

        <!-- Controles de Accesibilidad & Temas -->
        <div class="flex items-center space-x-2 sm:space-x-3">
          <!-- Búsqueda en móvil -->
          <button
            @click="actions.openGlobalSearch()"
            class="md:hidden p-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
            title="Buscar"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 1114 0z" />
            </svg>
          </button>

          <!-- Selector de Tamaño de Letra (A- / A / A+) -->
          <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-0.5 rounded-lg border border-slate-200 dark:border-slate-700">
            <button
              @click="actions.setFontSize('font-sm')"
              :class="state.fontSize === 'font-sm' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500'"
              class="px-2 py-1 text-xs font-bold rounded-md transition"
              title="Letra Pequeña"
            >A-</button>
            <button
              @click="actions.setFontSize('font-normal')"
              :class="state.fontSize === 'font-normal' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500'"
              class="px-2 py-1 text-xs font-bold rounded-md transition"
              title="Letra Normal"
            >A</button>
            <button
              @click="actions.setFontSize('font-lg')"
              :class="state.fontSize === 'font-lg' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500'"
              class="px-2 py-1 text-xs font-bold rounded-md transition"
              title="Letra Grande"
            >A+</button>
          </div>

          <!-- Selector de 3 Temas Institucionales -->
          <div class="relative group">
            <button
              class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition"
              title="Cambiar Color de Tema"
            >
              <span class="w-3.5 h-3.5 rounded-full inline-block" :class="themeBulletClass"></span>
            </button>
            <div class="absolute right-0 mt-2 w-48 py-2 bg-white dark:bg-slate-900 rounded-xl shadow-xl border border-slate-100 dark:border-slate-800 hidden group-hover:block z-50">
              <p class="px-3 py-1 text-xs font-semibold text-slate-400 uppercase tracking-wider">3 Temas Visuales</p>
              <button
                @click="actions.setTheme('uagrm')"
                class="w-full text-left px-3 py-2 text-sm flex items-center space-x-2 hover:bg-slate-50 dark:hover:bg-slate-800"
              >
                <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                <span class="text-slate-700 dark:text-slate-200">UAGRM Azul/Rojo</span>
              </button>
              <button
                @click="actions.setTheme('emerald')"
                class="w-full text-left px-3 py-2 text-sm flex items-center space-x-2 hover:bg-slate-50 dark:hover:bg-slate-800"
              >
                <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                <span class="text-slate-700 dark:text-slate-200">Esmeralda Académico</span>
              </button>
              <button
                @click="actions.setTheme('indigo')"
                class="w-full text-left px-3 py-2 text-sm flex items-center space-x-2 hover:bg-slate-50 dark:hover:bg-slate-800"
              >
                <span class="w-3 h-3 rounded-full bg-indigo-600"></span>
                <span class="text-slate-700 dark:text-slate-200">Índigo Corporativo</span>
              </button>
            </div>
          </div>

          <!-- Botón Modo Oscuro / Claro -->
          <button
            @click="actions.toggleDark()"
            class="p-2 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition"
            :title="state.isDark ? 'Cambiar a Modo Claro' : 'Cambiar a Modo Oscuro'"
          >
            <svg v-if="state.isDark" class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
            </svg>
            <svg v-else class="w-5 h-5 text-slate-700" fill="currentColor" viewBox="0 0 20 20">
              <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
            </svg>
          </button>

          <!-- Perfil o Login -->
          <div v-if="state.user" class="flex items-center space-x-2 pl-2 border-l border-slate-200 dark:border-slate-800">
            <div class="text-right hidden sm:block">
              <p class="text-xs font-bold text-slate-900 dark:text-white leading-none">{{ state.user.nombre }} {{ state.user.apellidos.split(' ')[0] }}</p>
              <span class="text-[10px] font-mono px-1.5 py-0.2 rounded font-semibold" :class="getRolColor(state.user.rol)">{{ state.user.rol }}</span>
            </div>
            <button
              @click="actions.logout()"
              class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg transition"
              title="Cerrar Sesión"
            >
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
            </button>
          </div>
          <button
            v-else
            @click="actions.setActiveTab('login')"
            class="px-3.5 py-1.5 text-xs sm:text-sm font-semibold rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow-sm transition"
          >
            Iniciar Sesión
          </button>

        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue';
import { state, actions } from '../stores/appState';

const themeBulletClass = computed(() => {
  switch (state.theme) {
    case 'emerald': return 'bg-emerald-500 ring-2 ring-emerald-300';
    case 'indigo': return 'bg-indigo-500 ring-2 ring-indigo-300';
    default: return 'bg-blue-600 ring-2 ring-red-400';
  }
});

function getRolColor(rol) {
  switch (rol) {
    case 'VICERRECTOR': return 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-300';
    case 'ENCARGADO': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300';
    case 'DOCENTE': return 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300';
    case 'ALUMNO': return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300';
    default: return 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300';
  }
}
</script>
