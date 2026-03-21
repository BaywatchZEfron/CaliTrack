import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  // Estado
  const token = ref<string | null>(localStorage.getItem('token'))
  const user = ref<{ id: number; nombre: string; peso_actual: number; nivel: string } | null>(null)

  // Computed
  const isLoggedIn = computed(() => token.value !== null)

  // Acciones
  function login(newToken: string, userData: typeof user.value) {
    token.value = newToken
    user.value = userData
    localStorage.setItem('token', newToken)
  }

  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('token')
  }

  return { token, user, isLoggedIn, login, logout }
})