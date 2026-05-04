<template>
  <AppLayout>
    <div class="profile-page">

      <div class="profile-grid">

        <!-- Tarjeta de identidad -->
        <div class="card identity-card">
          <div class="avatar">{{ initials }}</div>
          <div class="identity-name">{{ user?.name ?? '—' }}</div>
          <div class="identity-email">{{ user?.email ?? '—' }}</div>
          <div class="identity-badge">{{ user?.level ?? 'Sin nivel' }}</div>
        </div>

        <!-- Formulario de edición -->
        <div class="card">
          <div class="card-title" style="margin-bottom: 20px">Editar perfil</div>

          <div class="form-grid">
            <div class="field-group">
              <label class="field-label">Edad</label>
              <input v-model.number="form.age" type="number" class="field-input" placeholder="25" />
            </div>
            <div class="field-group">
              <label class="field-label">Peso (kg)</label>
              <input v-model.number="form.weight_kg" type="number" step="0.1" class="field-input" placeholder="70" />
            </div>
            <div class="field-group">
              <label class="field-label">Altura (cm)</label>
              <input v-model.number="form.height_cm" type="number" class="field-input" placeholder="175" />
            </div>
            <div class="field-group">
              <label class="field-label">Nivel</label>
              <select v-model="form.level" class="field-input">
                <option value="beginner">Principiante</option>
                <option value="intermediate">Intermedio</option>
                <option value="advanced">Avanzado</option>
              </select>
            </div>
            <div class="field-group full-width">
              <label class="field-label">Objetivo</label>
              <select v-model="form.goal" class="field-input">
                <option value="strength">Fuerza</option>
                <option value="endurance">Resistencia</option>
                <option value="weight_loss">Pérdida de peso</option>
                <option value="skill">Habilidades</option>
              </select>
            </div>
          </div>

          <div class="form-actions">
            <button class="save-btn" :disabled="isSaving" @click="handleSave">
              {{ isSaving ? 'Guardando...' : 'Guardar cambios' }}
            </button>
            <div v-if="saved" class="saved-msg">✓ Perfil actualizado</div>
          </div>
        </div>

      </div>

      <!-- Stats rápidos -->
      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-label">Peso actual</div>
          <div class="stat-val">{{ user?.weight_kg ?? '—' }}<span class="stat-unit"> kg</span></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Altura</div>
          <div class="stat-val">{{ user?.height_cm ?? '—' }}<span class="stat-unit"> cm</span></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Edad</div>
          <div class="stat-val">{{ user?.age ?? '—' }}<span class="stat-unit"> años</span></div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Objetivo</div>
          <div class="stat-val">{{ goalLabel }}</div>
        </div>
      </div>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import AppLayout from '@/components/layout/AppLayout.vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const user = computed(() => authStore.user)

const isSaving = ref(false)
const saved = ref(false)

const form = reactive({
  age: user.value?.age ?? null,
  weight_kg: user.value?.weight_kg ?? null,
  height_cm: user.value?.height_cm ?? null,
  level: user.value?.level ?? null,
  goal: user.value?.goal ?? null,
})

// Sincroniza el form si el usuario cambia
watch(user, (u) => {
  if (!u) return
  form.age = u.age
  form.weight_kg = u.weight_kg
  form.height_cm = u.height_cm
  form.level = u.level
  form.goal = u.goal
})

const initials = computed(() => {
  const name = user.value?.name ?? ''
  return name.slice(0, 2).toUpperCase() || 'CT'
})

const goalLabel = computed(() => {
  const labels: Record<string, string> = {
    strength: 'Fuerza',
    endurance: 'Resistencia',
    weight_loss: 'Bajar peso',
    skill: 'Habilidades',
  }
  return labels[user.value?.goal ?? ''] ?? '—'
})

async function handleSave() {
  isSaving.value = true
  try {
    await authStore.updateProfile(form)
    saved.value = true
    setTimeout(() => { saved.value = false }, 3000)
  } finally {
    isSaving.value = false
  }
}
</script>

<style scoped>
.profile-page { display: flex; flex-direction: column; gap: 20px; }

.profile-grid {
  display: grid;
  grid-template-columns: 240px 1fr;
  gap: 20px;
  align-items: start;
}

.card {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 20px;
}
.card-title {
  font-family: var(--font-head);
  font-size: 13px;
  font-weight: 600;
  color: var(--text2);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

/* Identity */
.identity-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 8px;
}
.avatar {
  width: 64px; height: 64px;
  border-radius: 50%;
  background: var(--accent-dim);
  border: 2px solid var(--accent);
  display: flex; align-items: center; justify-content: center;
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 22px;
  color: var(--accent);
  margin-bottom: 4px;
}
.identity-name { font-size: 16px; font-weight: 600; color: var(--text); }
.identity-email { font-size: 12px; color: var(--text2); }
.identity-badge {
  margin-top: 4px;
  padding: 3px 10px;
  background: var(--accent-dim);
  color: var(--accent);
  border-radius: 20px;
  font-size: 11px;
  font-weight: 500;
  text-transform: capitalize;
}

/* Form */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
  margin-bottom: 20px;
}
.full-width { grid-column: 1 / -1; }

.field-group { display: flex; flex-direction: column; gap: 6px; }
.field-label { font-size: 12px; color: var(--text2); font-weight: 500; }
.field-input {
  width: 100%;
  background: var(--bg3);
  border: 1px solid var(--border2);
  border-radius: 8px;
  padding: 10px 12px;
  color: var(--text);
  font-family: var(--font-body);
  font-size: 13px;
}
.field-input:focus { outline: none; border-color: var(--accent); }

.form-actions { display: flex; align-items: center; gap: 14px; }
.save-btn {
  padding: 10px 24px;
  background: var(--accent);
  color: #fff;
  border: none;
  border-radius: 8px;
  font-family: var(--font-body);
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s;
}
.save-btn:hover:not(:disabled) { background: var(--accent2); }
.save-btn:disabled { opacity: 0.6; cursor: not-allowed; }
.saved-msg { font-size: 13px; color: var(--green); }

/* Stats */
.stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
}
.stat-card {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 16px;
}
.stat-label { font-size: 11px; color: var(--text2); margin-bottom: 6px; }
.stat-val {
  font-family: var(--font-head);
  font-size: 22px;
  font-weight: 700;
  color: var(--text);
}
.stat-unit { font-size: 13px; color: var(--text2); }

@media (max-width: 900px) {
  .profile-grid { grid-template-columns: 1fr; }
  .stats-row { grid-template-columns: repeat(2, 1fr); }
}
</style>