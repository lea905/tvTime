<template>
  <div class="min-h-screen">
    <nav class="fixed top-0 left-0 right-0 z-50 glass m-4 rounded-2xl px-6 py-4 flex items-center justify-between">
      <NuxtLink to="/" class="text-2xl font-bold text-blue-500 hover:text-blue-400 transition-colors">
        TV-TIME
      </NuxtLink>
      
      <div class="hidden md:flex items-center gap-8">
        <NuxtLink to="/" class="nav-link">Accueil</NuxtLink>
        <NuxtLink to="/movies" class="nav-link">Films</NuxtLink>
        <NuxtLink to="/series" class="nav-link">Séries</NuxtLink>
        <NuxtLink to="/watchlist" class="nav-link">Mes listes</NuxtLink>
      </div>

      <div class="flex items-center gap-4">
        <NuxtLink to="/search" class="p-2 hover:bg-white/10 rounded-full transition-colors">
          <LucideSearch class="w-5 h-5" />
        </NuxtLink>
        <button 
          @click="handleSync" 
          :disabled="syncing"
          class="p-2 hover:bg-white/10 rounded-full transition-colors disabled:opacity-50"
          title="Synchroniser avec TMDB"
        >
          <LucideRefreshCw :class="['w-5 h-5', syncing ? 'animate-spin text-blue-500' : '']" />
        </button>
        <template v-if="user">
          <span class="text-sm font-bold text-white/60">Salut, {{ user.name }}</span>
          <button @click="logout" class="bg-white/5 hover:bg-white/10 text-white px-4 py-2 rounded-xl transition-all active:scale-95 text-sm">
            Déconnexion
          </button>
        </template>
        <NuxtLink v-else to="/auth/login" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-xl transition-all shadow-lg shadow-blue-900/20 active:scale-95">
          Connexion
        </NuxtLink>
      </div>
    </nav>

    <main class="pt-32 px-4 md:px-8 pb-20">
      <slot />
    </main>
  </div>
</template>

<style scoped>
.nav-link {
  @apply text-sm font-medium text-white/70 hover:text-white transition-colors relative;
}

.nav-link.router-link-active {
  @apply text-white;
}

.nav-link.router-link-active::after {
  content: '';
  @apply absolute -bottom-1 left-0 right-0 h-0.5 bg-blue-500 rounded-full;
}
</style>

<script setup>
import { Search as LucideSearch, RefreshCw as LucideRefreshCw } from 'lucide-vue-next'
const { user, fetchUser, logout } = useAuth()
const syncing = ref(false)

const handleSync = async () => {
  if (syncing.value) return
  syncing.value = true
  try {
    await $fetch('/api/sync/tmdb?type=movie', { method: 'POST' })
    await $fetch('/api/sync/tmdb?type=tv', { method: 'POST' })
    alert('Synchronisation terminée avec succès !')
    // Refresh the page or current data if needed
    window.location.reload()
  } catch (e) {
    alert('Erreur lors de la synchronisation')
  } finally {
    syncing.value = false
  }
}

onMounted(() => {
  fetchUser()
})
</script>
