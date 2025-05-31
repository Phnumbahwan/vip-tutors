import { defineStore } from 'pinia'

// Helper functions for cookie handling
const cookieStorage = {
    getItem(key) {
        const cookies = document.cookie.split('; ');
        for (let i = 0; i < cookies.length; i++) {
            const cookie = cookies[i].split('=');
            if (cookie[0] === key) {
                return decodeURIComponent(cookie[1]);
            }
        }
        return null;
    },
    setItem(key, value) {
        // Set cookie with a reasonable expiry (e.g., 7 days)
        const date = new Date();
        date.setTime(date.getTime() + (7 * 24 * 60 * 60 * 1000));
        const expires = "; expires=" + date.toUTCString();
        document.cookie = key + "=" + encodeURIComponent(value) + expires + "; path=/; SameSite=Lax; Secure";
    },
    removeItem(key) {
        document.cookie = key + "=; Expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/;";
    }
};

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: null
    }),

    getters: {
        isAuthenticated: (state) => !!state.token // Check authentication based on token presence
    },

    actions: {
        login(data) {
            this.user = data.user;
            this.token = data.token;
        },
        logout() {
            this.user = null;
            this.token = null;
        }
    },

    persist: {
        enabled: true,
        storage: cookieStorage, // Use the custom cookie storage
        paths: ['token'] // Only persist the token
    }
})
