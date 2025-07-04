import { createRouter, createWebHistory } from '@ionic/vue-router'
import { RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes: Array<RouteRecordRaw> = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/auth/LoginView.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/views/auth/RegisterView.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('@/views/DashboardView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/blueprints',
    name: 'Blueprints',
    component: () => import('@/views/blueprint/BlueprintListView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/blueprints/create',
    name: 'CreateBlueprint',
    component: () => import('@/views/blueprint/CreateBlueprintView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/blueprints/:id',
    name: 'BlueprintDetails',
    component: () => import('@/views/blueprint/BlueprintDetailsView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/blueprints/:id/edit',
    name: 'EditBlueprint',
    component: () => import('@/views/blueprint/EditBlueprintView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/documents/:blueprintCode',
    name: 'DocumentList',
    component: () => import('@/views/document/DocumentListView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/documents/:blueprintCode/create',
    name: 'CreateDocument',
    component: () => import('@/views/document/CreateDocumentView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/documents/:blueprintCode/:id',
    name: 'DocumentDetails',
    component: () => import('@/views/document/DocumentDetailsView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/documents/:blueprintCode/:id/edit',
    name: 'EditDocument',
    component: () => import('@/views/document/EditDocumentView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/files',
    name: 'Files',
    component: () => import('@/views/file/FileListView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('@/views/auth/ProfileView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: () => import('@/views/SettingsView.vue'),
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// Navigation guard
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  
  if (requiresAuth && !authStore.isAuthenticated) {
    // Try to restore session from storage
    await authStore.restoreSession()
    
    if (!authStore.isAuthenticated) {
      next('/login')
      return
    }
  }
  
  if (!requiresAuth && authStore.isAuthenticated && (to.path === '/login' || to.path === '/register')) {
    next('/dashboard')
    return
  }
  
  next()
})

export default router