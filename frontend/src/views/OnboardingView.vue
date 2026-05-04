<template>
  <div class="onboarding-page">
    <div class="ob-bg" />
    <div class="ob-grid" />

    <div class="ob-box">
      <!-- Header -->
      <div class="ob-header">
        <div class="ob-logo">
          <div class="logo-mark">CT</div>
          <div class="logo-text">Cali<span>Track</span></div>
        </div>
        <div class="ob-step-counter">{{ stepActual + 1 }} / {{ totalSteps }}</div>
      </div>

      <!-- Barra de progreso -->
      <div class="ob-progress">
        <div
          class="ob-progress-fill"
          :style="{ width: ((stepActual + 1) / totalSteps * 100) + '%' }"
        />
      </div>

      <!-- Paso 0 — Datos personales -->
      <div v-if="stepActual === 0" class="ob-step">
        <h2 class="ob-title">Sobre ti</h2>
        <p class="ob-sub">Configura tu perfil para personalizar tu experiencia.</p>

        <div class="field-row">
          <div class="field-group">
            <label class="field-label">Nombre</label>
            <input
              v-model="form.nombre"
              type="text"
              class="field-input"
              :class="{ error: errores.nombre }"
              placeholder="Javier"
            />
            <span v-if="errores.nombre" class="error-msg">{{ errores.nombre }}</span>
          </div>
          <div class="field-group">
            <label class="field-label">Apellido</label>
            <input
              v-model="form.apellido"
              type="text"
              class="field-input"
              placeholder="Díaz"
            />
          </div>
        </div>

        <div class="field-row">
          <div class="field-group">
            <label class="field-label">Peso actual (kg)</label>
            <input
              v-model.number="form.peso"
              type="number"
              step="0.1"
              class="field-input"
              :class="{ error: errores.peso }"
              placeholder="70"
            />
            <span v-if="errores.peso" class="error-msg">{{ errores.peso }}</span>
          </div>
          <div class="field-group">
            <label class="field-label">Altura (cm)</label>
            <input
              v-model.number="form.altura"
              type="number"
              class="field-input"
              :class="{ error: errores.altura }"
              placeholder="175"
            />
            <span v-if="errores.altura" class="error-msg">{{ errores.altura }}</span>
          </div>
        </div>

        <div class="field-group">
          <label class="field-label">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="field-input"
            :class="{ error: errores.email }"
            placeholder="javier@ejemplo.com"
          />
          <span v-if="errores.email" class="error-msg">{{ errores.email }}</span>
        </div>

        <div class="field-row">
          <div class="field-group">
            <label class="field-label">Contraseña</label>
            <input
              v-model="form.password"
              type="password"
              class="field-input"
              :class="{ error: errores.password }"
              placeholder="••••••••"
            />
            <span v-if="errores.password" class="error-msg">{{ errores.password }}</span>
          </div>
          <div class="field-group">
            <label class="field-label">Confirmar contraseña</label>
            <input
              v-model="form.passwordConfirm"
              type="password"
              class="field-input"
              :class="{ error: errores.passwordConfirm }"
              placeholder="••••••••"
            />
            <span v-if="errores.passwordConfirm" class="error-msg">{{ errores.passwordConfirm }}</span>
          </div>
        </div>
      </div>

      <!-- Paso 1 — Nivel -->
      <div v-if="stepActual === 1" class="ob-step">
        <h2 class="ob-title">Tu nivel</h2>
        <p class="ob-sub">Esto determina cómo interpretamos tu progresión y calculamos tu score.</p>

        <div class="choices-list">
          <div
            v-for="n in niveles"
            :key="n.val"
            class="choice-item"
            :class="{ active: form.nivel === n.val }"
            @click="form.nivel = n.val"
          >
            <div class="choice-icon">{{ n.icon }}</div>
            <div class="choice-body">
              <div class="choice-title">{{ n.val }}</div>
              <div class="choice-sub">{{ n.desc }}</div>
            </div>
            <div class="choice-check" :class="{ visible: form.nivel === n.val }">✓</div>
          </div>
        </div>
      </div>

      <!-- Paso 2 — Objetivo -->
      <div v-if="stepActual === 2" class="ob-step">
        <h2 class="ob-title">Tu objetivo</h2>
        <p class="ob-sub">Define tu meta principal para los próximos 3 meses.</p>

        <div class="choices-list">
          <div
            v-for="obj in objetivos"
            :key="obj.val"
            class="choice-item"
            :class="{ active: form.objetivo === obj.val }"
            @click="form.objetivo = obj.val"
          >
            <div class="choice-icon">{{ obj.icon }}</div>
            <div class="choice-body">
              <div class="choice-title">{{ obj.label }}</div>
              <div class="choice-sub">{{ obj.desc }}</div>
            </div>
            <div class="choice-check" :class="{ visible: form.objetivo === obj.val }">✓</div>
          </div>
        </div>
      </div>

      <!-- Paso 3 — Ejercicios principales -->
      <div v-if="stepActual === 3" class="ob-step">
        <h2 class="ob-title">Tus ejercicios</h2>
        <p class="ob-sub">Selecciona los ejercicios que quieres trackear. Puedes añadir más después.</p>

        <div class="ejercicios-grid">
          <div
            v-for="ex in ejerciciosDisponibles"
            :key="ex.nombre"
            class="ex-choice"
            :class="{ active: form.ejercicios.includes(ex.nombre) }"
            @click="toggleEjercicio(ex.nombre)"
          >
            <div class="ex-choice-icon">{{ ex.icon }}</div>
            <div class="ex-choice-name">{{ ex.nombre }}</div>
            <div class="ex-choice-tipo">{{ ex.tipo }}</div>
          </div>
        </div>

        <div v-if="errores.ejercicios" class="error-msg" style="margin-top: 8px">
          {{ errores.ejercicios }}
        </div>
      </div>

      <!-- Paso 4 — Resumen -->
      <div v-if="stepActual === 4" class="ob-step">
        <h2 class="ob-title">Todo listo</h2>
        <p class="ob-sub">Confirma tu información antes de empezar.</p>

        <div class="resumen-card">
          <div class="resumen-avatar">{{ iniciales }}</div>
          <div class="resumen-nombre">{{ form.nombre }} {{ form.apellido }}</div>
          <div class="resumen-email">{{ form.email }}</div>
        </div>

        <div class="resumen-grid">
          <div class="resumen-item">
            <div class="resumen-label">Peso</div>
            <div class="resumen-val">{{ form.peso }} kg</div>
          </div>
          <div class="resumen-item">
            <div class="resumen-label">Altura</div>
            <div class="resumen-val">{{ form.altura }} cm</div>
          </div>
          <div class="resumen-item">
            <div class="resumen-label">Nivel</div>
            <div class="resumen-val">{{ form.nivel }}</div>
          </div>
          <div class="resumen-item">
            <div class="resumen-label">Objetivo</div>
            <div class="resumen-val">{{ labelObjetivo }}</div>
          </div>
        </div>

        <div class="resumen-ejercicios">
          <div class="resumen-label" style="margin-bottom: 8px">Ejercicios seleccionados</div>
          <div class="resumen-pills">
            <span
              v-for="ex in form.ejercicios"
              :key="ex"
              class="resumen-pill"
            >
              {{ ex }}
            </span>
          </div>
        </div>
      </div>

      <!-- Navegación -->
      <div class="ob-nav">
        <button
          v-if="stepActual > 0"
          class="btn-back"
          @click="stepActual--"
        >
          ← Atrás
        </button>
        <button
          v-else
          class="btn-back"
          @click="router.push('/')"
        >
          ← Inicio
        </button>

        <button
          v-if="stepActual < totalSteps - 1"
          class="btn-next"
          @click="siguiente"
        >
          Continuar →
        </button>
        <button
          v-else
          class="btn-next"
          :disabled="cargando"
          @click="finalizar"
        >
          {{ cargando ? 'Creando cuenta...' : 'Empezar →' }}
        </button>
      </div>

    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const stepActual = ref(0)
