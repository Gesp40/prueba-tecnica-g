<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Reportes" />
    <h1 class="text-xl font-semibold mb-4">Reportes</h1>
    <div class="grid gap-4 sm:grid-cols-2">
      <Link href="/reportes/pendientes">
        <div class="font-medium text-gray-900">Piezas pendientes</div>
        <div class="text-sm text-gray-500">Resumen por proyecto.</div>
      </Link>
      <Link href="/reportes/estadisticas">
        <div class="font-medium text-gray-900">Estadísticas</div>
        <div class="text-sm text-gray-500">Pendientes vs Fabricadas por proyecto.</div>
      </Link>
    </div>
  </AuthenticatedLayout>
</template>