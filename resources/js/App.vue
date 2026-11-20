<template>
  <div class="min-h-screen flex flex-col bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-200">
    <!-- Navbar Superior -->
    <Navbar />

    <!-- Contenido Principal Dinámico según Rol y Estado -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
      
      <!-- Si no ha iniciado sesión, o solicita la vista de Login -->
      <LoginView v-if="!state.user || state.activeTab === 'login'" />

      <!-- Vistas según el Rol del Usuario Autenticado -->
      <template v-else>
        <!-- Menú de Navegación Rápida -->
        <div class="flex items-center space-x-2 overflow-x-auto pb-4 mb-6 border-b border-slate-200 dark:border-slate-800">
          <button
            @click="actions.setActiveTab('dashboard')"
            :class="state.activeTab === 'dashboard' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-2 shrink-0 border border-slate-200 dark:border-slate-800"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Mi Panel ({{ state.user.rol }})</span>
          </button>

          <button
            @click="actions.setActiveTab('reportes')"
            :class="state.activeTab === 'reportes' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-2 shrink-0 border border-slate-200 dark:border-slate-800"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Reportes & SLA 72h</span>
          </button>

          <button
            @click="actions.openGlobalSearch()"
            class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition flex items-center space-x-2 shrink-0 border border-transparent dark:border-slate-700"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 1114 0z"/></svg>
            <span>Buscador General (Ctrl+K)</span>
          </button>
        </div>

        <!-- Vista de Reportes General -->
        <ReportesView v-if="state.activeTab === 'reportes'" />

        <!-- Vistas especializadas por Rol -->
        <template v-else>
          <!-- Rol Alumno -->
          <AlumnoDashboard v-if="state.user.rol === 'ALUMNO'" />

          <!-- Rol Vicerrector -->
          <VicerrectorDashboard v-else-if="state.user.rol === 'VICERRECTOR'" />

          <!-- Rol Encargado / Administrativo -->
          <EncargadoDashboard v-else-if="state.user.rol === 'ENCARGADO' || state.user.rol === 'ADMINISTRATIVO'" />

          <!-- Rol Docente -->
          <DocenteDashboard v-else-if="state.user.rol === 'DOCENTE'" />
        </template>
      </template>

    </main>

    <!-- Modal de Búsqueda Global Integrado -->
    <GlobalSearchModal />

    <!-- Footer Institucional UAGRM -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
      <p class="font-bold text-slate-700 dark:text-slate-300">Universidad Autónoma Gabriel René Moreno — Vicerrectorado</p>
      <p class="mt-1">Sistema de Firma Digital y Titulación Académica | Conforme a Ley N° 164</p>
    </footer>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { state, actions, applyThemeClasses } from './stores/appState';
import Navbar from './components/Navbar.vue';
import GlobalSearchModal from './components/GlobalSearchModal.vue';
import LoginView from './views/LoginView.vue';
import AlumnoDashboard from './views/AlumnoDashboard.vue';
import VicerrectorDashboard from './views/VicerrectorDashboard.vue';
import EncargadoDashboard from './views/EncargadoDashboard.vue';
import DocenteDashboard from './views/DocenteDashboard.vue';
import ReportesView from './views/ReportesView.vue';

onMounted(() => {
  applyThemeClasses();
});
</script>
