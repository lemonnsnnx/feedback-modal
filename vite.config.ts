import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  server: {
    proxy: {
      // Все запросы, начинающиеся с /api, будут перенаправляться на OpenServer
      '/api': {
        target: 'http://localhost/ваш_проект', // <--- Укажите ваш путь в OpenServer/XAMPP
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, '')
      }
    }
  }
})