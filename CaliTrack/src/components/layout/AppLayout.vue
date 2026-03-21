<template>
  <div class="app-shell">

    <!-- Sidebar -->
    <aside class="sidebar" :class="{ open: sidebarOpen }">
      <div class="sidebar-logo">
        <div class="logo-mark">CT</div>
        <div class="logo-text">Cali<span>Track</span></div>
      </div>

      <nav class="nav">
        <div class="nav-section-label">Principal</div>

        <RouterLink to="/" class="nav-item" @click="closeSidebar">
          <IconHome />
          Inicio
        </RouterLink>

        <RouterLink to="/dashboard" class="nav-item" @click="closeSidebar">
          <IconGrid />
          Dashboard
        </RouterLink>

        <RouterLink to="/log" class="nav-item" @click="closeSidebar">
          <IconPlus />
          Registrar
        </RouterLink>

        <RouterLink to="/progress" class="nav-item" @click="closeSidebar">
          <IconChart />
          Progresión
        </RouterLink>

        <div class="nav-section-label">Personal</div>

        <RouterLink to="/profile" class="nav-item" @click="closeSidebar">
          <IconUser />
          Mi perfil
        </RouterLink>
      </nav>

      <div class="sidebar-user">
        <div class="user-avatar">{{ userInitials }}</div>
        <div>
          <div class="user-name">{{ authStore.user?.nombre ?? 'Usuario' }}</div>
          <div class="user-level">{{ authStore.user?.nivel ?? '—' }} · {{ authStore.user?.peso_actual ?? '—' }} kg</div>
        </div>
      </div>
    </aside>

    <!-- Overlay móvil -->
    <div v-if="sidebarOpen" class="sidebar-overlay" @click="closeSidebar" />

    <!-- Contenido principal -->
    <div class="main-content">
      <!-- Topbar -->
      <header class="topbar">
        <div class="topbar-left">
          <button class="hamburger" @click="toggleSidebar" aria-label="Menú">
            <IconMenu />
          </button>
          <h1 class="page-title">{{ pageTitle }}</h1>
        </div>
        <div class="topbar-right">
          <RouterLink to="/log" class="topbar-btn primary">
            + Registrar entrenamiento
          </RouterLink>
        </div>
      </header>

      <!-- Aquí se inyecta la vista activa -->
      <main class="view-content">
        <slot />
      </main>
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Iconos inline simples (SVG)
const IconHome = { template: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>` }
const IconGrid = { template: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>` }
const IconPlus = { template: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>` }
const IconChart = { template: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>` }
const IconUser = { template: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>` }
const IconMenu = { template: `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>` }

const authStore = useAuthStore()
const route = useRoute()

// Sidebar abierto/cerrado (para móvil)
const sidebarOpen = ref(false)
function toggleSidebar() { sidebarOpen.value = !sidebarOpen.value }
function closeSidebar() { sidebarOpen.value = false }

// Título dinámico según la ruta activa
const pageTitles: Record<string, string> = {
  '/dashboard': 'Dashboard',
  '/log': 'Registrar',
  '/progress': 'Progresión',
  '/profile': 'Mi perfil',
}
const pageTitle = computed(() => pageTitles[route.path] ?? 'CaliTrack')

// Iniciales del usuario para el avatar
const userInitials = computed(() => {
  const nombre = authStore.user?.nombre ?? ''
  return nombre.slice(0, 2).toUpperCase() || 'CT'
})
</script>

<style scoped>
.app-shell {
  display: flex;
  height: 100vh;
  overflow: hidden;
  background: var(--bg);
}

/* ── Sidebar ── */
.sidebar {
  width: 220px;
  min-width: 220px;
  background: var(--bg2);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  z-index: 100;
  transition: transform 0.3s ease;
}
.sidebar-logo {
  padding: 24px 20px 20px;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 10px;
}
.logo-mark {
  width: 32px; height: 32px;
  background: var(--accent);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 14px;
  color: #fff;
}
.logo-text {
  font-family: var(--font-head);
  font-weight: 700;
  font-size: 16px;
  color: var(--text);
}
.logo-text span { color: var(--accent); }

.nav {
  padding: 16px 12px;
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 2px;
  overflow-y: auto;
}
.nav-section-label {
  font-size: 10px;
  font-weight: 500;
  color: var(--text3);
  letter-spacing: 0.08em;
  text-transform: uppercase;
  padding: 12px 8px 6px;
}
.nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 10px;
  border-radius: 8px;
  color: var(--text2);
  text-decoration: none;
  font-size: 13px;
  transition: all 0.15s;
}
.nav-item:hover { background: var(--bg3); color: var(--text); }
.nav-item.router-link-active {
  background: var(--accent-dim);
  color: var(--accent);
  font-weight: 500;
}

.sidebar-user {
  padding: 16px;
  border-top: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 10px;
}
.user-avatar {
  width: 34px; height: 34px;
  border-radius: 50%;
  background: var(--accent-dim);
  border: 2px solid var(--accent);
  display: flex; align-items: center; justify-content: center;
  font-family: var(--font-head);
  font-weight: 700;
  font-size: 12px;
  color: var(--accent);
  flex-shrink: 0;
}
.user-name { font-size: 13px; font-weight: 500; color: var(--text); }
.user-level { font-size: 11px; color: var(--text2); }

/* ── Main ── */
.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 28px;
  border-bottom: 1px solid var(--border);
  background: var(--bg2);
  flex-shrink: 0;
}
.topbar-left { display: flex; align-items: center; gap: 12px; }
.page-title {
  font-family: var(--font-head);
  font-weight: 700;
  font-size: 18px;
  color: var(--text);
}
.topbar-btn {
  padding: 7px 14px;
  border-radius: 8px;
  border: none;
  font-size: 12px;
  font-family: var(--font-body);
  cursor: pointer;
  text-decoration: none;
  transition: all 0.15s;
}
.topbar-btn.primary {
  background: var(--accent);
  color: #fff;
}
.topbar-btn.primary:hover { background: var(--accent2); }

.hamburger {
  display: none;
  background: none;
  border: none;
  color: var(--text);
  cursor: pointer;
  padding: 4px;
}

.view-content {
  flex: 1;
  overflow-y: auto;
  padding: 24px 28px;
}

/* Overlay móvil */
.sidebar-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);
  z-index: 99;
}

/* ── Mobile ── */
@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    top: 0; left: 0; bottom: 0;
    transform: translateX(-100%);
  }
  .sidebar.open { transform: translateX(0); }
  .hamburger { display: flex; }
  .topbar { padding: 12px 16px; }
  .view-content { padding: 16px; }
  .topbar-right { display: none; }
}
</style>