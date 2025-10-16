<script setup>
import { Head, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const registros = computed(() => page.props.registros)
const flashSuccess = computed(() => page.props.flash?.success ?? null)

const borrar = (id) => {
  if (confirm('¿Eliminar este registro? La pieza volverá a “pendiente” si no tiene más registros.')) {
    router.delete(`/registros/${id}`, { preserveScroll: true })
  }
}
</script>

<template>
  <Head title="Registros" />
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto bg-white shadow rounded-lg p-6">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Registros</h1>
        <div class="space-x-3">
          /registros/crearNuevo registro</a>
          /reportes/estadisticasVer estadísticas</a>
        </div>
      </div>

      <div v-if="flashSuccess" class="mb-4 rounded bg-emerald-50 text-emerald-800 px-4 py-2">
        {{ flashSuccess }}
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">#</th>
              <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Proyecto</th>
              <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Bloque</th>
              <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Pieza</th>
              <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Peso teórico</th>
              <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Peso real</th>
              <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Diferencia</th>
              <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Usuario</th>
              <th class="px-3 py-2 text-left text-xs font-semibold text-gray-700">Fecha</th>
              <th class="px-3 py-2 text-right text-xs font-semibold text-gray-700">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="r in registros.data" :key="r.id">
              <td class="px-3 py-2 text-gray-800">{{ r.id }}</td>
              <td class="px-3 py-2 text-gray-800">{{ r.proyecto }}</td>
              <td class="px-3 py-2 text-gray-800">{{ r.bloque }}</td>
              <td class="px-3 py-2 text-gray-800">{{ r.pieza?.codigo }}</td>
              <td class="px-3 py-2 text-gray-800">{{ r.peso_teorico }}</td>
              <td class="px-3 py-2 text-gray-800">{{ r.peso_real }}</td>
              <td class="px-3 py-2 text-gray-800">{{ r.diferencia }}</td>
              <td class="px-3 py-2 text-gray-800">{{ r.usuario }}</td>
              <td class="px-3 py-2 text-gray-800">{{ r.registrado_en }}</td>
              <td class="px-3 py-2 text-right">
                </registros/${r.id}/editar`Editar</a>
                <button @click="borrar(r.id)" class="text-red-600 hover:underline">Eliminar</button>
              </td>
            </tr>
            <tr v-if="!registros.data?.length">
              <td colspan="10" class="px-3 py-6 text-center text-gray-500">Sin registros</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginación simple -->
      <div class="flex items-center justify-between mt-4" v-if="registros.links?.length">
        <div class="text-sm text-gray-600">Total: {{ registros.total }}</div>
        <div class="flex gap-2">
          <template v-for="(lnk, i) in registros.links" :key="i">
            <a v-if="lnk.url" :href="lnk.url" v-html="lnk.label"
               class="px-3 py-1 rounded border"
               :classpan>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>