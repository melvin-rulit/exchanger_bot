<template>
    <div class="main">
      <div class="table-container">

        <table class="w-full whitespace-nowrap">
            <thead class="head_table">
            <tr tabindex="0" class="focus:outline-none h-10 rounded">

            </tr>
            </thead>
            <tbody v-if="messages.length">
            <tr v-for="message of messages" tabindex="0" @click="showModalShowChat(message.id)" class="table_body inside_table focus:outline-none h-10 rounded hover:bg-gray-100">
                <td class="pl-4">
                    <div class="flex items-center">
                        <Icon icon="wpf:message-outline" width="26" height="26"/>
                    </div>
                </td>
              <td class="">
                <div class="flex items-center">
                  <p class="text-sm leading-none text-gray-600 ml-2">{{ message.message }}</p>
                </div>
              </td>
            </tr>
            <tr class="h-3">
            </tr>
            </tbody>

          <tbody v-else>
          <tr>
            <td colspan="2" class="no-messages">Нет сообщений</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

  <ModalShowChat
    :is-active="isModalChatShow"
    :messageId="messageId"
    @close="closeModalShowChat"
  />
</template>

<script>
import Pusher from 'pusher-js';
import { Icon } from '@iconify/vue';
import ModalShowChat from '@/Pages/Consultation/Chats/Modal/ModalShowChat.vue'
import { ConsultationService } from '@/services/ConsultationService.js'

export default {
    components: { Icon, ConsultationService},
    data: function () {
        return {
            messages: '',
            messageId: '',
            form: {
                dateFrom: '',
                dateTo: '',
            },
            statusTranslations: {
                new: 'Новый',
                active: 'Активный',
                deleted: 'Удален',
            },
            query: '',
            limit: 5,
            total: 1,
            errors: '',
            message: null,
            loading: false,
            showFilter: false,
            isModalChatShow: false,
        }
    },
    mounted() {
      this.getTodayMessages()
    },
    computed: {
        // page() {
        //     return Number(this.$route.query.page) ?? 1;
        // },
        hasFilters() {
            return Object.values(this.form).some(value => value !== '')
        },
    },
    methods: {
      getTodayMessages: function () {
        ConsultationService.getMessages().then(response => {
          this.messages = response.data.messages
        })
      },
        getStatusColor(status) {
            switch (status) {
                case 'new':
                    return 'text-[#38b0b0]';
                case 'active':
                    return 'text-[#00008B]';
                case 'stoped':
                    return 'text-[#FFA500]';
                case 'deleted':
                    return 'text-[#FF0000]';
                default:
                    return 'text-black';
            }
        },
        // checkOrders() {
        //     const pusher = new Pusher('6c99314bac482dfe845e', {
        //         cluster: 'eu', logToConsole: true,
        //     })
        //     const channel = pusher.subscribe('check_amount')
        //
        //     channel.bind('my-event', (data) => {
        //         this.getOrders()
        //     })
        // },
        // checkOrdersUpdate() {
        //     const pusher = new Pusher('6c99314bac482dfe845e', {
        //         cluster: 'eu', logToConsole: true,
        //     })
        //     const channel = pusher.subscribe('update_order');
        //
        //     channel.bind('order-updated', (data) => {
        //
        //         this.getOrders()
        //         // if (data.order.is_message !== this.orders.is_message) {
        //         //     this.orders = data.order;
        //         // }
        //     });
        // },
        translateStatus(status) {
            return this.statusTranslations[status] || status;
        },
        // reset() {
        //     this.form = mapValues(this.form, () => '')
        // },
        showModalShowChat($messageId) {
            this.isModalChatShow = true;
            this.messageId = $messageId
        },
        closeModalShowChat() {
            // this.getOrders()
            this.isModalChatShow = false
        }
    },
}
</script>
<style lang="scss">
.main {
  min-height: 93vh;
  display: flex;
  flex-direction: column;
}
.table-container {
  flex-grow: 1;
  overflow-y: auto;
  background-color: white;
}

.filters_block {
    margin-bottom: 10px;

    span{
        font-size: 11px;
    }
}
.head_table {
    background-color: ghostwhite;
}
.inside_table {
    background-color: white;
    cursor: pointer;
  border-bottom: 1px solid rgba(0, 0, 0, .05);
}
.no-messages {
  text-align: center;
  padding: 20px;
  font-size: 16px;
  font-weight: bold;
  color: #888;
}
</style>
