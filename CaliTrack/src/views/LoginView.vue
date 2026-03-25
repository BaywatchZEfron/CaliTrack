<template>
  <div class="login-page">
    <div class="login-bg" />
    <div class="login-grid" />

    <div class="login-box">
      <!-- Logo -->
      <div class="login-logo">
        <div class="logo-mark">CT</div>
        <div class="logo-text">Cali<span>Track</span></div>
      </div>

      <h2 class="login-title">Bienvenido de nuevo</h2>
      <p class="login-sub">Introduce tus credenciales para continuar</p>

      <div class="login-form">
        <div class="field-group">
          <label class="field-label">Email</label>
          <input
            v-model="email"
            type="email"
            class="field-input"
            placeholder="javier@ejemplo.com"
            :class="{ error: errores.email }"
          />
          <span v-if="errores.email" class="error-msg">{{ errores.email }}</span>
        </div>

        <div class="field-group">
          <label class="field-label">Contraseña</label>
          <input
            v-model="password"
            :type="mostrarPassword ? 'text' : 'password'"
            class="field-input"
            placeholder="••••••••"
            :class="{ error: errores.password }"
          />
          <span
            class="toggle-pass"
            @click="mostrarPassword = !mostrarPassword"
          >
            {{ mostrarPassword ? 'Ocultar' : 'Mostrar' }}
          </span>
          <span v-if="errores.password" class="error-msg">{{ errores.password }}</span>
        </div>

        <div v-if="errorGeneral" class="error-general">
          {{ errorGeneral }}
        </div>

        <button class="submit-btn" :disabled="cargando" @click="handleLogin">
          <span v-if="cargando">Entrando...</span>
          <span v-else>Entrar</span>
        </button>

        <div class="login-footer">
          ¿No tienes cuenta?
          <RouterLink to="/onboarding" class="login-link">Regístrate gratis</RouterLink>
        </div>

        <!-- Acceso rápido demo -->
        <button class="demo-btn" @click="loginDemo">
          Entrar con cuenta demo
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const mostrarPassword = ref(false)
const cargando = ref(false)
const errorGeneral = ref('')

const errores = reactive({ email: '', password: '' })

function validar(): boolean {
  errores.email = ''
  errores.password = ''

  if (!email.value) {
    errores.email = 'El email es obligatorio'
    return false
  }
  if (!email.value.includes('@')) {
    errores.email = 'Introduce un email válido'
    return false
  }
  if (!password.value || password.value.length < 6) {
    errores.password = 'La contraseña debe tener al menos 6 caracteres'
    return false
  }
  return true
}

async function handleLogin() {
  if (!validar()) return

  cargando.value = true
  errorGeneral.value = ''

  try {
    // Simulamos la llamada a la API con un timeout
    // Cuando tengamos Laravel, esto será: await api.post('/auth/login', {...})
    await new Promise(resolve => setTimeout(resolve, 800))

    // Login simulado — credenciales de demo
    if (email.value === 'demo@calitrack.com' && password.value === '123456') {
      authStore.login('fake-token-123', {
        id: 1,
        nombre: 'Javier',
        peso_actual: 68,
        nivel: 'Intermedio',
      })
      router.push('/dashboard')
    } else {
      errorGeneral.value = 'Email o contraseña incorrectos'
    }
  } finally {
    cargando.value = false
  }
}

function loginDemo() {
  authStore.login('fake-token-123', {
    id: 1,
    nombre: 'Javier',
    peso_actual: 68,
    nivel: 'Intermedio',
  })
  router.push('/dashboard')
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  background: #050709;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.login-bg {
  position: absolute; inset: 0;
  background: linear-gradient(160deg, rgba(30,80,180,0.15) 0%, transparent 60%);
}
.login-grid {
  position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(61,142,248,0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(61,142,248,0.03) 1px, transparent 1px);
  background-size: 48px 48px;
}

.login-box {
  position: relative;
  z-index: 2;
  width: 100%;
  max-width: 400px;
  background: var(--bg2);
  border: 1px solid var(--border2);
  border-radius: 20px;
  padding: 36px;
  margin: 20px;
}

.login-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 28px;
}
.logo-mark {
  width: 32px; height: 32px;
  background: var(--accent);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 13px;
  color: #fff;
}
.logo-text {
  font-family: var(--font-head);
  font-weight: 700;
  font-size: 16px;
  color: var(--text);
}
.logo-text span { color: var(--accent); }

.login-title {
  font-family: var(--font-head);
  font-size: 22px;
  font-weight: 800;
  color: var(--text);
  margin-bottom: 6px;
  letter-spacing: -0.5px;
}
.login-sub {
  font-size: 13px;
  color: var(--text2);
  margin-bottom: 28px;
}

.login-form { display: flex; flex-direction: column; gap: 14px; }

.field-group { display: flex; flex-direction: column; gap: 6px; position: relative; }
.field-label { font-size: 12px; color: var(--text2); font-weight: 500; }
.field-input {
  background: var(--bg3);
  border: 1px solid var(--border2);
  border-radius: 8px;
  padding: 11px 12px;
  color: var(--text);
  font-family: var(--font-body);
  font-size: 13px;
  width: 100%;
  transition: border-color 0.15s;
}
.field-input:focus { outline: none; border-color: var(--accent); }
.field-input.error { border-color: var(--red); }

.toggle-pass {
  position: absolute;
  right: 12px;
  top: 34px;
  font-size: 11px;
  color: var(--accent);
  cursor: pointer;
}

.error-msg { font-size: 11px; color: var(--red); }
.error-general {
  background: rgba(240,82,82,0.1);
  border: 1px solid rgba(240,82,82,0.2);
  border-radius: 8px;
  padding: 10px 12px;
  font-size: 13px;
  color: var(--red);
}

.submit-btn {
  width: 100%;
  padding: 13px;
  background: var(--accent);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-family: var(--font-body);
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s;
  margin-top: 4px;
}
.submit-btn:hover:not(:disabled) { background: var(--accent2); }
.submit-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.login-footer {
  text-align: center;
  font-size: 13px;
  color: var(--text2);
}
.login-link { color: var(--accent); text-decoration: none; margin-left: 4px; }
.login-link:hover { text-decoration: underline; }

.demo-btn {
  width: 100%;
  padding: 11px;
  background: transparent;
  border: 1px solid var(--border2);
  border-radius: 10px;
  color: var(--text2);
  font-family: var(--font-body);
  font-size: 13px;
  cursor: pointer;
  transition: all 0.15s;
}
.demo-btn:hover { background: var(--bg3); color: var(--text); }
</style>