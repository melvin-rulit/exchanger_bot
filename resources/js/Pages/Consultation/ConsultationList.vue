<template>
    <div class="main">
      <div class="table-container rounded-xl">
        <table class="w-full whitespace-nowrap">
            <thead class="head_table text-gray-800">
            <tr tabindex="0" class="focus:outline-none h-7 rounded">
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

            <tbody v-if="groupedMessages.length">

            <tr v-for="item of groupedMessages" tabindex="0" @click="showModalChat(item.last_message, item.client ?? item.last_message.client)" class="table_body inside_table focus:outline-none h-10 rounded hover:bg-gray-100">
                <td class="pl-4">
                    <div class="flex items-center text-gray-800">
                        <Icon icon="wpf:message-outline" width="26" height="26" :class="{'animate-fade-bounce': !item.last_message.is_message}"/>
                    </div>
                </td>
              <td class="">
                <div class="flex items-center">
                  <p v-if="item.last_message.messages !== null" class="text-sm leading-none text-gray-600 ml-2">
                    {{ item.last_message.messages }}
                    <span class="text-xs text-gray-400 ml-2">
        (от {{ item.last_message.sender_type === 'client' ? 'клиента' : 'менеджера' }})
      </span>
                  </p>
                  <div v-else class="flex items-center">
                    <img :src="item.last_message.image_url" alt="Изображение" class="rounded-md w-10 h-10 cursor-pointer" />
                    <span class="text-xs text-gray-400 ml-2">
        (от {{ item.last_message.sender_type === 'client' ? 'клиента' : 'менеджера' }})
      </span>
                  </div>
                </div>
              </td>

              <td class="">
                <div class="flex items-center">
                  <p v-if="item.last_message.is_close" class="text-sm leading-none text-gray-600 ml-2 flex items-center gap-1"><Icon icon="ci:chat-close" width="24" height="24" :class="iconColor"/> Чат закрыт</p>
                </div>
              </td>
              <td class="">
                <div class="flex items-center ml-2">
                  {{ (item.client ?? item.last_message.client)?.first_name || 'Клиент не найден' }}
<!--                  <span class="px-4 bg-gray-50 text-gray-800 rounded-md shadow-md">{{ item.client.first_name }}</span>-->
                </div>
              </td>
            </tr>
            <tr class="h-3">
            </tr>
            </tbody>

          <tbody v-else>
          <tr>
            <td class="absolute top-[48%] left-[47%]">
              <hollow-dots-spinner
                v-if="isLoadingSpiner"
                :animation-duration="1000"
                :dot-size="20"
                :dots-num="3"
                color="#4caf50"
              />
            </td>
            <td v-if="!messages.length && !isLoadingSpiner" class="absolute top-[47%] left-[44%] text-xl text-muted">На сегодня нет сообщений</td>
          </tr>
          </tbody>
        </table>
        <div class="pagination-wrapper">
          <Pagination
            :total="messageMeta.total"
            :limit="messageMeta.per_page"
            :currentPage="messageMeta.current_page"
            @page-change="getTodayMessages"
          />
        </div>
      </div>

      <div class="flex justify-between items-center rounded-lg shadow">

      </div>

      <div class="flex justify-between items-center rounded-lg shadow h-[20px]">
        <div  class="w-[420px] text-sm sm:text-base font-medium text-white mt-[67px] ml-6">

        </div>

        <div v-if="startFunction" class="text-sm sm:text-base font-medium text-white">
          <div class="flex items-center gap-3 mt-14">
            <Icon @click="toggleNotification(notificationSettings)" v-show="notificationSettings && notificationSettings.is_active" icon="system-uicons:bell-ringing" width="40" height="40" class="hover:text-gray-400 cursor-pointer" />
            <Icon @click="toggleNotification(notificationSettings)" v-show="notificationSettings && !notificationSettings.is_active" icon="system-uicons:bell-disabled" width="40" height="40" class="hover:text-gray-400 cursor-pointer" />
            <Icon icon="lsicon:find-outline" width="48" height="48" class="hover:text-gray-400 cursor-pointer" />
            <flat-pickr
              v-model="date"
              ref="calendar"
              :config="flatpickrConfig"
              @on-close=""
              class="invisible absolute z-10 ml-[110px] mb-4"
            />
            <Icon @click="openCalendar" icon="bi:calendar-date" width="38" height="38" :class="['hover:text-gray-400 cursor-pointer',isCalendarOpen ? 'text-gray-400' : '']"/>
            <Icon icon="material-symbols-light:app-registration-outline-rounded" width="48" height="48" class="hover:text-gray-400 cursor-pointer" />
            <Icon @click="clickShowLockScreen" icon="hugeicons:lock-sync-01" width="45" height="45" class="hover:text-gray-400 cursor-pointer"/>
          </div>
        </div>

        <AlertForNotification :message="alertMessage" :type="alertType" @clearMessages="clearAlertMessage" ref="alertComponent">
          <template #buttons>
            <div class="pl-4">
              <ButtonUI @click="alertButtonFunction" type="submit" color="green">{{alertButtonName}}</ButtonUI>
            </div>
          </template>
        </AlertForNotification>

        <div class="w-[450px] flex items-center justify-end gap-3 text-white mt-[50px]">
          <PinChatsInConsultantList
            v-for="chat in pinnedChats.filter(chat => !chat.order)"
            :key="chat.id"
            :chat = chat
            :onClick="() => showModalChat(chat)"
          />
        </div>
      </div>

      <ModalShowChat
        :is-active="isModalChatShow"
        :message="message"
        :client="client"
        @close="closeModalShowChat"
      />

      <SetLockScreenPassword
        :is-active="isVisibleSetPassword"
        @closeModal="closeScreenLockPassword"
      />

    </div>
