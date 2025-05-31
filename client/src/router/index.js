import { createRouter, createWebHistory } from 'vue-router'
import HelloWorld from '../components/HelloWorld.vue'
import Dashboard from '../views/Dashboard.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import { useAuthStore } from '../stores/auth'

const routes = [
    {
        path: '',
        name: 'Home',
        redirect: (to) => {
            const auth = useAuthStore()
            return auth.isAuthenticated ? '/dashboard' : '/login'
        }
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }
    },
    {
        path: '/login',
        name: 'Login',
        component: Login
    },
    {
        path: '/register',
        name: 'Register',
        component: Register
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// Global navigation guard
router.beforeEach((to, from, next) => {
    const auth = useAuthStore()

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        next('/login')
    } else if ((to.name === 'Login' || to.name === 'Register') && auth.isAuthenticated) {
        next('/dashboard')
    } else {
        next()
    }
})

export default router
