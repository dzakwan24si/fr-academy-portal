import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
    plugins: [
        react(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/views/Top_Management/pages/main.jsx',
                'resources/views/Administrasi/Pages/Monitoring_Spesifik/main.jsx',
                'resources/views/Administrasi/Pages/Program/ProgramBatch.jsx',
                'resources/views/Administrasi/Pages/Program/SchedulePage.jsx',
                'resources/views/Administrasi/Pages/Administrasi/Rekrutmen.jsx',
                'resources/views/Administrasi/Pages/Monitoring/Asisten.jsx',
                'resources/views/Administrasi/Pages/Monitoring/MonitoringLulusan.jsx',
                'resources/views/Administrasi/Pages/Monitoring/MonitoringSummarized.jsx',
                'resources/views/Administrasi/Pages/Monitoring/MonitoringAlumni.jsx',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@services': path.resolve(__dirname, './services'),
            '@top': path.resolve(__dirname, './resources/views/Top_Management'),
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
