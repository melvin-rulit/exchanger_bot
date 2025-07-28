<template>
  <Alert ref="alertComponent" :message="alertMessage" :type="alertType" />

  <div v-if="isActive" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="flex items-center gap-7 absolute top-10 right-[310px]">
      <PinChatsInChatModal
        v-for="chat in pinnedChats.filter(chat => chat.order === null && chat.user?.id === $page.props.auth.user.id)"
        :key="chat.id"
        :chats="chat"
        :pinnedChats="pinnedChats"
        :onClick="() => showNextModalChat(chat.order)"
      />
    </div>

    <div class="bg-white max-h-[90vh] rounded-lg overflow-hidden flex flex-col shadow-lg">

      <div class="p-2 bg-gray-100 border-b border-gray-300">
        <div class="flex justify-between items-center flex-wrap gap-2 max-h-20 overflow-auto pr-2">

          <!-- Левая часть: информация о заказе и кнопки -->
          <div class="flex items-center flex-wrap gap-2 ml-3">
            <div class="text-lg font-semibold text-black">Чат с клиентом</div>
            {{client.first_name}}
<!--            <span class="ml-2 text-black">№{{ message.id }}</span>-->
            <p v-if="!message.is_close" @click="closeChat"
               class="text-sm  hover:underline leading-none text-blue-500 ml-5 flex items-center gap-2 cursor-pointer">
              <Icon icon="qlementine-icons:close-all-16" width="24" height="24" />
              Завершить чат
            </p>
          </div>

          <div class="flex justify-between items-center">
            <div class="flex items-center gap-5">
              <p v-if="message.is_close" class="text-sm leading-none text-gray-600 ml-2 flex items-center gap-1">
                <Icon icon="ci:chat-close" width="24" height="24" :class="iconColorClass" />
                Чат закрыт
              </p>
            </div>
          </div>

          <!-- Правая часть: дата -->
          <div class="flex items-center gap-2 text-xs">
            <div v-if="pinedChat && !pinedChat.is_pinned" class="cursor-pointer flex items-center">
              <Icon @click="pinChat(selectedOrder)" icon="bi:pin-angle" width="20" height="20" />
            </div>
            <div v-if="pinedChat && pinedChat.is_pinned" class="cursor-pointer flex items-center">
              <Icon @click="unPinChat(null, pinedChat.id)" icon="bi:pin-angle-fill" width="20" height="20" />
            </div>
            <div class="text-gray-400 whitespace-nowrap">
              <span>Сегодня: {{ new Date().toLocaleDateString('ru-RU') }}</span>
            </div>

            <button
              @click.stop="close"
              class="text-black close-btn">
              <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" />
            </button>
          </div>
        </div>
      </div>

      <div @click.stop class="relative bg-white chat_container w-96 h-96 rounded-lg  flex flex-col">

        <div ref="chatContainer" class="flex-1 overflow-auto mb-4">
          <div v-for="(group, groupIndex) in groupedMessages" :key="'group-' + groupIndex">
            <div class="flex justify-center my-2">
              <span class="text-sm text-gray-800">{{ group.date }}</span>
            </div>

            <div v-for="(message, index) in group.messages" :key="'message-' + groupIndex + '-' + index" class="mb-4">
              <div :class="message.sender_type === 'user' ? 'flex items-start' : 'flex items-start justify-end'">

                <!-- Сообщение от поддержки -->
                <div v-if="message.sender_type === 'user'" class="flex items-center gap-2 ml-4">
                  <div class="flex items-center gap-1 text-xs text-gray-500">
                    <img v-if="message.user.image_url" :src="message.user.image_url" class="rounded-xl w-6 h-6"
                         alt="" />
                    <span>{{ message.user?.name || 'Гость' }} в {{ formatTime(message.created_at) }}</span>
                  </div>
                  <div class="rounded-md shadow-md text-gray-700">
                    <div class="p-2" v-if="message.message">
                      <p class="whitespace-pre-line">{{ message.message }}</p>
                    </div>
                    <template v-else-if="message.image_url">
                      <img @click="showModalScreenshot(message.image_url)" :src="message.image_url" alt="Изображение"
                           class="rounded-md w-10 h-10 cursor-pointer" />
                    </template>
                  </div>
                </div>

                <!-- Сообщение от клиента -->
                <div v-if="message.sender_type === 'client'" class="flex items-center gap-2 mr-6">
                  <div class="rounded-md shadow-md text-gray-700">
                    <div class="p-2" v-if="message.message">
                      {{ message.message }}
                    </div>
                    <template v-else="message.media && message.media.length">
                      <img @click="showModalScreenshot(message.image_url)" :src="message.image_url" alt="Изображение"
                           class="rounded-md w-10 h-10 cursor-pointer" />
                    </template>
                  </div>
                  <div class="flex items-center gap-1 text-xs text-gray-500">
                    <img v-if="client && client.image_url" :src="client.image_url" class="rounded-xl w-6 h-6" alt="" />
                    <span>{{ client.first_name }} в {{ formatTime(message.created_at) }}</span>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>

        <div v-if="!message.is_close" class="flex items-center">
          <div class="mr-4 cursor-pointer">

            <img
              v-if="newMessagePhoto.photo_path"
              :src="newMessagePhoto.previewUrl"
              alt="preview"
              class="w-22 h-20 object-cover rounded"
              title="выбрать другое"
              @click="triggerFileInput"
            />

            <Icon
              v-else
              @click="triggerFileInput"
              icon="streamline-emojis:paperclip"
              width="30"
              height="30"
            />

            <file-input
              ref="fileInputRef"
              v-model="newMessagePhoto.photo_path"
              :error="errors"
              type="file"
              accept="image/*"
            />
          </div>

          <div v-if="!newMessagePhoto.photo_path" class="mr-4 cursor-pointer text-black">
            <Icon @click="openTemplates" icon="icon-park-twotone:text-message" width="30" height="30" />
          </div>

          <div v-if="showTemplates"
               class="absolute bottom-[110px] left-[158px] z-20 bg-white border rounded-lg shadow-lg animate-fade-in">
            <div class="max-h-60 overflow-y-auto">
              <div
                v-for="(template, index) in templates"
                :key="index"
                @click="selectTemplate(template.text)"
                class="px-4 py-2 cursor-pointer text-black hover:bg-gray-100">
                {{ template.text }}
              </div>
            </div>
          </div>

          <textarea
            ref="textareaRef"
            v-model="newMessage"
            @input="resizeTextarea"
            @keydown="handleEnterKey"
            @paste="handlePaste"
            class="border border-gray-300 p-2 w-[85%] max-h-[80px] resize-none rounded-md text-black focus:outline-none focus:ring-0 focus:border-gray-400"
            :placeholder="inputPlaceholder"/>

          <button
            @click="sendMessages"
            class="bg-blue-500 hover:bg-blue-600 text-white p-3 ml-14 rounded-md transition-colors duration-200 cursor-pointer "
            aria-label="Отправить">Отправить
          </button>

          <div class="relative group">
            <div class="sm:ms-6 sm:flex">
              <span class="text-xs text-[color:theme(colors.gray.400)] rounded opacity-0 group-hover:opacity-100 transition-opacity absolute bottom-[0.1rem] right-[12rem]"> Очистить</span>
              <Icon @click="clearInput"
                    class="cursor-pointer absolute bottom-[-1rem] right-[9rem] text-black"
                    icon="fluent:text-clear-formatting-32-light" width="32" height="32" />
            </div>
          </div>
        </div>
      </div>

      <ModalShowOrderScreenshot
        :is-active="isModalScreenshotShow"
        :currentImageUrl="currentImageUrl"
        @close="closeModalShowOrderScreenshot"
      />
    </div>
  </div>
