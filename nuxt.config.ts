// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  future: { compatibilityVersion: 4 },
  modules: ['@nuxtjs/tailwindcss'],
  css: ['~/assets/css/main.css'],
  app: {
    head: {
      title: 'TV-TIME',
      meta: [
        { name: 'description', content: 'Explorez vos films et séries préférés.' }
      ],
      link: [
        { rel: 'icon', type: 'image/png', href: 'https://www.pngplay.com/wp-content/uploads/6/Film-Icon-Background-PNG-Image.png' }
      ]
    },
    pageTransition: { name: 'page', mode: 'out-in' }
  },
  runtimeConfig: {
    tmdbToken: process.env.TMDB_TOKEN,
    public: {}
  },
  vite: {
    optimizeDeps: {
      include: [
        '@vue/devtools-core',
        '@vue/devtools-kit',
        'lucide-vue-next'
      ]
    }
  }
})
