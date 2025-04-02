<template>
    <div class="main">
      <div class="table-container">

        <table class="w-full whitespace-nowrap">
            <thead class="head_table">
            <tr tabindex="0" class="focus:outline-none h-10 rounded">
                <td>
                  <div class="flex pl-6">
                    <p class="font-semibold mr-2"></p>
                  </div>
                </td>
              <td>
                <div class="flex">
                  <p class="font-semibold mr-2">Последнее сообщение</p>
                </div>
              </td>
              <td>
                <div class="flex">
                  <p class="font-semibold mr-2"></p>
                </div>
              </td>
            </tr>
            </thead>
            <tbody v-if="messages.length">
            <tr v-for="message of messages" tabindex="0" @click="showModalShowChat(message.id)" class="table_body inside_table focus:outline-none h-10 rounded hover:bg-gray-100">
                <td class="pl-4">
                    <div class="flex items-center">
                        <Icon icon="wpf:message-outline" width="26" height="26" :class="{'flashing-icon': !message.is_message}"/>
                    </div>
                </td>
              <td class="">
                <div class="flex items-center">
                  <p class="text-sm leading-none text-gray-600 ml-2">{{ message.message }}</p>
                </div>
              </td>
              <td class="">
                <div class="flex items-center">
                  <p class="text-sm leading-none text-gray-600 ml-2 flex items-center gap-1"><Icon icon="ci:chat-close" width="24" height="24" :class="iconColorClass"/> Чат закрыт клиентом</p>
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

      <ModalShowChat
        :is-active="isModalChatShow"
        :messageId="messageId"
        @close="closeModalShowChat"
      />
    </div>
</template>

<script>
import Pusher from 'pusher-js';
import { Icon } from '@iconify/vue';
import ModalShowChat from '@/Pages/Consultation/Chats/Modal/ModalShowChat.vue'
import { ConsultationService } from '@/services/ConsultationService.js'
import { OrdersService } from '@/services/OrdersService.js'

export default {
    components: { Icon, ConsultationService, ModalShowChat},
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
            type: 'error',
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
      this.checkNewMessagesUpdate()

      // this.notificationAudio = new Audio('/audio/new_sms_consultant.wav');
      // document.addEventListener('click', () => {
      //   this.notificationAudio.play().then(() => {
      //     this.notificationAudio.pause();
      //     this.notificationAudio.currentTime = 0;
      //   }).catch(() => {});
      // }, { once: true });
    },
    computed: {
      iconColorClass() {
        switch (this.type) {
          case 'success':
            return 'icon-success';
          case 'error':
            return 'icon-error';
          case 'danger':
            return 'icon-danger';
          case 'info':
            return 'icon-info';
          default:
            return 'icon-success';
        }
      },
        hasFilters() {
            return Object.values(this.form).some(value => value !== '')
        },
    },
    methods: {
      getTodayMessages: function () {
        ConsultationService.getMessages().then(response => {
          this.messages = response.data.last_message
        })
      },
        checkNewMessagesUpdate() {
            const pusher = new Pusher('6c99314bac482dfe845e', {
                cluster: 'eu', logToConsole: true,
            })
            const channel = pusher.subscribe('consultation');

            channel.bind('new_message', (data) => {
              // this.new_sms()
              if (!this.isModalChatShow){
                let audio = new Audio('/audio/new_sms_consultant_2.wav');
                audio.play().catch(err => console.error('Ошибка воспроизведения:', err));
              }else{
                let audio = new Audio('/audio/new_sms_consultant_chat.wav');
                audio.play().catch(err => console.error('Ошибка воспроизведения:', err));
              }


              // const test = new Audio('/audio/new_sms_consultant.wav');
              // test.play().catch(err => console.error('Ошибка воспроизведения:', err));
                this.getTodayMessages()

            });
        },
        translateStatus(status) {
            return this.statusTranslations[status] || status;
        },
        showModalShowChat($messageId) {
            this.isModalChatShow = true;
            this.messageId = $messageId
            this.setMessagesRead()
        },
      setMessagesRead() {
        ConsultationService.setConsultantMessagesRead(this.messageId).then(response => {
        })
      },
        closeModalShowChat() {
            this.getTodayMessages()
            this.isModalChatShow = false
        },
      new_sms() {
        let audio = new Audio('/audio/new_sms_consultant_2.wav');
        audio.play();
      },
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
/* Цвет иконки в зависимости от типа */
.icon-success {
  color: #4caf50;
}

.icon-error {
  color: #f44336;
}

.icon-danger {
  color: #ff9800;
}

.icon-info {
  color: #2196f3;
}
.no-messages {
  text-align: center;
  padding: 20px;
  font-size: 16px;
  font-weight: bold;
  color: #888;
}
.flashing-icon {
  animation: flash 1.5s infinite; /* Анимация будет повторяться бесконечно */
}
@keyframes flash {
  0% {
    opacity: 1; /* Полная видимость */
  }
  50% {
    opacity: 0; /* Иконка пропадает (мигает) */
  }
  100% {
    opacity: 1; /* Возвращается в норму */
  }
}
</style>
