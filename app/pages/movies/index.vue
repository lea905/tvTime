<template>
  <div class="space-y-12">
    <header class="space-y-4">
      <h1 class="text-5xl font-black">Films</h1>
      <div class="flex flex-wrap gap-2">
        <button 
          v-for="genre in data?.genres" 
          :key="genre"
          @click="selectedGenre = genre"
          :class="[
            'px-4 py-2 rounded-full text-sm font-medium transition-all',
            selectedGenre === genre ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/40' : 'glass hover:bg-white/10'
          ]"
        >
          {{ genre }}
        </button>
      </div>
    </header>

    <div v-if="pending" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
      <div v-for="i in 10" :key="i" class="animate-pulse space-y-4">
        <div class="aspect-[2/3] bg-white/5 rounded-2xl" />
        <div class="h-4 bg-white/5 rounded w-3/4" />
        <div class="h-3 bg-white/5 rounded w-1/2" />
      </div>
    </div>

    <div v-else class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
      <MediaCard v-for="movie in sortedMovies" :key="movie.id" :item="movie" type="movies" />
    </div>
  </div>
</template>

<script setup>
const selectedGenre = ref('Tout')
const config = useRuntimeConfig()

const { data, pending } = await useFetch(() => `/api/movies?genre=${selectedGenre.value}`, {
  watch: [selectedGenre]
})

watchEffect(() => {
  if (data.value && !selectedGenre.value) {
    selectedGenre.value = data.value.selectedGenre
  }
})

const sortedMovies = computed(() => data.value?.byGenre || [])
</script>
