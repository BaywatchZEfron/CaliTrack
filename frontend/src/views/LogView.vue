<template>
  <AppLayout>
    <div class="log-page">

      <div class="log-grid">
        <!-- Formulario -->
        <div class="card">
          <div class="card-title" style="margin-bottom: 20px">Nuevo entrenamiento</div>

          <div class="log-form">
            <!-- Fecha y peso -->
            <div class="field-row">
              <div class="field-group">
                <label class="field-label">Fecha</label>
                <input v-model="fecha" type="date" class="field-input" />
              </div>
              <div class="field-group">
                <label class="field-label">Peso corporal (kg)</label>
                <input v-model.number="pesoCorporal" type="number" step="0.1" class="field-input" />
              </div>
            </div>

            <!-- Ejercicio -->
            <div class="field-group">
              <label class="field-label">Ejercicio</label>
              <select v-model="ejercicioSeleccionado" class="field-input">
                <option v-for="ex in ejercicios" :key="ex.id" :value="ex.nombre">
                  {{ ex.nombre }}
                </option>
              </select>
            </div>

            <!-- Tabla de series -->
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
                    <tr v-for="(serie, index) in series" :key="index">
                      <td class="serie-num">{{ serie.serie }}</td>
                      <td>
                        <input
                          v-model.number="serie.reps"
                          type="number"
                          min="0"
                          class="td-input"
                          placeholder="0"
                        />
                      </td>
                      <td>
                        <input
                          v-model.number="serie.carga_kg"
                          type="number"
                          min="0"
                          step="0.5"
                          class="td-input"
                          placeholder="0"
                        />
                      </td>
                      <td>
                        <input
                          v-model.number="serie.rpe"
                          type="number"
                          min="1"
                          max="10"
                          step="0.5"
                          class="td-input"
                          placeholder="0"
                        />
                      </td>
                      <td>
                        <button
                          class="delete-btn"
                          @click="eliminarSerie(index)"
                          :disabled="series.length <= 1"
                        >
                          ✕
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
                <div class="add-serie-row" @click="añadirSerie">
                  + Añadir serie
                </div>
              </div>
            </div>

            <!-- Notas -->
            <div class="field-group">
              <label class="field-label">Notas (opcional)</label>
              <textarea
                v-model="notas"
                class="field-input"
                rows="2"
                placeholder="Grip aguantó bien, fatiga baja..."
              />
            </div>

            <!-- Preview calculado -->
            <div class="preview-strip">
              <div class="preview-item">
                <div class="preview-label">Volumen total</div>
                <div class="preview-val" style="color: var(--accent)">
                  {{ Math.round(volumenTotal) }} kg
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
                  {{ rpeMedia || '—' }}
                </div>
              </div>
            </div>

            <button class="submit-btn" @click="handleGuardar">
              Guardar entrenamiento
            </button>

            <!-- Feedback al guardar -->
            <div v-if="guardado" class="guardado-msg">
              ✓ Entrenamiento guardado correctamente
            </div>
          </div>
        </div>

        <!-- Historial -->
        <div class="historial">
          <div class="card-title" style="margin-bottom: 16px">Registros recientes</div>
          <div class="historial-list">
            <div
              v-for="workout in historial.slice(0, 6)"
              :key="workout.id"
              class="hist-item"
            >
              <div class="hist-header">
                <div>
                  <div class="hist-ejercicio">{{ workout.ejercicio }}</div>
                  <div class="hist-fecha">{{ formatFecha(workout.fecha) }} · {{ workout.peso_corporal }} kg</div>
                </div>
                <span v-if="esPR(workout)" class="pr-badge">PR</span>
              </div>
              <div class="hist-series">
                <span
                  v-for="s in workout.series"
                  :key="s.serie"
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
import { mockExercises } from '@/data/mockData'
import type { Workout } from '@/types'

