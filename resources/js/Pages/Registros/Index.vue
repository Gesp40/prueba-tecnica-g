<script setup>
import { Head, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

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
  <div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Registros</h1>
        <div class="space-x-3">
          <!-- ← usar /registros/create -->
          <Link 
            href="/registros/create"
            class="bg-[#093972] text-white px-4 py-2 rounded-lg hover:bg-[#093972]/90"
          >
            Nuevo registro
          </Link>

          <Link href="/reportes" class="text-[#093972] font-medium">
            Reportes
          </Link>
        </div>
      </div>

      <!-- Tabla -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-[#093972]">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                Proyecto
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                Bloque
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                Pieza
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                Peso teórico
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                Peso real
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                Diferencia
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                Usuario
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                Fecha
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">
                Acciones
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="r in registros.data" :key="r.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ r.proyecto }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.bloque }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.pieza?.codigo }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.peso_teorico }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.peso_real }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.diferencia }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.usuario }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ r.registrado_en }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                <Link
                  :href="`/registros/${r.id}/edit`"
                  class="text-[#093972] hover:text-[#093972]/80 mr-3"
                >
                  Editar
                </Link>
                <button 
                  @click="borrar(r.id)"
                  class="text-red-600 hover:text-red-800"
                >
                  Eliminar
                </button>
              </td>
            </tr>
            <tr v-if="!registros.data?.length">
              <td colspan="9" class="px-6 py-4 text-center text-gray-500">Sin registros</td>
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
               :class="{ 'bg-indigo-100': lnk.active }"></a>
            <span v-else v-html="lnk.label" class="px-3 py-1 rounded border text-gray-400"></span>
          </template>
        </div>
      </div>
    </div>
  </div>
</template>