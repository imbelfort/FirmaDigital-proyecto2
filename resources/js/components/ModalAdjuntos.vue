<template>
  <dialog ref="dialogoAdjuntos" :aria-labelledby="tituloId" @close="tramiteAdjuntos = null; adjuntoSeleccionado = null"
          class="m-auto w-[calc(100%-2rem)] max-w-5xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white dark:bg-slate-900 text-slate-900 dark:text-white p-5 backdrop:bg-slate-950/60">
    <div v-if="tramiteAdjuntos" class="space-y-4">
      <header class="flex items-center justify-between gap-3">
        <h2 :id="tituloId" class="font-bold">Adjuntos de {{ tramiteAdjuntos.codigo_tramite }}</h2>
        <button type="button" autofocus @click="dialogoAdjuntos.close()" class="rounded-full px-3 py-1.5 text-sm bg-slate-100 dark:bg-slate-800 cursor-pointer">Cerrar</button>
      </header>
      <p v-if="!tramiteAdjuntos.documentos?.length" class="text-sm text-slate-500">El trámite todavía no tiene archivos adjuntos.</p>
      <div v-else class="space-y-3">
        <label v-if="tramiteAdjuntos.documentos.length > 1" :for="selectorId" class="block text-sm font-semibold">Documento</label>
        <select v-if="tramiteAdjuntos.documentos.length > 1" :id="selectorId" v-model="adjuntoSeleccionado" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2 text-sm">
          <option v-for="doc in tramiteAdjuntos.documentos" :key="doc.id" :value="doc">
            {{ doc.nombre_documento }} · {{ formatoEstado(doc.estado_validacion) }}
          </option>
        </select>
        <p v-else class="text-sm font-semibold">{{ adjuntoSeleccionado.nombre_documento }}</p>
        <template v-if="archivoLocal">
          <a :href="archivoLocal + '?descargar=1'" class="inline-block rounded-full bg-blue-700 text-white px-4 py-2 text-sm font-semibold">Descargar archivo</a>
          <div v-if="adjuntoEsImagen" class="flex h-[55vh] items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 p-3">
            <img :key="archivoLocal" :src="archivoLocal" :alt="adjuntoSeleccionado.nombre_documento"
                 class="max-h-full max-w-full object-contain" />
          </div>
          <iframe v-else :key="archivoLocal" :src="archivoLocal" :title="adjuntoSeleccionado.nombre_documento"
                  class="block w-full h-[55vh] rounded-lg border border-slate-200 dark:border-slate-700"></iframe>
        </template>
        <a v-else-if="archivoExterno" :href="archivoExterno" target="_blank" rel="noopener noreferrer" class="text-sm underline">Abrir archivo en el sitio de origen</a>
        <p v-else class="text-sm text-slate-500">No hay un archivo disponible para este documento.</p>
      </div>
    </div>
  </dialog>
</template>

<script setup>
import { computed, nextTick, ref, useId } from 'vue';

defineProps({ formatoEstado: { type: Function, required: true } });

const tituloId = useId();
const selectorId = useId();
const dialogoAdjuntos = ref(null);
const tramiteAdjuntos = ref(null);
const adjuntoSeleccionado = ref(null);
const archivoExterno = computed(() => {
  const url = adjuntoSeleccionado.value?.archivo_url;
  return /^https?:\/\//i.test(url ?? '') ? url : null;
});
const rutaAdjunto = computed(() => (adjuntoSeleccionado.value?.archivo_url ?? '').split(/[?#]/, 1)[0]);
const adjuntoEsImagen = computed(() => /\.(?:png|jpe?g)$/i.test(rutaAdjunto.value));
const archivoLocal = computed(() => {
  return /(?:^|\/)storage\/expedientes\/[a-z0-9_-]+\.(?:pdf|png|jpe?g)$/i.test(rutaAdjunto.value)
    ? `/api/documentos/${adjuntoSeleccionado.value.id}/archivo`
    : null;
});

async function abrir(tramite, documento = tramite.documentos?.[0]) {
  tramiteAdjuntos.value = tramite;
  adjuntoSeleccionado.value = documento ?? null;
  await nextTick();
  if (!dialogoAdjuntos.value.open) dialogoAdjuntos.value.showModal();
}

defineExpose({ abrir });
</script>
