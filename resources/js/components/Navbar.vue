<template>
  <header class="sticky top-0 z-40 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 transition-colors shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        
        <!-- Logo y Título Institucional M3 -->
        <div class="flex items-center space-x-3 cursor-pointer group" @click="actions.setActiveTab('dashboard')">
          <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-700 via-blue-600 to-red-600 flex items-center justify-center text-white font-bold shadow-md shadow-blue-500/20 group-hover:scale-105 transition">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
          </div>
          <div>
            <div class="flex items-center space-x-2">
              <span class="text-lg font-black tracking-tight heading-font text-slate-900 dark:text-white">UAGRM</span>
              <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase bg-red-100 dark:bg-red-950/80 text-red-700 dark:text-red-400">Vicerrectorado</span>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">Firma Digital & Titulación Académica</p>
          </div>
        </div>

        <!-- Botón Acceso Rápido al Portal de Seguimiento Público con PIN (Visible siempre) -->
        <button
          @click="actions.setActiveTab('portal_seguimiento')"
          :class="state.activeTab === 'portal_seguimiento' ? 'bg-blue-600 text-white font-bold shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
          class="hidden sm:flex items-center space-x-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border border-slate-200 dark:border-slate-700 transition cursor-pointer"
          title="Consultar trámite externamente con PIN"
        >
          <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
          <span>Seguimiento con PIN</span>
        </button>

        <!-- Buscador Global Trigger (Solo visible cuando hay sesión iniciada) -->
        <div v-if="state.user" class="flex-1 max-w-md mx-4 block">
          <button
            @click="actions.openGlobalSearch()"
            class="w-full flex items-center justify-between px-3.5 py-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700/60 rounded-full border border-slate-200 dark:border-slate-700 transition cursor-pointer shadow-xs"
          >
            <div class="flex items-center space-x-2">
              <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 1114 0z" />
              </svg>
              <span class="truncate">Buscar trámites, personas, opciones...</span>
            </div>
            <kbd class="hidden sm:inline-block text-[10px] font-mono bg-white dark:bg-slate-900 px-2 py-0.5 rounded-full text-slate-500 border border-slate-200 dark:border-slate-700 shadow-2xs">Ctrl K</kbd>
          </button>
        </div>
        <div v-else class="flex-1"></div>

        <!-- Barra de Accesibilidad y Personalización Material 3 (Persistente) -->
        <div class="flex items-center space-x-1.5 sm:space-x-2.5">
          
          <!-- Búsqueda en móvil (Solo cuando hay sesión) -->
          <button
            v-if="state.user"
            @click="actions.openGlobalSearch()"
            class="md:hidden p-2 rounded-full text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer"
            title="Buscar"
          >
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 1114 0z" />
            </svg>
          </button>

          <!-- Control de Tamaño de Letra (A- / A / A+) M3 Segmented Button -->
          <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-0.5 rounded-full border border-slate-200 dark:border-slate-700">
            <button
              @click="actions.setFontSize('font-sm')"
              :class="state.fontSize === 'font-sm' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white'"
              class="px-2 py-1 text-xs font-black rounded-full transition cursor-pointer"
              title="Letra Pequeña (14px)"
            >A-</button>
            <button
              @click="actions.setFontSize('font-normal')"
              :class="state.fontSize === 'font-normal' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white'"
              class="px-2 py-1 text-xs font-black rounded-full transition cursor-pointer"
              title="Letra Normal (16px)"
            >A</button>
            <button
              @click="actions.setFontSize('font-lg')"
              :class="state.fontSize === 'font-lg' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-xs' : 'text-slate-500 hover:text-slate-800 dark:hover:text-white'"
              class="px-2 py-1 text-xs font-black rounded-full transition cursor-pointer"
              title="Letra Grande (18.5px)"
            >A+</button>
          </div>

          <!-- Selector de 3 Temas Material Design 3 -->
          <div class="relative">
            <button
              @click="themeMenuOpen = !themeMenuOpen"
              class="p-2 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer flex items-center space-x-1"
              title="Cambiar Color de Tema Visual"
            >
              <span class="w-4 h-4 rounded-full inline-block" :class="themeBulletClass"></span>
              <svg class="w-3 h-3 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div
              v-if="themeMenuOpen"
              class="absolute right-0 mt-2 w-64 py-2 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 z-50 animate-fade-in"
            >
              <p class="px-3.5 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Temas Etarios & Dinámicos</p>
              
              <!-- Tema Niños -->
              <button
                @click="seleccionarTema('ninos')"
                class="w-full text-left px-3.5 py-2 text-xs flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                :class="state.theme === 'ninos' ? 'bg-orange-50/80 dark:bg-orange-950/40 font-bold' : ''"
              >
                <div class="flex items-center space-x-2.5">
                  <span class="w-4 h-4 rounded-full bg-orange-500 shadow-xs"></span>
                  <span class="text-slate-700 dark:text-slate-200">🎈 Niños (Lúdico / Cálido)</span>
                </div>
                <span v-if="state.theme === 'ninos'" class="text-orange-600 font-bold text-xs">✓</span>
              </button>

              <!-- Tema Jóvenes -->
              <button
                @click="seleccionarTema('jovenes')"
                class="w-full text-left px-3.5 py-2 text-xs flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                :class="state.theme === 'jovenes' ? 'bg-sky-50/80 dark:bg-sky-950/40 font-bold' : ''"
              >
                <div class="flex items-center space-x-2.5">
                  <span class="w-4 h-4 rounded-full bg-sky-500 shadow-xs"></span>
                  <span class="text-slate-700 dark:text-slate-200">⚡ Jóvenes (Cian Tecnológico)</span>
                </div>
                <span v-if="state.theme === 'jovenes'" class="text-sky-600 font-bold text-xs">✓</span>
              </button>

              <!-- Tema Adultos -->
              <button
                @click="seleccionarTema('adultos')"
                class="w-full text-left px-3.5 py-2 text-xs flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                :class="state.theme === 'adultos' ? 'bg-slate-100 dark:bg-slate-800 font-bold' : ''"
              >
                <div class="flex items-center space-x-2.5">
                  <span class="w-4 h-4 rounded-full bg-slate-700 shadow-xs"></span>
                  <span class="text-slate-700 dark:text-slate-200">🏛️ Adultos (Formal / Sobrio)</span>
                </div>
                <span v-if="state.theme === 'adultos'" class="text-slate-800 dark:text-slate-200 font-bold text-xs">✓</span>
              </button>

              <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
              <p class="px-3.5 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Paletas Universitarias M3</p>

              <button
                @click="seleccionarTema('uagrm')"
                class="w-full text-left px-3.5 py-2 text-xs flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                :class="state.theme === 'uagrm' ? 'bg-blue-50/70 dark:bg-blue-950/40 font-bold' : ''"
              >
                <div class="flex items-center space-x-2.5">
                  <span class="w-4 h-4 rounded-full bg-blue-600 shadow-xs"></span>
                  <span class="text-slate-700 dark:text-slate-200">UAGRM Azul Institucional</span>
                </div>
                <span v-if="state.theme === 'uagrm'" class="text-blue-600 text-xs">✓</span>
              </button>
              <button
                @click="seleccionarTema('emerald')"
                class="w-full text-left px-3.5 py-2 text-xs flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                :class="state.theme === 'emerald' ? 'bg-emerald-50/70 dark:bg-emerald-950/40 font-bold' : ''"
              >
                <div class="flex items-center space-x-2.5">
                  <span class="w-4 h-4 rounded-full bg-emerald-600 shadow-xs"></span>
                  <span class="text-slate-700 dark:text-slate-200">Esmeralda Académico</span>
                </div>
                <span v-if="state.theme === 'emerald'" class="text-emerald-600 text-xs">✓</span>
              </button>
              <button
                @click="seleccionarTema('indigo')"
                class="w-full text-left px-3.5 py-2 text-xs flex items-center justify-between hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer"
                :class="state.theme === 'indigo' ? 'bg-indigo-50/70 dark:bg-indigo-950/40 font-bold' : ''"
              >
                <div class="flex items-center space-x-2.5">
                  <span class="w-4 h-4 rounded-full bg-indigo-600 shadow-xs"></span>
                  <span class="text-slate-700 dark:text-slate-200">Índigo Moderno</span>
                </div>
                <span v-if="state.theme === 'indigo'" class="text-indigo-600 text-xs">✓</span>
              </button>
            </div>
          </div>

          <!-- Botón de Accesibilidad de Alto Contraste -->
          <button
            @click="actions.toggleContrast()"
            :class="state.highContrast ? 'bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-300 ring-2 ring-amber-400' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
            class="p-2 rounded-full hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer"
            title="Accesibilidad: Alternar Alto Contraste"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </button>

          <!-- Selector de Modo Día / Noche (Manual + Automático por Horario) -->
          <div class="flex items-center bg-slate-100 dark:bg-slate-800 p-0.5 rounded-full border border-slate-200 dark:border-slate-700">
            <!-- Botón Manual Sol / Luna -->
            <button
              @click="actions.toggleDark()"
              class="p-1.5 rounded-full hover:bg-slate-200 dark:hover:bg-slate-700 transition cursor-pointer"
              :title="state.isDark ? 'Cambiar a Modo Claro' : 'Cambiar a Modo Oscuro'"
            >
              <svg v-if="state.isDark" class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
              </svg>
              <svg v-else class="w-4 h-4 text-slate-700" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
              </svg>
            </button>
            <!-- Interruptor Modo Automático por Horario (19:00 a 06:00) -->
            <button
              @click="actions.toggleAutoDark()"
              :class="state.autoDarkSchedule ? 'bg-blue-600 text-white font-extrabold shadow-2xs' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200'"
              class="px-2 py-0.5 text-[10px] rounded-full transition cursor-pointer"
              title="Modo Automático: Oscuro de noche (19:00 a 06:00), Claro de día"
            >
              AUTO
            </button>
          </div>

          <!-- Perfil o Login -->
          <div v-if="state.user" class="flex items-center space-x-2 pl-2 border-l border-slate-200 dark:border-slate-800">
            <div class="text-right hidden sm:block">
              <p class="text-xs font-black text-slate-900 dark:text-white leading-none">{{ state.user.nombre }} {{ state.user.apellidos.split(' ')[0] }}</p>
              <span class="text-[9px] font-mono px-1.5 py-0.2 rounded font-bold" :class="getRolColor(state.user.rol)">{{ state.user.rol }}</span>
            </div>
            <button
              @click="actions.logout()"
              class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-full transition cursor-pointer"
              title="Cerrar Sesión"
            >
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
            </button>
          </div>
          <button
            v-else
            @click="actions.setActiveTab('login')"
            class="px-4 py-2 text-xs font-bold rounded-full bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition cursor-pointer"
          >
            Iniciar Sesión
          </button>

        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue';
import { state, actions } from '../stores/appState';

const themeMenuOpen = ref(false);

function seleccionarTema(tema) {
  actions.setTheme(tema);
  themeMenuOpen.value = false;
}

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
