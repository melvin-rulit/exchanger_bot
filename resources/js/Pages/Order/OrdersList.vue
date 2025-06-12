<template>
  <Alert ref="alertComponent" :message="alertMessage" :type="alertType" />

  <div class="main">
    <div class="table-container">
      <table class="w-full">
        <thead class="head_table">
        <tr tabindex="0" class="focus:outline-none h-10 rounded sticky top-0 bg-white">
          <td>
            <div class="flex pl-6">
              <p class="font-semibold mr-2">№</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-3">Статус</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-2">Клиент</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-2">Сумма обмена</p>
            </div>
          </td>
          <td class="pl-3">
            <p class="font-semibold ml-2">Чек</p>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-2">Кем взят</p>
            </div>
          </td>
          <td>
            <div class="flex">

            </div>
          </td>
        </tr>
        </thead>

        <tbody v-if="isLoadingSpiner">
        <tr>
          <td class="absolute top-[50%] left-[47%]">
            <hollow-dots-spinner
              v-if="isLoadingSpiner"
              :animation-duration="1000"
              :dot-size="20"
              :dots-num="3"
              color="#4caf50"
            />
          </td>

        </tr>
        </tbody>

        <tbody v-else-if="!isLoadingSpiner && orders.length === 0 ">
        <tr>
          <td class="absolute top-[47%] left-[47%] text-xl text-muted">На сегодня нет заказов</td>
        </tr>
        </tbody>

        <tbody v-else>

        <tr v-for="(order, index) in filteredOrders" :key="order.id" tabindex="0" @click="showModalOrderDetail(order, order.user)" class="h-12 inside_table">
          <td v-if="order.is_message" class="pl-5">
            <div @click="showModalChat(order)" class="flex items-center">
              <Icon icon="wpf:message-outline" width="26" height="26" class="animate-fade-bounce" />
            </div>
          </td>
          <td v-else-if="!order.is_message && order.status !== 'success' && order.status !== 'closed'" class="pl-4">
            <div class="flex items-center">
              <!-- <p class="text-sm leading-none text-gray-600 ml-3">{{ index + 1 }}</p>-->
                           <p class="text-sm leading-none text-gray-600 ml-2">{{ order.id }}</p>
            </div>
          </td>
          <td v-else-if="!order.is_message && order.status !== 'success' && order.status === 'closed'" class="pl-4">
            <div class="flex items-center">
              <Icon icon="carbon:close-outline" width="26" height="26" class="icon-error"/>
            </div>
          </td>
          <td v-else class="pl-4">
            <div class="flex items-center ml-2">
              <Icon icon="mdi:success" width="26" height="26" class="icon-success" />
            </div>
          </td>

          <td class="pl-2">
            <div class="flex items-center">
                <span :class="['text-sm font-medium leading-none mr-2', getStatusColor(order.status)]">
                  {{ translateStatus(order.status) }}
                </span>
            </div>
          </td>

          <td class="">
            <div class="flex items-center">

                            <span v-if="editableClientNameId !== order.id && order.client"
                                  @click="enableEdit(order)"
                                  class="px-4 bg-gray-50 rounded-md shadow-md cursor-pointer">
                              {{ order.client.first_name }}
                            </span>
              <div v-if="editableClientNameId === order.id" class="flex items-center gap-2">
                <div>
                  <hollow-dots-spinner
                    :animation-duration="1000"
                    :dot-size="15"
                    :dots-num="1"
                    color="#4caf50"
                  />
                </div>

                <div class="relative">
                  <TextInput
                    @enter="saveClientName(order)"
                    v-model="editableClientName"
                    :limitLength="true"
                    :maxLength="10"
                    :trimWhitespace="true"
                    placeholder="Введите имя клиента"
                    class="h-8 text-sm px-2 py-1 pr-10"
                  />
                  <div class="absolute right-1 top-1/2 -translate-y-1/2 text-xs text-gray-500">
                    {{ editableClientName.length }}/10
                  </div>
                </div>

                <button @click.stop="closeRenameClientInput" class="close-btn h-8 w-8 flex items-center justify-center">
                  <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" class="icon-error" />
                </button>
              </div>


            </div>
          </td>

          <td class="">
            <div class="flex items-center">
              <p class="text-sm leading-none text-gray-600 ml-10">{{ order.amount }}</p>
            </div>
          </td>

          <td class="pl-5">
            <div class="flex items-center">
              <img v-if="order.image_url" @click="showModalScreenshot(order.image_url)" :src="order.image_url"
                   class="h-8 w-8 object-cover rounded cursor-pointer" alt="" />
            </div>
          </td>

          <td class="">
            <div class="flex">
              <span v-if="order.user?.name" class="px-4 bg-gray-50 rounded-md shadow-md">
                {{ order.user.name }}
              </span>
              <span v-else class="px-2 text-gray-500"></span>
            </div>
          </td>

          <td class="">
            <div class="flex">
                  <Icon v-if="attentionOrders.includes(order.id) && !order.is_requisite" icon="mdi:alarm-multiple" width="24" height="24" class="icon-danger animate-fade-bounce" />
            </div>
          </td>
        </tr>
        </tbody>



      </table>
    </div>

    <ModalShowOrderScreenshot
      :is-active="isModalShow"
      :currentImageUrl="currentImageUrl"
      @close="closeModalShowOrderScreenshot"
    />
    <ModalLock
      :is-active="isModalLockShow"
      @unlocked="unLockScreen"
    />
    <ModalShowChat
      :is-active="isModalChatShow"
      :orderId="orderId"
      :order="order"
      :selectedOrder="selectedOrder"
      :selectedUser="selectedUser"
      :clientName="clientName"
      @close="closeModalShowChat"
      @openOrderDetail="openOrderDetail"
    />
    <ModalShowOrderDetail
      v-if="isModalShowOrderDetail"
      :is-active="isModalShowOrderDetail"
      :selectedOrder="selectedOrder"
      :selectedUser="selectedUser"
      :clientName="clientName"
      @close="closeModalShowOrderDetail"
      @successCloseOrder="closeModalShowChat"
    />

    <SetLockScreenPassword
      :is-active="isVisibleSetPassword"
      @closeModal="closeScreenLockPassword"
    />

    <div class="flex justify-between items-center rounded-lg shadow h-[20px]">
      <div class="w-[450px] text-sm sm:text-base font-medium text-gray-700 mt-16">
        <Pagination
          :total="ordersMeta.total"
          :limit="ordersMeta.per_page"
          :currentPage="ordersMeta.current_page"
          @page-change="getOrders"
        />
      </div>

      <div v-show="startFunction" class="text-sm sm:text-base font-medium text-white">
        <div class="flex items-center gap-3 mt-20">
          <Icon @click="toggleNotification(notificationSettings)" v-show="notificationSettings && notificationSettings.is_active" icon="system-uicons:bell-ringing" width="40" height="40" class="hover:text-gray-400 cursor-pointer" />
          <Icon @click="toggleNotification(notificationSettings)" v-show="notificationSettings && !notificationSettings.is_active" icon="system-uicons:bell-disabled" width="40" height="40" class="hover:text-gray-400 cursor-pointer" />
          <Icon icon="lsicon:find-outline" width="48" height="48" class="hover:text-gray-400 cursor-pointer" />
          <flat-pickr
            v-model="date"
            ref="calendar"
            :config="flatpickrConfig"
            class="invisible absolute z-10 ml-[110px] mb-4"
          />
          <Icon @click="openCalendar" icon="bi:calendar-date" width="38" height="38" :class="['hover:text-gray-400 cursor-pointer', isCalendarOpen ? 'text-gray-400' : '']"/>
          <Icon @click="openFilterBlock" icon="material-symbols-light:app-registration-outline-rounded" width="48" height="48" class="hover:text-gray-400 cursor-pointer" />
          <Icon @click="clickShowLockScreen" icon="hugeicons:lock-sync-01" width="45" height="45" class="hover:text-gray-400 cursor-pointer"/>
        </div>
      </div>

  <FilterBlock :isActive="filterBlock" @filter-change="applyFilters" @close="closeFilterBlock"/>

      <AlertForNotification :message="alertMessage" :type="alertType" @clearMessages="clearAlertMessage" ref="alertNotificationComponent">
        <template #buttons>
          <div class="pl-4">
            <ButtonUI @click="alertButtonFunction" type="submit" color="green">{{alertButtonName}}</ButtonUI>
          </div>
        </template>
      </AlertForNotification>

      <div class="w-[450px] flex items-center justify-end gap-3 text-white mt-20">
        <PinChatsInOrderList
          v-for="chat in pinnedChats.filter(chat => chat.order)"
          :key="chat.id"
          :orderFullInfo="order"
          :order="chat.order"
          :onClick="() => showModalChat(chat.order)"
        />
      </div>

    </div>


  </div>
