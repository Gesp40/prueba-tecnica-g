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
    <div class="max-w-3xl mx-auto bg-white shadow-sm rounded-lg p-6">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Registro de fabricación</h1>
        <a href="/registros" 
           class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
          Ver registros
        </a>
      </div>

      <!-- Flash message -->
      <div v-if="flashSuccess" 
           class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700">
        {{ flashSuccess }}
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Proyecto -->
        <div>
          <label for="proyecto_id" class="block text-sm font-medium text-gray-700 mb-1">
            Proyecto <span class="text-red-500">*</span>
          </label>
          <select id="proyecto_id"
                  v-model="form.proyecto_id"
                  :disabled="form.processing"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                         focus:border-indigo-500 focus:ring-indigo-500 
                         disabled:opacity-50 disabled:bg-gray-50">
            <option value="">Selecciona proyecto...</option>
            <option v-for="p in proyectos" 
                    :key="p.id" 
                    :value="p.id">
              {{ p.nombre }}
            </option>
          </select>
          <p v-if="form.errors.proyecto_id" 
             class="mt-1 text-sm text-red-600">
            {{ form.errors.proyecto_id }}
          </p>
        </div>

        <!-- Bloque -->
        <div>
          <label for="bloque_id" class="block text-sm font-medium text-gray-700 mb-1">
            Bloque <span class="text-red-500">*</span>
          </label>
          <select id="bloque_id"
                  v-model="form.bloque_id"
                  :disabled="!form.proyecto_id || form.processing"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                         focus:border-indigo-500 focus:ring-indigo-500 
                         disabled:opacity-50 disabled:bg-gray-50">
            <option value="">Selecciona bloque...</option>
            <option v-for="b in bloques" 
                    :key="b.id" 
                    :value="b.id">
              {{ b.nombre }}
            </option>
          </select>
          <p v-if="form.errors.bloque_id" 
             class="mt-1 text-sm text-red-600">
            {{ form.errors.bloque_id }}
          </p>
        </div>

        <!-- Pieza -->
        <div>
          <label for="pieza_id" class="block text-sm font-medium text-gray-700 mb-1">
            Pieza pendiente <span class="text-red-500">*</span>
          </label>
          <select id="pieza_id"
                  v-model="form.pieza_id"
                  :disabled="!form.bloque_id || form.processing"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                         focus:border-indigo-500 focus:ring-indigo-500 
                         disabled:opacity-50 disabled:bg-gray-50">
            <option value="">Selecciona pieza...</option>
            <option v-for="pz in piezas" 
                    :key="pz.id" 
                    :value="pz.id">
              {{ pz.codigo }} — {{ pz.nombre ?? 'Sin nombre' }}
            </option>
          </select>
          <p v-if="form.errors.pieza_id" 
             class="mt-1 text-sm text-red-600">
            {{ form.errors.pieza_id }}
          </p>
        </div>

        <!-- Grid de pesos -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Peso teórico -->
          <div>
            <label for="peso_teorico" 
                   class="block text-sm font-medium text-gray-700 mb-1">
              Peso teórico (kg)
            </label>
            <input id="peso_teorico"
                   type="text" 
                   :value="pesoTeorico.toFixed(3)" 
                   readonly
                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 
                          text-gray-500 shadow-sm" />
          </div>

          <!-- Peso real -->
          <div>
            <label for="peso_real" 
                   class="block text-sm font-medium text-gray-700 mb-1">
              Peso real (kg) <span class="text-red-500">*</span>
            </label>
            <input id="peso_real"
                   type="number" 
                   step="0.001" 
                   min="0"
                   v-model="form.peso_real"
                   :disabled="!form.pieza_id || form.processing"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm 
                          focus:border-indigo-500 focus:ring-indigo-500 
                          disabled:opacity-50 disabled:bg-gray-50" />
            <p v-if="form.errors.peso_real" 
               class="mt-1 text-sm text-red-600">
              {{ form.errors.peso_real }}
            </p>
          </div>

          <!-- Diferencia -->
          <div>
            <label for="diferencia" 
                   class="block text-sm font-medium text-gray-700 mb-1">
              Diferencia (kg)
            </label>
            <input id="diferencia"
                   type="text" 
                   :value="diferencia" 
                   readonly
                   :class="[
                     'mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm font-medium',
                     Number(diferencia) === 0 ? 'text-emerald-600' : 
                     Number(diferencia) > 0 ? 'text-amber-600' : 'text-red-600'
                   ]" />
          </div>
        </div>

        <!-- Submit -->
        <div class="pt-4">
          <button type="submit"
                  :disabled="form.processing || !form.pieza_id || !form.peso_real"
                  class="inline-flex items-center px-4 py-2 border border-transparent
                         text-sm font-medium rounded-md shadow-sm text-white
                         bg-indigo-600 hover:bg-indigo-700 focus:outline-none 
                         focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                         disabled:opacity-50 disabled:cursor-not-allowed">
            <svg v-if="form.processing"
                 class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24">
              <circle class="opacity-25"
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      stroke-width="4"></circle>
              <path class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            {{ form.processing ? 'Guardando...' : 'Guardar registro' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>