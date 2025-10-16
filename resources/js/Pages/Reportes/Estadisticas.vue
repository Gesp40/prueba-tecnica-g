<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import axios from 'axios'
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Chart } from 'chart.js/auto'

const loading = ref(true)
const error = ref(null)
const canvasRef = ref(null)
let chartInstance = null

const load = async () => {
  loading.value = true
  error.value = null
  try {
    const { data } = await axios.get('/api/reportes/estadisticas')
    const payload = data?.data ?? { labels: [], datasets: [] }

    if (chartInstance) chartInstance.destroy()

    chartInstance = new Chart(canvasRef.value.getContext('2d'), {
      type: 'bar',
      data: {
        labels: payload.labels,
        datasets: [
          {
            label: payload.datasets?.[0]?.label ?? 'Pendientes',
            data: payload.datasets?.[0]?.data ?? [],
            backgroundColor: 'rgba(59, 130, 246, 0.6)',
          },
          {
            label: payload.datasets?.[1]?.label ?? 'Fabricadas',
            data: payload.datasets?.[1]?.data ?? [],
            backgroundColor: 'rgba(16, 185, 129, 0.6)',
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom' },
          tooltip: { mode: 'index', intersect: false },
          title: { display: true, text: 'Estado de piezas por proyecto' },
        },
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0 } },
          x: { ticks: { autoSkip: false, maxRotation: 45, minRotation: 0 } },
        },
      },
    })
  } catch (e) {
    console.error(e)
    error.value = 'No se pudieron cargar las estadísticas.'
  } finally {
    loading.value = false
  }
}

onMounted(load)
onBeforeUnmount(() => { if (chartInstance) chartInstance.destroy() })
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Estadísticas" />

    <div class="flex items-center justify-between mb-6">
      <h1 class="text-xl font-semibold">Estadísticas</h1>
      <button
        @click="load"
        :disabled="loading"
        class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-50"
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

    <div class="relative h-[400px] rounded-lg border bg-white p-4">
      <div v-if="loading" class="absolute inset-0 grid place-items-center">
        <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-indigo-500"></div>
      </div>
      <canvas v-show="!loading" ref="canvasRef" />
    </div>
  </AuthenticatedLayout>
</template>