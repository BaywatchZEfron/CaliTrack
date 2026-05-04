import '@/assets/main.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import { useAuthStore } from '@/stores/auth'

const app = createApp(App)

app.use(createPinia())
app.use(router)

// Recupera el usuario si ya hay token guardado en localStorage
// Esto permite que al recargar la página la sesión persista
const authStore = useAuthStore()
authStore.fetchUser().finally(() => {
  app.mount('#app')
})
// finally() → monta la app siempre, tanto si fetchUser tiene éxito como si falla
// Sin esto la app no montaría si el backend no responde