<template>
  <div class="space-y-12">
    <header class="space-y-4">
      <h1 class="text-5xl font-black">Recherche</h1>
      <div class="max-w-xl relative">
        <input 
          v-model="query" 
          type="search" 
          placeholder="Rechercher un film ou une série..." 
          class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:ring-2 focus:ring-blue-600 transition-all text-lg"
          @keyup.enter="refresh"
        />
        <LucideSearch class="absolute right-6 top-1/2 -translate-y-1/2 text-white/40 w-6 h-6" />
      </div>
    </header>

    <div v-if="pending" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
      <div v-for="i in 5" :key="i" class="animate-pulse space-y-4">
        <div class="aspect-[2/3] bg-white/5 rounded-2xl" />
      </div>
    </div>

    <div v-else-if="data" class="space-y-12">
      <section v-if="data.movies.length > 0" class="space-y-6">
        <h2 class="text-2xl font-bold">Films ({{ data.movies.length }})</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
          <MediaCard v-for="movie in data.movies" :key="movie.id" :item="movie" type="movie" />
        </div>
      </section>

      <section v-if="data.series.length > 0" class="space-y-6">
        <h2 class="text-2xl font-bold">Séries ({{ data.series.length }})</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
          <MediaCard v-for="serie in data.series" :key="serie.id" :item="serie" type="series" />
        </div>
      </section>

      <div v-if="data.movies.length === 0 && data.series.length === 0 && query" class="text-center py-20 bg-white/5 rounded-[3rem]">
        <p class="text-white/60 text-xl">Aucun résultat pour "<span class="text-white font-bold">{{ query }}</span>"</p>
        <button @click="query = ''" class="mt-4 text-blue-400 hover:underline">Effacer la recherche</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Search as LucideSearch } from 'lucide-vue-next'

const route = useRoute()
const query = ref(route.query.q || '')
const config = useRuntimeConfig()

const { data, pending, refresh } = await useFetch(() => `/api/search?q=${query.value}`, {
  watch: [query],
  debounce: 500
})
</script>
