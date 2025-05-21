<template>
    <div class="main">
      <div class="table-container">

        <table class="w-full whitespace-nowrap">
            <thead v-if="messages.length" class="head_table text-gray-800">
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
              <td>
                <div class="flex">
                  <p class="font-semibold pl-2">Клиент</p>
                </div>
              </td>
            </tr>
            </thead>

            <tbody v-if="messages.length">

            <tr v-for="message of messages" tabindex="0" @click="showModalShowChat(message.id, message.client)" class="table_body inside_table focus:outline-none h-10 rounded hover:bg-gray-100">
                <td class="pl-4">
                    <div class="flex items-center text-gray-800">
                        <Icon icon="wpf:message-outline" width="26" height="26" :class="{'animate-fade-bounce': !message.is_message}"/>
                    </div>
                </td>
              <td class="">
                <div class="flex items-center">
                  <p v-if="message.messages !== null" class="text-sm leading-none text-gray-600 ml-2">{{ message.messages }}</p>
                  <img v-else :src="message.image_url" alt="Изображение" class="rounded-md w-10 h-10 cursor-pointer" />
                </div>
              </td>
              <td class="">
                <div class="flex items-center">
                  <p v-if="message.is_close" class="text-sm leading-none text-gray-600 ml-2 flex items-center gap-1"><Icon icon="ci:chat-close" width="24" height="24" :class="iconColorClass"/> Чат закрыт клиентом</p>
                </div>
              </td>
              <td class="">
                <div class="flex items-center">
                  <span class="px-4 bg-gray-50 text-gray-800 rounded-md shadow-md">{{ message.client.first_name }}</span>
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

      <div class="flex justify-between items-center rounded-lg shadow">
        <div class="w-[450px] text-sm sm:text-base font-medium text-gray-700">
          <Pagination
            :total="messageMeta.total"
            :limit="messageMeta.per_page"
            :currentPage="messageMeta.current_page"
            @page-change="getTodayMessages"
          />
        </div>

        <div v-if="startFunction" class="text-sm sm:text-base font-medium text-white">
          <div class="flex items-center gap-3 mt-5">
            <Icon icon="arcticons:filterbox" width="48" height="48" class="hover:text-gray-400 cursor-pointer" />
            <Icon icon="lsicon:find-outline" width="48" height="48" class="hover:text-gray-400 cursor-pointer" />
            <Icon icon="bi:calendar-date" width="41" height="41" class="hover:text-gray-400 cursor-pointer" />
            <Icon icon="material-symbols-light:app-registration-outline-rounded" width="48" height="48" class="hover:text-gray-400 cursor-pointer" />
            <Icon @click="clickShowLockScreen" icon="hugeicons:lock-sync-01" width="45" height="45" class="hover:text-gray-400 cursor-pointer"/>

            <AlertForNotification :message="alertMessage" :type="alertType" @clearMessages="clearAlertMessage" ref="alertComponent">
              <template #buttons>
                <div class="pl-4">
                  <ButtonUI @click="alertButtonFunction" type="submit" color="green">{{alertButtonName}}</ButtonUI>
                </div>
              </template>
            </AlertForNotification>

          </div>
        </div>

        <div class="w-[450px] flex items-center justify-end gap-3 text-white mt-4">
          <PinChatsInOrderList
            v-for="chat in pinnedChats"
            :key="chat.id"
            :orderFullInfo="order"
            :order="chat.order"
          />
        </div>
      </div>

      <ModalShowChat
        :is-active="isModalChatShow"
        :messageId="messageId"
        :client="client"
        @close="closeModalShowChat"
      />

      <ModalLock
        :is-active="isModalLockShow"
        @unlocked="unLockScreen"
      />
    </div>
</template>

<script>
import { usePusher } from '@/helpers/usePusher'
import { useSound } from '@/helpers/useSound'
import { Icon } from '@iconify/vue';
import ModalShowChat from '@/Pages/Consultation/Chats/Modal/ModalShowChat.vue'
import { ConsultationService } from '@/services/ConsultationService.js'
import { useConsultationStore } from '@/stores/consultationStore'
import AlertForNotification from '@/Components/AlertForNotification.vue'
import PinChatsInOrderList from '@/Pages/Order/Chats/PinedChats/PinChatsInOrderList.vue'
import ButtonUI from '@/Components/ButtonUI.vue'
import Pagination from '@/Components/Pagination.vue'
import { UserService } from '@/services/UserService.js'
import { useUserStore } from '@/stores/userStore.js'
import ModalLock from '@/Components/ModalLock.vue'

