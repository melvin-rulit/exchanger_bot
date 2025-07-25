<template>
  <Alert ref="alertComponent" :message="alertMessage" :type="alertType" />

  <div v-if="isActive" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="flex items-center gap-7 absolute top-10 right-[310px]">

      <PinChatsInChatModal
        v-for="chat in pinnedChats.filter(chat => chat.order && chat.order.user?.id === $page.props.auth.user.id)"
        :key="chat.id"
        :chats="chat"
        :selectedOrderId="orderId"
        :pinnedChats="pinnedChats"
        :onClick="() => showNextModalChat(chat.order)"
      />
    </div>

    <div class="bg-white max-h-[90vh] rounded-lg overflow-hidden flex flex-col shadow-lg">

      <div class="p-2 bg-gray-100 border-b border-gray-300">
        <div class="flex justify-between items-center flex-wrap gap-2 max-h-20 overflow-auto pr-2">

          <!-- Левая часть: информация о заказе и кнопки -->
          <div class="flex items-center flex-wrap gap-2 ml-3">
            <div class="text-lg font-semibold">Заказ</div>
            <span class="ml-1">№{{ orderId }}</span>

            <span @click="showModalOrderDetail" class="text-xs text-blue-500 hover:underline cursor-pointer ml-4">Перейти к заказу</span>
          </div>

                    <div class="flex justify-between items-center">


                      <div class="flex items-center gap-3 w-[370px]">
                        <div v-if="localOrder?.media?.length && receiptNotice">
                        <div v-for="file in localOrder.media" :key="file.id">
                          <img
                            @click="showModalScreenshot(file.original_url)"
                            :src="file.original_url"
                            alt="Чек"
                            class="rounded-md w-8 h-8 cursor-pointer"/>
                        </div>
                        </div>
                        <div v-if="localOrder?.client?.status === 'send_screenshot' && receiptNotice" class="flex items-start gap-2 p-1 text-sm text-green-800 bg-green-100 border border-green-300 rounded-lg shadow-sm animate-fade-bounce">
                          <Icon icon="fxemoji:left" width="20" height="20" class="self-center"/>
                          <div>
                            <span class="font-medium">К этому заказу клиент выслал чек</span>
                          </div>
                        </div>
                      </div>


                    </div>

          <!-- Правая часть: дата -->
          <div class="flex items-center gap-2 text-xs">
            <div v-if="pinedChat && !pinedChat.is_pinned" class="cursor-pointer flex items-center">
              <Icon @click="pinChat(selectedOrder)" icon="bi:pin-angle" width="20" height="20"/>
            </div>
            <div v-if="pinedChat && pinedChat.is_pinned" class="cursor-pointer flex items-center">
              <Icon @click="unPinChat(null, pinedChat.id)" icon="bi:pin-angle-fill" width="20" height="20"/>
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

          <div class="flex justify-between items-center pb-2 ">
            <div class="flex items-center gap-3 w-[370px]">
              <div v-if="localOrder?.media?.length">
              <div v-for="file in localOrder.media" :key="file.id">
                <img
                  @click="showModalScreenshot(file.original_url)"
                  :src="file.original_url"
                  alt="Чек"
                  class="rounded-md w-8 h-8 cursor-pointer"/>
              </div>
              </div>
              <div v-if="localOrder?.client?.status === 'send_screenshot' && receiptNotice" class="flex items-start gap-2 p-1 text-sm text-green-800 bg-green-100 border border-green-300 rounded-lg shadow-sm animate-fade-bounce">
                <Icon icon="fxemoji:left" width="20" height="20" class="self-center"/>
                <div>
                  <span class="font-medium">К этому заказу клиент выслал чек</span>
                </div>
              </div>
            </div>
          </div>


          <div ref="chatContainer" class="flex-1 overflow-auto mb-4">

                <div v-for="(group, groupIndex) in groupedMessages" :key="'group-' + groupIndex">
                    <div class="flex justify-center my-2">
                        <span class="text-sm ">{{ group.date }}</span>
                    </div>

                  <div v-for="(message, index) in group.messages" :key="'message-' + groupIndex + '-' + index" class="mb-4">
                    <div :class="message.sender_type === 'user' ? 'flex items-start' : 'flex items-start justify-end'">

                      <!-- Сообщение от поддержки -->
                      <div v-if="message.sender_type === 'user'" class="flex items-center gap-2 ml-4">
                        <div class="flex items-center gap-1 text-xs text-gray-500">
                          <img v-if="message.user.image_url" :src="message.user.image_url" class="rounded-xl w-6 h-6" alt=""/>
                          <span>{{ message.user?.name || 'Гость' }} в {{formatTime(message.created_at)}}</span>
                        </div>
                        <div class="rounded-md shadow-md text-gray-700">
                          <div class="p-2" v-if="message.message">
                            <p class="whitespace-pre-line">{{ message.message }}</p>
                          </div>
                          <template v-else-if="message.image_url">
                            <img @click="showModalScreenshot(message.image_url)" :src="message.image_url" alt="Изображение" class="rounded-md w-10 h-10 cursor-pointer" />
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
                            <img @click="showModalScreenshot(message.image_url)" :src="message.image_url" alt="Изображение" class="rounded-md w-10 h-10 cursor-pointer" />
                          </template>
                        </div>

                        <div class="flex items-center gap-1 text-xs text-gray-500">
                          <img v-if="order.client && order.client.image_url" :src="order.client.image_url" class="rounded-xl w-6 h-6"  alt=""/>
                          <span>{{order.client.first_name}} в {{formatTime(message.created_at)}}</span>
                        </div>

                      </div>

                    </div>
                  </div>
                </div>
            </div>

            <div class="flex items-center">
              <div class="mr-4 cursor-pointer">
                <img
                  v-if="newMessagePhoto.photo_path"
                  :src="newMessagePhoto.previewUrl"
                  alt="preview"
                  class="w-20 h-20 object-cover rounded"
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

              <div v-if="!newMessagePhoto.photo_path" class="mr-4 cursor-pointer"> <Icon @click="openTemplates" icon="icon-park-twotone:text-message" width="30" height="30" /></div>
              <div v-if="!newMessagePhoto.photo_path" class="mr-4 cursor-pointer"> <Icon @click="sendRequisite" icon="wpf:bank-cards" width="30" height="30" /></div>

              <div v-if="showTemplates" class="absolute bottom-[110px] left-[200px] z-20 bg-white border rounded-lg shadow-lg animate-fade-in">
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
                @paste="handlePaste"
                @keydown="handleEnterKey"
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
                  <Icon @click="clearInput" class="cursor-pointer absolute bottom-[-1rem] right-[9rem]" icon="fluent:text-clear-formatting-32-light" width="32" height="32" />
                </div>
              </div>
            </div>
        </div>

      <ModalShowOrderScreenshot
        :is-active="isModalScreenshotShow"
        :currentImageUrl="currentImageUrl"
        @close="closeModalShowOrderScreenshot"
      />

      <ModalShowOrderDetail
        :is-active="isModalShowOrderDetail"
        :selectedOrder="selectedOrder"
        :selectedUser="selectedUser"
        :clientName="clientName"
        @close="closeModalOrderDetail"
      />
    </div>
    </div>
