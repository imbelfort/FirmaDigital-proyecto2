<template>
  <div class="space-y-6">
    <!-- Encabezado Encargado -->
    <div class="bg-gradient-to-r from-blue-700 via-sky-800 to-indigo-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
      <div class="relative z-10">
        <span class="text-xs font-semibold tracking-wider uppercase px-3 py-1 rounded-full bg-white/20 backdrop-blur-xs">
          Ventanilla de Despacho & Control SLA
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold mt-2 heading-font">
          Encargado: {{ state.user.nombre }} {{ state.user.apellidos }}
        </h1>
        <p class="text-sky-200 text-sm mt-1">
          Unidad: <span class="font-bold text-white">{{ state.user.unidad_academica || 'Despacho Vicerrectorado' }}</span> | Código: <span class="font-mono">{{ state.user.registro }}</span>
        </p>
      </div>
    </div>

    <!-- Navegación de Pestañas -->
    <div class="flex space-x-2 border-b border-slate-200 dark:border-slate-800 pb-2">
      <button
        @click="activeTab = 'cotejo'"
        :class="activeTab === 'cotejo' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-500'"
        class="pb-2 px-3 text-sm transition"
      >
        Revisión, Cotejo y Requisitos (CU3)
      </button>
      <button
        @click="activeTab = 'despacho'"
        :class="activeTab === 'despacho' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-500'"
        class="pb-2 px-3 text-sm transition"
      >
        Despacho de Legajos (CU5)
      </button>
      <button
        @click="activeTab = 'sla'"
        :class="activeTab === 'sla' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-500'"
        class="pb-2 px-3 text-sm transition"
      >
        Monitoreo SLA 72h & Alertas (CU7)
      </button>
      <button
        @click="activeTab = 'usuarios'"
        :class="activeTab === 'usuarios' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-500'"
        class="pb-2 px-3 text-sm transition"
      >
        Gestión de Usuarios (CU1)
      </button>
    </div>

    <!-- Tab 1: Revisión y Cotejo de Trámites -->
    <div v-if="activeTab === 'cotejo'" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="t in tramites"
          :key="t.id"
          class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 space-y-3"
        >
          <div class="flex items-center justify-between">
            <span class="font-mono text-xs font-bold text-blue-600 dark:text-blue-400">{{ t.codigo_tramite }}</span>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full" :class="getEstadoBadge(t.estado)">{{ t.estado }}</span>
          </div>

          <h3 class="font-bold text-slate-900 dark:text-white text-base">{{ t.asunto }}</h3>
          <p class="text-xs text-slate-500">Solicitante: {{ t.solicitante ? t.solicitante.nombre + ' ' + t.solicitante.apellidos : 'N/A' }} | Reg: {{ t.solicitante ? t.solicitante.registro : '' }}</p>

          <!-- Documentos del Expediente y Botones de Validación -->
          <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Recaudos Presentados:</span>
            <div v-for="d in t.documentos" :key="d.id" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 text-xs flex items-center justify-between">
              <div>
                <p class="font-semibold text-slate-800 dark:text-slate-200">{{ d.nombre_documento }}</p>
                <p class="text-[10px] font-mono text-slate-400">Hash SHA-256: {{ d.hash_documento ? d.hash_documento.substring(0, 16) + '...' : '' }}</p>
              </div>
              <div class="flex items-center space-x-1">
                <button
                  @click="validarDoc(d.id, 'APROBADO')"
                  class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold"
                  title="Aprobar Recaudo"
                >✓</button>
                <button
                  @click="validarDoc(d.id, 'OBSERVADO')"
                  class="px-2 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded text-[10px] font-bold"
                  title="Observar Recaudo"
                >!</button>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="getDocBadge(d.estado_validacion)">{{ d.estado_validacion }}</span>
              </div>
            </div>
          </div>

          <!-- Acciones de Flujo -->
          <div class="pt-2 flex space-x-2">
            <button
              @click="avanzar(t, 'Firma Vicerrectoral', 'EN_FIRMA')"
              class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition"
            >
              Pasar a Firma Vicerrectoral
            </button>
            <button
              @click="openObservacion(t)"
              class="py-2 px-3 bg-red-100 dark:bg-red-950/40 text-red-700 dark:text-red-300 font-bold text-xs rounded-xl hover:bg-red-200 transition"
            >
              Observar
            </button>
          </div>

        </div>
      </div>
    </div>

    <!-- Tab 2: Despacho de Legajos -->
    <div v-if="activeTab === 'despacho'" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="t in tramitesParaDespacho"
          :key="t.id"
          class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 space-y-3"
        >
          <div class="flex items-center justify-between">
            <span class="font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ t.codigo_tramite }}</span>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">FIRMADO</span>
          </div>
          <h3 class="font-bold text-slate-900 dark:text-white">{{ t.asunto }}</h3>
          <p class="text-xs text-slate-500">Solicitante: {{ t.solicitante.nombre }} {{ t.solicitante.apellidos }}</p>

          <div v-if="t.despacho" class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-xs space-y-1">
            <p class="font-bold text-emerald-600">Despachado Oficialmente</p>
            <p>Nro. Despacho: <span class="font-mono font-bold">{{ t.despacho.numero_despacho }}</span></p>
            <p>Entrega: {{ t.despacho.tipo_entrega }}</p>
          </div>
          <div v-else class="flex space-x-2 pt-2">
            <button
              @click="ejecutarDespacho(t, 'DIGITAL_CORREO')"
              class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow"
            >
              Despacho por Correo
            </button>
            <button
              @click="ejecutarDespacho(t, 'PORTAL_DESCARGA')"
              class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow"
            >
              Habilitar Descarga Portal
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab 3: Monitoreo SLA 72 Horas y Alertas -->
    <div v-if="activeTab === 'sla'" class="space-y-4">
      <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 space-y-4">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Panel de Control de Tiempos y Alertas SLA (Límite: 72 Horas Hábiles)</h3>
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800 text-slate-400 uppercase font-semibold">
              <tr>
                <th class="p-3">Código</th>
                <th class="p-3">Trámite / Solicitante</th>
                <th class="p-3">Horas Consumidas</th>
                <th class="p-3">Límite</th>
                <th class="p-3">Estado SLA</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
              <tr v-for="m in monitoreos" :key="m.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                <td class="p-3 font-mono font-bold text-blue-600 dark:text-blue-400">{{ m.tramite ? m.tramite.codigo_tramite : 'N/A' }}</td>
                <td class="p-3 font-medium text-slate-800 dark:text-slate-200">
                  {{ m.tramite ? m.tramite.asunto : '' }}
                  <span class="block text-[10px] text-slate-400">{{ m.tramite && m.tramite.solicitante ? m.tramite.solicitante.nombre + ' ' + m.tramite.solicitante.apellidos : '' }}</span>
                </td>
                <td class="p-3 font-bold">{{ m.horas_transcurridas }} hrs</td>
                <td class="p-3 font-mono">{{ m.horas_sla_limite }} hrs</td>
                <td class="p-3">
                  <span class="px-2 py-0.5 rounded-full font-bold" :class="getSlaBadge(m.estado_sla)">
                    {{ m.estado_sla }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Tab 4: Gestión de Usuarios (CU1) -->
    <div v-if="activeTab === 'usuarios'" class="space-y-4">
      <div class="flex justify-between items-center">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Usuarios del Sistema (RU y Cédula Numéricos)</h3>
        <button @click="modalNuevoUsuario = true" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow">
          + Agregar Usuario
        </button>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 dark:bg-slate-800 text-slate-400 uppercase font-semibold">
            <tr>
              <th class="p-3">Nombre</th>
              <th class="p-3">Registro</th>
              <th class="p-3">C.I. (Clave inicial)</th>
              <th class="p-3">Rol</th>
              <th class="p-3">Unidad / Carrera</th>
              <th class="p-3">Estado</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr v-for="u in usuarios" :key="u.id_usuario" class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
              <td class="p-3 font-bold text-slate-800 dark:text-slate-200">{{ u.nombre }} {{ u.apellidos }}</td>
              <td class="p-3 font-mono">{{ u.registro }}</td>
              <td class="p-3 font-mono">{{ u.ci }}</td>
              <td class="p-3 font-bold text-blue-600 dark:text-blue-400">{{ u.rol }}</td>
              <td class="p-3 text-slate-500">{{ u.unidad_academica || u.cargo || 'UAGRM' }}</td>
              <td class="p-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">{{ u.estado }}</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Observación -->
    <div v-if="modalObs" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-md w-full space-y-4 shadow-2xl border border-slate-100 dark:border-slate-800">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Emitir Observación al Trámite</h3>
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tipo de Observación</label>
          <select v-model="formObs.tipo_observacion" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm">
            <option value="FALTA_REQUISITO">Falta de Requisito Obligatorio</option>
            <option value="DOCUMENTO_ILEGIBLE">Documento Ilegible o Incompleto</option>
            <option value="ERROR_DATOS">Error en los Datos Personales</option>
          </select>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Descripción</label>
          <textarea v-model="formObs.descripcion" rows="3" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white text-sm" placeholder="Detalle la observación para el estudiante..."></textarea>
        </div>
        <div class="flex justify-end space-x-2 pt-4">
          <button @click="modalObs = false" class="px-4 py-2 text-xs font-semibold text-slate-500">Cancelar</button>
          <button @click="guardarObservacion" class="px-5 py-2 text-xs font-bold bg-red-600 hover:bg-red-700 text-white rounded-xl shadow">Guardar Observación</button>
        </div>
      </div>
    </div>

    <!-- Modal Nuevo Usuario -->
    <div v-if="modalNuevoUsuario" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-md w-full space-y-4 shadow-2xl border border-slate-100 dark:border-slate-800">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Registrar Nuevo Usuario</h3>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nombre</label>
            <input v-model="formUser.nombre" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Apellidos</label>
            <input v-model="formUser.apellidos" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Registro (Numérico)</label>
            <input v-model="formUser.registro" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono" placeholder="219000000" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">C.I. (Numérico)</label>
            <input v-model="formUser.ci" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono" placeholder="4567890" />
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Correo Institucional</label>
          <input v-model="formUser.email" type="email" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs" placeholder="usuario@uagrm.edu.bo" />
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Rol</label>
          <select v-model="formUser.rol" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs">
            <option value="ALUMNO">ALUMNO</option>
            <option value="DOCENTE">DOCENTE</option>
            <option value="ENCARGADO">ENCARGADO</option>
            <option value="ADMINISTRATIVO">ADMINISTRATIVO</option>
            <option value="VICERRECTOR">VICERRECTOR</option>
          </select>
        </div>

        <div class="flex justify-end space-x-2 pt-4">
          <button @click="modalNuevoUsuario = false" class="px-4 py-2 text-xs font-semibold text-slate-500">Cancelar</button>
          <button @click="guardarUsuario" class="px-5 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow">Crear Usuario</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { state } from '../stores/appState';
import axios from 'axios';

const activeTab = ref('cotejo');
const tramites = ref([]);
const monitoreos = ref([]);
const usuarios = ref([]);

const modalObs = ref(false);
const tramiteSeleccionado = ref(null);
const formObs = ref({ tipo_observacion: 'FALTA_REQUISITO', descripcion: '' });

const modalNuevoUsuario = ref(false);
const formUser = ref({
  nombre: '',
  apellidos: '',
  registro: '',
  ci: '',
  email: '',
  rol: 'ALUMNO',
});

const tramitesParaDespacho = computed(() => {
  return tramites.value.filter(t => t.estado === 'FIRMADO' || t.estado === 'DESPACHADO');
});

async function loadData() {
  try {
    const [resTramites, resSla, resUsers] = await Promise.all([
      axios.get('/api/tramites'),
      axios.get('/api/seguimiento/monitoreo'),
      axios.get('/api/usuarios'),
    ]);
    tramites.value = resTramites.data.data;
    monitoreos.value = resSla.data.data;
    usuarios.value = resUsers.data.data;
  } catch (e) {
    console.error(e);
  }
}

async function validarDoc(idDoc, estado) {
  try {
    await axios.post(`/api/documentos/${idDoc}/validar`, {
      estado_validacion: estado,
      observacion_validacion: `Validado por encargado: ${estado}`,
    });
    await loadData();
  } catch (e) {
    alert('Error al validar documento');
  }
}

async function avanzar(t, etapa, nuevoEstado) {
  try {
    await axios.post(`/api/tramites/${t.id}/flujo`, {
      etapa_actual: etapa,
      nuevo_estado_tramite: nuevoEstado,
    });
    alert(`Trámite pasado a ${etapa}`);
    await loadData();
  } catch (e) {
    alert('Error al avanzar flujo');
  }
}

function openObservacion(t) {
  tramiteSeleccionado.value = t;
  formObs.value.descripcion = '';
  modalObs.value = true;
}

async function guardarObservacion() {
  try {
    await axios.post(`/api/tramites/${tramiteSeleccionado.value.id}/observaciones`, formObs.value);
    modalObs.value = false;
    alert('Observación registrada');
    await loadData();
  } catch (e) {
    alert('Error al emitir observación');
  }
}

async function ejecutarDespacho(t, tipoEntrega) {
  try {
    await axios.post(`/api/firma/despacho/${t.id}`, { tipo_entrega: tipoEntrega });
    alert('Legajo despachado y SLA cerrado exitosamente.');
    await loadData();
  } catch (e) {
    alert('Error al despachar legajo');
  }
}

async function guardarUsuario() {
  try {
    await axios.post('/api/usuarios', formUser.value);
    modalNuevoUsuario.value = false;
    alert('Usuario creado con éxito (Contraseña = C.I.)');
    await loadData();
  } catch (e) {
    alert(e.response?.data?.message || 'Error al crear usuario');
  }
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

function getSlaBadge(estado) {
  switch (estado) {
    case 'EN_TIEMPO':
    case 'CUMPLIDO_EN_TIEMPO': return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
    case 'POR_VENCER': return 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300';
    default: return 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300';
  }
}

onMounted(() => {
  loadData();
});
</script>
