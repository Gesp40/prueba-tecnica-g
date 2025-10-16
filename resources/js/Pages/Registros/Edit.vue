<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const registro = page.props.registro

const form = useForm({
  peso_real: Number(registro.peso_real),
})

const pesoTeorico = Number(registro.peso_teorico)
const diferencia  = computed(() => {
  const real = Number(form.peso_real || 0)
  const dif  = (pesoTeorico - real)
  return isNaN(dif) ? '0.000' : dif.toFixed(3)
})

const submit = () => {
  form.put(`/registros/${registro.id}`, { preserveScroll: true })
}
</script>

<template>
  <Head title="Editar registro" />
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Editar registro #{{ registro.id }}</h1>
        /registrosVolver</a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div><span class="text-sm text-gray-500">Proyecto</span><div class="font-medium">{{ registro.proyecto }}</div></div>
        <div><span class="text-sm text-gray-500">Bloque</span><div class="font-medium">{{ registro.bloque }}</div></div>
        <div><span class="text-sm text-gray-500">Pieza</span><div class="font-medium">{{ registro.pieza?.codigo }}</div></div>
        <div><span class="text-sm text-gray-500">Registrado el</span><div class="font-medium">{{ registro.registrado_en }}</div></div>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Peso teórico (kg)</label>
            <input type="text" :value="Number(registro.peso_teorico).toFixed(3)" disabled
                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Peso real (kg)</label>
            <input type="number" step="0.001" min="0" v-model="form.peso_real"
                   class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
            <p v-if="form.errors.peso_real" class="text-sm text-red-600 mt-1">{{ form.errors.peso_real }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Diferencia (kg)</label>
            <input type="text" :value="diferencia" disabled
                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100" />
          </div>
        </div>

        <div class="pt-2">
          <button type="submit"
                  :disabled="form.processing || !form.peso_real"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50">
            Guardar cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</template>