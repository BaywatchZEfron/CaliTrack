import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'splash',
      component: () => import('@/views/SplashView.vue')
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/LoginView.vue')
    },
    {
      path: '/onboarding',
      name: 'onboarding',
      component: () => import('@/views/OnboardingView.vue'),
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('@/views/DashboardView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/log',
      name: 'log',
      component: () => import('@/views/LogView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/progress',
      name: 'progress',
      component: () => import('@/views/ProgressView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/progress/:id',
      name: 'progress-detail',
      component: () => import('@/views/ProgressDetailView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('@/views/ProfileView.vue'),
      meta: { requiresAuth: true }
    },
    { path: '/plans', component: () => import('@/views/PlansView.vue'), meta: { requiresAuth: true } },
    { path: '/invoice', component: () => import('@/views/InvoiceView.vue'), meta: { requiresAuth: true } }
  ]
})

// Navigation guard — redirige a /login si no hay sesión
 router.beforeEach((to) => {
  const authStore = useAuthStore()
   if (to.meta.requiresAuth && !authStore.isLoggedIn) {
     return { name: 'login' }
  }
 })
export default router