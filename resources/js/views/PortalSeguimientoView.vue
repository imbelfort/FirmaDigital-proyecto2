<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <!-- Header del Portal de Seguimiento Estudiantil -->
    <div class="m3-hero-gradient rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden transition-all duration-300">
      <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center space-x-2.5">
            <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white shadow-xs">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
              </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold heading-font tracking-tight">
              Portal de Seguimiento de Trámites
            </h1>
          </div>
          <p class="text-white/85 text-xs sm:text-sm mt-1.5 font-medium">
            Consulta pública y transparente en tiempo real con tu PIN secreto o Código de Trámite.
          </p>
        </div>
        <div class="flex items-center space-x-2 self-start sm:self-center">
          <span class="text-[11px] font-bold tracking-wider uppercase px-3.5 py-1.5 rounded-full bg-white/20 border border-white/25 backdrop-blur-md shadow-xs flex items-center space-x-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <span>Acceso Seguro con PIN</span>
          </span>
        </div>
      </div>
      <div class="absolute right-0 bottom-0 opacity-10 translate-x-12 translate-y-12 pointer-events-none">
        <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a10.97 10.97 0 00-.25 2.449c0 4.148 2.5 7.9 6 9.4 3.5-1.5 6-5.252 6-9.4 0-.847-.09-1.67-.25-2.45l2.644-1.13a1 1 0 000-1.84l-7-3z"/></svg>
      </div>
    </div>

    <!-- Caja de Consulta de PIN -->
    <div class="m3-card p-6 sm:p-8 border border-slate-200 dark:border-slate-800 space-y-4 shadow-sm">
      <div class="max-w-xl mx-auto text-center space-y-2">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Ingrese el PIN Secreto de su Trámite</h2>
        <p class="text-xs text-slate-500">
          El PIN fue generado al momento de iniciar su solicitud (Ejemplo: <span class="font-mono font-bold text-blue-600 dark:text-blue-400 cursor-pointer" @click="pinInput = 'PIN-9482'; consultar()">PIN-9482</span>).
        </p>

        <form @submit.prevent="consultar" class="flex flex-col sm:flex-row items-center gap-2 pt-2">
          <div class="relative flex-1 w-full">
            <input
              v-model="pinInput"
              type="text"
              required
              placeholder="Ej: PIN-9482"
              class="w-full pl-10 pr-4 py-3 rounded-2xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono uppercase text-sm font-bold focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
          </div>
          <button
            type="submit"
            :disabled="loading || !pinInput"
            class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm cursor-pointer flex items-center justify-center space-x-1.5"
          >
            <svg v-if="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            <span>{{ loading ? 'Buscando...' : 'Consultar Trámite' }}</span>
          </button>
        </form>
      </div>
    </div>

    <!-- Mensaje de Error si el PIN no existe -->
    <div v-if="errorMensaje" class="m3-card p-4 border border-red-200 dark:border-red-900 bg-red-50/60 dark:bg-red-950/40 text-red-700 dark:text-red-300 text-xs text-center font-bold">
      {{ errorMensaje }}
    </div>

    <!-- Resultados Detallados de la Consulta -->
    <div v-if="resultadoTramite" class="space-y-6">
      
      <!-- Ficha Principal del Trámite -->
      <div class="m3-card p-6 sm:p-8 border border-slate-200 dark:border-slate-800 space-y-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 gap-3">
          <div>
            <span class="font-mono text-xs font-black text-blue-600 dark:text-blue-400 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/40">
              {{ resultadoTramite.codigo_tramite }}
            </span>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white mt-2">
              {{ resultadoTramite.asunto }}
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
              Solicitante: <strong class="text-slate-700 dark:text-slate-300">{{ resultadoTramite.solicitante ? resultadoTramite.solicitante.nombre + ' ' + resultadoTramite.solicitante.apellidos : 'N/A' }}</strong>
              (Reg: {{ resultadoTramite.solicitante?.registro }})
            </p>
          </div>

          <div class="text-left sm:text-right">
            <span class="m3-badge uppercase inline-block text-xs font-bold px-3 py-1 rounded-full" :class="getEstadoBadge(resultadoTramite.estado)">
              {{ formatEstadoLegible(resultadoTramite.estado) }}
            </span>
            <p class="text-[11px] text-slate-400 mt-1">
              Iniciado: {{ formatFecha(resultadoTramite.fecha_solicitud) }}
            </p>
          </div>
        </div>

        <!-- Barra de Progreso en Tiempo Real -->
        <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-3">
          <div class="flex items-center justify-between text-xs">
            <span class="font-bold text-slate-700 dark:text-slate-300">Progreso del Trámite en Vicerrectorado:</span>
            <span class="font-mono font-bold text-blue-600 dark:text-blue-400">{{ getPorcentaje(resultadoTramite.estado) }}% Completado</span>
          </div>

          <div class="w-full bg-slate-200 dark:bg-slate-700 h-2.5 rounded-full overflow-hidden">
            <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" :style="`width: ${getPorcentaje(resultadoTramite.estado)}%`"></div>
          </div>

          <div class="grid grid-cols-5 text-[11px] text-center text-slate-500">
            <span :class="resultadoTramite.estado === 'INICIADO' ? 'font-bold text-blue-600' : ''">Recepción</span>
            <span :class="resultadoTramite.estado === 'REQUISITOS_VALIDADOS' ? 'font-bold text-blue-600' : ''">Cotejo</span>
            <span :class="resultadoTramite.estado === 'EXPEDIENTE_COMPILADO' ? 'font-bold text-blue-600' : ''">Legajo</span>
            <span :class="resultadoTramite.estado === 'EN_FIRMA' ? 'font-bold text-purple-600' : ''">Firma VR</span>
            <span :class="['FIRMADO', 'DESPACHADO'].includes(resultadoTramite.estado) ? 'font-bold text-emerald-600' : ''">Finalizado</span>
          </div>
        </div>

        <!-- Etapas Procesales Históricas -->
        <div class="space-y-3">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Historial Procesal y Fechas</h3>
          <div class="relative pl-6 border-l-2 border-blue-500 space-y-4 text-xs">
            <div v-for="f in resultadoTramite.flujos" :key="f.id" class="relative">
              <span class="absolute -left-[31px] top-0.5 w-3.5 h-3.5 rounded-full bg-blue-600 ring-4 ring-white dark:ring-slate-900"></span>
              <p class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ f.etapa_actual }}</p>
              <p class="text-[11px] text-slate-500">Fecha: {{ formatFecha(f.fecha_inicio_etapa) }}</p>
              <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold" :class="f.estado_etapa === 'COMPLETADO' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800'">
                {{ formatEstadoLegible(f.estado_etapa) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Certificado Oficial y QR Emitido (Si ya fue firmado por el Vicerrector) -->
        <div v-if="resultadoTramite.certificado" class="p-6 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border-2 border-emerald-500/40 text-center space-y-4">
          <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
          </div>
          <div>
            <h4 class="text-base font-extrabold text-emerald-900 dark:text-emerald-200 uppercase tracking-wide">
              Documento Académico Firmado Digitalmente
            </h4>
            <p class="text-xs text-slate-600 dark:text-slate-300 mt-1">{{ resultadoTramite.certificado.titulo_documento }}</p>
          </div>

          <!-- Código QR Generado -->
          <div class="p-3 bg-white rounded-2xl inline-block shadow-inner border border-slate-200">
            <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=${encodeURIComponent(resultadoTramite.certificado.qr_data)}`" alt="QR Verificación" class="w-36 h-36 mx-auto" />
          </div>

          <div class="text-xs space-y-1 text-slate-500 font-mono">
            <p>UUID: <strong class="text-slate-800 dark:text-slate-200">{{ resultadoTramite.certificado.codigo_verificacion }}</strong></p>
            <p class="text-[11px] text-emerald-600 font-bold">Validez Jurídica Conforme a Ley N° 164 (ADSIB - UAGRM)</p>
          </div>
        </div>

      </div>

    </div>
  </div>
</template>

<script setup>
import { formatFecha } from '../fechas';
import { ref } from 'vue';
import axios from 'axios';
import { actions } from '../stores/appState';

const pinInput = ref('');
const loading = ref(false);
const errorMensaje = ref('');
const resultadoTramite = ref(null);

async function consultar() {
  if (!pinInput.value || loading.value) return;
  loading.value = true;
  errorMensaje.value = '';
  resultadoTramite.value = null;

  try {
    const res = await axios.get(`/api/seguimiento/portal/${encodeURIComponent(pinInput.value.trim())}`);
    if (res.data && res.data.success) {
      resultadoTramite.value = res.data.data.tramite;
      actions.toast('Trámite encontrado con éxito.', 'success', 'Portal de Seguimiento');
    } else {
      errorMensaje.value = 'No se encontró ningún trámite con el PIN proporcionado.';
      actions.toast(errorMensaje.value, 'warning', 'PIN Inválido');
    }
  } catch (e) {
    errorMensaje.value = e.response?.data?.message || 'Error al consultar el PIN. Verifique e intente nuevamente.';
    actions.toast(errorMensaje.value, 'error', 'Error de Consulta');
  } finally {
    loading.value = false;
  }
}

function getPorcentaje(estado) {
  switch (estado) {
    case 'INICIADO': return 25;
    case 'REQUISITOS_VALIDADOS': return 45;
    case 'EXPEDIENTE_COMPILADO': return 65;
    case 'EN_FIRMA': return 85;
    case 'FIRMADO':
    case 'DESPACHADO': return 100;
    default: return 30;
  }
}

function formatEstadoLegible(texto) {
  if (!texto) return '';
  return texto.split('_').map(p => p.charAt(0).toUpperCase() + p.slice(1).toLowerCase()).join(' ');
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
</script>
