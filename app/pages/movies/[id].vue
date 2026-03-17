<template>
  <div v-if="movie" class="space-y-12">
    <!-- Hero Header -->
    <div class="relative h-[50vh] -mx-4 md:-mx-8 -mt-32 rounded-b-[4rem] overflow-hidden shadow-2xl">
      <img :src="'https://image.tmdb.org/t/p/original' + movie.picture" class="w-full h-full object-cover" />
      <div class="absolute inset-0 bg-gradient-to-t from-[#050505] via-[#050505]/40 to-transparent" />
      
      <div class="absolute bottom-12 left-8 md:left-16 space-y-4 max-w-4xl">
        <div class="flex items-center gap-3">
          <span class="glass px-3 py-1 rounded-lg text-sm font-bold text-blue-400 uppercase tracking-widest">Film</span>
          <div class="flex items-center gap-1 text-yellow-500">
            <LucideStar class="w-5 h-5 fill-current" />
            <span class="font-bold text-lg">{{ movie.rating }}</span>
          </div>
        </div>
        <h1 class="text-6xl font-black">{{ movie.title }}</h1>
        <div class="flex flex-wrap gap-2">
          <span v-for="genre in movie.genres" :key="genre" class="bg-white/10 px-3 py-1 rounded-full text-xs font-medium">
            {{ genre }}
          </span>
        </div>
      </div>
    </div>

    <div class="grid md:grid-cols-3 gap-16">
      <div class="md:col-span-2 space-y-8">
        <section class="space-y-4">
          <h2 class="text-2xl font-bold">Synopsis</h2>
          <p class="text-lg text-white/70 leading-relaxed">{{ movie.description || 'Aucun synopsis disponible.' }}</p>
        </section>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-8 pt-8 border-t border-white/5">
          <div>
            <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Date de sortie</p>
            <p class="font-bold">{{ formatDate(movie.releaseDate) }}</p>
          </div>
          <div>
            <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Durée</p>
            <p class="font-bold">{{ movie.duration }} minutes</p>
          </div>
        </div>
      </div>
      
      <aside class="space-y-6">
        <div class="glass p-8 rounded-[2.5rem] space-y-6 shadow-xl">
          <h3 class="text-xl font-bold">Ma Bibliothèque</h3>
          <p class="text-white/60 text-sm">Organisez vos visionnages et notez vos émotions.</p>
          
          <div class="space-y-3">
            <button class="w-full bg-blue-600 hover:bg-blue-500 text-white py-4 rounded-2xl font-bold transition-all shadow-lg shadow-blue-900/40">
              Marquer comme vu
            </button>
            <button class="w-full glass hover:bg-white/10 text-white py-4 rounded-2xl font-bold transition-all">
              Ajouter à une liste
            </button>
          </div>
        </div>
      </aside>
    </div>
  </div>
  
  <div v-else-if="pending" class="animate-pulse space-y-8">
    <div class="h-[50vh] bg-white/5 rounded-3xl" />
    <div class="h-8 bg-white/5 rounded w-1/4" />
    <div class="h-24 bg-white/5 rounded w-full" />
  </div>
</template>

<script setup>
import { Star as LucideStar } from 'lucide-vue-next'

const route = useRoute()
const config = useRuntimeConfig()

const { data: movie, pending } = await useFetch(`/api/movies/${route.params.id}`)

const formatDate = (dateString) => {
  if (!dateString) return 'Inconnu'
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}
</script>
