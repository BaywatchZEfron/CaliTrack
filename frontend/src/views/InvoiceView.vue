<template>
  <AppLayout>
    <div class="invoice-page">

      <div class="invoice-actions">
        <button class="print-btn" @click="handlePrint">🖨 Imprimir / Guardar PDF</button>
      </div>

      <div class="invoice-doc" id="invoice">

        <div class="inv-header">
          <div class="inv-logo">
            <div class="logo-mark">CT</div>
            <div class="logo-text">Cali<span>Track</span></div>
          </div>
          <div class="inv-meta">
            <div class="inv-title">FACTURA</div>
            <div class="inv-num">Nº {{ invoiceNumber }}</div>
            <div class="inv-date">Fecha: {{ today }}</div>
          </div>
        </div>

        <div class="inv-divider" />

        <div class="inv-parties">
          <div class="inv-party">
            <div class="inv-party-label">Emisor</div>
            <div class="inv-party-name">CaliTrack S.L.</div>
            <div class="inv-party-detail">NIF: B-12345678</div>
            <div class="inv-party-detail">Calle Dominada al Fallo 42, S/C de Tenerife</div>
            <div class="inv-party-detail">facturacion@calitrack.com</div>
          </div>
          <div class="inv-party">
            <div class="inv-party-label">Cliente</div>
            <div class="inv-party-name">{{ user?.name ?? '—' }}</div>
            <div class="inv-party-detail">{{ user?.email ?? '—' }}</div>
            <div class="inv-party-detail">Plan: {{ planLabel }}</div>
          </div>
        </div>

        <table class="inv-table">
          <thead>
            <tr>
              <th>Descripción</th>
              <th>Cantidad</th>
              <th>Precio unitario</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Suscripción CaliTrack {{ planLabel }}</td>
              <td>1 mes</td>
              <td>{{ planPrice }} €</td>
              <td>{{ planPrice }} €</td>
            </tr>
          </tbody>
        </table>

        <div class="inv-totals">
          <div class="inv-total-row">
            <span>Subtotal</span>
            <span>{{ planPrice }} €</span>
          </div>
          <div class="inv-total-row">
            <span>IGIC (7%)</span>
            <span>{{ IGIC }} €</span>
          </div>
          <div class="inv-divider" />
          <div class="inv-total-row total">
            <span>Total</span>
            <span>{{ totalConIGIC }} €</span>
          </div>
        </div>

        <div class="inv-footer">
          Gracias por confiar en CaliTrack. Este documento tiene validez como justificante de pago.
        </div>

      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import AppLayout from '@/components/layout/AppLayout.vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const user = computed(() => authStore.user)

const planLabel = computed(() => user.value?.plan === 'premium' ? 'Premium' : 'Free')
const planPrice = computed(() => user.value?.plan === 'premium' ? '9.99' : '0.00')
const IGIC = computed(() => user.value?.plan === 'premium' ? '0.69' : '0.00')
const totalConIGIC = computed(() => user.value?.plan === 'premium' ? '10.68' : '0.00')

const today = new Date().toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' })
const invoiceNumber = 'CT-' + new Date().getFullYear() + '-' + String(user.value?.id ?? 1).padStart(4, '0')

function handlePrint() {
  window.print()
}
</script>

<style scoped>
.invoice-page { display: flex; flex-direction: column; gap: 16px; max-width: 760px; }

.invoice-actions { display: flex; justify-content: flex-end; }
.print-btn {
  padding: 8px 16px;
  background: var(--bg2);
  border: 1px solid var(--border2);
  border-radius: 8px;
  color: var(--text2);
  font-family: var(--font-body);
  font-size: 13px;
  cursor: pointer;
  transition: all 0.15s;
}
.print-btn:hover { background: var(--bg3); color: var(--text); }

.invoice-doc {
  background: var(--bg2);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 36px;
}

/* Header */
.inv-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
}
.inv-logo { display: flex; align-items: center; gap: 10px; }
.logo-mark {
  width: 36px; height: 36px;
  background: var(--accent);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 14px;
  color: #fff;
}
.logo-text { font-family: var(--font-head); font-weight: 700; font-size: 18px; color: var(--text); }
.logo-text span { color: var(--accent); }

.inv-meta { text-align: right; }
.inv-title {
  font-family: var(--font-head);
  font-size: 22px;
  font-weight: 800;
  color: var(--text);
  letter-spacing: 0.1em;
}
.inv-num { font-size: 13px; color: var(--text2); margin-top: 4px; }
.inv-date { font-size: 12px; color: var(--text3); margin-top: 2px; }

.inv-divider { height: 1px; background: var(--border); margin: 20px 0; }

/* Parties */
.inv-parties { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
.inv-party-label { font-size: 10px; color: var(--text3); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 8px; }
.inv-party-name { font-size: 15px; font-weight: 600; color: var(--text); margin-bottom: 4px; }
.inv-party-detail { font-size: 12px; color: var(--text2); margin-top: 2px; }

/* Table */
.inv-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
.inv-table th {
  text-align: left;
  font-size: 11px;
  color: var(--text3);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  padding: 8px 12px;
  border-bottom: 1px solid var(--border);
}
.inv-table td {
  padding: 12px;
  font-size: 13px;
  color: var(--text2);
  border-bottom: 1px solid var(--border);
}
.inv-table td:first-child { color: var(--text); }

/* Totals */
.inv-totals { max-width: 280px; margin-left: auto; }
.inv-total-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  color: var(--text2);
  padding: 6px 0;
}
.inv-total-row.total {
  font-size: 16px;
  font-weight: 700;
  color: var(--text);
  padding-top: 10px;
}

/* Footer */
.inv-footer {
  margin-top: 32px;
  font-size: 11px;
  color: var(--text3);
  text-align: center;
  line-height: 1.6;
}

@media print {
  .invoice-actions { display: none; }
  .invoice-doc { border: none; padding: 0; }
}
</style>