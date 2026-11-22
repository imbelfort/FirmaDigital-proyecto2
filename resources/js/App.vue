<template>
  <div class="min-h-screen flex flex-col bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-200">
    <!-- Navbar Superior -->
    <Navbar />

    <!-- Contenido Principal Dinámico según Rol y Estado -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
      
      <!-- Vista Pública: Portal de Seguimiento Estudiantil con PIN -->
      <PortalSeguimientoView v-if="state.activeTab === 'portal_seguimiento'" />

      <!-- Si no ha iniciado sesión, o solicita la vista de Login -->
      <LoginView v-else-if="!state.user || state.activeTab === 'login'" />

      <!-- Vistas según el Rol del Usuario Autenticado -->
      <template v-else>
        <!-- Menú de Navegación Rápida (Pestañas limpias por Rol; búsqueda centralizada únicamente en el Navbar) -->
        <div v-if="state.user.rol !== 'ALUMNO' && state.user.rol !== 'DOCENTE'" class="flex items-center space-x-2 overflow-x-auto pb-4 mb-6 border-b border-slate-200 dark:border-slate-800">
          <button
            @click="actions.setActiveTab('dashboard')"
            :class="state.activeTab === 'dashboard' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-2 shrink-0 border border-slate-200 dark:border-slate-800 cursor-pointer"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Mi Panel ({{ state.user.rol }})</span>
          </button>

          <button
            @click="actions.setActiveTab('reportes')"
            :class="state.activeTab === 'reportes' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'"
            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center space-x-2 shrink-0 border border-slate-200 dark:border-slate-800 cursor-pointer"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Reportes & SLA 72h</span>
          </button>
        </div>

        <!-- Vista de Reportes General -->
        <ReportesView v-if="state.activeTab === 'reportes'" />

        <!-- Vistas especializadas por Rol -->
        <template v-else>
          <!-- Rol Alumno o Docente (misma vista de autogestión de trámites según requerimiento) -->
          <AlumnoDashboard v-if="state.user.rol === 'ALUMNO' || state.user.rol === 'DOCENTE'" />

          <!-- Rol Vicerrector -->
          <VicerrectorDashboard v-else-if="state.user.rol === 'VICERRECTOR'" />

          <!-- Rol Administrador General -->
          <AdminDashboard v-else-if="state.user.rol === 'ADMINISTRATIVO'" />

          <!-- Rol Encargado Mesa de Entrada -->
          <EncargadoDashboard v-else-if="state.user.rol === 'ENCARGADO'" />
        </template>
      </template>

    </main>

    <!-- Modal de Búsqueda Global Integrado -->
    <GlobalSearchModal />

    <!-- Sistema Global Reusable de Notificaciones Toast -->
    <ToastNotification />

    <!-- Footer Institucional UAGRM con Contador de Visitas por Vista / Página -->
    <footer class="mt-auto border-t border-slate-200 dark:border-slate-800 py-6 text-center text-xs text-slate-500 dark:text-slate-400 bg-white/50 dark:bg-slate-900/50 backdrop-blur-xs">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-2">
        <p class="font-bold text-slate-700 dark:text-slate-300">Universidad Autónoma Gabriel René Moreno — Vicerrectorado</p>
        <p>Sistema de Firma Digital y Titulación Académica | Conforme a Ley N° 164</p>
        
        <!-- Contador de Accesos y Visitas por Página -->
        <div class="inline-flex items-center space-x-3 pt-2">
          <div class="flex items-center space-x-1.5 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[11px]">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-slate-600 dark:text-slate-400">Visitas de esta vista ({{ vistaNombreActual }}):</span>
            <span class="font-mono font-bold text-blue-600 dark:text-blue-400">{{ state.visitasPagina }}</span>
          </div>
          <div class="hidden sm:flex items-center space-x-1.5 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[11px]">
            <span class="text-slate-600 dark:text-slate-400">Total Accesos Web:</span>
            <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ state.visitasGlobales }}</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onMounted, computed, watch } from 'vue';
import { state, actions, applyThemeClasses } from './stores/appState';
import axios from 'axios';
import Navbar from './components/Navbar.vue';
import GlobalSearchModal from './components/GlobalSearchModal.vue';
import ToastNotification from './components/ToastNotification.vue';
import LoginView from './views/LoginView.vue';
import PortalSeguimientoView from './views/PortalSeguimientoView.vue';
import AlumnoDashboard from './views/AlumnoDashboard.vue';
import VicerrectorDashboard from './views/VicerrectorDashboard.vue';
import EncargadoDashboard from './views/EncargadoDashboard.vue';
import AdminDashboard from './views/AdminDashboard.vue';
import ReportesView from './views/ReportesView.vue';

const vistaNombreActual = computed(() => {
  if (state.activeTab === 'portal_seguimiento') return 'Portal Público de Seguimiento';
  if (!state.user || state.activeTab === 'login') return 'Login Institucional';
  if (state.activeTab === 'reportes') return 'Reportes y Métricas SLA';
  switch (state.user.rol) {
    case 'ALUMNO': return 'Dashboard Alumno';
    case 'VICERRECTOR': return 'Despacho Vicerrector';
    case 'ADMINISTRATIVO': return 'Panel Administrador General';
    case 'ENCARGADO': return 'Mesa de Entrada';
    case 'DOCENTE': return 'Portal Docente';
    default: return 'Portal General';
  }
});

async function registrarVisitaActual() {
  try {
    const res = await axios.post('/api/visitas/registrar', {
      pagina: vistaNombreActual.value,
    });
    if (res.data && res.data.success) {
      actions.setVisitas(res.data.visitas_pagina, res.data.visitas_globales);
    }
  } catch (e) {
    // Silencioso
  }
}

watch(vistaNombreActual, () => {
  registrarVisitaActual();
});

onMounted(() => {
  applyThemeClasses();
  actions.checkAutoDarkByTime();
  // Revisar horario periódicamente cada 60 segundos
  setInterval(() => {
    actions.checkAutoDarkByTime();
  }, 60000);
  registrarVisitaActual();
});
</script>