const totalSteps = 5
const cargando = ref(false)

// Formulario completo — sessionStorage para persistir entre pasos
// si el usuario recarga accidentalmente
const formGuardado = sessionStorage.getItem('ob-form')
const form = reactive({
  nombre: '',
  apellido: '',
  email: '',
  password: '',
  passwordConfirm: '',
  peso: null as number | null,
  altura: null as number | null,
  nivel: 'Intermedio',
  objetivo: 'fuerza',
  ejercicios: [] as string[],
  ...(formGuardado ? JSON.parse(formGuardado) : {}),
})

const errores = reactive({
  nombre: '',
  peso: '',
  altura: '',
  email: '',
  password: '',
  passwordConfirm: '',
  ejercicios: '',
})

const niveles = [
  { val: 'Principiante', icon: '🌱', desc: 'Menos de 6 meses entrenando calistenia' },
  { val: 'Intermedio',   icon: '⚡', desc: '6 meses a 2 años — dominas los básicos' },
  { val: 'Avanzado',     icon: '🔥', desc: 'Más de 2 años, habilidades de fuerza' },
]

const objetivos = [
  { val: 'fuerza',      icon: '💪', label: 'Ganar fuerza',  desc: 'Aumentar repeticiones y cargas máximas' },
  { val: 'peso',        icon: '⚖️', label: 'Bajar peso',    desc: 'Reducir peso corporal manteniendo fuerza' },
  { val: 'habilidades', icon: '🤸', label: 'Habilidades',   desc: 'Muscle-up, handstand, front lever...' },
]

