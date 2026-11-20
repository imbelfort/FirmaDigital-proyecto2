<template>
  <div class="space-y-6">
    <!-- Encabezado de Bienvenida del Alumno -->
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
      <div class="relative z-10">
        <span class="text-xs font-semibold tracking-wider uppercase px-3 py-1 rounded-full bg-white/20 backdrop-blur-xs">
          Portal del Estudiante
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold mt-2 heading-font">
          Hola, {{ state.user.nombre }} {{ state.user.apellidos }}
        </h1>
        <p class="text-emerald-100 text-sm mt-1 max-w-2xl">
          Registro Universitario: <span class="font-mono font-bold">{{ state.user.registro }}</span> | Cédula: <span class="font-mono">{{ state.user.ci }}</span> | Carrera: {{ state.user.alumno ? state.user.alumno.carrera : state.user.unidad_academica }}
        </p>
        <div class="mt-4 flex flex-wrap gap-3">
          <button
            @click="modalNuevoTramite = true"
            class="px-4 py-2 rounded-xl bg-white text-emerald-800 font-bold text-sm shadow hover:bg-emerald-50 transition flex items-center space-x-2"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Iniciar Nuevo Trámite</span>
          </button>
          <button
            @click="activeSubTab = 'pin'"
            class="px-4 py-2 rounded-xl bg-emerald-800/60 hover:bg-emerald-800 text-white font-medium text-sm border border-emerald-400/30 transition flex items-center space-x-2"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            <span>Consultar por PIN</span>
          </button>
        </div>
      </div>
      <div class="absolute right-0 bottom-0 opacity-10 translate-x-8 translate-y-8">
        <svg class="w-72 h-72 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a10.97 10.97 0 00-.25 2.449c0 4.148 2.5 7.9 6 9.4 3.5-1.5 6-5.252 6-9.4 0-.847-.09-1.67-.25-2.45l2.644-1.13a1 1 0 000-1.84l-7-3z"/></svg>
      </div>
    </div>

    <!-- Pestañas de Vista de Alumno -->
    <div class="flex space-x-2 border-b border-slate-200 dark:border-slate-800 pb-2">
      <button
        @click="activeSubTab = 'mis_tramites'"
        :class="activeSubTab === 'mis_tramites' ? 'border-b-2 border-emerald-600 text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500'"
        class="pb-2 px-3 text-sm transition"
      >
        Mis Trámites Académicos ({{ misTramites.length }})
      </button>
      <button
        @click="activeSubTab = 'catalogo'"
        :class="activeSubTab === 'catalogo' ? 'border-b-2 border-emerald-600 text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500'"
        class="pb-2 px-3 text-sm transition"
      >
        Catálogo y Requisitos Oficiales
      </button>
      <button
        @click="activeSubTab = 'pin'"
        :class="activeSubTab === 'pin' ? 'border-b-2 border-emerald-600 text-emerald-600 dark:text-emerald-400 font-bold' : 'text-slate-500'"
        class="pb-2 px-3 text-sm transition"
      >
        Seguimiento con PIN Secreto
      </button>
    </div>

    <!-- SubTab 1: Mis Trámites -->
    <div v-if="activeSubTab === 'mis_tramites'" class="space-y-4">
      <div v-if="loading" class="p-12 text-center text-slate-500">Cargando trámites...</div>
      <div v-else-if="misTramites.length === 0" class="bg-white dark:bg-slate-900 rounded-2xl p-10 text-center border border-slate-100 dark:border-slate-800">
        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <h3 class="mt-2 text-sm font-bold text-slate-800 dark:text-white">Aún no tiene trámites iniciados</h3>
        <p class="text-xs text-slate-500 mt-1">Presione el botón para iniciar su solicitud de titulación o certificado.</p>
        <button
          @click="modalNuevoTramite = true"
          class="mt-4 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition"
        >
          Iniciar Solicitud
        </button>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="t in misTramites"
          :key="t.id"
          class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-md transition space-y-3"
        >
          <div class="flex items-center justify-between">
            <span class="font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded bg-emerald-50 dark:bg-emerald-950/40">
              {{ t.codigo_tramite }}
            </span>
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full" :class="getEstadoBadge(t.estado)">
              {{ t.estado }}
            </span>
          </div>

          <h3 class="font-bold text-slate-900 dark:text-white text-base">{{ t.asunto }}</h3>
          <p class="text-xs text-slate-500">{{ t.tipo_tramite ? t.tipo_tramite.nombre : 'Trámite Académico' }}</p>

          <!-- PIN de seguimiento y Monitoreo SLA -->
          <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl flex items-center justify-between text-xs">
            <div>
              <span class="text-slate-400 block text-[10px] uppercase font-semibold">PIN de Consulta</span>
              <span class="font-mono font-bold text-slate-800 dark:text-slate-200 text-sm">{{ t.portal_seguimiento ? t.portal_seguimiento.pin_seguimiento : 'N/A' }}</span>
            </div>
            <div class="text-right">
              <span class="text-slate-400 block text-[10px] uppercase font-semibold">Límite SLA 72h</span>
              <span class="font-semibold text-blue-600 dark:text-blue-400">{{ t.monitoreo_sla ? t.monitoreo_sla.estado_sla : 'EN_TIEMPO' }}</span>
            </div>
          </div>

          <!-- Documentos y Estado de Requisitos -->
          <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center justify-between text-xs mb-2">
              <span class="font-semibold text-slate-700 dark:text-slate-300">Expediente Digital ({{ t.documentos ? t.documentos.length : 0 }} recaudos)</span>
              <button @click="openSubirDoc(t)" class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline">+ Adjuntar</button>
            </div>
            <div class="space-y-1.5 max-h-32 overflow-y-auto">
              <div
                v-for="doc in t.documentos"
                :key="doc.id"
                class="flex items-center justify-between p-2 rounded-lg bg-slate-50 dark:bg-slate-800 text-xs"
              >
                <div class="truncate max-w-[200px]">
                  <span class="font-medium text-slate-700 dark:text-slate-200 block truncate">{{ doc.nombre_documento }}</span>
                  <span class="font-mono text-[10px] text-slate-400 truncate block">Hash: {{ doc.hash_documento ? doc.hash_documento.substring(0, 16) + '...' : 'Generado' }}</span>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="getDocBadge(doc.estado_validacion)">
                  {{ doc.estado_validacion }}
                </span>
              </div>
            </div>
          </div>

          <!-- Si el trámite ya está FIRMADO / DESPACHADO mostrar Certificado Oficial con QR -->
          <div v-if="t.certificado" class="p-3 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-950/40 dark:to-teal-950/40 rounded-xl border border-emerald-200 dark:border-emerald-800/60">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300">Certificado Oficial Emitido</p>
                <p class="text-[10px] font-mono text-slate-500">UUID: {{ t.certificado.codigo_verificacion }}</p>
              </div>
              <button
                @click="verCertificadoModal(t.certificado)"
                class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition"
              >
                Ver Certificado QR
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- SubTab 2: Catálogo Oficial de Trámites -->
    <div v-if="activeSubTab === 'catalogo'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div
        v-for="tipo in tiposTramite"
        :key="tipo.id"
        class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 space-y-3"
      >
        <div class="flex items-center justify-between">
          <span class="font-mono text-xs px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 font-bold text-blue-600 dark:text-blue-400">{{ tipo.codigo }}</span>
          <span class="text-sm font-extrabold text-emerald-600 dark:text-emerald-400">Bs. {{ tipo.costo_bs }}</span>
        </div>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ tipo.nombre }}</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400">{{ tipo.descripcion }}</p>
        
        <div class="p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl space-y-2 text-xs">
          <p class="font-semibold text-slate-700 dark:text-slate-300">Requisitos indispensables:</p>
          <ul class="list-disc list-inside space-y-1 text-slate-600 dark:text-slate-400">
            <li v-for="r in tipo.requisitos" :key="r.id">
              {{ r.nombre }} <span v-if="r.es_obligatorio" class="text-red-500 font-bold">*</span>
            </li>
          </ul>
        </div>
        <button
          @click="iniciarDesdeTipo(tipo)"
          class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition"
        >
          Solicitar este Trámite
        </button>
      </div>
    </div>

    <!-- SubTab 3: PIN Secreto -->
    <div v-if="activeSubTab === 'pin'" class="max-w-xl mx-auto bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 space-y-4">
      <h2 class="text-xl font-bold text-slate-900 dark:text-white">Portal de Consulta por PIN</h2>
      <p class="text-xs text-slate-500">Ingrese el PIN secreto asignado para conocer en qué etapa procesal se encuentra su legajo.</p>
      
      <div class="flex space-x-2">
        <input
          v-model="inputPin"
          type="text"
          class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white font-mono text-sm uppercase"
          placeholder="Ej: PIN-9482"
        />
        <button
          @click="consultarPorPin"
          class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition"
        >
          Consultar
        </button>
      </div>

      <div v-if="pinResultado" class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 space-y-3 text-xs">
        <div class="flex items-center justify-between">
          <span class="font-bold text-emerald-600 dark:text-emerald-400 font-mono text-sm">{{ pinResultado.tramite.codigo_tramite }}</span>
          <span class="px-2 py-0.5 rounded-full font-bold" :class="getEstadoBadge(pinResultado.tramite.estado)">{{ pinResultado.tramite.estado }}</span>
        </div>
        <p class="font-semibold text-slate-800 dark:text-white text-sm">{{ pinResultado.tramite.asunto }}</p>
        <p class="text-slate-500">Solicitante: {{ pinResultado.tramite.solicitante.nombre }} {{ pinResultado.tramite.solicitante.apellidos }}</p>
        
        <div class="pt-2 border-t border-slate-200 dark:border-slate-700 space-y-1">
          <p class="font-bold text-slate-700 dark:text-slate-300">Etapas Procesales:</p>
          <div v-for="f in pinResultado.tramite.flujos" :key="f.id" class="flex justify-between text-[11px]">
            <span>{{ f.etapa_actual }}</span>
            <span class="font-semibold text-emerald-600">{{ f.estado_etapa }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Nuevo Trámite -->
    <div v-if="modalNuevoTramite" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-lg w-full space-y-4 shadow-2xl border border-slate-100 dark:border-slate-800">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Iniciar Solicitud de Trámite Académico</h3>
        
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipo de Trámite</label>
          <select
            v-model="formNuevo.id_tipo_tramite"
            class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm"
          >
            <option v-for="t in tiposTramite" :key="t.id" :value="t.id">{{ t.nombre }} (Bs. {{ t.costo_bs }})</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Asunto / Descripción de la solicitud</label>
          <input
            v-model="formNuevo.asunto"
            type="text"
            class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm"
            placeholder="Ej. Solicitud de Diploma Académico de Grado"
          />
        </div>

        <div class="flex justify-end space-x-2 pt-4">
          <button @click="modalNuevoTramite = false" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-700">Cancelar</button>
          <button @click="crearTramite" class="px-5 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow">Crear Trámite</button>
        </div>
      </div>
    </div>

    <!-- Modal Adjuntar Documento -->
    <div v-if="modalAdjuntar" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-md w-full space-y-4 shadow-2xl border border-slate-100 dark:border-slate-800">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Adjuntar Recaudo al Expediente</h3>
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nombre del Documento</label>
          <input
            v-model="formDoc.nombre_documento"
            type="text"
            class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm"
            placeholder="Ej. Fotocopia Legalizada CI"
          />
        </div>
        <p class="text-[11px] text-slate-500">Se computará automáticamente el Hash criptográfico SHA-256 para asegurar la integridad jurídica del legajo.</p>

        <div class="flex justify-end space-x-2 pt-4">
          <button @click="modalAdjuntar = false" class="px-4 py-2 text-xs font-semibold text-slate-500">Cancelar</button>
          <button @click="guardarDocumento" class="px-5 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow">Subir Recaudo</button>
        </div>
      </div>
    </div>

    <!-- Modal Certificado Oficial con QR -->
    <div v-if="certModalData" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 max-w-lg w-full space-y-5 shadow-2xl border-4 border-emerald-500/30 text-center">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center">
          <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white heading-font uppercase tracking-wide">
          Certificación Oficial UAGRM
        </h2>
        <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ certModalData.titulo_documento }}</p>
        
        <!-- QR Visual SVG Representativo -->
        <div class="p-4 bg-white rounded-2xl inline-block shadow-inner border border-slate-200">
          <img :src="`https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(certModalData.qr_data)}`" alt="QR Verificación" class="w-44 h-44 mx-auto" />
        </div>

        <div class="text-xs space-y-1 text-slate-500">
          <p class="font-mono font-bold text-slate-800 dark:text-slate-200 text-xs">UUID: {{ certModalData.codigo_verificacion }}</p>
          <p>Firma Digital Oficial con Token Criptográfico ADSIB / UAGRM</p>
          <p class="text-[10px] text-emerald-600 font-bold">Estado: DOCUMENTO VIGENTE Y VALIDADOR HABILITADO</p>
        </div>

        <button @click="certModalData = null" class="w-full py-2.5 bg-slate-800 text-white font-bold rounded-xl text-xs hover:bg-slate-700 transition">
          Cerrar Visualizador
        </button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { state } from '../stores/appState';
