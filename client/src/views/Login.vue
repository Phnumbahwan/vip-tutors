<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 px-4">
        <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8">
            <h2 class="text-2xl font-bold text-center text-indigo-700 mb-6">Welcome Back</h2>

            <form @submit.prevent="submit" class="space-y-5">
                <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input v-model="email" type="email" id="email" placeholder="you@example.com"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        required />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input v-model="password" type="password" id="password" placeholder="••••••••"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        required />
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md transition disabled:opacity-50"
                    :disabled="loading">
                    {{ loading ? 'Logging in...' : 'Login' }}
                </button>

                <p class="text-sm text-center text-gray-600">
                    Don't have an account?
                    <router-link to="/register" class="text-indigo-600 hover:underline font-medium">
                        Register
                    </router-link>
                </p>
            </form>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth' // Import the auth store

const router = useRouter()
const auth = useAuthStore() // Initialize the auth store

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref<string | null>(null) // Add error state

const submit = async () => {
    loading.value = true
    error.value = null // Clear previous errors

    try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: email.value,
                password: password.value,
            }),
        })

        const data = await response.json()

        if (!response.ok) {
            // Handle API errors
            throw new Error(data.message || `Login failed with status: ${response.status}`)
        }

        // Assuming your API returns { user: { name, email }, token: '...' }
        auth.login(data)

        // Redirect to dashboard on success
        router.push('/dashboard')

    } catch (err) {
        // Handle network errors or other exceptions
        console.error('Login error:', err)
        error.value = err instanceof Error ? err.message : 'An unexpected error occurred.'
    } finally {
        loading.value = false
    }
}
</script>
