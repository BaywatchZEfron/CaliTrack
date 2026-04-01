<template>
  <div class="splash">
    <div class="splash-bg" />
    <div class="splash-grid" />
    <div class="splash-glow" />

    <div class="splash-content">
      <div class="splash-tag">
        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor">
          <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
        </svg>
        Análisis de calistenia
      </div>

      <h1 class="splash-title">
        TRAIN.<br>
        TRACK.<br>
        <span class="accent">PROGRESS.</span>
      </h1>

      <p class="splash-sub">
        Transforma tus entrenamientos en datos accionables.
        Visualiza tu progresión real en fuerza, volumen y técnica.
      </p>

      <div class="splash-actions">
        <RouterLink to="/onboarding" class="btn-primary">
          Empezar ahora
        </RouterLink>
        <RouterLink to="/login" class="btn-ghost">
          Ya tengo cuenta
        </RouterLink>
      </div>
    </div>

    <div class="splash-stats">
      <div class="splash-stat">
        <div class="stat-n">12k+</div>
        <div class="stat-l">Entrenamientos registrados</div>
      </div>
      <div class="splash-stat">
        <div class="stat-n">98%</div>
        <div class="stat-l">Progresión documentada</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Sin lógica — pantalla puramente visual
</script>

<style scoped>
.splash {
  min-height: 100vh;
  background: #050709;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.splash-bg {
  position: absolute; inset: 0;
  background:
    linear-gradient(160deg, rgba(30,80,180,0.25) 0%, transparent 50%),
    linear-gradient(340deg, rgba(10,10,20,0.95) 40%, rgba(30,60,140,0.15) 100%);
}

.splash-grid {
  position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(61,142,248,0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(61,142,248,0.04) 1px, transparent 1px);
  background-size: 48px 48px;
}

.splash-glow {
  position: absolute;
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(61,142,248,0.12) 0%, transparent 70%);
  top: -150px; right: -150px;
}

.splash-content {
  position: relative;
  z-index: 2;
  max-width: 520px;
  padding: 48px 40px;
}

.splash-tag {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(61,142,248,0.12);
  border: 1px solid rgba(61,142,248,0.25);
  border-radius: 20px;
  padding: 4px 14px;
  font-size: 11px;
  color: var(--accent);
  font-weight: 500;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  margin-bottom: 28px;
}

.splash-title {
  font-family: var(--font-head);
  font-size: clamp(42px, 6vw, 72px);
  font-weight: 800;
  line-height: 0.95;
  color: var(--text);
  margin-bottom: 20px;
  letter-spacing: -2px;
}
.splash-title .accent { color: var(--accent); }

.splash-sub {
  font-size: 15px;
  color: var(--text2);
  line-height: 1.6;
  margin-bottom: 36px;
  max-width: 380px;
}

.splash-actions {
  display: flex;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
}

.btn-primary {
  padding: 14px 28px;
  background: var(--accent);
  color: #fff;
  border-radius: 10px;
  font-family: var(--font-body);
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s;
  display: inline-block;
}
.btn-primary:hover { background: var(--accent2); transform: translateY(-1px); }

.btn-ghost {
  padding: 14px 24px;
  background: transparent;
  color: var(--text2);
  border: 1px solid var(--border2);
  border-radius: 10px;
  font-family: var(--font-body);
  font-size: 14px;
  text-decoration: none;
  transition: all 0.2s;
  display: inline-block;
}
.btn-ghost:hover { color: var(--text); border-color: var(--text3); }

.splash-stats {
  position: absolute;
  bottom: 40px;
  right: 40px;
  z-index: 2;
  display: flex;
  gap: 16px;
}
.splash-stat {
  background: rgba(17,19,24,0.85);
  border: 1px solid var(--border2);
  border-radius: 12px;
  padding: 14px 20px;
}
.stat-n {
  font-family: var(--font-head);
  font-size: 22px;
  font-weight: 700;
  color: var(--text);
}
.stat-l { font-size: 11px; color: var(--text2); margin-top: 2px; }

@media (max-width: 768px) {
  .splash-content { padding: 32px 24px; }
  .splash-stats { display: none; }
}
</style>