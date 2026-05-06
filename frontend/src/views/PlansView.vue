<template>
  <AppLayout>
    <div class="plans-page">

      <div class="plans-header">
        <h2 class="plans-title">Planes</h2>
        <p class="plans-sub">Elige el plan que mejor se adapta a tus objetivos.</p>
      </div>

      <div class="plans-grid">

        <!-- FREE -->
        <div class="plan-card" :class="{ active: user?.plan === 'free' }">
          <div class="plan-badge free">Actual</div>
          <div class="plan-name">Free</div>
          <div class="plan-price">0 <span class="plan-currency">€/mes</span></div>
          <ul class="plan-features">
            <li>✓ Registro de entrenamientos</li>
            <li>✓ Dashboard básico</li>
            <li>✓ Progresión por ejercicio</li>
            <li>✓ Hasta 3 ejercicios</li>
            <li class="disabled">✗ Smart Insights avanzados</li>
            <li class="disabled">✗ Exportar datos</li>
            <li class="disabled">✗ Análisis nutricional</li>
          </ul>
          <button class="plan-btn current" disabled>Plan actual</button>
        </div>

        <!-- PREMIUM -->
        <div class="plan-card premium-card" :class="{ active: user?.plan === 'premium' }">
          <div class="plan-badge premium">Recomendado</div>
          <div class="plan-name">Premium</div>
          <div class="plan-price">9.99 <span class="plan-currency">€/mes</span></div>
          <ul class="plan-features">
            <li>✓ Todo lo del plan Free</li>
            <li>✓ Ejercicios ilimitados</li>
            <li>✓ Smart Insights avanzados</li>
            <li>✓ Exportar datos CSV</li>
            <li>✓ Análisis nutricional</li>
            <li>✓ Soporte prioritario</li>
            <li>✓ Historial completo</li>
          </ul>
          <button
            class="plan-btn upgrade"
            :disabled="user?.plan === 'premium' || isUpgrading"
            @click="handleUpgrade"
          >
            {{ user?.plan === 'premium' ? 'Plan actual' : isUpgrading ? 'Procesando...' : 'Actualizar a Premium' }}
          </button>
        </div>

      </div>

      <!-- Confirmación -->
      <div v-if="upgraded" class="upgrade-msg">
        ✓ ¡Bienvenido a Premium! Tu plan ha sido actualizado.
      </div>

    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import AppLayout from '@/components/layout/AppLayout.vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const user = computed(() => authStore.user)
const isUpgrading = ref(false)
const upgraded = ref(false)

async function handleUpgrade() {
  isUpgrading.value = true
  try {
    await authStore.updateProfile({ plan: 'premium' })
    upgraded.value = true
    setTimeout(() => { upgraded.value = false }, 4000)
  } finally {
    isUpgrading.value = false
  }
}
</script>

<style scoped>
.plans-page { display: flex; flex-direction: column; gap: 24px; }

.plans-header { margin-bottom: 4px; }
.plans-title {
  font-family: var(--font-head);
  font-size: 22px;
  font-weight: 800;
  color: var(--text);
  margin-bottom: 6px;
}
.plans-sub { font-size: 13px; color: var(--text2); }

.plans-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  max-width: 760px;
}

.plan-card {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 28px;
  position: relative;
  transition: border-color 0.2s;
}
.plan-card.active { border-color: var(--accent); }
.premium-card { border-color: var(--accent-dim); }
.premium-card.active { border-color: var(--accent); }

.plan-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 10px;
  font-weight: 600;
  margin-bottom: 14px;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.plan-badge.free { background: var(--bg3); color: var(--text2); }
.plan-badge.premium { background: var(--accent-dim); color: var(--accent); }

.plan-name {
  font-family: var(--font-head);
  font-size: 20px;
  font-weight: 800;
  color: var(--text);
  margin-bottom: 8px;
}
.plan-price {
  font-family: var(--font-head);
  font-size: 32px;
  font-weight: 800;
  color: var(--text);
  margin-bottom: 20px;
  line-height: 1;
}
.plan-currency { font-size: 14px; color: var(--text2); font-weight: 400; }

.plan-features {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-bottom: 24px;
}
.plan-features li { font-size: 13px; color: var(--text2); }
.plan-features li.disabled { color: var(--text3); }

.plan-btn {
  width: 100%;
  padding: 12px;
  border-radius: 10px;
  border: none;
  font-family: var(--font-body);
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.15s;
}
.plan-btn.current {
  background: var(--bg3);
  color: var(--text2);
  cursor: not-allowed;
}
.plan-btn.upgrade {
  background: var(--accent);
  color: #fff;
}
.plan-btn.upgrade:hover:not(:disabled) { background: var(--accent2); }
.plan-btn.upgrade:disabled { opacity: 0.6; cursor: not-allowed; }

.upgrade-msg {
  font-size: 13px;
  color: var(--green);
  padding: 12px 16px;
  background: var(--green-dim);
  border-radius: 8px;
  max-width: 760px;
}

@media (max-width: 600px) {
  .plans-grid { grid-template-columns: 1fr; }
}
</style>