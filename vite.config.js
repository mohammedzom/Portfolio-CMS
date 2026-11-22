import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { google } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Fonts are downloaded at build time and served from /build, so the
            // public site makes no runtime requests to Google Fonts.
            fonts: [
                google('Space Grotesk', {
                    alias: 'face-display',
                    weights: [400, 500, 600, 700],
                    subsets: ['latin'],
                    preload: [{ weight: 600 }],
                    optimizedFallbacks: false,
                    fallbacks: ['Inter', 'system-ui', 'sans-serif'],
                }),
                google('Inter', {
                    alias: 'face-body',
                    weights: [400, 500, 600],
                    subsets: ['latin'],
                    preload: [{ weight: 400 }],
                    optimizedFallbacks: false,
                    fallbacks: ['system-ui', '-apple-system', 'Segoe UI', 'sans-serif'],
                }),
                google('JetBrains Mono', {
                    alias: 'face-mono',
                    weights: [400, 500],
                    subsets: ['latin'],
                    preload: false,
                    optimizedFallbacks: false,
                    fallbacks: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'monospace'],
                }),
            ],
        }),
        tailwindcss(),
    ],
});
