<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import axios from 'axios'

const loading = ref(true)
const error = ref(null)
const piezas = ref([])

const load = async () => {
  loading.value = true
  error.value = null
  try {
    const { data } = await axios.get('/api/reportes/pendientes-detalle')
    piezas.value = data.data
  } catch (e) {
    console.error(e)
    error.value = 'Error al cargar las piezas pendientes.'
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Detalle Pendientes" />

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">
          Detalle de Piezas Pendientes
        </h1>
        <button
          @click="load"
          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          :disabled="loading"
        >
          <svg
            v-if="loading"
            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            />
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
            />
          </svg>
          {{ loading ? 'Cargando...' : 'Recargar' }}
        </button>
      </div>

      <!-- Error -->
      <div
        v-if="error"
        class="mb-4 rounded-md bg-red-50 p-4 border border-red-200"
      >
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-red-700">{{ error }}</p>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="mt-4 flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proyecto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bloque</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pieza</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Peso Teórico</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <!-- Loading skeleton -->
                  <template v-if="loading">
                    <tr v-for="i in 3" :key="i" class="animate-pulse">
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="h-4 bg-gray-200 rounded w-16 ml-auto"></div>
                      </td>
                    </tr>
                  </template>

                  <!-- Data rows -->
                  <template v-else>
                    <tr v-for="pieza in piezas" :key="pieza.id" class="hover:bg-gray-50">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ pieza.proyecto }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ pieza.bloque }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ pieza.codigo }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ pieza.pieza }}</td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">{{ pieza.peso_teorico }}</td>
                    </tr>

                    <!-- Empty state -->
                    <tr v-if="!loading && piezas.length === 0">
                      <td colspan="5" class="px-6 py-4 text-sm text-gray-500 text-center">
                        No hay piezas pendientes.
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>