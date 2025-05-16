import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
    state: () => ({
        user: null,
    }),
    getters: {
        isLockedScreen: (state) => state.user?.is_locked,
    },
    actions: {
        setUser(userData) {
            this.user = userData
        },
    },
})
