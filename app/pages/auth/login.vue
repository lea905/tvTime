<template>
  <div class="max-w-md mx-auto py-12">
    <div class="glass p-10 rounded-[3rem] shadow-2xl space-y-8">
      <div class="text-center space-y-2">
        <h1 class="text-4xl font-black">Bon retour !</h1>
        <p class="text-white/40">Connectez-vous pour gérer vos listes.</p>
      </div>

      <form class="space-y-6" @submit.prevent="handleLogin">
        <div class="space-y-2">
          <label class="text-xs font-bold uppercase tracking-widest text-white/40 px-2">Email</label>
          <input 
            v-model="email"
            type="email" 
            placeholder="votre@email.com" 
            class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:ring-2 focus:ring-blue-600 transition-all"
            required
          />
        </div>
        <div class="space-y-2">
          <label class="text-xs font-bold uppercase tracking-widest text-white/40 px-2">Mot de passe</label>
          <input 
            v-model="password"
            type="password" 
            placeholder="••••••••" 
            class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 outline-none focus:ring-2 focus:ring-blue-600 transition-all"
            required
          />
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white py-4 rounded-2xl font-bold transition-all shadow-lg shadow-blue-900/40 active:scale-95">
          Se connecter
        </button>
      </form>

      <div class="text-center pt-4">
        <p class="text-white/40 text-sm">
          Pas encore de compte ? 
          <NuxtLink to="/auth/register" class="text-blue-400 hover:underline font-bold">Inscrivez-vous</NuxtLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
const { login } = useAuth()
const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  try {
    await login({ email: email.value, password: password.value })
  } catch (e) {
    error.value = 'Email ou mot de passe incorrect'
  } finally {
    loading.value = false
  }
}
</script>