</template>

<script>
import { eventBus } from '@/utils/eventBus.js'
import { useSound } from '@/helpers/useSound'
import { usePusher } from '@/helpers/usePusher'
import { handleApiError } from '@/helpers/errors.js'
import { getIconColorClass } from '@/helpers/iconColorClass.js'
import { UserService } from '@/services/UserService.js'
import { ConsultationService } from '@/services/ConsultationService.js'
import { useLockScreenStore } from '@/stores/lockScreenStore.js'
import ButtonUI from '@/Components/Button/ButtonUI.vue'
import Pagination from '@/Components/Pagination.vue'
import ModalLock from '@/Components/Modal/ModalLock.vue'
import Spiner from '@/Components/Spiner/Spiner.vue'
import { Icon } from '@iconify/vue';
import { HollowDotsSpinner } from 'epic-spinners'
import { useConsultationStore } from '@/stores/consultationStore'
import { useUserStore } from '@/stores/userStore.js'
import ModalShowChat from '@/Pages/Consultation/Chats/Modal/ModalShowChat.vue'
import AlertForNotification from '@/Components/Notifications/AlertForNotification.vue'
import PinChatsInConsultantList from '@/Pages/Consultation/Chats/PinedChats/PinChatsInConsultantList.vue'
import SetLockScreenPassword from '@/Pages/Order/Notifications/SetLockScreenPassword.vue'
import flatPickr from 'vue-flatpickr-component'
import { Russian } from "flatpickr/dist/l10n/ru.js"
import 'flatpickr/dist/flatpickr.css';