const ejerciciosDisponibles = [
  { nombre: 'Dominadas',    icon: '🏋️', tipo: 'Reps' },
  { nombre: 'Fondos',       icon: '💪', tipo: 'Reps' },
  { nombre: 'Handstand',    icon: '🤸', tipo: 'Tiempo' },
  { nombre: 'Muscle-up',    icon: '🔥', tipo: 'Reps' },
  { nombre: 'Pistol squat', icon: '🦵', tipo: 'Reps' },
  { nombre: 'L-sit',        icon: '🧘', tipo: 'Tiempo' },
  { nombre: 'Front lever',  icon: '⭐', tipo: 'Tiempo' },
  { nombre: 'Push-ups',     icon: '👐', tipo: 'Reps' },
]

const iniciales = computed(() =>
  (form.nombre.slice(0, 1) + form.apellido.slice(0, 1)).toUpperCase()
)

const labelObjetivo = computed(() =>
  objetivos.find(o => o.val === form.objetivo)?.label ?? ''
)

function toggleEjercicio(nombre: string) {
  const idx = form.ejercicios.indexOf(nombre)
  if (idx === -1) {
    form.ejercicios.push(nombre)
  } else {
    form.ejercicios.splice(idx, 1)
  }
}

// Validación por paso
function validarStep(): boolean {
  // Limpiar errores
  Object.keys(errores).forEach(k => {
    (errores as Record<string, string>)[k] = ''
  })

  if (stepActual.value === 0) {
    if (!form.nombre.trim()) {
      errores.nombre = 'El nombre es obligatorio'
      return false
    }
    if (!form.peso || form.peso < 30 || form.peso > 300) {
      errores.peso = 'Introduce un peso válido'
      return false
    }
    if (!form.altura || form.altura < 100 || form.altura > 250) {
      errores.altura = 'Introduce una altura válida'
      return false
    }
    if (!form.email || !form.email.includes('@')) {
      errores.email = 'Introduce un email válido'
      return false
    }
    if (!form.password || form.password.length < 6) {
      errores.password = 'Mínimo 6 caracteres'
      return false
    }
    if (form.password !== form.passwordConfirm) {
      errores.passwordConfirm = 'Las contraseñas no coinciden'
      return false
    }
  }

  if (stepActual.value === 3) {
    if (form.ejercicios.length === 0) {
      errores.ejercicios = 'Selecciona al menos un ejercicio'
      return false
    }
  }

  return true
}

