<script setup>
import { ref, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'

const props = defineProps({
    proyectos: Array,
    bloques: Array,
    piezas: Array
})

const form = useForm({
    proyecto_id: '',
    bloque_id: '',
    pieza_id: '',
    peso_real: ''
})

const pesoTeorico = ref(0)
const diferencia = ref(0)

watch(() => form.pieza_id, (newVal) => {
    const pieza = props.piezas.find(p => p.id === newVal)
    pesoTeorico.value = pieza ? pieza.peso_teorico : 0
    calcularDiferencia()
})

watch(() => form.peso_real, () => {
    calcularDiferencia()
})

const calcularDiferencia = () => {
    diferencia.value = form.peso_real - pesoTeorico.value
}
</script>

<template>
    <AppLayout>
        <Head title="Nuevo Registro" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="form.post(route('registros.store'))">
                        <!-- Proyecto -->
                        <div class="mb-4">
                            <label class="block mb-2">Proyecto</label>
                            <select v-model="form.proyecto_id" class="w-full border rounded">
                                <option value="">Seleccione un proyecto</option>
                                <option v-for="proyecto in proyectos" :key="proyecto.id" :value="proyecto.id">
                                    {{ proyecto.nombre }}
                                </option>
                            </select>
                        </div>

                        <!-- Bloque -->
                        <div class="mb-4">
                            <label class="block mb-2">Bloque</label>
                            <select v-model="form.bloque_id" class="w-full border rounded">
                                <option value="">Seleccione un bloque</option>
                                <option v-for="bloque in bloques" :key="bloque.id" :value="bloque.id">
                                    {{ bloque.nombre }}
                                </option>
                            </select>
                        </div>

                        <!-- Pieza -->
                        <div class="mb-4">
                            <label class="block mb-2">Pieza</label>
                            <select v-model="form.pieza_id" class="w-full border rounded">
                                <option value="">Seleccione una pieza</option>
                                <option v-for="pieza in piezas" :key="pieza.id" :value="pieza.id">
                                    {{ pieza.nombre }}
                                </option>
                            </select>
                        </div>

                        <!-- Peso Real -->
                        <div class="mb-4">
                            <label class="block mb-2">Peso Real</label>
                            <input type="number" v-model="form.peso_real" step="0.01" class="w-full border rounded">
                        </div>

                        <!-- Información de Peso -->
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-2">Peso Teórico</label>
                                <input type="text" :value="pesoTeorico" disabled class="w-full border rounded bg-gray-100">
                            </div>
                            <div>
                                <label class="block mb-2">Diferencia</label>
                                <input type="text" :value="diferencia" disabled class="w-full border rounded bg-gray-100">
                            </div>
                        </div>

                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                            Guardar Registro
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>