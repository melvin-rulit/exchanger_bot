import { defineStore } from 'pinia'

export const useOrdersStore = defineStore('orders', {
    state: () => ({
        orders: [],
        unreadByOrderId: {},     // orderId: true/false
        receiptNoticeByOrderId: {}, // orderId: true/false
        activeOrderId: null,
        selectedFilters: JSON.parse(localStorage.getItem('selectedFilters')) || []
    }),

    getters: {
        unreadMessagesCount: (state) =>
            Object.values(state.unreadByOrderId).filter(Boolean).length,

        activeOrder(state) {
            return state.orders.find(order => order.id === state.activeOrderId)
        },

        showReceiptNotice(state) {
            return !!state.receiptNoticeByOrderId[state.activeOrderId]
        }
    },

    actions: {
        setOrders(orders) {
            const oldReceiptNotice = { ...this.receiptNoticeByOrderId }
            this.orders = orders
            this.unreadByOrderId = {}

            const newReceiptNotice = {}

            for (const order of orders) {
                this.unreadByOrderId[order.id] = order.is_message && order.status !== 'success'

                if (oldReceiptNotice.hasOwnProperty(order.id)) {
                    newReceiptNotice[order.id] = oldReceiptNotice[order.id]
                } else {
                    newReceiptNotice[order.id] = false // 👈 новый заказ
                }
            }

            this.receiptNoticeByOrderId = newReceiptNotice
        },
        markAsRead(orderId) {
            this.unreadByOrderId[orderId] = false
        },

        setActiveOrder(id) {
            this.activeOrderId = id
        },
        setOrderCheckRead(id) {
            this.receiptNoticeByOrderId[id] = false
        },

        addOrder(newOrder) {
            this.orders.unshift(newOrder)
            this.unreadByOrderId[newOrder.id] =
                newOrder.is_message && newOrder.status !== 'success'
            // не устанавливаем receiptNotice
        },

        updateOrder(updatedOrder) {
            console.log(updatedOrder)
            const index = this.orders.findIndex(o => o.id === updatedOrder.id)
            if (index !== -1) {
                this.orders[index] = { ...this.orders[index], ...updatedOrder }
                this.receiptNoticeByOrderId[updatedOrder.id] = true

                console.log(
                    `Установлен флаг receiptNotice для заказа ${updatedOrder.id}:`,
                    this.receiptNoticeByOrderId[updatedOrder.id]
                )
            } else {
                console.warn('Обновление не выполнено — заказ не найден')
            }
        },
        setSelectedFilters(filters) {
            this.selectedFilters = filters
            localStorage.setItem('selectedFilters', JSON.stringify(filters))
        },
    }
})
