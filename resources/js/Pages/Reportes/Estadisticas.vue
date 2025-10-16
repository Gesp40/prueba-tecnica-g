<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import axios from 'axios'
import { Chart, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js'

Chart.register(BarElement, CategoryScale, LinearScale, Tooltip, Legend)

const canvasRef = ref(null)
let chartInstance = null

onMounted(async () => {
  const { data } = await axios.get('/api/reportes/estadisticas')

  const ctx = canvasRef.value.getContext('2d')
  if (chartInstance) chartInstance.destroy()

  chartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: data.labels,
      datasets: [
        {
          label: 'Pendientes',
          data: data.datasets[0].data,
          backgroundColor: 'rgba(59, 130, 246, 0.6)', // indigo-500
        },
        {
          label: 'Fabricadas',
          data: data.datasets[1].data,
          backgroundColor: 'rgba(16, 185, 129, 0.6)', // emerald-500
        },
      ]
    },
    options: {
      responsive: true,
      scales: {
        y: { beginAtZero: true, precision: 0 }
      }
    }
  })
})
</script>

<template>
  <Head title="Estadísticas" />
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-6">
      <h1 class="text-2xl font-semibold mb-6">Pendientes vs Fabricadas (por proyecto)</h1>
      <div class="w-full">
        <canvas ref="canvasRef" height="120"></canvas>
      </div>
    </div>
  </div>
</template>