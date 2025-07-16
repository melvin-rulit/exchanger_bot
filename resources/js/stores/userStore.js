import { defineStore } from 'pinia'

export const useUserStore = defineStore('user', {
    state: () => ({
        users: [],
        currentUser: null,
        selectedFilters: JSON.parse(localStorage.getItem('selectedFiltersUsers')) || [],
        isOnUserStatistics: false,
    }),
    getters: {

    },
    actions: {
        setUser(userData) {
            this.users = userData
        },
        setUsers(users) {
            this.users = users
        },
        setIsOnUserStatistics(value) {
            this.isOnUserStatistics = value
        },
        setCurrentUser(userData) {
            this.currentUser = userData
        },
        async fetchUser() {
            try {
                const response = await axios.get('/users')
                this.setUser(response.data.data)
            } catch (error) {
                console.error('Не удалось получить пользователя:', error)
            }
        },
        async getCurrentUser(id) {
            if (!Array.isArray(this.users) || this.users.length === 0) {
                await this.fetchUser()
            }
            return this.users.find(user => user.id === id) || null
        },
        async getUserLockPassword(id) {
            if (!Array.isArray(this.users) || this.users.length === 0) {
                await this.fetchUser()
            }
            const user = this.users.find(u => u.id === id)
            return user ? user.lock_password : null
        },
        async getUserLockScreen(id) {
            if (!Array.isArray(this.users) || this.users.length === 0) {
                await this.fetchUser()
            }
            const user = this.users.find(u => u.id === id)
            return user ? user.is_locked : null
        },
        setSelectedFilters(filters) {
            this.selectedFilters = filters
            localStorage.setItem('selectedFiltersUsers', JSON.stringify(filters))
        },
        getSelectedFilters() {
            return this.selectedFilters
        }
    },
})
