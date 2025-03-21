<template>
    <div
        v-if="isActive"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

        <div @click.stop class="relative bg-white chat_container w-96 h-96 rounded-lg  flex flex-col">

            <div class="flex-1 overflow-auto mb-4">
                <div v-for="(group, groupIndex) in groupedMessages" :key="'group-' + groupIndex">
                    <div class="flex justify-center my-2">
                        <span class="text-sm ">{{ group.date }}</span>
                    </div>

                    <div v-for="(message, index) in group.messages" :key="'message-' + groupIndex + '-' + index" class="mb-4">
                        <div :class="message.sender_type === 'user' ? 'flex items-start' : 'flex items-start justify-end'">
                            <div v-if="message.sender_type === 'user'" class="flex flex-col ml-4">
                                <div class="p-2 rounded-md shadow-md text-gray-700 bg-blue-100">
                                    {{ message.message }}
                                </div>
                            </div>
                            <div v-if="message.sender_type === 'client'" class="flex flex-col items-end mr-6">
                                <div class="bg-white-100 p-2 rounded-md shadow-md text-gray-700">
                                    {{ message.message }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <input
                    v-model="newMessage"
                    @keydown.enter="sendMessages"
                    type="text"
                    class="border p-2 w-full rounded-l-md"
                    placeholder="Введите сообщение..."/>
                <button
                    @click="sendMessages"
                    class="bg-blue-500 text-white p-2 rounded-r-md"
                    aria-label="Send message">Отправить
                </button>
            </div>
            <button
                @click.stop="close"
                class="absolute top-4 right-4 p-2 text-black">
              <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" />
            </button>
        </div>
    </div>
</template>

<script>
import { OrdersService } from '@/services/OrdersService.js'
import Pusher from 'pusher-js'
import { Icon } from '@iconify/vue';

export default {
  components: {Icon},
    props: {
        isActive: {
            type: Boolean,
            default: false,
            required: true
        },
        orderId: {
            required: true
        },
    },
    data() {
        return {
            messages: [],
            order: {},
            clientMessages: [],
            supportMessages: [],
            newMessage: '',
            errors: '',
            pusher: null,
            channel: null,
        };
    },
    mounted() {
        this.checkOrdersUpdate()
    },
    computed: {
            groupedMessages() {
                const grouped = [];
                let currentGroup = null;

                // Сортировка сообщений по дате (по убыванию)
                const sortedMessages = [...this.messages].sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

                sortedMessages.forEach((message) => {
                    const messageDate = new Date(message.created_at);
                    const messageDateString = messageDate.toLocaleDateString(); // Форматируем дату, чтобы её сравнивать

                    // Если это новый день, создаем новую группу
                    if (!currentGroup || currentGroup.date !== messageDateString) {
                        currentGroup = {
                            date: messageDateString,
                            showDate: true, // Показываем дату для первого сообщения в группе
                            messages: [message],
                        };
                        grouped.push(currentGroup);
                    } else {
                        // Если это тот же день, добавляем сообщение в текущую группу
                        currentGroup.messages.push(message);
                        currentGroup.showDate = false; // Для остальных сообщений той же даты показывать дату не нужно
                    }
                });

                return grouped;
            }
    },
    methods: {
      async getOrder() {
        try {
          const response = await OrdersService.getOrder(this.orderId);
          this.messages = response.data.data;
          this.organizeMessages();
          await this.$nextTick(() => this.scrollToBottom());
        } catch (error) {
          this.errors = error.response?.data?.errors || 'Ошибка загрузки данных';
        }
      },
      setMessagesRead() {
            OrdersService.setOrderMessagesRead(this.orderId).then(response => {
            })
        },
        checkOrdersUpdate() {
            this.pusher = new Pusher('6c99314bac482dfe845e', {
                cluster: 'eu', logToConsole: true,
            })
            this.channel =  this.pusher.subscribe('update_order');

            this.channel.bind('order-updated', (data) => {

            if (this.isActive) {
                this.getOrder()
            }
            });
        },
        organizeMessages() {
            this.clientMessages = this.messages.filter(message => message.sender_type === 'user');
            this.supportMessages = this.messages.filter(message => message.sender_type === 'client');
        },
      async sendMessages() {
        if (!this.newMessage.trim()) {
          return;
        }
        try {
          const response = await OrdersService.sendOrderMessages(
            this.orderId,
            this.newMessage
          );
          this.messages.push(response.data);
          this.$nextTick(() => this.scrollToBottom());
          this.newMessage = '';
        } catch (error) {
          console.error('Ошибка отправки:', error);
        }
      },
        scrollToBottom() {
            const chatContainer = this.$el.querySelector('.flex-1');
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        },
        close() {
            // this.setMessagesRead()

            setTimeout(() => {
                this.$emit('close');
              this.messages = ''
            }, 500)
        },
    },
    watch: {
      isActive(newVal) {
        if (newVal) {
          this.getOrder();
        }
      },
        orderId(newOrderId) {
            if (newOrderId) {
                this.getOrder();
            }
        },
    },
};
</script>

<style scoped>
.fixed {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    backdrop-filter: blur(4px);
    background-color: rgba(0, 0, 0, 0.9);
}
.flex {
    display: flex;
}
.items-center {
    align-items: center;
}
.justify-center {
    justify-content: center;
}
.z-50 {
    z-index: 50;
}

/* Стиль для чат окна */
.bg-white {
    background-color: white;
}
.chat_container {
    padding-left: 4rem;
    padding-right: 4rem;
    padding-top: 2rem;
    padding-bottom: 2rem;
}
.w-96 {
    width: 84rem;
}
.h-96 {
    height: 46rem;
}
.rounded-lg {
    border-radius: 0.5rem;
}
.flex-col {
    flex-direction: column;
}
.overflow-auto {
    overflow: auto;
}
.mb-4 {
    margin-bottom: 1rem;
}

/* Чат сообщения */
.flex-1 {
    flex: 1;
    overflow-y: auto;
}

/* Поле ввода сообщения */
input {
    border: 1px solid #ddd;
}
input:focus {
  outline: none;
  box-shadow: none;
  border-color: inherit;
}
.border {
    border-width: 1px;
}
.p-2 {
    padding: 0.5rem;
}
.w-full {
    width: 100%;
}
.rounded-l-md {
    border-radius: 0.375rem 0 0 0.375rem;
}

/* Кнопка отправки */
button {
    cursor: pointer;
}
.bg-blue-500 {
    background-color: #3b82f6;
}
.text-white {
    color: white;
}
.rounded-r-md {
    border-radius: 0 0.375rem 0.375rem 0;
}

/* Оформление аватарок */
.w-8 {
    width: 2rem;
}
.h-8 {
    height: 2rem;
}
.rounded-full {
    border-radius: 50%;
}
.bg-blue-600 {
    background-color: #3b82f6;
}
.bg-green-600 {
    background-color: #10b981;
}
.text-white {
    color: white;
}

/* Оформление сообщения */
.bg-blue-100 {
    background-color: #ebf8ff;
}
.bg-green-100 {
    background-color: #d1fae5;
}
.p-3 {
    padding: 0.75rem;
}
.rounded-lg {
    border-radius: 0.375rem;
}
.shadow-md {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
.text-gray-700 {
    color: #4a5568;
}
/* Оформление для даты */
.flex.justify-center {
    align-items: center;
}

.text-sm {
    font-size: 0.875rem;
    font-weight: 600;
}

.text-gray-500 {
    color: #6b7280;
}

.bg-white-100 {
    background-color: #f9fafb;
}

</style>