</template>

<script>
import { usePusher } from '@/helpers/usePusher'
import { OrdersService } from '@/services/OrdersService.js'
import ModalShowOrderScreenshot from './Modal/ModalShowOrderScreenshot.vue'
import ModalShowChat from './Chats/Modal/ModalShowChat.vue'
import ModalShowOrderDetail from './Modal/ModalShowOrderDetail.vue'
import { Icon } from '@iconify/vue'
import { useOrdersStore } from '@/stores/ordersStore'
import TextInput from '@/Components/Input/TextInput.vue'
import { HollowDotsSpinner } from 'epic-spinners'
import { useReminder } from '@/helpers/useReminder'
import { useSound } from '@/helpers/useSound'
import { getStatusColorClass } from '@/helpers/statusColorClass.js'
import { translateStatus } from '@/helpers/statusTranslationClass.js'
import { REMINDER_TIMEOUT_MS } from '@/helpers/constants.js'
import Pagination from '@/Components/Pagination.vue'
import AlertForNotification from '@/Components/Notifications/AlertForNotification.vue'
import ModalLock from '@/Components/Modal/ModalLock.vue'
import { useUserStore } from '@/stores/userStore'
import { UserService } from '@/services/UserService.js'
import ButtonUI from '@/Components/Button/ButtonUI.vue'
import SetLockScreenPassword from '@/Pages/Order/Notifications/SetLockScreenPassword.vue'
import PinChatsInOrderList from '@/Pages/Order/Chats/PinedChats/PinChatsInOrderList.vue'
import { handleApiError } from '@/helpers/errors.js'
import flatPickr from 'vue-flatpickr-component'
import { Russian } from "flatpickr/dist/l10n/ru.js"
import 'flatpickr/dist/flatpickr.css';
import FilterBlock from '@/Pages/Order/Filter/FilterBlock.vue'
import Alert from '@/Components/Notifications/Alert.vue'
import { eventBus } from '@/utils/eventBus.js'

