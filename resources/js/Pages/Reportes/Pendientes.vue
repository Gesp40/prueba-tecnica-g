<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import axios from 'axios'

const filas = ref([])

onMounted(async () => {
  const { data } = await axios.get('/api/reportes/pendientes')
  filas.value = data
})
</script>

<template>
  <Head title="Pendientes por proyecto" />
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-6">
      <h1 class="text-2xl font-semibold mb-6">Piezas pendientes por proyecto</h1>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Proyecto</th>
              <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Pendientes</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="r in filas" :key="r.proyecto">
              <td class="px-4 py-2 text-gray-800">{{ r.proyecto }}</td>
              <td class="px-4 py-2 text-gray-800">{{ r.pendientes }}</td>
            </tr>
            <tr v-if="!filas.length">
              <td colspan="2" class="px-4 py-6 text-center text-gray-500">Sin datos</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </div>
</template>