function siguiente() {
  if (!validarStep()) return

  // Guardar progreso en sessionStorage
  sessionStorage.setItem('ob-form', JSON.stringify(form))
  stepActual.value++
}

async function finalizar() {
  cargando.value = true

  try {
    await authStore.register(form.nombre, form.email, form.password)

    const nivelMap: Record<string, string> = {
      'Principiante': 'beginner',
      'Intermedio': 'intermediate',
      'Avanzado': 'advanced',
    }
    const goalMap: Record<string, string> = {
      'fuerza': 'strength',
      'peso': 'weight_loss',
      'habilidades': 'skill',
    }

    await authStore.updateProfile({
      weight_kg: form.peso ?? undefined,
      height_cm: form.altura ?? undefined,
      level: (nivelMap[form.nivel] as 'beginner' | 'intermediate' | 'advanced') ?? undefined,
      goal: (goalMap[form.objetivo] as 'strength' | 'weight_loss' | 'skill') ?? undefined,
    })

    sessionStorage.removeItem('ob-form')
    router.push('/dashboard')

  } catch (err: any) {
    // Muestra el error de Laravel si existe
    const msg = err?.response?.data?.message ?? 'Error al crear la cuenta'
    alert(msg)  // puedes cambiarlo por un ref visible en la UI si quieres
  } finally {
    cargando.value = false
  }
}
</script>

<style scoped>
.onboarding-page {
  min-height: 100vh;
  background: #050709;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  padding: 20px;
}

.ob-bg {
  position: absolute; inset: 0;
  background: linear-gradient(160deg, rgba(30,80,180,0.15) 0%, transparent 60%);
}
.ob-grid {
  position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(61,142,248,0.03) 1px, transparent 1px),
    linear-gradient(90deg, rgba(61,142,248,0.03) 1px, transparent 1px);
  background-size: 48px 48px;
}

.ob-box {
  position: relative;
  z-index: 2;
  width: 100%;
  max-width: 520px;
  background: var(--bg2);
  border: 1px solid var(--border2);
  border-radius: 20px;
  padding: 36px;
}

/* Header */
.ob-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}
.ob-logo { display: flex; align-items: center; gap: 10px; }
.logo-mark {
  width: 30px; height: 30px;
  background: var(--accent);
  border-radius: 7px;
  display: flex; align-items: center; justify-content: center;
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 12px;
  color: #fff;
}
.logo-text { font-family: var(--font-head); font-weight: 700; font-size: 15px; color: var(--text); }
.logo-text span { color: var(--accent); }
.ob-step-counter { font-size: 12px; color: var(--text2); }

/* Progress bar */
.ob-progress {
  height: 3px;
  background: var(--bg4);
  border-radius: 2px;
  overflow: hidden;
  margin-bottom: 28px;
}
.ob-progress-fill {
  height: 100%;
  background: var(--accent);
  border-radius: 2px;
  transition: width 0.4s ease;
}

/* Step content */
.ob-step { margin-bottom: 28px; }
.ob-title {
  font-family: var(--font-head);
  font-size: 22px;
  font-weight: 800;
  color: var(--text);
  margin-bottom: 6px;
  letter-spacing: -0.5px;
}
.ob-sub { font-size: 13px; color: var(--text2); margin-bottom: 20px; line-height: 1.5; }

/* Campos */
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
.field-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
.field-group:last-child { margin-bottom: 0; }
.field-label { font-size: 12px; color: var(--text2); font-weight: 500; }
.field-input {
  background: var(--bg3);
  border: 1px solid var(--border2);
  border-radius: 8px;
  padding: 10px 12px;
  color: var(--text);
  font-family: var(--font-body);
  font-size: 13px;
  width: 100%;
  transition: border-color 0.15s;
}
.field-input:focus { outline: none; border-color: var(--accent); }
.field-input.error { border-color: var(--red); }
.error-msg { font-size: 11px; color: var(--red); }