export default {
  components: { Alert, FilterBlock, SetLockScreenPassword, ButtonUI, ModalLock, AlertForNotification, Pagination, Icon, ModalShowOrderScreenshot, ModalShowChat, ModalShowOrderDetail, TextInput, HollowDotsSpinner, PinChatsInOrderList, flatPickr},
  data: function() {
    return {
      orders: [],
      oldOrders: [],
      pinnedChats: [],
      filters: JSON.parse(localStorage.getItem('selectedFilters')) || [],
      selectedFilters: [],
      filteredOrders: [],
      orderId: '',
      order: '',
      selectedOrder: '',
      selectedUser: '',
      currentUser: [],
      clientName: '',
      editableClientNameId: null,
      editableClientName: '',
      form: {
        dateFrom: '',
        dateTo: '',
      },
      date: new Date(),
      isCalendarOpen: false,
      flatpickrConfig: {
        onOpen: () => {
          //this.isCalendarOpen = true;
        },
        onClose: () => {
          this.isCalendarOpen = false;
        },
        allowInput: false,
        altFormat: 'd.m.Y',
        dateFormat: 'd.m.Y',
        locale: Russian
      },
      filterBlock: false,
      updateOrderChannel: null,
      newOrderChannel: null,
      messageChannel: null,
      closedOrderChannel: null,
      ordersMeta: {},
      searchQuery: '',
      currentPage: 1,
      query: '',
      limit: 5,
      total: 1,
      errors: '',
      isLoadingSpiner: true,
      alertMessage: '',
      alertType: 'success',
      alertButtonName: '',
      alertButtonFunction: '',
      startFunction: true,
      isVisibleSetPassword: false,
      message: null,
      loading: false,
      showFilter: false,
      isModalShow: false,
      isModalLockShow: false,
      isModalChatShow: false,
      isModalShowOrderDetail: false,
      fromChatToDetail: false,
      currentImageUrl: '',
    }
  },
  setup() {
    const { playSound, stopSound} = useSound()
    const ordersStore = useOrdersStore()
    const userStore = useUserStore()
    const { attentionOrders, setReminder, removeReminder, } = useReminder()
    return { attentionOrders, playSound, stopSound, ordersStore, userStore, setReminder, removeReminder, REMINDER_TIMEOUT_MS, translateStatus}
  },
  beforeUnmount() {
    if (this.messageChannel) {
      this.messageChannel.unbind('send_message')
      this.pusher.unsubscribe('send_message')
      this.messageChannel = null
    }
    if (this.newOrderChannel) {
      this.newOrderChannel.unbind('new_order')
      this.pusher.unsubscribe('new_order')
      this.newOrderChannel = null
    }
    if (this.closedOrderChannel) {
      this.closedOrderChannel.unbind('order_closed')
      this.pusher.unsubscribe('order_closed')
      this.closedOrderChannel = null
    }
    if (this.updateOrderChannel) {
      this.updateOrderChannel.unbind('update_order')
      this.pusher.unsubscribe('update_order')
      this.updateOrderChannel = null
    }
  },
  async mounted() {
    const { pusher } = usePusher()
    this.pusher = pusher
    await this.userStore.fetchUser()
    this.spinerLoading()
    await this.getOrders(this.currentPage, this.searchQuery);
    this.checkNewMessage()
    this.checkNewOrders()
    await this.getCurrentUser()
    await this.checkOrdersUpdate()
    this.checkOrdersClosed()
    await this.getPinedChat()
    await this.showLockScreen()
    this.applyFilters(this.ordersStore.selectedFilters)
  },
  computed: {
    notificationSettings() {
      return this.userStore.currentUser?.settings?.find(s => s.key === 'notification') || { is_active: false }
    }
  },
  methods: {
    async getOrders(page = 1, query = '', showSpiner = true) {
      this.isLoadingSpiner = showSpiner

      try {
        const response = await OrdersService.getOrders(query, page)
        this.orders = response.data.data || []
        this.ordersStore.setOrders(this.orders)
        this.ordersMeta = response.data.meta || {}
        this.currentPage = page
        this.applyFilters(this.ordersStore.selectedFilters)
      } catch (error) {
        this.errors = handleApiError(error)
        this.orders = []
        this.filteredOrders = []
        this.ordersMeta = {}
      } finally {
        this.isLoadingSpiner = false
      }
    },
    async getCurrentUser() {
      try {
        const response = await UserService.currentUser();
        this.currentUser = response.data.data || null;
      } catch (error) {
        this.errors = handleApiError(error)
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
    getStatusColor(status) {
      return getStatusColorClass(status)
    },
    checkNewMessage() {
      if (this.messageChannel) {
        return;
      }
      this.messageChannel = this.pusher.subscribe('send_message')

      this.messageChannel.bind('send_message', (data) => {

        if (this.notificationSettings.is_active){
          this.playSound('new_sms.mp3')
        }
        this.getOrders(1, '', false)
      })
    },
    async checkOrdersUpdate() {
      if (this.updateOrderChannel) {
        return;
      }
      this.updateOrderChannel = this.pusher.subscribe('update_order');
      this.updateOrderChannel.bind('order-updated', async (data) => {
        this.ordersStore.updateOrder(data.order)
        //await this.getOrders();
        this.applyFilters(this.ordersStore.selectedFilters)
        await this.getPinedChat()

        switch (data.type) {
          case 'attach_user':
            await this.unPinChat(data.order.id, null);
            if (this.notificationSettings.is_active){
              this.playSound('attach_user_out.wav')
            }
            break;
            case 'send_chek':
              if(!this.isModalShowOrderDetail && !this.isModalChatShow) {
                await this.getOrders();
              }
              eventBus.emit('newChek', data.order)
            break;
          default:
        }
      });
    },
    checkOrdersClosed() {
      if (this.closedOrderChannel) {
        return;
      }
      this.closedOrderChannel = this.pusher.subscribe('order_closed');
      this.closedOrderChannel.bind('order_closed', (data) => {

        this.isModalShow = false
        this.isModalChatShow = false
        this.isModalShowOrderDetail = false

        this.$nextTick(() => {
          if (data.order) {
            this.removeReminder(data.order.id)
            this.triggerErrorAlert(`Заказ ${data.order.id} был отменен клиентом`, 'Перейти', () => this.showModalOrderDetail(data.order, 'fgj'), 'alertNotificationComponent');
          }
        });

        this.getOrders();

        if (this.notificationSettings.is_active){
          this.playSound('close_order.ogg')
        }
      });
    },
    async saveClientName(order) {
      if (this.editableClientName.trim() !== order.client.first_name) {
        order.client.first_name = this.editableClientName
        await OrdersService.updateClientName(order.id, this.editableClientName)
        this.getOrders()
        await this.getPinedChat()
      }
      this.editableClientNameId = null
    },
    checkNewOrders() {
      if (this.newOrderChannel) {
        return;
      }
      this.newOrderChannel = this.pusher.subscribe('new_order')
      this.newOrderChannel.bind('new_order', async (data) => {
        this.ordersStore.addOrder(data.order)
        await this.getOrders();
        this.applyFilters(this.ordersStore.selectedFilters)
        this.setReminder(data.order.id, REMINDER_TIMEOUT_MS)

        if (this.notificationSettings.is_active){
          this.playSound('new_order.wav')
        }
      })
    },
    async unPinChat(orderId, chatId) {
      try {
        const response = await UserService.unPinChat(orderId, chatId);
        this.pinnedChats = response.data.data;
      } catch (error) {
        this.errors = handleApiError(error)
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
    triggerSuccessAlert($message, $type) {
      this.alertMessage = $message;
      this.alertType = 'success';
      const refName = $type;
      if (this.$refs[refName]) {
        this.$refs[refName].showAlert();
      } else {
        console.warn(`Ref "${refName}" не найден`);
      }
    },
    triggerErrorAlert($message, $buttonName, $buttonFunction, $type) {
      this.alertMessage = $message;
      this.alertType = 'error';
      this.alertButtonName = $buttonName;
      this.alertButtonFunction = $buttonFunction;

      const refName = $type;
      if (this.$refs[refName]) {
        this.$refs[refName].showAlert();
      } else {
        console.warn(`Ref "${refName}" не найден`);
      }
    },
    clearAlertMessage() {
      this.alertMessage = '';
    },
    reset() {
      this.form = mapValues(this.form, () => '')
    },
    showModalScreenshot(image) {
      this.currentImageUrl = image
      this.isModalShow = true
    },
    async clickShowLockScreen() {
      if (await this.userStore.getUserLockPassword(this.$page.props.auth.user.id) === null){
        this.triggerErrorAlert('Вы не задали пароль для этого действия', 'Установить', this.setScreenLockPassword, 'alertNotificationComponent');
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
      this.$refs.alertNotificationComponent.closeAlert();
      this.isVisibleSetPassword = true
    },
    async closeScreenLockPassword() {
      this.startFunction = true
      this.isVisibleSetPassword = false
      await this.userStore.fetchUser()
      await this.getOrders()
    },
    openFilterBlock() {
      this.startFunction = false
      this.filterBlock = true
    },
    applyFilters(selectedFilters) {
      this.selectedFilters = selectedFilters

      if (!this.selectedFilters.length) {
        this.filteredOrders = this.ordersStore.orders
      } else {
        this.filteredOrders = this.orders.filter(order =>
          this.selectedFilters.includes(order.status)
        )
      }
    },
    setSelectedFilters(filters) {
      this.filters = filters
      localStorage.setItem('selectedFilters', JSON.stringify(filters))
    },
    closeFilterBlock() {
      this.startFunction = true
      this.filterBlock = false
    },
    showModalChat($order) {
      this.isModalChatShow = true
      this.orderId = $order.id
      this.order = this.allOrderByChatId($order.id)
    },
    allOrderByChatId(orderId) {
      const order = this.orders.find(o => o.id === orderId)
      if (order) {
        return order
      }
  },
    showModalOrderDetail(selectedOrder, selectedUser) {
      // if (selectedOrder.is_pinned){
      //   this.removeReminder(selectedOrder.id)
      // }

      this.removeReminder(selectedOrder.id)
      this.$refs.alertNotificationComponent.closeAlert();

      if (!this.isModalChatShow && !this.isModalShow && this.editableClientNameId === null) {
        this.selectedOrder = { ...selectedOrder, orderStatus: this.translateStatus(selectedOrder.status) }
        this.selectedUser = selectedOrder.user ?? null;
      }
    },
    openOrderDetail(data) {
      this.fromChatToDetail = data.fromChatToDetail
      this.selectedOrder = { ...data.selectedOrder, orderStatus: this.translateStatus(data.selectedOrder.status) }
      this.selectedUser = data.selectedOrder.user
    },
    closeModalShowOrderScreenshot() {
      this.isModalShow = false
    },
    closeModalShowChat(data) {
      this.getOrders(this.currentPage)
      this.getPinedChat()
      this.isModalChatShow = false

      if (data.nextChat) {
        this.showModalChat(data.order, data.orderId)
      }
    },
    closeModalShowOrderDetail(data) {
      if (data.openChat) {
        this.showModalChat(data.order, data.orderId)
      }
      //TODO тут надо сделать чтоб при закрытии модалку детальной информации не обновлялись каждый раз а только в нужный момент
      // this.getPinedChat()
       //this.getOrders(this.currentPage)
      this.isModalShowOrderDetail = false
      this.fromChatToDetail = false
        //console.log(this.fromChatToDetail )
//TODO тут нужно понимать если мы перешли из чата к заказу и перекинули его другому то нужно так же закрывать и чат
      //this.isModalChatShow = false
    },
    spinerLoading() {
      if (this.orders || this.filteredOrders) {
        this.isLoadingSpiner = false
      }
    },
    enableEdit(order) {
      this.editableClientNameId = order.id
      this.editableClientName = order.client.first_name
    },
    closeRenameClientInput() {
      this.editableClientNameId = null
    },
  },

  watch: {
    orders(newOrders) {
      if (this.selectedOrder && this.fromChatToDetail) {
        const updated = newOrders.find(o => o.id === this.selectedOrder.id)
        if (updated) {
          this.selectedOrder = {
            ...updated,
            orderStatus: this.translateStatus(updated.status)
          }
        }
      }
    },
    selectedOrder(newOrder) {
      if (newOrder && Object.keys(newOrder).length > 0) {
        this.isModalShowOrderDetail = true;
      }
    },
    filters: {
      handler(newFilters) {
        this.setSelectedFilters(newFilters)
        this.applyFilters(newFilters)
      },
      deep: true
    }
  }
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
  max-height: calc(94vh - 89px);
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
</style>
