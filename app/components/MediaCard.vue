<template>
  <NuxtLink :to="`/${type}s/${item.id}`" class="group block">
    <div class="relative aspect-[2/3] rounded-2xl overflow-hidden mb-3 shadow-lg group-hover:shadow-blue-900/30 transition-all group-hover:-translate-y-2">
      <img 
        :src="'https://image.tmdb.org/t/p/w500' + item.picture" 
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" 
        :alt="item.title" 
      />
      <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-4">
        <div class="flex items-center gap-1 text-yellow-500 mb-1">
          <LucideStar class="w-4 h-4 fill-current" />
          <span class="text-sm font-bold">{{ item.rating || 'N/A' }}</span>
        </div>
        <button class="bg-blue-600 text-white w-full py-2 rounded-xl font-bold text-sm shadow-lg shadow-blue-900/40">
          Détails
        </button>
      </div>
      
      <!-- Rating Badge (always visible) -->
      <div class="absolute top-3 right-3 glass px-2 py-1 rounded-lg flex items-center gap-1">
        <LucideStar class="w-3 h-3 text-yellow-500 fill-current" />
        <span class="text-xs font-bold">{{ item.rating || 'N/A' }}</span>
      </div>
    </div>
    <h3 class="font-bold text-lg truncate group-hover:text-blue-400 transition-colors">{{ item.title }}</h3>
    <p class="text-white/40 text-sm">{{ formatDate(item.releaseDate) }}</p>
  </NuxtLink>
</template>

<script setup>
import { Star as LucideStar } from 'lucide-vue-next'

const props = defineProps({
  item: { type: Object, required: true },
  type: { type: String, default: 'movie' }
})

const formatDate = (dateString) => {
  if (!dateString) return 'Date inconnue'
  return new Date(dateString).getFullYear()
}
</script>