</template>

<script>
import { eventBus } from '@/utils/eventBus.js'
import { ConsultationService } from '@/services/ConsultationService.js'
import { TemplateService } from '@/services/TemplateMessagesService.js'
import { Icon } from '@iconify/vue'
import ModalShowOrderScreenshot from '@/Pages/Order/Modal/ModalShowOrderScreenshot.vue'
import { handleApiError } from '@/helpers/errors.js'
import { getIconColorClass } from '@/helpers/iconColorClass'
import Alert from '@/Components/Notifications/Alert.vue'
import { UserService } from '@/services/UserService.js'
import FileInput from '@/Components/Input/FileInput.vue'
import PinChatsInChatModal from '@/Pages/Order/Chats/PinedChats/PinChatsInChatModal.vue'
import ModalShowOrderDetail from '@/Pages/Order/Modal/ModalShowOrderDetail.vue'

export default {
  components: {
    ModalShowOrderDetail,
    PinChatsInChatModal,
    FileInput,
    Alert,
    ModalShowOrderScreenshot,
    ConsultationService,
    TemplateService,
    Icon,
  },
  props: {
    isActive: {
      type: Boolean,
      default: false,
      required: true,
    },
    message: {
      required: true,
    },
    client: {
      required: true,
    },
  },
  data() {
    return {
      messages: [],
      newMessagePhoto: {
        photo_path: null,
        previewUrl: null,
      },
      order: {},
      clientMessages: [],
      supportMessages: [],
      newMessage: '',
      statusTranslations: {
        country_input: 'выбора страны',
        bank_input: 'выбора банка',
        amount_input: 'ввода суммы обмена',
      },
      messageChannel: null,
      isModalScreenshotShow: false,
      pinnedChats: [],
      pinedChat: false,
      currentImageUrl: '',
      inputPlaceholder: 'Введите сообщение...',
      errors: '',
      type: 'error',
      alertMessage: '',
      alertType: 'success',
      pusher: null,
      showTemplates: false,
      templates: [],
    }
  },
  beforeMount() {
    this.getPinedChat()
  },
  async mounted() {
    eventBus.on('newMessage', this.handleNewMessage)
    this.checkMessagesUpdate()
    //await this.getPinedChat();
  },
  computed: {
    groupedMessages() {
      const grouped = []
      let currentGroup = null

      const sortedMessages = [...this.messages].sort((a, b) => new Date(a.created_at) - new Date(b.created_at))

      sortedMessages.forEach((message) => {
        const messageDate = new Date(message.created_at)
        const messageDateString = messageDate.toLocaleDateString()

        if (!currentGroup || currentGroup.date !== messageDateString) {
          currentGroup = {
            date: messageDateString,
            showDate: true,
            messages: [message],
          }
          grouped.push(currentGroup)
        } else {
          currentGroup.messages.push(message)
          currentGroup.showDate = false
        }
      })

      return grouped
    },
    iconColorClass() {
      return getIconColorClass(this.type)
    },
  },
  methods: {
    openTemplates() {
      if (!this.templates.length) {
        //TODO сделать вывод уведомления, что нет шаблонных сообщений
        return
      }
      this.showTemplates = !this.showTemplates
    },
    selectTemplate(template) {
      this.newMessage = template
      this.showTemplates = false
    },
    formatTime(datetime) {
      const date = new Date(datetime)
      return date.toLocaleTimeString('ru-RU', {
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'Europe/Chisinau',
      })
    },
    async getTodayMessages() {
      try {
        const response = await ConsultationService.getTodayMessages(this.message.id)
        this.messages = response.data.data
        this.organizeMessages()
        await this.$nextTick(() => this.scrollToBottom())
      } catch (error) {
        this.errors = handleApiError(error)
      }
    },
    async getTemplatesMessages() {
      try {
        const response = await TemplateService.getTemplateMessages()
        this.templates = Array.isArray(response.data.template.messages) ? response.data.template.messages : []
      } catch (error) {
        this.templates = []
        this.errors = handleApiError(error)
      }
    },
    async handleNewMessage(newMessages) {
      await this.getTodayMessages()
    },
    checkMessagesUpdate() {
      // if (this.messageChannel) {
      //   return;
      // }

      // this. messageChannel = this.pusher.subscribe('consultation');
      // this.messageChannel.bind('new_message', (data) => {
      //
      // if (this.isActive) {
      //   this.getTodayMessages()
      // }
      // });
    },
    organizeMessages() {
      this.clientMessages = this.messages.filter(message => message.sender_type === 'user')
      this.supportMessages = this.messages.filter(message => message.sender_type === 'client')
    },
    handleEnterKey(event) {
      if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        this.sendMessages();
      }
    },
    resizeTextarea() {
      const el = this.$refs.textareaRef;
      if (!el) return;

      el.style.height = 'auto';
      el.style.height = `${el.scrollHeight}px`;
    },
    async sendMessages() {
      const hasText = this.newMessage.trim() !== ''
      const hasImage = this.newMessagePhoto?.photo_path !== null

      if (!hasText && !hasImage) return

      try {
        let response

        if (hasImage) {
          response = await ConsultationService.sendConsultantMessagesWithImage(this.newMessagePhoto.photo_path, this.message.chat_id, this.newMessage)
        } else {
          response = await ConsultationService.sendConsultantMessages(
            this.message.id,
            this.newMessage,
          )
        }
        this.messages.push(response.data.data)
        this.newMessage = ''
        this.inputPlaceholder = 'Введите сообщение...'
        this.newMessagePhoto.photo_path = null

        await this.$nextTick(() => {
          this.scrollToBottom()
        })
      } catch (error) {
        this.errors = handleApiError(error)
      }
    },
    handlePaste(e) {
      const items = e.clipboardData?.items
      if (!items) return

      for (const item of items) {
        if (item.kind === 'file') {
          const file = item.getAsFile()
          if (file && file.type && file.type.startsWith('image/')) {
            const objectUrl = URL.createObjectURL(file)
            this.newMessagePhoto.photo_path = file
            this.newMessagePhoto.previewUrl = objectUrl
            break
          }
        }
      }
    },
    async getPinedChat() {
      try {
        const response = await UserService.getPinedChat()
        this.pinnedChats = response.data.data
      } catch (error) {
        this.errors = handleApiError(error)
      }
    },
    pinChat(orderId = null, clientId) {
      UserService.pinChat(orderId, clientId)
        .then(response => {
          if (response.data.data) {
            this.pinedChat = response.data.data
            this.getPinedChat()
          }
        })
        .catch(error => {
          this.errors = handleApiError(error)
        })
    },
    unPinChat(order, pinedChatId) {
      UserService.unPinChat(order.id, order.client.id, pinedChatId)
        .then(response => {
          if (response.data.data) {
            this.pinedChat = response.data.data
            this.getPinedChat()
          }
        })
        .catch(error => {
          this.errors = handleApiError(error)
        })
    },
    triggerFileInput() {
      this.inputPlaceholder = 'Отправьте выбранное изображение. Можете прикрепить к нему сообщение'
      this.$refs.fileInputRef.browse()
    },
    scrollToBottom() {
      this.$nextTick(() => {
        const chatContainer = this.$refs.chatContainer
        if (chatContainer) {
          chatContainer.scrollTop = chatContainer.scrollHeight
        }
      })
    },
    showModalScreenshot(image) {
      this.currentImageUrl = image
      this.isModalScreenshotShow = true
    },
    closeModalShowOrderScreenshot() {
      this.isModalScreenshotShow = false
    },
    triggerErrorAlert($message) {
      this.alertMessage = $message
      this.alertType = 'error'
      this.$refs.alertComponent.showAlert()
    },
    translateStatus(status) {
      return this.statusTranslations[status] || status
    },
    clearInput() {
      this.newMessage = ''
      this.newMessagePhoto.photo_path = null
      this.inputPlaceholder = 'Введите сообщение...'
    },
    closeChat() {
      try {
        const response = ConsultationService.closeChat(this.message.chat_id)
        //this.messages = response.data.data;
      } catch (error) {
        this.errors = handleApiError(error)
      }
    },
    close() {
      this.showTemplates = false
      this.messages = []
      this.$emit('close')
    },
  },
  watch: {
    isActive(newVal) {
      if (newVal) {
        this.getTodayMessages()
        this.getTemplatesMessages()
        this.$nextTick(() => {
          this.scrollToBottom()
        })
      }
    },
    message(newVal) {
      if (newVal) {
        if (newVal.pinned_messages?.length) {
          this.pinedChat = newVal.pinned_messages[0]
        } else {
          this.pinedChat = {}
        }
      }
    },
    'newMessagePhoto.photo_path'(file) {
      if (file && file.type.startsWith('image/')) {
        this.newMessagePhoto.previewUrl = URL.createObjectURL(file)
      } else {
        this.newMessagePhoto.previewUrl = null
      }
    },
  },
}
</script>

<style scoped>
.fixed {
  position: fixed;
  backdrop-filter: blur(4px);
  background-color: rgba(0, 0, 0, 0.9);
}

@media (prefers-color-scheme: dark) {
  .dark\:bg-gray-800 {
    background-color: transparent !important;
  }
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
  padding-top: 0.5rem;
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

/* Оформление сообщения */
.bg-blue-100 {
  background-color: #ebf8ff;
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

.close-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  transition: transform 0.2s ease;

  &:hover {
    transform: scale(1.2);
  }
}
</style>
