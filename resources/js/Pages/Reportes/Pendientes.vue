<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import axios from 'axios'
import { ref, onMounted } from 'vue'

const loading = ref(true)
const error = ref(null)
const rows = ref([])

const load = async () => {
  loading.value = true
  error.value = null
  try {
    const { data } = await axios.get('/api/reportes/pendientes')
    rows.value = data?.data ?? []
  } catch (e) {
    console.error(e)
    error.value = 'No se pudieron cargar las piezas pendientes.'
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Pendientes" />

    <div class="flex items-center justify-between mb-6">
      <h1 class="text-xl font-semibold">Piezas Pendientes</h1>
      <button
        @click="load"
        class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50"
        :disabled="loading"
      >
        <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
        Recargar
      </button>
    </div>

    <div v-if="error" class="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-red-700">
      {{ error }}
    </div>

    <!-- Desktop -->
    <div class="hidden md:block">
      <div class="rounded-lg border bg-white overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proyecto</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pendientes</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <template v-if="loading">
              <tr v-for="i in 3" :key="i" class="animate-pulse">
                <td class="px-6 py-4"><div class="h-4 bg-gray-200 rounded w-3/4"></div></td>
                <td class="px-6 py-4"><div class="h-4 bg-gray-200 rounded w-16 ml-auto"></div></td>
              </tr>
            </template>

            <template v-else>
              <tr v-for="row in rows" :key="row.proyecto" class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">{{ row.proyecto }}</td>
                <td class="px-6 py-4 text-sm text-gray-900 text-right font-medium">{{ row.pendientes }}</td>
              </tr>

              <tr v-if="!loading && rows.length === 0">
                <td colspan="2" class="px-6 py-4 text-sm text-gray-500 text-center">No hay piezas pendientes.</td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Mobile -->
    <div class="md:hidden space-y-4">
      <template v-if="loading">
        <div v-for="i in 3" :key="i" class="animate-pulse rounded-lg border bg-white p-4">
          <div class="h-4 bg-gray-200 rounded w-3/4 mb-3"></div>
          <div class="h-4 bg-gray-200 rounded w-16"></div>
        </div>
      </template>

      <template v-else>
        <div v-for="row in rows" :key="row.proyecto" class="rounded-lg border bg-white p-4 hover:bg-gray-50">
          <div class="text-sm text-gray-900 font-medium mb-1">{{ row.proyecto }}</div>
          <div class="text-sm text-gray-600">{{ row.pendientes }} pendientes</div>
        </div>

        <div v-if="!loading && rows.length === 0" class="rounded-lg border bg-white p-4 text-sm text-gray-500 text-center">
          No hay piezas pendientes.
        </div>
      </template>
    </div>
  </AuthenticatedLayout>
</template>