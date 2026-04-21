import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import type { User } from '@/types'

export const useAuthStore = defineStore('auth', () => {

  const token = ref<string | null>(localStorage.getItem('auth_token'))
  // 'auth_token' — consistente con lo que guarda api.ts en el interceptor

  const user = ref<User | null>(null)

  const isLoggedIn = computed(() => token.value !== null)

  // ── AUTH ──────────────────────────────────────────────────────────────────

  async function login(email: string, password: string): Promise<void> {
    const response = await api.post('/auth/login', { email, password })
    // api.post → llama a POST /api/auth/login en Laravel

    token.value = response.data.token
    user.value = response.data.user

    localStorage.setItem('auth_token', response.data.token)
    // Guardamos el token para que persista al recargar la página
  }

  async function register(name: string, email: string, password: string): Promise<void> {
    const response = await api.post('/auth/register', {
      name,
      email,
      password,
      password_confirmation: password,
    })

    token.value = response.data.token
    user.value = response.data.user

    localStorage.setItem('auth_token', response.data.token)
  }

  async function logout(): Promise<void> {
    try {
      await api.post('/auth/logout')
      // Intenta invalidar el token en el backend
    } catch {
      // Si falla (token ya expirado, sin conexión), limpiamos igualmente
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('auth_token')
    }
  }

  async function fetchUser(): Promise<void> {
    // Recupera los datos del usuario usando el token guardado
    // Se llama al arrancar la app si ya hay token en localStorage
    if (!token.value) return
    try {
      const response = await api.get('/user')
      user.value = response.data
    } catch {
      // Token inválido o expirado — limpiamos
      token.value = null
      localStorage.removeItem('auth_token')
    }
  }

  async function updateProfile(data: Partial<User>): Promise<void> {
    const response = await api.put('/user/profile', data)
    user.value = response.data
    // Actualiza el usuario en el store con los datos que devuelve la API
  }

  return {
    token,
    user,
    isLoggedIn,
    login,
    register,
    logout,
    fetchUser,
    updateProfile,
  }
})