export default {
    components: { flatPickr, Spiner, SetLockScreenPassword, ModalLock, Pagination, ButtonUI, PinChatsInConsultantList, AlertForNotification, Icon, ConsultationService, ModalShowChat, HollowDotsSpinner},
    data: function () {
        return {
            messages: '',
            client: '',
            pinnedChats: [],
            form: {
                dateFrom: '',
                dateTo: '',
            },
            date: new Date(),
            isCalendarOpen: false,
            flatpickrConfig: {
              onOpen: () => {
                this.isCalendarOpen = true;
              },
              onClose: () => {
                this.isCalendarOpen = false;
              },
              allowInput: false,
              altFormat: 'd.m.Y',
              dateFormat: 'd.m.Y',
              locale: Russian
            },
            closeConsultation: null,
            isLoadingSpiner: true,
            messageMeta: {},
            currentPage: 1,
            query: '',
            alertMessage: '',
            alertButtonName: '',
            alertType: 'success',
            alertButtonFunction: '',
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
            isVisibleSetPassword: false,
        }
    },
  setup() {
    const { playSound, stopSound} = useSound()
    const consultationStore = useConsultationStore()
    const userStore = useUserStore()
    const lockStore = useLockScreenStore()
    return { playSound, stopSound, consultationStore, userStore, lockStore }
  },

    async mounted() {
      const { pusher } = usePusher()
      this.pusher = pusher
      eventBus.on('newMessage', this.handleNewMessage)
      await this.userStore.fetchUser()
      this.checkCloseConsultation()
      await this.getTodayMessages()
      await this.getPinedChat()
      await this.showLockScreen()
      this.spinerLoading()
    },
    computed: {
      iconColor() {
        return getIconColorClass(this.type);
      },
      notificationSettings() {
        return this.userStore.currentUser?.settings?.find(s => s.key === 'notification') || { is_active: false }
      },
      groupedMessages() {
        const messages = Array.isArray(this.messages) ? this.messages : [];

        const map = new Map();

        messages.forEach(msg => {
          const chatId = msg.chat_id;

          if (!map.has(chatId)) {
            map.set(chatId, {
              last_message: msg,
              client: msg.client, // запоминаем клиента
            });
          } else {
            const existing = map.get(chatId);
            if (new Date(msg.created_at) > new Date(existing.last_message.created_at)) {
              map.set(chatId, {
                last_message: msg,
                client: existing.client, // клиент берём из первого
              });
            }
          }
        });

        return Array.from(map.values());
      }
    },
    methods: {
      async getPinedChat() {
        try {
          const response = await UserService.getPinedChat();
          this.pinnedChats = response.data.data;
        } catch (error) {
          this.errors = handleApiError(error)
        }
      },
      async getTodayMessages(page = 1, query = '') {
        const response = await ConsultationService.getMessages(query, page)
        this.messages = response.data.data
        this.messageMeta = response.data.meta;
        this.currentPage = page;
        this.consultationStore.setMessages(this.messages)
      },
      async handleNewMessage(data) {
        if(!this.isModalChatShow) {
          await this.getTodayMessages()
        }
        if (this.isModalChatShow) {
          this.message = data
        }
      },
      checkCloseConsultation() {
        if (this.closeConsultation) {
          return;
        }
        this.closeConsultation = this.pusher.subscribe('consultation_closed')

        this.closeConsultation.bind('consultation_closed', async (data) => {
          this.closeModalShowChat()
        })
      },
      showModalChat($message, $client) {
            this.isModalChatShow = true;
            this.message = $message
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
        this.lockStore.showLockModal()
      },
      async showLockScreen() {
        const is_locked = await this.userStore.getUserLockScreen(this.$page.props.auth.user.id)

        if (!is_locked){
          return
        }

        this.lockStore.showLockModal()
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
      },
      async setMessagesRead() {
        try {
          this.consultationStore.markAsRead(this.message.id)
          await ConsultationService.setConsultantMessagesRead(this.message.id);
        } catch (error) {
          this.errors = handleApiError(error);
        }
      },
      async toggleNotification(notificationSettings) {
        try {
          const response = await UserService.toggleNotification(notificationSettings);
          const newIsActive = response.data.notification.is_active;

          const userStore = useUserStore();

          const settingsArray = userStore.currentUser.settings || [];
          const idx = settingsArray.findIndex(s => s.id === notificationSettings.id);

          if (idx !== -1) {
            const updatedSettings = [...settingsArray];
            updatedSettings[idx] = {
              ...updatedSettings[idx],
              is_active: newIsActive
            };

            userStore.setCurrentUser({
              ...userStore.currentUser,
              settings: updatedSettings
            });
          }
        } catch (error) {
          this.errors = handleApiError(error);
        }
      },
      openCalendar() {
        if (this.$refs.calendar?.fp) {
          this.isCalendarOpen = true
          this.$refs.calendar.fp.open()
        }
      },
      closeModalShowChat() {
          this.isModalChatShow = false
           this.getTodayMessages()
           this.getPinedChat()
        },
      spinerLoading() {
        if (this.messages) {
          this.isLoadingSpiner = false
        }
      },
      triggerErrorAlert($message, $buttonName, $buttonFunction) {
        this.alertMessage = $message;
        this.alertType = 'error';
        this.alertButtonName = $buttonName;
        this.alertButtonFunction = $buttonFunction;
        this.$refs.alertComponent.showAlert();
      },
      clearAlertMessage() {
        this.alertMessage = '';
      },
    },
}
</script>

<style lang="scss" scoped>
.main {
  min-height: 94vh;
  display: flex;
  flex-direction: column;
}
.table-container {
  flex-grow: 1;
  overflow-y: auto;
  max-height: calc(97vh - 89px);
  background-color: white;
}
.filters_block {
  margin-bottom: 10px;

  span {
    font-size: 11px;
  }
}
.head_table {
  border-bottom: 1px solid #e5e7eb;
}
.inside_table {
  background-color: white;
  cursor: pointer;
  border-bottom: 2px solid rgba(0, 0, 0, .09);
}
.inside_table:hover {
  background-color: #f3f4f6;
}
.pagination-wrapper {
  position: fixed;
  bottom: 65px;
  left: 16px;
}
.no-messages {
  text-align: center;
  padding: 20px;
  font-size: 16px;
  font-weight: bold;
  color: #888;
}
</style>
