<script setup>
import { ref, watch, onMounted, computed } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import axios from 'axios'

const page = usePage()
const flashSuccess = computed(() => page.props.flash?.success ?? null)

// Estado de selects
const proyectos = ref([])
const bloques   = ref([])
const piezas    = ref([])

// Formulario
const form = useForm({
  proyecto_id: null,
  bloque_id:   null,
  pieza_id:    null,
  peso_real:   null,
})

// Derivados
const piezaActual = computed(() => piezas.value.find(x => x.id === form.pieza_id) || null)
const pesoTeorico = computed(() => piezaActual.value ? Number(piezaActual.value.peso_teorico) : 0)
const diferencia  = computed(() => {
  const real = Number(form.peso_real || 0)
  const dif  = (pesoTeorico.value - real)
  return isNaN(dif) ? '0.000' : dif.toFixed(3)
})

// Cargas
const cargarProyectos = async () => {
  const { data } = await axios.get('/api/proyectos')
  proyectos.value = data
}

const cargarBloques = async (proyectoId) => {
  bloques.value = []
  piezas.value  = []
  form.bloque_id = null
  form.pieza_id  = null
  if (!proyectoId) return
  const { data } = await axios.get(`/api/proyectos/${proyectoId}/bloques`)
  bloques.value = data
}

const cargarPiezas = async (bloqueId) => {
  piezas.value = []
  form.pieza_id = null
  if (!bloqueId) return
  const { data } = await axios.get(`/api/bloques/${bloqueId}/piezas`, { params: { estado: 'pendiente' } })
  piezas.value = data
}

watch(() => form.proyecto_id, cargarBloques)
watch(() => form.bloque_id,   cargarPiezas)

onMounted(async () => {
  await cargarProyectos()
})

// Validación simple en cliente
const validarCliente = () => {
  const errs = {}
  if (!form.proyecto_id) errs.proyecto_id = 'Selecciona un proyecto.'
  if (!form.bloque_id)   errs.bloque_id   = 'Selecciona un bloque.'
  if (!form.pieza_id)    errs.pieza_id    = 'Selecciona una pieza.'
  const pr = Number(form.peso_real)
  if (isNaN(pr) || pr < 0) errs.peso_real = 'El peso real debe ser un número válido.'
  return errs
}

const submit = () => {
  const errs = validarCliente()
  if (Object.keys(errs).length) {
    form.setError(errs)
    return
  }
  form.post('/registros', {
    preserveScroll: true,
    onSuccess: () => {
      // Mantén proyecto/bloque para agilidad; limpia pieza y peso
      form.pieza_id  = null
      form.peso_real = null
    }
  })
}
</script>

<template>
  <Head title="Registrar pieza" />
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto bg-white shadow rounded-lg p-6">
      <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Registro de fabricación</h1>
        /registrosVer registros</a>
      </div>

      <div v-if="flashSuccess" class="mb-4 rounded bg-emerald-50 text-emerald-800 px-4 py-2">
        {{ flashSuccess }}
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Proyecto -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Proyecto</label>
          <select v-model="form.proyecto_id"
                  class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            <option :value="null" disabled>Selecciona...</option>
            <option v-for="p in proyectos" :key="p.id" :value="p.id">{{ p.nombre }}</option>
          </select>
          <p v-if="form.errors.proyecto_id" class="text-sm text-red-600 mt-1">{{ form.errors.proyecto_id }}</p>
        </div>

        <!-- Bloque -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Bloque</label>
          <select v-model="form.bloque_id"
                  :disabled="!form.proyecto_id"
                  class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100">
            <option :value="null" disabled>Selecciona...</option>
            <option v-for="b in bloques" :key="b.id" :value="b.id">{{ b.nombre }}</option>
          </select>
          <p v-if="form.errors.bloque_id" class="text-sm text-red-600 mt-1">{{ form.errors.bloque_id }}</p>
        </div>

        <!-- Pieza -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Pieza (solo pendientes)</label>
          <select v-model="form.pieza_id"
                  :disabled="!form.bloque_id"
                  class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100">
            <option :value="null" disabled>Selecciona...</option>
            <option v-for="pz in piezas" :key="pz.id" :value="pz.id">
              {{ pz.codigo }} — {{ pz.nombre ?? 'Sin nombre' }}
            </option>
          </select>
          <p v-if="form.errors.pieza_id" class="text-sm text-red-600 mt-1">{{ form.errors.pieza_id }}</p>
        </div>

        <!-- Pesos -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Peso teórico (kg)</label>
            <input type="text" :value="pesoTeorico.toFixed(3)" disabled
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
                  :disabled="form.processing || !form.pieza_id || !form.peso_real"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50">
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</template>