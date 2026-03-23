<template>
  <div class="space-y-12">
    <header class="flex justify-between items-end">
      <div class="space-y-4">
        <h1 class="text-5xl font-black">Mes listes</h1>
        <p class="text-white/40">Gérez vos collections personnelles de films et séries.</p>
      </div>
      <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-blue-900/40">
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
            {{ (list.movies ? list.movies.length : 0) + (list.series ? list.series.length : 0) }} éléments
          </span>
        </div>

        <div class="flex -space-x-4">
          <img
            v-for="item in [...(list.movies || []), ...(list.series || [])].slice(0, 5)"
            :key="item.id"
            :src="'https://image.tmdb.org/t/p/w200' + item.picture"
            class="w-16 h-24 object-cover rounded-xl border-4 border-[#050505] shadow-lg"
            alt="Poster"
          />
          <div v-if="(list.movies ? list.movies.length : 0) + (list.series ? list.series.length : 0) > 5" class="w-16 h-24 glass rounded-xl border-4 border-[#050505] flex items-center justify-center font-bold text-white/40">
            +{{ ((list.movies ? list.movies.length : 0) + (list.series ? list.series.length : 0)) - 5 }}
          </div>
        </div>

        <!-- Boutons de suppression et édition -->
        <div class="flex gap-2 mt-4 justify-end">
          <button @click.stop="editList(list)" class="px-3 py-1 bg-white/10 hover:bg-white/20 rounded-lg text-sm transition-colors">
            Modifier
          </button>
          <button @click.stop="deleteList(list.id)" class="px-3 py-1 bg-red-500/20 hover:bg-red-500/40 text-red-400 rounded-lg text-sm transition-colors">
            Supprimer
          </button>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-32 glass rounded-[3rem] border-dashed border-2 border-white/10">
      <div class="inline-flex p-6 bg-white/5 rounded-full mb-6">
        <LucidePlus class="w-12 h-12 text-white/20" />
      </div>
      <h2 class="text-2xl font-bold mb-2">Aucune liste pour le moment</h2>
      <p class="text-white/40 mb-8">Commencez par créer votre première liste de visionnage !</p>
      <button @click="showModal = true" class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-4 rounded-2xl font-bold transition-all">
        Créer ma première liste
      </button>
    </div>
  </div>

  <!-- Modal pour créer/modifier une liste -->
  <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeModal"></div>

    <div class="relative glass p-8 rounded-[2.5rem] w-full max-w-md border border-white/10 shadow-2xl">
      <h3 class="text-2xl font-bold mb-6">{{ isEditing ? 'Modifier la liste' : 'Nouvelle liste' }}</h3>

      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-white/60 mb-2">Nom de la liste</label>
          <input
              v-model="newListData.title"
              type="text"
              placeholder="Ex: Mes films d'horreur"
              class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-white/60 mb-2">Description (optionnel)</label>
          <textarea
              v-model="newListData.description"
              placeholder="De quoi parle cette liste ?"
              class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-colors h-24"
          ></textarea>
        </div>

        <div class="flex gap-4 mt-8">
          <button @click="closeModal" class="flex-1 px-6 py-3 rounded-xl font-bold hover:bg-white/5 transition-colors">
            Annuler
          </button>
          <button @click="saveList" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold transition-all">
            {{ isEditing ? 'Enregistrer' : 'Créer' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Plus as LucidePlus } from 'lucide-vue-next'
import { ref } from 'vue'

const { data: watchlists, pending, refresh } = await useFetch('/api/watchlists')

const showModal = ref(false)
const isEditing = ref(false)
const selectedListId = ref(null)

const newListData = ref({
  title: '',
  description: ''
})

const closeModal = () => {
  showModal.value = false
  isEditing.value = false
  selectedListId.value = null
  newListData.value = { title: '', description: '' }
}

const editList = (list) => {
  isEditing.value = true
  selectedListId.value = list.id
  newListData.value = {
    title: list.title,
    description: list.description || ''
  }
  showModal.value = true
}

const saveList = async () => {
  try {
    if (isEditing.value) {
      // Appel PUT pour modifier
      await $fetch(`/api/watchlists/${selectedListId.value}`, {
        method: 'PUT',
        body: newListData.value
      })
    } else {
      // Appel POST pour créer
      await $fetch('/api/watchlists', {
        method: 'POST',
        body: newListData.value
      })
    }

    closeModal()
    await refresh()
  } catch (e) {
    console.error(e)
  }
}

const deleteList = async (id) => {
  if (!confirm("Supprimer cette liste définitivement ?")) return

  try {
    await $fetch(`/api/watchlists/${id}`, { method: 'DELETE' })
    await refresh()
  } catch (e) {
    console.error(e)
  }
}
</script>