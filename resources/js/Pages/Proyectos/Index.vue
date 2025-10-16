<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'

defineProps({
    proyectos: Array
})

const formVisible = ref(false)
const form = ref({
    nombre: '',
    descripcion: '',
    estado: 'activo'
})
</script>

<template>
    <AppLayout>
        <Head title="Proyectos" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold">Proyectos</h2>
                        <button
                            @click="formVisible = true"
                            class="bg-blue-500 text-white px-4 py-2 rounded"
                        >
                            Nuevo Proyecto
                        </button>
                    </div>

                    <!-- Tabla de Proyectos -->
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="proyecto in proyectos" :key="proyecto.id">
                                <td>{{ proyecto.nombre }}</td>
                                <td>{{ proyecto.descripcion }}</td>
                                <td>{{ proyecto.estado }}</td>
                                <td>
                                    <button class="text-blue-500">Editar</button>
                                    <button class="text-red-500 ml-2">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>