</template>

<script>
import { OrdersService } from '@/services/OrdersService.js'
import { TemplateService } from '@/services/TemplateMessagesService.js'
import { UserService } from '@/services/UserService.js'
import { usePusher } from '@/helpers/usePusher'
import { useReminder } from '@/helpers/useReminder'
import { handleApiError } from '@/helpers/errors.js'
import { eventBus } from '@/utils/eventBus.js'
import { useOrdersStore } from '@/stores/ordersStore'
import Alert from '@/Components/Notifications/Alert.vue'
import { Icon } from '@iconify/vue';
import ModalShowOrderScreenshot from '@/Pages/Order/Modal/ModalShowOrderScreenshot.vue'
import ModalShowOrderDetail from '@/Pages/Order/Modal/ModalShowOrderDetail.vue'
import PinChatsInChatModal from '@/Pages/Order/Chats/PinedChats/PinChatsInChatModal.vue'
import { HollowDotsSpinner } from 'epic-spinners'
import FileInput from '@/Components/Input/FileInput.vue'

export default {
  components: { FileInput, ModalShowOrderDetail, Alert, ModalShowOrderScreenshot, Icon, PinChatsInChatModal, HollowDotsSpinner},
    props: {
        isActive: {
            type: Boolean,
            default: false,
            required: true
        },
      orderId: {
            required: true
        },
      order: {
            required: true
        },
      selectedUser: {
        type: Object,
        required: true
      },
      selectedOrder: {
        type: Object,
        required: true
      },
      clientName: {
        required: true
      },
    },
    data() {
        return {
            messages: [],
            pinnedChats: [],
            clientMessages: [],
            supportMessages: [],
            localOrder: { ...this.selectedOrder },
            orderIdForGoToDetailIfNextModalChat: null,
            newMessage: '',
            newMessagePhoto: {
              photo_path: null,
              previewUrl: null,
            },
            messageChannel: null,
            isModalScreenshotShow: false,
            isModalShowOrderDetail: false,
            currentImageUrl: '',
            showTemplates: false,
            pinedChat: false,
            templates: [],
            errors: '',
            alertMessage: '',
            inputPlaceholder: 'Введите сообщение...',
            alertType: 'success',
            pusher: null,
        };
    },
  setup() {
    const ordersStore = useOrdersStore()
    const { removeReminder } = useReminder()
    return { ordersStore, removeReminder}
  },
  beforeMount() {
    this.getPinedChat();
  },
  beforeUnmount() {
    eventBus.off('newChek', this.onOrderUpdated)
  },
  async mounted() {
    const { pusher } = usePusher()
    this.pusher = pusher
    eventBus.on('newChek', this.onOrderUpdated)
    this.checkOrdersUpdate();
    await this.getPinedChat();
  },
    computed: {
            groupedMessages() {
                const grouped = [];
                let currentGroup = null;
                const sortedMessages = [...this.messages].sort((a, b) => new Date(a.created_at) - new Date(b.created_at));

                sortedMessages.forEach((message) => {
                    const messageDate = new Date(message.created_at);
                    const messageDateString = messageDate.toLocaleDateString();

                    if (!currentGroup || currentGroup.date !== messageDateString) {
                        currentGroup = {
                            date: messageDateString,
                            showDate: true,
                            messages: [message],
                        };
                        grouped.push(currentGroup);
                    } else {
                        currentGroup.messages.push(message);
                        currentGroup.showDate = false;
                    }
                });

                return grouped;
            },
      receiptNotice() {
        const ordersStore = useOrdersStore()
        return !!ordersStore.receiptNoticeByOrderId[this.orderId]
      }
    },
    methods: {
      formatTime(datetime) {
        const date = new Date(datetime);
        return date.toLocaleTimeString('ru-RU', {
          hour: '2-digit',
          minute: '2-digit',
          timeZone: 'Europe/Chisinau'
        });
      },
      async getOrder() {
        try {
          const response = await OrdersService.getOrder(this.orderId);
          this.messages = response.data.data;
          this.organizeMessages();
          await this.$nextTick(() => this.scrollToBottom());
        } catch (error) {
          this.errors = handleApiError(error)
        }finally {
          await this.setMessagesRead()
        }
      },
      async getPinedChat() {
        try {
          const response = await UserService.getPinedChat();
          this.pinnedChats = response.data.data;
        } catch (error) {
          this.errors = handleApiError(error)
        }
      },
      async getTemplatesMessages() {
        try {
          const response = await TemplateService.getTemplateMessages();
          this.templates = Array.isArray(response.data.template.messages) ? response.data.template.messages: [];
        } catch (error) {
          this.templates = [];
          this.errors = handleApiError(error)
        }
      },
      async setMessagesRead() {
        try {
          const response = await OrdersService.setOrderMessagesRead(this.orderId);
        } catch (error) {
          this.errors = handleApiError(error)
        }
      },
        checkOrdersUpdate() {
            this.messageChannel =  this.pusher.subscribe('send_message');

            this.messageChannel.bind('send_message', (data) => {

            if (this.isActive) {this.getOrder() }
            });
        },
        organizeMessages() {
            this.clientMessages = this.messages.filter(message => message.sender_type === 'user');
            this.supportMessages = this.messages.filter(message => message.sender_type === 'client');
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
        const hasText = this.newMessage.trim() !== '';
        const hasImage = this.newMessagePhoto?.photo_path !== null;

        if (!hasText && !hasImage) return;

        if (hasText && this.isRequisiteMessage(this.newMessage)) {
          const matches = this.newMessage.match(/вот реквизиты:\s*(.+)/i);
          this.removeReminder(this.orderId);
          if (!matches || !matches[1].trim()) {
            this.triggerErrorAlert('Вы не указали реквизиты после фразы "Вот реквизиты:"');
            return;
          }
        }

        try {
          let response;

          if (hasImage) {
            response = await OrdersService.sendOrderMessagesWithImage(this.orderId, this.newMessagePhoto.photo_path, this.newMessage);
          } else {
            response = await OrdersService.sendOrderMessages(
              this.orderId,
              this.newMessage,
              this.isRequisiteMessage(this.newMessage)
            );
          }

          this.messages.push(response.data.order.message);
          await this.$nextTick(() => {
            this.scrollToBottom();
          });
        } catch (error) {
          this.errors = handleApiError(error);
        } finally {
          this.newMessage = '';
          this.inputPlaceholder = 'Введите сообщение...'
          this.newMessagePhoto.photo_path = null;
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
      onOrderUpdated(updatedOrder) {
        if (updatedOrder.id === this.localOrder.id) {
          this.localOrder = { ...this.localOrder, ...updatedOrder }
        }
      },
      triggerFileInput() {
        this.inputPlaceholder = 'Отправьте выбранное изображение. Можете прикрепить к нему сообщение'
        this.$refs.fileInputRef.browse()
      },
      scrollToBottom() {
        this.$nextTick(() => {
          const chatContainer = this.$refs.chatContainer;
          if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
          }
        });
      },
      showNextModalChat(order) {
        this.orderIdForGoToDetailIfNextModalChat = order
        this.close(order, true)
      },
      showModalScreenshot(image) {
        this.currentImageUrl = image
        this.isModalScreenshotShow = true
      },
      closeModalShowOrderScreenshot() {
        this.isModalScreenshotShow = false
      },
      async showModalOrderDetail() {
        const order = Object.keys(this.orderIdForGoToDetailIfNextModalChat ?? {}).length? this.orderIdForGoToDetailIfNextModalChat: this.localOrder;
        let orderCheck = '';
        if (this.ordersStore.receiptNoticeByOrderId[order.id]) {
          const imageUrl = this.localOrder.media?.[0]?.original_url;
          if (imageUrl) {
            orderCheck = imageUrl;
          }

          this.ordersStore.setOrderCheckRead(order.id)
        }

        this.$emit('openOrderDetail', {fromChatToDetail: true, selectedUser: this.selectedUser, selectedOrder: order, orderCheck: orderCheck});
        this.orderIdForGoToDetailIfNextModalChat = null
      },
      closeModalOrderDetail() {
        this.isModalShowOrderDetail = false
      },
      triggerErrorAlert($message) {
        this.alertMessage = $message;
        this.alertType = 'error';
        this.$refs.alertComponent.showAlert();
      },
      openTemplates() {
        if (!this.templates.length) {
          this.triggerErrorAlert('Вы не установили ни одного шаблонного сообщения');
          return;
        }
        this.showTemplates = !this.showTemplates;
      },
      sendRequisite() {
        this.showTemplates = false;
        this.newMessage = 'Вот реквизиты: '
      },
      isRequisiteMessage($text) {
        return $text.toLowerCase().includes('вот реквизиты');
      },
      selectTemplate(template) {
        this.newMessage = template;
        this.showTemplates = false;
      },
      pinChat(order) {
          UserService.pinChat(order.id, order.client.id)
            .then(response => {
              if (response.data.data) {
                this.pinedChat = response.data.data;
                this.getPinedChat()
              }
            })
            .catch(error => {
              this.errors = handleApiError(error)
            })
      },
      unPinChat(orderId, pinedChatId) {
          UserService.unPinChat(orderId, pinedChatId)
            .then(response => {
              if (response.data.data) {
                this.pinedChat = response.data.data;
                this.getPinedChat()
              }
            })
            .catch(error => {
              this.errors = handleApiError(error)
            })
      },
      clearInput() {
        this.newMessage = '';
        this.newMessagePhoto.photo_path = null;
        this.inputPlaceholder = 'Введите сообщение...'
      },
        close(order, nextChat = false) {
          this.newMessagePhoto.photo_path = null
          this.ordersStore.setOrderCheckRead(this.orderId)

            setTimeout(() => {
              this.$emit('close', {nextChat: nextChat, order: order, orderId: order.id});
              this.showTemplates = false;
              this.messages = []
            }, 500)
        },
    },
    watch: {
      isActive(newVal) {
        if (newVal) {
          this.newMessage = ''
          this.getOrder();
          this.getTemplatesMessages();
          this.$nextTick(() => {
            this.scrollToBottom();
          });
        }
      },
      order(newVal) {
            if (newVal) {
                this.getOrder();
              if (newVal.pinned_messages?.length) {
                this.pinedChat = newVal.pinned_messages[0];
              } else {
                this.pinedChat = {};
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
      selectedOrder: {
        handler(newOrder) {
          this.localOrder = newOrder ? { ...newOrder } : {}
        },
        immediate: true
      }
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
    backdrop-filter: blur(8px);
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