/* Choices */
.choices-list { display: flex; flex-direction: column; gap: 8px; }
.choice-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 16px;
  background: var(--bg3);
  border: 1px solid var(--border);
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.15s;
}
.choice-item:hover { border-color: var(--border2); }
.choice-item.active { border-color: var(--accent); background: rgba(61,142,248,0.05); }
.choice-icon { font-size: 20px; width: 28px; text-align: center; flex-shrink: 0; }
.choice-title { font-size: 14px; font-weight: 500; color: var(--text); }
.choice-sub { font-size: 12px; color: var(--text2); margin-top: 2px; }
.choice-check {
  margin-left: auto;
  width: 20px; height: 20px;
  border-radius: 50%;
  background: var(--accent);
  color: #fff;
  font-size: 11px;
  display: flex; align-items: center; justify-content: center;
  opacity: 0;
  transition: opacity 0.15s;
  flex-shrink: 0;
}
.choice-check.visible { opacity: 1; }

/* Ejercicios grid */
.ejercicios-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
}
.ex-choice {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  padding: 16px 12px;
  background: var(--bg3);
  border: 1px solid var(--border);
  border-radius: 10px;
  cursor: pointer;
  text-align: center;
  transition: all 0.15s;
}
.ex-choice:hover { border-color: var(--border2); }
.ex-choice.active { border-color: var(--accent); background: rgba(61,142,248,0.05); }
.ex-choice-icon { font-size: 24px; }
.ex-choice-name { font-size: 13px; font-weight: 500; color: var(--text); }
.ex-choice-tipo { font-size: 11px; color: var(--text2); }

/* Resumen */
.resumen-card {
  text-align: center;
  background: var(--bg3);
  border-radius: 14px;
  padding: 20px;
  margin-bottom: 16px;
}
.resumen-avatar {
  width: 56px; height: 56px;
  border-radius: 50%;
  background: var(--accent-dim);
  border: 2px solid var(--accent);
  display: flex; align-items: center; justify-content: center;
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 20px;
  color: var(--accent);
  margin: 0 auto 10px;
}
.resumen-nombre { font-family: var(--font-head); font-size: 16px; font-weight: 700; color: var(--text); }
.resumen-email { font-size: 12px; color: var(--text2); margin-top: 4px; }
.resumen-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
  margin-bottom: 14px;
}
.resumen-item {
  background: var(--bg3);
  border-radius: 8px;
  padding: 10px 12px;
}
.resumen-label { font-size: 10px; color: var(--text3); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px; }
.resumen-val { font-size: 14px; font-weight: 500; color: var(--text); }
.resumen-ejercicios { background: var(--bg3); border-radius: 10px; padding: 14px; }
.resumen-pills { display: flex; gap: 6px; flex-wrap: wrap; }
.resumen-pill {
  background: var(--accent-dim);
  color: var(--accent);
  border-radius: 6px;
  padding: 4px 10px;
  font-size: 12px;
}

/* Navegación */
.ob-nav {
  display: flex;
  gap: 10px;
}
.btn-back {
  padding: 12px 18px;
  background: transparent;
  border: 1px solid var(--border2);
  border-radius: 10px;
  color: var(--text2);
  font-family: var(--font-body);
  font-size: 14px;
  cursor: pointer;
  transition: all 0.15s;
}
.btn-back:hover { background: var(--bg3); color: var(--text); }
.btn-next {
  flex: 1;
  padding: 12px;
  background: var(--accent);
  color: #fff;
  border: none;
  border-radius: 10px;
  font-family: var(--font-body);
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s;
}
.btn-next:hover:not(:disabled) { background: var(--accent2); }
.btn-next:disabled { opacity: 0.6; cursor: not-allowed; }

@media (max-width: 560px) {
  .ob-box { padding: 24px 20px; }
  .field-row { grid-template-columns: 1fr; }
  .ejercicios-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>