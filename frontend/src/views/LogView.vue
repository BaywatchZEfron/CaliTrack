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

            <div class="field-group">
              <label class="field-label">Tipo de carga</label>

              <select v-model="loadType" class="field-input">
                <option value="bodyweight">Bodyweight</option>
                <option value="weighted">Con lastre</option>
                <option value="assisted">Asistido (banda)</option>
              </select>
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
                        <input v-model.number="set.rpe" type="number" class="td-input" max="10" min="0" />
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
  loadType,
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
.log-page { display: flex; flex-direction: column; gap: 20px; }

.log-grid {
  display: grid;
  grid-template-columns: 1fr 360px;
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

.log-form { display: flex; flex-direction: column; gap: 14px; }

.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
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

/* Series */
.series-wrap { overflow-x: auto; }
.series-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.series-table th {
  color: var(--text2);
  font-size: 11px;
  font-weight: 500;
  text-align: left;
  padding: 8px 10px;
  border-bottom: 1px solid var(--border);
  letter-spacing: 0.04em;
  text-transform: uppercase;
}
.series-table td {
  padding: 8px 10px;
  border-bottom: 1px solid var(--border);
  color: var(--text);
}
.serie-num { color: var(--text3); }
.td-input {
  background: var(--bg4);
  border: 1px solid transparent;
  border-radius: 6px;
  padding: 4px 8px;
  color: var(--text);
  font-family: var(--font-body);
  font-size: 12px;
  width: 60px;
  text-align: center;
}
.td-input:focus { outline: none; border-color: var(--accent); }
.delete-btn {
  background: none;
  border: none;
  color: var(--text3);
  cursor: pointer;
  font-size: 14px;
  padding: 2px 6px;
  border-radius: 4px;
  transition: color 0.15s;
}
.delete-btn:hover:not(:disabled) { color: var(--red); }
.delete-btn:disabled { opacity: 0.3; cursor: not-allowed; }

.add-serie-row {
  font-size: 12px;
  color: var(--accent);
  padding: 10px;
  text-align: center;
  cursor: pointer;
}
.add-serie-row:hover { text-decoration: underline; }

/* Preview strip */
.preview-strip {
  display: flex;
  align-items: center;
  background: var(--bg3);
  border-radius: 10px;
  padding: 14px;
  gap: 12px;
}
.preview-item { flex: 1; text-align: center; }
.preview-label { font-size: 11px; color: var(--text2); margin-bottom: 4px; }
.preview-val { font-family: var(--font-head); font-size: 18px; font-weight: 700; color: var(--text); }
.preview-divider { width: 1px; height: 32px; background: var(--border); }

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
}
.submit-btn:hover { background: var(--accent2); }

.guardado-msg {
  text-align: center;
  font-size: 13px;
  color: var(--green);
  padding: 8px;
}

/* Historial */
.historial { display: flex; flex-direction: column; }
.historial-list { display: flex; flex-direction: column; gap: 10px; }
.hist-item {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 14px 16px;
}
.hist-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 10px;
}
.hist-ejercicio { font-size: 14px; font-weight: 500; color: var(--text); }
.hist-fecha { font-size: 11px; color: var(--text2); margin-top: 2px; }
.hist-series { display: flex; gap: 6px; flex-wrap: wrap; }
.hist-badge {
  background: var(--bg3);
  border-radius: 6px;
  padding: 3px 8px;
  font-size: 11px;
  color: var(--text2);
}
.pr-badge {
  background: rgba(34,200,122,0.12);
  color: var(--green);
  border-radius: 4px;
  font-size: 10px;
  padding: 2px 6px;
  font-weight: 600;
}

@media (max-width: 900px) {
  .log-grid { grid-template-columns: 1fr; }
}
</style>