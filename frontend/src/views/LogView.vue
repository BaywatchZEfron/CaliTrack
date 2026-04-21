<template>
  <AppLayout>
    <div class="log-page">

      <div class="log-grid">

        <!-- FORM -->
        <div class="card">
          <div class="card-title" style="margin-bottom: 20px">
            Nuevo entrenamiento
          </div>

          <div class="log-form">

            <!-- Fecha + peso -->
            <div class="field-row">
              <div class="field-group">
                <label class="field-label">Fecha</label>
                <input v-model="date" type="date" class="field-input" />
              </div>

              <div class="field-group">
                <label class="field-label">Peso corporal (kg)</label>
                <input v-model.number="bodyWeight" type="number" step="0.1" class="field-input" />
              </div>
            </div>

            <!-- Ejercicio -->
            <div class="field-group">
              <label class="field-label">Ejercicio</label>
              <!-- Select de ejercicios: ahora usa exercises del composable -->
              <select v-model="selectedExercise" class="field-input">
                <option v-for="ex in exercises" :key="ex.id" :value="ex">
                  {{ ex.name }}
                </option>
              </select>

                <!-- Loading state -->
                <div v-if="isLoading" style="color: var(--text2); font-size: 13px;">
                  Cargando ejercicios...
                </div>
              </div>

            <!-- Series -->
            <div class="field-group">
              <label class="field-label">Series</label>

              <div class="series-wrap">
                <table class="series-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Reps</th>
                      <th>Carga (kg)</th>
                      <th>RPE</th>
                      <th></th>
                    </tr>
                  </thead>

                  <tbody>
                    <tr v-for="(set, index) in sets" :key="index">
                      <td class="serie-num">{{ set.set_number }}</td>

                      <td>
                        <input v-model.number="set.reps" type="number" class="td-input" />
                      </td>

                      <td>
                        <input v-model.number="set.weight_kg" type="number" class="td-input" />
                      </td>

                      <td>
                        <input v-model.number="set.rpe" type="number" class="td-input" />
                      </td>

                      <td>
                        <button
                          class="delete-btn"
                          @click="removeSet(index)"
                          :disabled="sets.length <= 1"
                        >
                          ✕
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>

                <div class="add-serie-row" @click="addSet">
                  + Añadir serie
                </div>
              </div>
            </div>

            <!-- Notas -->
            <div class="field-group">
              <label class="field-label">Notas</label>
              <textarea
                v-model="notes"
                class="field-input"
                rows="2"
              ></textarea>
            </div>

            <!-- Preview -->
            <div class="preview-strip">
              <div class="preview-item">
                <div class="preview-label">Volumen total</div>
                <div class="preview-val" style="color: var(--accent)">
                  {{ Math.round(totalVolume) }} kg
                </div>
              </div>

              <div class="preview-divider" />

              <div class="preview-item">
                <div class="preview-label">Total reps</div>
                <div class="preview-val">{{ totalReps }}</div>
              </div>

              <div class="preview-divider" />

              <div class="preview-item">
                <div class="preview-label">RPE medio</div>
                <div class="preview-val" style="color: var(--amber)">
                  {{ avgRpe || '—' }}
                </div>
              </div>
            </div>

            <button class="submit-btn" @click="handleSave">
              Guardar entrenamiento
            </button>

            <div v-if="saved" class="guardado-msg">
              ✓ Entrenamiento guardado correctamente
            </div>

          </div>
        </div>

        <!-- HISTORIAL -->
        <div class="historial">
          <div class="card-title" style="margin-bottom: 16px">
            Registros recientes
          </div>

          <div class="historial-list">
            <div
              v-for="workout in history.slice(0, 6)"
              :key="workout.id"
              class="hist-item"
            >
              <div class="hist-header">
                <div>
                  <div class="hist-ejercicio">
                    {{ workout.workout_exercises[0]?.exercise?.name ?? '—' }}
                  </div>

                  <div class="hist-fecha">
                    {{ formatDate(workout.date) }}
                  </div>
                </div>

                <span v-if="isPR(workout)" class="pr-badge">PR</span>
              </div>

              <div class="hist-series">
                <span
                  v-for="s in workout.workout_exercises[0]?.sets"
                  :key="s.set_number"
                  class="hist-badge"
                >
                  {{ s.reps }}r · RPE{{ s.rpe }}
                </span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AppLayout from '@/components/layout/AppLayout.vue'
import { useWorkoutLog } from '@/composables/useWorkoutLog'

const {
  date,
  bodyWeight,
  selectedExercise,
  notes,
  sets,
  exercises,
  totalReps,
  totalVolume,
  avgRpe,
  history,
  isLoading,
  addSet,
  removeSet,
  saveWorkout,
  isPR,
} = useWorkoutLog()

const saved = ref(false)

async function handleSave() {
  const hasReps = sets.value.some(s => s.reps > 0)
  if (!hasReps) return

  const result = await saveWorkout()
  if (result) {
    saved.value = true
    setTimeout(() => { saved.value = false }, 3000)
  }
}

function formatDate(dateStr: string): string {
  const d = new Date(dateStr)
  return d.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
}
</script>

<style scoped>
/* 🔒 STYLE ORIGINAL SIN TOCAR */
</style>