const {
  fecha,
  pesoCorporal,
  ejercicioSeleccionado,
  notas,
  series,
  totalReps,
  volumenTotal,
  rpeMedia,
  historial,
  añadirSerie,
  eliminarSerie,
  guardarEntrenamiento,
} = useWorkoutLog()

const ejercicios = mockExercises
const guardado = ref(false)

function handleGuardar() {
  // Validación mínima
  const hayReps = series.value.some(s => s.reps > 0)
  if (!hayReps) return

  guardarEntrenamiento()
  guardado.value = true
  setTimeout(() => { guardado.value = false }, 3000)
}

function formatFecha(fecha: string): string {
  const d = new Date(fecha + 'T00:00:00')
  return d.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })
}

// Marca PR si es el máximo de reps del ejercicio en el historial
function esPR(workout: Workout): boolean {
  const mismoEjercicio = historial.value.filter(w => w.ejercicio === workout.ejercicio)
  const maxReps = Math.max(...mismoEjercicio.map(w => Math.max(...w.series.map(s => s.reps))))
  const repsEste = Math.max(...workout.series.map(s => s.reps))
  return repsEste === maxReps && mismoEjercicio[0]?.id === workout.id
}
</script>

<style scoped>
.log-page { display: flex; flex-direction: column; gap: 20px; }

.log-grid {
  display: grid;
  grid-template-columns: 1fr 340px;
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
  font-size: 12px;
  font-weight: 600;
  color: var(--text2);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

/* Formulario */
.log-form { display: flex; flex-direction: column; gap: 14px; }

.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

.field-group { display: flex; flex-direction: column; gap: 6px; }

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

/* Tabla de series */
.series-wrap {
  background: var(--bg3);
  border: 1px solid var(--border2);
  border-radius: 8px;
  overflow: hidden;
}
.series-table { width: 100%; border-collapse: collapse; }
.series-table th {
  font-size: 11px;
  font-weight: 500;
  color: var(--text2);
  text-align: left;
  padding: 8px 12px;
  border-bottom: 1px solid var(--border);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.series-table td {
  padding: 6px 8px;
  border-bottom: 1px solid var(--border);
}
.series-table tr:last-child td { border-bottom: none; }
.serie-num { color: var(--text3); font-size: 12px; padding-left: 12px; width: 30px; }
.td-input {
  background: var(--bg4);
  border: 1px solid transparent;
  border-radius: 6px;
  padding: 5px 8px;
  color: var(--text);
  font-family: var(--font-body);
  font-size: 13px;
  width: 64px;
  text-align: center;
  transition: border-color 0.15s;
}
.td-input:focus { outline: none; border-color: var(--accent); }
.delete-btn {
  background: none;
  border: none;
  color: var(--text3);
  cursor: pointer;
  font-size: 11px;
  padding: 4px 8px;
  border-radius: 4px;
  transition: all 0.15s;
}
.delete-btn:hover:not(:disabled) { color: var(--red); background: var(--bg4); }
.delete-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.add-serie-row {
  padding: 10px 12px;
  font-size: 12px;
  color: var(--accent);
  cursor: pointer;
  text-align: center;
  transition: background 0.15s;
}
.add-serie-row:hover { background: var(--bg4); }

/* Preview strip */
.preview-strip {
  display: flex;
  align-items: center;
  background: var(--bg3);
  border-radius: 10px;
  padding: 14px;
  gap: 0;
}
.preview-item { flex: 1; text-align: center; }
.preview-label { font-size: 10px; color: var(--text3); text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 4px; }
.preview-val { font-family: var(--font-head); font-size: 20px; font-weight: 700; color: var(--text); }
.preview-divider { width: 1px; height: 36px; background: var(--border2); }

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
  background: var(--green-dim);
  border-radius: 8px;
}

/* Historial */
.historial-list { display: flex; flex-direction: column; gap: 10px; }
.hist-item {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 14px 16px;
}
.hist-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 8px;
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
  flex-shrink: 0;
}

@media (max-width: 900px) {
  .log-grid { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
  .field-row { grid-template-columns: 1fr; }
}
</style>