export default {
    components: {ModalLock, Pagination, ButtonUI, PinChatsInOrderList, AlertForNotification, Icon, ConsultationService, ModalShowChat},
    data: function () {
        return {
            messages: '',
            messageId: '',
            client: '',
            pinnedChats: [],
            form: {
                dateFrom: '',
                dateTo: '',
            },
            statusTranslations: {
                new: 'Новый',
                active: 'Активный',
                deleted: 'Удален',
            },
            consultantChannel: null,
            messageMeta: {},
            currentPage: 1,
            query: '',
            type: 'error',
            limit: 5,
            total: 1,
            errors: '',
            message: null,
            loading: false,
            startFunction: true,
            showFilter: false,
            isModalChatShow: false,
            isModalLockShow: false,
        }
    },
  setup() {
    const { playSound, stopSound} = useSound()
    const consultationStore = useConsultationStore()
    const userStore = useUserStore()
    return { playSound, stopSound, consultationStore, userStore }
  },

    async mounted() {
      const { pusher } = usePusher()
      this.pusher = pusher

      await this.userStore.fetchUser()
      await this.getTodayMessages()
      this.checkNewMessagesUpdate()
      await this.getPinedChat()
      await this.showLockScreen()
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
      async getPinedChat() {
        try {
          const response = await UserService.getPinedChat();
          this.pinnedChats = response.data.data;
        } catch (error) {
          this.errors = error.response?.data?.errors || 'Ошибка загрузки данных';
        }finally {

        }
      },
      async getTodayMessages(page = 1, query = '') {
        const response = await ConsultationService.getMessages(query, page)
        this.messages = response.data.data
        this.messageMeta = response.data.meta;
        this.currentPage = page;
        this.consultationStore.setMessages(this.messages)
      },
        checkNewMessagesUpdate() {
            this.consultantChannel = this.pusher.subscribe('consultation');

            this.consultantChannel.bind('new_message', (data) => {
              if (!this.isModalChatShow){
                this.playSound('new_sms.mp3')
                // let audio = new Audio('/audio/new_sms_consultant_2.wav');
                // audio.play().catch(err => console.error('Ошибка воспроизведения:', err));
              }else{
                this.playSound('new_sms.mp3')
                // let audio = new Audio('/audio/new_sms_consultant_chat.mp3');
                // audio.play().catch(err => console.error('Ошибка воспроизведения:', err));
              }
                this.getTodayMessages()
            });
        },
        showModalShowChat($messageId, $client) {
            this.isModalChatShow = true;
            this.messageId = $messageId
            this.client = $client
            this.setMessagesRead()
        },
      async clickShowLockScreen() {

        if (await this.userStore.getUserLockPassword(this.$page.props.auth.user.id) === null){
          this.triggerErrorAlert('Вы не задали пароль для этого действия', 'Установить', this.setScreenLockPassword);
          return
        }
        const is_lock = await this.userStore.getUserLockScreen(this.$page.props.auth.user.id)

        if (!is_lock){
          await this.saveIsLock()
        }
        this.isModalLockShow = true
      },
      async showLockScreen() {
        const is_locked = await this.userStore.getUserLockScreen(this.$page.props.auth.user.id)

        if (!is_locked){
          return
        }

        this.isModalLockShow = true
      },
      unLockScreen() {
        this.isModalLockShow = false
      },
      async saveIsLock() {
        await UserService.saveIsLock()
      },
      setScreenLockPassword() {
        this.startFunction = false
        this.isVisibleSetPassword = true

      },
      async closeScreenLockPassword() {
        this.startFunction = true
        this.isVisibleSetPassword = false
        await this.userStore.fetchUser()
        this.getOrders()
      },
      setMessagesRead() {
        ConsultationService.setConsultantMessagesRead(this.messageId).then(response => {
        })
      },
        closeModalShowChat() {
            this.getTodayMessages()
            this.isModalChatShow = false
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
.no-messages {
  text-align: center;
  padding: 20px;
  font-size: 16px;
  font-weight: bold;
  color: #888;
}
</style>