import axios from 'axios';

const activeSubTab = ref('mis_tramites');
const misTramites = ref([]);
const tiposTramite = ref([]);
const loading = ref(false);

const modalNuevoTramite = ref(false);
const formNuevo = ref({
  id_tipo_tramite: 1,
  asunto: '',
});

const modalAdjuntar = ref(false);
const selectedTramite = ref(null);
const formDoc = ref({
  nombre_documento: '',
});

const inputPin = ref('');
const pinResultado = ref(null);
const certModalData = ref(null);

async function loadData() {
  loading.value = true;
  try {
    const [resTramites, resTipos] = await Promise.all([
      axios.get('/api/tramites', { params: { solicitante_id: state.user.id_usuario } }),
      axios.get('/api/tramites/tipos'),
    ]);
    misTramites.value = resTramites.data.data;
    tiposTramite.value = resTipos.data.data;
  } catch (e) {
    console.error(e);
  } finally {
    loading.value = false;
  }
}

function iniciarDesdeTipo(tipo) {
  formNuevo.value.id_tipo_tramite = tipo.id;
  formNuevo.value.asunto = 'Solicitud de ' + tipo.nombre;
  modalNuevoTramite.value = true;
}

async function crearTramite() {
  if (!formNuevo.value.asunto) return;
  try {
    await axios.post('/api/tramites', {
      ...formNuevo.value,
      id_usuario_solicitante: state.user.id_usuario,
    });
    modalNuevoTramite.value = false;
    formNuevo.value.asunto = '';
    await loadData();
  } catch (e) {
    alert('Error al iniciar trámite');
  }
}

function openSubirDoc(t) {
  selectedTramite.value = t;
  formDoc.value.nombre_documento = '';
  modalAdjuntar.value = true;
}

async function guardarDocumento() {
  if (!formDoc.value.nombre_documento) return;
  try {
    await axios.post('/api/documentos', {
      id_tramite: selectedTramite.value.id,
      nombre_documento: formDoc.value.nombre_documento,
    });
    modalAdjuntar.value = false;
    await loadData();
  } catch (e) {
    alert('Error al subir documento');
  }
}

async function consultarPorPin() {
  if (!inputPin.value) return;
  try {
    const res = await axios.get(`/api/seguimiento/portal/${inputPin.value.trim()}`);
    pinResultado.value = res.data.data;
  } catch (e) {
    alert('PIN no encontrado');
    pinResultado.value = null;
  }
}

function verCertificadoModal(cert) {
  certModalData.value = cert;
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

function getDocBadge(val) {
  switch (val) {
    case 'APROBADO': return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
    case 'OBSERVADO': return 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300';
    case 'RECHAZADO': return 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300';
    default: return 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300';
  }
}

onMounted(() => {
  loadData();
});
</script>
