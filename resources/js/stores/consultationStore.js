import { defineStore } from 'pinia'

export const useConsultationStore = defineStore('consultation', {
    state: () => ({
        messages: [],
        unreadByChatId: {}, // ключ: message.id, значение: true/false
    }),

    getters: {
        unreadMessagesCount(state) {
            return Object.values(state.unreadByChatId).filter(Boolean).length
        },
    },

    actions: {
        setMessages(messages) {
            this.messages = messages
            this.unreadByChatId = {}

            for (const msg of messages) {
                this.unreadByChatId[msg.id] = !msg.is_message
            }
        },

        markAsRead(messageId) {
            this.unreadByChatId[messageId] = false
        }
    }
})
