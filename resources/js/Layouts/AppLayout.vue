<template>
    <nav>
        <Link
            :href="route('dashboard')"
            :class="{ active: route().current('dashboard') }"
        >
            Dashboard
        </Link>
        <Link
            :href="route('proyectos.index')"
            :class="{ active: route().current('proyectos.*') }"
        >
            Proyectos
        </Link>
        <Link
            :href="route('registros.create')"
            :class="{ active: route().current('registros.*') }"
        >
            Nuevo Registro
        </Link>
    </nav>
</template>
