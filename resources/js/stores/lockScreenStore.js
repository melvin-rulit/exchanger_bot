import { defineStore } from 'pinia'

export const useLockScreenStore = defineStore('lockScreen', {
    state: () => ({
        isModalLockShow: false,
        isLocked: false,
    }),

    getters: {

    },

    actions: {
        showLockModal() {
            this.isModalLockShow = true
        },
        hideLockModal() {
            this.isModalLockShow = false
        },
        lockScreen() {
            this.isLocked = true
            this.showLockModal()
        },
        unlockScreen() {
            this.isLocked = false
            this.hideLockModal()
        },
        setInitialLockState(isLocked) {
            this.isLocked = isLocked
            this.isModalLockShow = isLocked
        }
    }
})
