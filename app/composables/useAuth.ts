export const useAuth = () => {
  const user = useState<any>('auth_user', () => null)
  const router = useRouter()

  const fetchUser = async () => {
    try {
      const data = await $fetch('/api/auth/user')
      user.value = data
    } catch {
      user.value = null
    }
  }

  const login = async (credentials: any) => {
    const { user: userData } = await $fetch('/api/auth/login', {
      method: 'POST',
      body: credentials
    })
    user.value = userData
    router.push('/')
  }

  const register = async (formData: any) => {
    const { user: userData } = await $fetch('/api/auth/register', {
      method: 'POST',
      body: formData
    })
    user.value = userData
    router.push('/')
  }

  const logout = async () => {
    await $fetch('/api/auth/logout', { method: 'POST' })
    user.value = null
    router.push('/auth/login')
  }

  return {
    user,
    fetchUser,
    login,
    register,
    logout
  }
}
