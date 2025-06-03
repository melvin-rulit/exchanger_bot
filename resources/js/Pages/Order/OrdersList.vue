<template>
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
        <tbody v-if="orders.length">

        <tr v-for="(order, index) in filteredOrders" :key="order.id" tabindex="0"
            @click="showModalOrderDetail(order, order.user)"
            class="h-12 inside_table">
          <td v-if="order.is_message && order.status !== 'success'" class="pl-5">
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
<!--          <td class="pl-3">-->
<!--            <div class="flex items-center">-->
<!--              <p class="text-sm leading-none text-gray-600 ml-2">{{ order.currency_name }}</p>-->
<!--            </div>-->
<!--          </td>-->
          <td class="pl-5">
            <div class="flex items-center">
              <img v-if="order.image_url" @click="showModalScreenshot(order.image_url)" :src="order.image_url"
                   class="h-8 w-8 object-cover rounded cursor-pointer" alt="" />
            </div>
          </td>

          <!--              <td class="pl-5">-->
          <!--                <div class="flex items-center">-->
          <!--                  <p class="text-lg leading-none font-bold text-gray-600 ml-2">{{ order.user?.name || "&#45;&#45;&#45;&#45;&#45;&#45;" }}</p>-->
          <!--                </div>-->
          <!--              </td>-->

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

        <tbody v-else>
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

          <td v-if="!orders.length && !isLoadingSpiner && (!filteredOrders || !filteredOrders.length)" class="absolute top-[47%] left-[47%] text-xl text-muted">На сегодня нет заказов</td>
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
          <Icon @click="openCalendar" icon="bi:calendar-date" width="38" height="38" :class="['hover:text-gray-400 cursor-pointer',isCalendarOpen ? 'text-gray-400' : '']"/>
          <Icon @click="openFilterBlock" icon="material-symbols-light:app-registration-outline-rounded" width="48" height="48" class="hover:text-gray-400 cursor-pointer" />
          <Icon @click="clickShowLockScreen" icon="hugeicons:lock-sync-01" width="45" height="45" class="hover:text-gray-400 cursor-pointer"/>
        </div>
      </div>

  <FilterBlock :isActive="filterBlock" @filter-change="applyFilters" @close="closeFilterBlock"/>

      <AlertForNotification :message="alertMessage" :type="alertType" @clearMessages="clearAlertMessage" ref="alertComponent">
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

export default {
  components: { FilterBlock, SetLockScreenPassword, ButtonUI, ModalLock, AlertForNotification, Pagination, Icon, ModalShowOrderScreenshot, ModalShowChat, ModalShowOrderDetail, TextInput, HollowDotsSpinner, PinChatsInOrderList, flatPickr},
  data: function() {
    return {
      orders: '',
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
      updateChannel: null,
      newChannel: null,
      messageChannel: null,
      closedChannel: null,
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

  async mounted() {
    const { pusher } = usePusher()
    this.pusher = pusher
    await this.userStore.fetchUser()
    this.spinerLoading()
    this.getOrders(this.currentPage, this.searchQuery);
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
      return this.currentUser?.settings?.find(s => s.key === 'notification') || null;
    }
  },
  methods: {
    getOrders(page = 1, query = '') {
      return OrdersService.getOrders(query, page)
        .then(response => {
          this.orders = response.data.data || []
          this.ordersStore.setOrders(this.orders)
          this.ordersMeta = response.data.meta || {}
          this.currentPage = page
        })
        .catch(error => {
          this.errors = handleApiError(error)
          this.orders = []
          this.ordersMeta = {}
        })
    },
    async getCurrentUser() {
      try {
        const response = await UserService.currentUser();
        this.currentUser = response.data.data[0] || null;
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
    hasNewOrders(newOrders) {
      return newOrders.length > this.oldOrders.length
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
        this.playSound('new_sms.mp3')
        this.getPinedChat()
        this.getOrders()
      })
    },
    async checkOrdersUpdate() {
      if (this.updateChannel) {
        return;
      }
      this.updateChannel = this.pusher.subscribe('update_order');
      this.updateChannel.bind('order-updated', async (data) => {
        this.ordersStore.updateOrder(data.order)
        await this.unPinChat(data.order.id, null);
        await this.getOrders();
        console.log('orders before filtering:', this.ordersStore.orders)
        this.applyFilters(this.ordersStore.selectedFilters)
        await this.getPinedChat()
        switch (data.type) {
          case 'attach_user':
            this.playSound('attach_user_out.wav')
            break;
          default:
        }
      });
    },
    checkOrdersClosed() {
      if (this.closedChannel) {
        return;
      }
      this.closedChannel = this.pusher.subscribe('order_closed');
      this.closedChannel.bind('order_closed', (data) => {

        this.isModalShow = false
        this.isModalChatShow = false
        this.isModalShowOrderDetail = false

        this.$nextTick(() => {
          if (data.order) {
            this.removeReminder(data.order.id)
            this.triggerErrorAlert(`Заказ ${data.order.id} был отменен клиентом`, 'Перейти', () => this.showModalOrderDetail(data.order, 'fgj'));
          }
        });

        this.getOrders();

        switch (data.type) {
          case 'order_closed':
            this.playSound('close_order.ogg')
            break;
          default:
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
      if (this.newChannel) {
        return;
      }
      this.newChannel = this.pusher.subscribe('new_order')

      this.newChannel.bind('new_order', async (data) => {
        this.ordersStore.addOrder(data.order)
        await this.getOrders();
        this.applyFilters(this.ordersStore.selectedFilters)
        this.setReminder(data.order.id, REMINDER_TIMEOUT_MS)
        switch (data.type) {
          case 'new_order':
            this.playSound('new_order.wav')
            break;
          default:
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
    async toggleNotification (notificationSettings) {
      try {
        const response = await UserService.toggleNotification(notificationSettings);
        // const is_active = response.data.notification.is_active;
        //
        // if (index !== -1) {
        //   const settings = [...this.currentUser.settings]; // копия массива
        //   settings[index] = {
        //     ...settings[index],
        //     is_active: false,
        //   };
        //   this.currentUser.settings = settings; // переустанавливаем весь массив
        // }
      } catch (error) {
        this.errors = handleApiError(error)
      }
    },
    // toggleCalendar() {
    //   const fp = this.$refs.calendar?.fp;
    //   if (!fp) return;
    //
    //   if (this.isCalendarOpen) {
    //     fp.close();
    //   } else {
    //     fp.open();
    //   }
    // },
    openCalendar() {
        if (this.$refs.calendar?.fp) {
          this.isCalendarOpen = true
          this.$refs.calendar.fp.open()
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
    reset() {
      this.form = mapValues(this.form, () => '')
    },
    showModalScreenshot(image) {
      this.currentImageUrl = image
      this.isModalShow = true
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
      this.$refs.alertComponent.closeAlert();
      this.isVisibleSetPassword = true
    },
    async closeScreenLockPassword() {
      this.startFunction = true
      this.isVisibleSetPassword = false
      await this.userStore.fetchUser()
      this.getOrders()
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
      this.$refs.alertComponent?.closeAlert();

      if (!this.isModalChatShow && !this.isModalShow && this.editableClientNameId === null) {
        this.selectedOrder = { ...selectedOrder, orderStatus: this.translateStatus(selectedOrder.status) }
        this.selectedUser = selectedOrder.user ?? null;
      }
    },
    openOrderDetail(data) {
      console.log(data.fromChatToDetail )
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
      this.getPinedChat()
      this.getOrders(this.currentPage)
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
