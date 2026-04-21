import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://backend.test/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Interceptor de request — añade el token en cada petición automáticamente
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
    // Así no tienes que añadir el header manualmente en cada llamada
  }
  return config
})

// Interceptor de response — si el token expira, cierra sesión automáticamente
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      window.location.href = '/login'
      // 401 = no autorizado → token inválido o expirado
      // Redirige al login automáticamente
    }
    return Promise.reject(error)
  }
)

export default api