import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.ts'],
      refresh: true,
    }),
    vue(),
  ],
  optimizeDeps: {
    include: ['vue'], // Pre-bundle 'vue' dependency
    entries: ['resources/js/app.ts'], // Explicit entry files
  },
  build: {
    rollupOptions: {
      input: {
        main: 'resources/js/app.ts', // Define your entry point explicitly
        style: 'resources/css/app.css',
      },
    },
  },
  resolve: {
    alias: {
      '@': '/resources/js', // Add alias for easier imports (optional)
    },
  },
});