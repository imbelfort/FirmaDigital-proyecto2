<template>
  <div class="space-y-6">
    <!-- Encabezado Administrador / Encargado -->
    <div class="m3-surface p-6 sm:p-8 bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-950 text-white shadow-xl relative overflow-hidden">
      <div class="relative z-10">
        <span class="text-[10px] font-bold tracking-wider uppercase px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-400/30">
          Mesa Central & Administración del Sistema
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold mt-2 heading-font">
          Panel de Administración: {{ state.user.nombre }} {{ state.user.apellidos }}
        </h1>
        <p class="text-slate-300 text-xs sm:text-sm mt-1">
          Rol: <span class="font-bold text-white">{{ state.user.rol }}</span> | Registro: <span class="font-mono text-blue-300">{{ state.user.registro }}</span> | Gestión general de trámites de estudiantes, tipos de trámites y usuarios.
        </p>
      </div>
    </div>

    <!-- Navegación de Pestañas M3 para el Administrador / Personal -->
    <div class="flex items-center space-x-2 border-b border-slate-200 dark:border-slate-800 pb-2 overflow-x-auto">
      <button
        @click="activeTab = 'todos_tramites'"
        :class="activeTab === 'todos_tramites' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
        class="pb-2.5 px-3.5 text-xs sm:text-sm transition cursor-pointer shrink-0"
      >
        Trámites de Todos los Estudiantes ({{ tramites.length }})
      </button>
      <button
        @click="activeTab = 'catalogo_gestion'"
        :class="activeTab === 'catalogo_gestion' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
        class="pb-2.5 px-3.5 text-xs sm:text-sm transition cursor-pointer shrink-0"
      >
        Gestión de Tipos de Trámites y Aranceles Bs
      </button>
      <button
        @click="activeTab = 'usuarios'"
        :class="activeTab === 'usuarios' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
        class="pb-2.5 px-3.5 text-xs sm:text-sm transition cursor-pointer shrink-0"
      >
        Gestión de Usuarios (Alumnos y Personal)
      </button>
      <button
        @click="activeTab = 'despacho'"
        :class="activeTab === 'despacho' ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-extrabold' : 'text-slate-500 hover:text-slate-800 dark:hover:text-slate-200'"
        class="pb-2.5 px-3.5 text-xs sm:text-sm transition cursor-pointer shrink-0"
      >
        Despacho de Legajos
      </button>
    </div>

    <!-- TAB 1: GESTIÓN DE TRÁMITES DE TODOS LOS ESTUDIANTES (Mover estados, cotejar, observar) -->
    <div v-if="activeTab === 'todos_tramites'" class="space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-900 dark:text-white">Bandeja de Trámites Solicitados por Alumnos</h2>
        <span class="text-xs text-slate-500">Mueve los estados procesales y valida los documentos</span>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="t in tramites"
          :key="t.id"
          class="m3-card p-5 border border-slate-200 dark:border-slate-800 space-y-4 hover:border-blue-400/50 transition"
        >
          <!-- Encabezado con código, solicitante y estado -->
          <div class="flex items-center justify-between">
            <div>
              <span class="font-mono text-xs font-black text-blue-600 dark:text-blue-400 px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-950/40">
                {{ t.codigo_tramite }}
              </span>
              <p class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1">
                Alumno: {{ t.solicitante ? t.solicitante.nombre + ' ' + t.solicitante.apellidos : 'N/A' }}
              </p>
              <p class="text-[11px] text-slate-400 font-mono">Reg: {{ t.solicitante ? t.solicitante.registro : '' }} | CI: {{ t.solicitante ? t.solicitante.ci : '' }}</p>
            </div>
            <div class="text-right">
              <span class="m3-badge uppercase" :class="getEstadoBadge(t.estado)">
                {{ formatEstadoLegible(t.estado) }}
              </span>
              <span class="block text-[10px] font-bold mt-1" :class="isPagado(t) ? 'text-emerald-600' : 'text-amber-600'">
                {{ isPagado(t) ? 'PAGADO' : 'PAGO PENDIENTE' }} (Bs. {{ t.tipo_tramite ? t.tipo_tramite.costo_bs : '0.00' }})
              </span>
            </div>
          </div>

          <h3 class="font-bold text-slate-900 dark:text-white text-sm">{{ t.asunto }}</h3>

          <!-- Cotejo de Recaudos -->
          <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Recaudos presentados ({{ t.documentos ? t.documentos.length : 0 }}):</span>
            <div v-for="d in t.documentos" :key="d.id" class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 text-xs flex items-center justify-between">
              <div>
                <p class="font-semibold text-slate-800 dark:text-slate-200">{{ d.nombre_documento }}</p>
                <p class="text-[10px] font-mono text-slate-400 truncate max-w-[200px]">SHA-256: {{ d.hash_documento ? d.hash_documento.substring(0, 16) + '...' : '' }}</p>
              </div>
              <div class="flex items-center space-x-1.5">
                <button
                  @click="validarDoc(d.id, 'APROBADO')"
                  class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold cursor-pointer"
                  title="Aprobar Recaudo"
                >✓ Aprobar</button>
                <button
                  @click="validarDoc(d.id, 'OBSERVADO')"
                  class="px-2 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded text-[10px] font-bold cursor-pointer"
                  title="Observar Recaudo"
                >! Observar</button>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold" :class="getDocBadge(d.estado_validacion)">{{ formatEstadoLegible(d.estado_validacion) }}</span>
              </div>
            </div>
          </div>

          <!-- BOTONES PARA MOVER DE ESTADO EL TRÁMITE -->
          <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-2">
            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">Mover Estado Procesal:</span>
            <div class="grid grid-cols-2 gap-2 text-xs">
              <button
                @click="avanzar(t, 'Revisión y Cotejo', 'REQUISITOS_VALIDADOS')"
                :disabled="procesandoAccion"
                class="py-2 px-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold rounded-xl transition cursor-pointer disabled:opacity-50"
              >
                1. Validar Requisitos
              </button>
              <button
                @click="avanzar(t, 'Compilación de Expediente', 'EXPEDIENTE_COMPILADO')"
                :disabled="procesandoAccion"
                class="py-2 px-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold rounded-xl transition cursor-pointer disabled:opacity-50"
              >
                2. Compilar Legajo
              </button>
              <button
                @click="avanzar(t, 'Firma Vicerrectoral', 'EN_FIRMA')"
                :disabled="procesandoAccion"
                class="py-2 px-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-xl transition cursor-pointer shadow-sm disabled:opacity-50"
              >
                3. Pasar a Firma VR
              </button>
              <button
                @click="openObservacion(t)"
                :disabled="procesandoAccion"
                class="py-2 px-2.5 bg-red-100 hover:bg-red-200 dark:bg-red-950/40 text-red-700 dark:text-red-300 font-bold rounded-xl transition cursor-pointer disabled:opacity-50"
              >
                Observar Trámite
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- TAB 2: GESTIÓN DE TIPOS DE TRÁMITES (Crear, Editar aranceles Bs y SLAs) -->
    <div v-if="activeTab === 'catalogo_gestion'" class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-base font-bold text-slate-900 dark:text-white">Tipos de Trámites Oficiales (tipo_tramite)</h2>
          <p class="text-xs text-slate-500">Crea nuevos trámites o modifica los precios oficiales en Bs y plazos de atención.</p>
        </div>
        <button
          @click="abrirModalTipoTramite(null)"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-full shadow-sm cursor-pointer"
        >
          + Crear Nuevo Tipo de Trámite
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="tipo in tiposTramite"
          :key="tipo.id"
          class="m3-card p-5 border border-slate-200 dark:border-slate-800 space-y-3"
        >
          <div class="flex items-center justify-between">
            <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-blue-600 dark:text-blue-400">{{ tipo.codigo }}</span>
            <div class="text-right">
              <span class="text-[10px] text-slate-400 block font-bold">Precio Oficial</span>
              <span class="text-lg font-black text-emerald-600 dark:text-emerald-400">Bs. {{ tipo.costo_bs }}</span>
            </div>
          </div>

          <h3 class="font-bold text-slate-900 dark:text-white text-base">{{ tipo.nombre }}</h3>
          <p class="text-xs text-slate-500">{{ tipo.descripcion }}</p>
          <p class="text-xs text-slate-600 dark:text-slate-300">⏱️ Plazo SLA: <strong class="font-mono text-blue-600">{{ tipo.tiempo_sla_horas }} horas</strong></p>

          <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex justify-end space-x-2">
            <button
              @click="abrirModalTipoTramite(tipo)"
              class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs rounded-full transition cursor-pointer"
            >
              Editar Arancel / Datos
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- TAB 3: GESTIÓN DE USUARIOS (Alumnos, Docentes, Personal) -->
    <div v-if="activeTab === 'usuarios'" class="space-y-4">
      <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-900 dark:text-white">Usuarios del Sistema</h2>
        <button @click="modalNuevoUsuario = true" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-full shadow-sm cursor-pointer">
          + Agregar Usuario
        </button>
      </div>

      <div class="m3-card border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-400 uppercase font-semibold">
              <tr>
                <th class="p-3">Nombre</th>
                <th class="p-3">Código/Registro</th>
                <th class="p-3">C.I.</th>
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
                <td class="p-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">{{ u.estado }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- TAB 4: DESPACHO DE LEGAJOS -->
    <div v-if="activeTab === 'despacho'" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div
          v-for="t in tramitesFirmados"
          :key="t.id"
          class="m3-card p-5 border border-slate-200 dark:border-slate-800 space-y-3"
        >
          <div class="flex items-center justify-between">
            <span class="font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ t.codigo_tramite }}</span>
            <span class="m3-badge bg-emerald-100 text-emerald-800">FIRMADO POR VR</span>
          </div>
          <h3 class="font-bold text-slate-900 dark:text-white">{{ t.asunto }}</h3>
          <p class="text-xs text-slate-500">Alumno: {{ t.solicitante ? t.solicitante.nombre + ' ' + t.solicitante.apellidos : '' }}</p>

          <div v-if="t.despacho" class="p-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-xs space-y-1">
            <p class="font-bold text-emerald-600">Despachado Oficialmente</p>
            <p>Nro. Despacho: <span class="font-mono font-bold">{{ t.despacho.numero_despacho }}</span></p>
          </div>
          <div v-else class="flex space-x-2 pt-2">
            <button
              @click="ejecutarDespacho(t, 'DIGITAL_CORREO')"
              class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-full transition shadow-sm cursor-pointer"
            >
              Despachar por Correo
            </button>
            <button
              @click="ejecutarDespacho(t, 'PORTAL_DESCARGA')"
              class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-full transition shadow-sm cursor-pointer"
            >
              Habilitar Descarga Portal
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL EDITAR / CREAR TIPO DE TRÁMITE -->
    <div v-if="modalTipoTramite" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="m3-card p-6 sm:p-8 max-w-md w-full space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
          {{ editandoTipoId ? 'Editar Tipo de Trámite' : 'Crear Nuevo Tipo de Trámite' }}
        </h3>
        
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Código del Trámite</label>
          <input
            v-model="formTipo.codigo"
            :disabled="!!editandoTipoId"
            type="text"
            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono uppercase"
            placeholder="EJ: DIPLOMA_ACAD"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Nombre del Trámite</label>
          <input
            v-model="formTipo.nombre"
            type="text"
            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
            placeholder="Ej: Diploma Académico de Grado"
          />
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Precio Oficial (Bs.)</label>
            <input
              v-model="formTipo.costo_bs"
              type="number"
              step="0.50"
              class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold"
            />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Tiempo SLA (Horas)</label>
            <input
              v-model="formTipo.tiempo_sla_horas"
              type="number"
              class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-bold"
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Descripción</label>
          <textarea
            v-model="formTipo.descripcion"
            rows="2"
            class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs"
          ></textarea>
        </div>

        <div class="flex justify-end space-x-2 pt-2">
          <button @click="modalTipoTramite = false" class="px-4 py-2 text-xs font-semibold text-slate-500 cursor-pointer">Cancelar</button>
          <button @click="guardarTipoTramite" class="px-5 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow cursor-pointer">Guardar</button>
        </div>
      </div>
    </div>

    <!-- Modal Observación -->
    <div v-if="modalObs" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="m3-card p-6 sm:p-8 max-w-md w-full space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl">
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
          <button @click="modalObs = false" class="px-4 py-2 text-xs font-semibold text-slate-500 cursor-pointer">Cancelar</button>
          <button @click="guardarObservacion" class="px-5 py-2 text-xs font-bold bg-red-600 hover:bg-red-700 text-white rounded-full shadow cursor-pointer">Guardar Observación</button>
        </div>
      </div>
    </div>

    <!-- Modal Nuevo Usuario -->
    <div v-if="modalNuevoUsuario" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
      <div class="m3-card p-6 sm:p-8 max-w-md w-full space-y-4 border border-slate-200 dark:border-slate-800 shadow-2xl">
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
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Código / Registro</label>
            <input v-model="formUser.registro" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono" placeholder="219000000" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">C.I.</label>
            <input v-model="formUser.ci" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-mono" placeholder="4567890" />
          </div>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Correo</label>
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
          <button @click="modalNuevoUsuario = false" class="px-4 py-2 text-xs font-semibold text-slate-500 cursor-pointer">Cancelar</button>
          <button @click="guardarUsuario" class="px-5 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow cursor-pointer">Crear Usuario</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { state } from '../stores/appState';
import axios from 'axios';

const activeTab = ref('todos_tramites');
const tramites = ref([]);
const tiposTramite = ref([]);
const usuarios = ref([]);

const modalObs = ref(false);
const tramiteSeleccionado = ref(null);
const formObs = ref({ tipo_observacion: 'FALTA_REQUISITO', descripcion: '' });

const procesandoAccion = ref(false);

const modalTipoTramite = ref(false);
const editandoTipoId = ref(null);
const formTipo = ref({
  codigo: '',
  nombre: '',
  descripcion: '',
  costo_bs: 0,
  tiempo_sla_horas: 72,
});

const modalNuevoUsuario = ref(false);
const formUser = ref({
  nombre: '',
  apellidos: '',
  registro: '',
  ci: '',
  email: '',
  rol: 'ALUMNO',
});

const tramitesFirmados = computed(() => {
  return tramites.value.filter(t => t.estado === 'FIRMADO' || t.estado === 'DESPACHADO');
});

function formatEstadoLegible(texto) {
  if (!texto) return '';
  return texto.split('_').map(palabra => palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase()).join(' ');
}

function isPagado(t) {
  return t.pago && t.pago.estado === 'COMPLETADO';
}

async function loadData() {
  try {
    const [resTramites, resTipos, resUsers] = await Promise.all([
      axios.get('/api/tramites'),
      axios.get('/api/tramites/tipos'),
      axios.get('/api/usuarios'),
    ]);
    tramites.value = resTramites.data?.data || [];
    tiposTramite.value = resTipos.data?.data || [];
    usuarios.value = resUsers.data?.data || [];
  } catch (e) {
    console.error(e);
  }
}

async function validarDoc(idDoc, estado) {
  if (procesandoAccion.value) return;
  procesandoAccion.value = true;
  try {
    await axios.post(`/api/documentos/${idDoc}/validar`, {
      estado_validacion: estado,
      observacion_validacion: `Validado: ${estado}`,
    });
    await loadData();
  } catch (e) {
    alert('Error al validar documento');
  } finally {
    procesandoAccion.value = false;
  }
}

async function avanzar(t, etapa, nuevoEstado) {
  if (procesandoAccion.value) return;
  procesandoAccion.value = true;
  try {
    await axios.post(`/api/tramites/${t.id}/flujo`, {
      etapa_actual: etapa,
      nuevo_estado_tramite: nuevoEstado,
    });
    alert(`Trámite pasado a: ${etapa}`);
    await loadData();
  } catch (e) {
    alert('Error al avanzar flujo');
  } finally {
    procesandoAccion.value = false;
  }
}

function openObservacion(t) {
  tramiteSeleccionado.value = t;
  formObs.value.descripcion = '';
  modalObs.value = true;
}

async function guardarObservacion() {
  if (procesandoAccion.value) return;
  procesandoAccion.value = true;
  try {
    await axios.post(`/api/tramites/${tramiteSeleccionado.value.id}/observaciones`, formObs.value);
    modalObs.value = false;
    alert('Observación registrada');
    await loadData();
  } catch (e) {
    alert('Error al emitir observación');
  } finally {
    procesandoAccion.value = false;
  }
}

function abrirModalTipoTramite(tipo) {
  if (tipo) {
    editandoTipoId.value = tipo.id;
    formTipo.value = { ...tipo };
  } else {
    editandoTipoId.value = null;
    formTipo.value = { codigo: '', nombre: '', descripcion: '', costo_bs: 100, tiempo_sla_horas: 72 };
  }
  modalTipoTramite.value = true;
}

async function guardarTipoTramite() {
  if (procesandoAccion.value) return;
  procesandoAccion.value = true;
  try {
    if (editandoTipoId.value) {
      await axios.put(`/api/tramites/tipos/${editandoTipoId.value}`, formTipo.value);
    } else {
      await axios.post('/api/tramites/tipos', formTipo.value);
    }
    modalTipoTramite.value = false;
    alert('Tipo de trámite guardado exitosamente.');
    await loadData();
  } catch (e) {
    alert(e.response?.data?.message || 'Error al guardar tipo de trámite');
  } finally {
    procesandoAccion.value = false;
  }
}

async function ejecutarDespacho(t, tipoEntrega) {
  if (procesandoAccion.value) return;
  procesandoAccion.value = true;
  try {
    await axios.post(`/api/firma/despacho/${t.id}`, { tipo_entrega: tipoEntrega });
    alert('Legajo despachado y SLA cerrado exitosamente.');
    await loadData();
  } catch (e) {
    alert('Error al despachar legajo');
  } finally {
    procesandoAccion.value = false;
  }
}

async function guardarUsuario() {
  if (procesandoAccion.value) return;
  procesandoAccion.value = true;
  try {
    await axios.post('/api/usuarios', formUser.value);
    modalNuevoUsuario.value = false;
    alert('Usuario creado con éxito');
    await loadData();
  } catch (e) {
    alert(e.response?.data?.message || 'Error al crear usuario');
  } finally {
    procesandoAccion.value = false;
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

onMounted(() => {
  loadData();
});
</script>
