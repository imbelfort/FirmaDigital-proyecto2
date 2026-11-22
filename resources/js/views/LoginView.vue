<template>
  <div class="min-h-[85vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-md w-full space-y-8 m3-card p-8 sm:p-10 border border-slate-200 dark:border-slate-800 shadow-xl">
      
      <!-- Encabezado Limpio -->
      <div class="text-center">
        <div class="mx-auto w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-700 via-blue-600 to-red-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/20 mb-4">
          <svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white heading-font">
          Iniciar Sesión
        </h2>
        <p class="mt-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
          Ingrese sus credenciales de acceso institucional
        </p>
      </div>

      <!-- Formulario de Login Estándar: Código y Contraseña -->
      <form class="mt-8 space-y-5" @submit.prevent="submitLogin">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
            Código / Registro
          </label>
          <div class="relative">
            <input
              v-model="form.registro"
              type="text"
              required
              class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition font-mono text-sm"
              placeholder="Ingrese su código o registro"
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
            Contraseña
          </label>
          <div class="relative">
            <input
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              required
              class="w-full px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition font-mono text-sm"
              placeholder="Ingrese su contraseña"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-3.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
            >
              <svg v-if="showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
              <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
          </div>
        </div>

        <!-- Mensaje de Error -->
        <div v-if="errorMsg" class="p-3 rounded-xl bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-300 text-xs font-semibold">
          {{ errorMsg }}
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full py-3.5 px-4 rounded-full text-white font-bold bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 transition cursor-pointer disabled:opacity-50 flex items-center justify-center space-x-2"
        >
          <span v-if="loading" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
          <span>{{ loading ? 'Autenticando...' : 'Entrar' }}</span>
        </button>
      </form>

      <!-- Cuentas para demostración rápida de roles según BD.md -->
      <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
        <p class="text-[11px] font-bold text-slate-400 mb-2 uppercase tracking-wider text-center">Cuentas disponibles para pruebas:</p>
        <div class="grid grid-cols-2 gap-2 text-xs">
          <button
            @click="fillDemo('219064748', '4567890')"
            class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-left transition cursor-pointer border border-slate-200/60 dark:border-slate-700"
          >
            <span class="font-bold text-blue-600 dark:text-blue-400 block">Alumno Juan Perez</span>
            <span class="text-[10px] text-slate-500 font-mono">219064748</span>
          </button>
          <button
            @click="fillDemo('1001', '1234567')"
            class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-left transition cursor-pointer border border-slate-200/60 dark:border-slate-700"
          >
            <span class="font-bold text-purple-600 dark:text-purple-400 block">Vicerrector Vargas</span>
            <span class="text-[10px] text-slate-500 font-mono">1001</span>
          </button>
          <button
            @click="fillDemo('1002', '2345678')"
            class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-left transition cursor-pointer border border-slate-200/60 dark:border-slate-700"
          >
            <span class="font-bold text-emerald-600 dark:text-emerald-400 block">Encargado Mendoza</span>
            <span class="text-[10px] text-slate-500 font-mono">1002</span>
          </button>
          <button
            @click="fillDemo('1045', '3456789')"
            class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-left transition cursor-pointer border border-slate-200/60 dark:border-slate-700"
          >
            <span class="font-bold text-amber-600 dark:text-amber-400 block">Docente Guzman</span>
            <span class="text-[10px] text-slate-500 font-mono">1045</span>
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { actions } from '../stores/appState';
import axios from 'axios';

const form = reactive({
  registro: '',
  password: '',
});

const showPassword = ref(false);
const loading = ref(false);
const errorMsg = ref('');

function fillDemo(reg, pass) {
  form.registro = reg;
  form.password = pass;
}

async function submitLogin() {
  loading.value = true;
  errorMsg.value = '';
  try {
    const res = await axios.post('/api/auth/login', form);
    if (res.data.success) {
      actions.setUser(res.data.usuario);
      actions.setActiveTab('dashboard');
    }
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Código o contraseña incorrectos.';
  } finally {
    loading.value = false;
  }
}
</script>
