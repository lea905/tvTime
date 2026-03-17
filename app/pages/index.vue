<template>
  <div class="space-y-16">
    <!-- Hero Section -->
    <section class="relative h-[60vh] flex items-center overflow-hidden rounded-3xl">
      <div class="absolute inset-0 bg-gradient-to-r from-black via-black/60 to-transparent z-10" />
      <img src="https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?q=80&w=2070&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover" />
      
      <div class="relative z-20 max-w-2xl space-y-6">
        <h1 class="text-6xl font-black leading-tight">
          Vos films et séries <br />
          <span class="text-blue-500">en un seul endroit</span>
        </h1>
        <p class="text-xl text-white/60">
          Suivez vos visionnages, créez des listes personnalisées et explorez les dernières sorties avec TV-TIME.
        </p>
        <div class="flex gap-4">
          <NuxtLink to="/movies" class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-4 rounded-2xl font-bold transition-all hover:scale-105 active:scale-95">
            Explorer les films
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Movies Section -->
    <section class="space-y-6">
      <div class="flex justify-between items-end">
        <h2 class="text-3xl font-bold">Films populaires</h2>
        <NuxtLink to="/movies" class="text-blue-400 hover:text-blue-300 text-sm font-medium">Tout voir</NuxtLink>
      </div>
      
      <div class="flex gap-6 overflow-x-auto pb-6 -mx-4 px-4 scrollbar-hide">
        <div v-for="movie in movies" :key="movie.id" class="flex-none w-64 group cursor-pointer">
          <div class="relative aspect-[2/3] rounded-2xl overflow-hidden mb-4 shadow-xl group-hover:shadow-blue-900/20 transition-all group-hover:-translate-y-2">
            <img :src="'https://image.tmdb.org/t/p/w500' + movie.picture" class="w-full h-full object-cover" :alt="movie.title" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
              <button class="bg-white text-black w-full py-2 rounded-xl font-bold text-sm">Détails</button>
            </div>
          </div>
          <h3 class="font-bold text-lg truncate">{{ movie.title }}</h3>
          <p class="text-white/40 text-sm">{{ formatDate(movie.releaseDate) }}</p>
        </div>
      </div>
    </section>

    <!-- Series Section -->
    <section class="space-y-6">
      <div class="flex justify-between items-end">
        <h2 class="text-3xl font-bold">Séries à ne pas manquer</h2>
        <NuxtLink to="/series" class="text-blue-400 hover:text-blue-300 text-sm font-medium">Tout voir</NuxtLink>
      </div>
      
      <div class="flex gap-6 overflow-x-auto pb-6 -mx-4 px-4 scrollbar-hide">
        <div v-for="serie in series" :key="serie.id" class="flex-none w-64 group cursor-pointer">
          <div class="relative aspect-[2/3] rounded-2xl overflow-hidden mb-4 shadow-xl group-hover:shadow-blue-900/20 transition-all group-hover:-translate-y-2">
            <img :src="'https://image.tmdb.org/t/p/w500' + serie.picture" class="w-full h-full object-cover" :alt="serie.title" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
              <button class="bg-white text-black w-full py-2 rounded-xl font-bold text-sm">Détails</button>
            </div>
          </div>
          <h3 class="font-bold text-lg truncate">{{ serie.title }}</h3>
          <p class="text-white/40 text-sm">{{ formatDate(serie.releaseDate) }}</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
const { data: moviesData } = await useFetch('/api/home-data')

const movies = computed(() => moviesData.value?.movies || [])
const series = computed(() => moviesData.value?.series || [])

const formatDate = (dateString) => {
  if (!dateString) return 'Date inconnue'
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}
</script>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
