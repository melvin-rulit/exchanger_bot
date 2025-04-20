import { defineStore } from 'pinia'

export const useOrdersStore = defineStore('orders', {
    state: () => ({
        orders: [],
        unreadByOrderId: {}, // 👈 объект, где ключ = order.id, значение = true/false
    }),

    getters: {
        unreadMessagesCount: (state) =>
            Object.values(state.unreadByOrderId).filter(Boolean).length
    },

    actions: {
        setOrders(orders) {
            this.orders = orders
            this.unreadByOrderId = {} // сброс перед записью

            for (const order of orders) {
                this.unreadByOrderId[order.id] =
                    order.is_message && order.status !== 'success'
            }
        },

        markAsRead(orderId) {
            this.unreadByOrderId[orderId] = false
        }
    }
})
