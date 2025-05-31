<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <form @submit.prevent="submit" class="bg-white p-6 rounded-xl shadow-md w-full max-w-sm space-y-4">
            <h2 class="text-2xl font-bold text-center text-indigo-600">Create Account</h2>

            <div v-if="error" class="text-red-500 text-sm text-center">{{ error }}</div>

            <input v-model="name" type="text" placeholder="Name" class="input w-full" required />

            <input v-model="email" type="email" placeholder="Email" class="input w-full" required />

            <input v-model="password" type="password" placeholder="Password" class="input w-full" required />

            <input v-model="passwordConfirmation" type="password" placeholder="Confirm Password" class="input w-full"
                required />

            <button type="submit" class="btn w-full" :disabled="loading">
                {{ loading ? 'Registering...' : 'Register' }}
            </button>

            <p class="text-sm text-center">
                Already have an account?
                <router-link to="/login" class="text-indigo-600 hover:underline font-medium">
                    Login
                </router-link>
            </p>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')

const error = ref('')
const loading = ref(false)

const submit = async () => {
    error.value = ''
    loading.value = true

    try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: name.value,
                email: email.value,
                password: password.value,
                password_confirmation: passwordConfirmation.value,
            }),
        })

        const data = await response.json()

        if (!response.ok) {
            throw new Error(data.message || 'Registration failed')
        }

        router.push('/login')
    } catch (err) {
        error.value = err instanceof Error ? err.message : 'Unexpected error occurred'
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.input {
    @apply border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition;
}

.btn {
    @apply bg-indigo-600 text-white font-medium py-2 px-4 rounded-md hover:bg-indigo-700 disabled:bg-indigo-300 transition;
}
</style>
