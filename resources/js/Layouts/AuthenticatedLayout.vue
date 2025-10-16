<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <nav class="px-4 py-2 flex gap-4 text-sm border-b bg-[#093972] text-white">
      <Link href="/registros">Registros</Link>
      <Link href="/reportes">Reportes</Link>
    </nav>

    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
      <slot />
    </main>
  </div>
</template>