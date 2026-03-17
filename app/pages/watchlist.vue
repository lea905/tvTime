<template>
  <div class="space-y-12">
    <header class="flex justify-between items-end">
      <div class="space-y-4">
        <h1 class="text-5xl font-black">Mes listes</h1>
        <p class="text-white/40">Gérez vos collections personnelles de films et séries.</p>
      </div>
      <button class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-blue-900/40">
        Nouvelle liste
      </button>
    </header>

    <div v-if="pending" class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <div v-for="i in 2" :key="i" class="animate-pulse glass p-8 rounded-[2.5rem] h-64" />
    </div>

    <div v-else-if="watchlists && watchlists.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <div v-for="list in watchlists" :key="list.id" class="glass p-8 rounded-[2.5rem] hover:bg-white/[0.07] transition-all group">
        <div class="flex justify-between items-start mb-6">
          <div>
            <h2 class="text-2xl font-bold group-hover:text-blue-400 transition-colors">{{ list.title }}</h2>
            <p class="text-white/40 text-sm mt-1">{{ list.description || 'Aucune description' }}</p>
          </div>
          <span class="bg-blue-600/20 text-blue-400 px-3 py-1 rounded-full text-xs font-bold">
            {{ list.movies.length + list.series.length }} éléments
          </span>
        </div>

        <div class="flex -space-x-4">
          <img 
            v-for="item in [...list.movies, ...list.series].slice(0, 5)" 
            :key="item.id"
            :src="'https://image.tmdb.org/t/p/w200' + item.picture"
            class="w-16 h-24 object-cover rounded-xl border-4 border-[#050505] shadow-lg"
          />
          <div v-if="list.movies.length + list.series.length > 5" class="w-16 h-24 glass rounded-xl border-4 border-[#050505] flex items-center justify-center font-bold text-white/40">
            +{{ (list.movies.length + list.series.length) - 5 }}
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-32 glass rounded-[3rem] border-dashed border-2 border-white/10">
      <div class="inline-flex p-6 bg-white/5 rounded-full mb-6">
        <LucidePlus class="w-12 h-12 text-white/20" />
      </div>
      <h2 class="text-2xl font-bold mb-2">Aucune liste pour le moment</h2>
      <p class="text-white/40 mb-8">Commencez par créer votre première liste de visionnage !</p>
      <button class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-4 rounded-2xl font-bold transition-all">
        Créer ma première liste
      </button>
    </div>
  </div>
</template>

<script setup>
import { Plus as LucidePlus } from 'lucide-vue-next'

const config = useRuntimeConfig()
const { data: watchlists, pending, error } = await useFetch('/api/watchlists')

// Note: Authentication will be needed for this to work properly.
// Currently returns mock data or 401 based on Symfony session.
</script>
