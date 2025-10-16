<?php
// filepath: c:\Users\innovacion\PRUEBA-TECNICA\resources\views\app.blade.php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', 'resources/css/app.css'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia

        <script>
            import './bootstrap';
            import '../css/app.css';

            import { createApp, h } from 'vue';
            import { createInertiaApp } from '@inertiajs/vue3';
            import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
            import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

            const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

            createInertiaApp({
                title: (title) => `${title} - ${appName}`,
                resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
                setup({ el, App, props, plugin }) {
                    return createApp({ render: () => h(App, props) })
                        .use(plugin)
                        .use(ZiggyVue)
                        .mount(el);
                },
                progress: {
                    color: '#4B5563',
                },
            });
        </script>
    </body>
</html>
