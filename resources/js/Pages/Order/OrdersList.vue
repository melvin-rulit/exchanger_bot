<template>
  <div class="main">
    <div class="table-container">

      <!--        <button v-show="!showFilter" @click="showFilter = true" type="submit"-->
      <!--                class="mb-3 rounded-sm bg-gray-700 px-3 py-1 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">-->
      <!--            поиск заказов-->
      <!--        </button>-->
      <!--        <button v-show="!showFilter" @click="showFilter = true" type="submit"-->
      <!--                class="mb-3 ml-2 rounded-sm bg-gray-700 px-3 py-1 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">-->
      <!--            Фильтр по параметрам-->
      <!--        </button>-->
      <!--       <div class="filters_block">-->
      <!--           <button-->
      <!--               class="bg-transparent hover:bg-white text-grey-dark  hover:border-b-1  px-3 border border-gray hover:border-transparent rounded ml-2">-->
      <!--             <span>поиск заказов</span>-->
      <!--           </button>-->
      <!--           <button-->
      <!--               class="bg-transparent hover:bg-white text-grey-dark  hover:border-b-1 px-3 border border-gray hover:border-transparent rounded ml-2">-->
      <!--            <span>фильтр по параметрам</span>-->
      <!--           </button>-->

      <!--           <div v-if="showFilter" class="flex-container flex space-x-4">-->
      <!--&lt;!&ndash;               <div class="relative w-1/5 mb-3 mr-5 group">&ndash;&gt;-->
      <!--&lt;!&ndash;                   <DateInput v-model:value="form.dateFrom" title="Дата с" type="date" />&ndash;&gt;-->
      <!--&lt;!&ndash;               </div>&ndash;&gt;-->

      <!--&lt;!&ndash;               <div class="relative w-1/5 mb-6 mr-5 group">&ndash;&gt;-->
      <!--&lt;!&ndash;                   <DateInput v-model:value="form.dateTo" title="Дата по" type="date" />&ndash;&gt;-->
      <!--&lt;!&ndash;               </div>&ndash;&gt;-->

      <!--               <div class="flex items-center">-->
      <!--                   <button v-show="hasFilters" @click="reset"-->
      <!--                           class="bg-transparent hover:bg-white text-grey-dark font-semibold hover:border-b-1 py-2 px-4 border border-gray hover:border-transparent rounded ml-2">-->
      <!--                       Очистить фильтр-->
      <!--                   </button>-->
      <!--               </div>-->
      <!--           </div>-->

      <!--       </div>-->


      <table class="w-full whitespace-nowrap">
        <thead class="head_table">
        <tr tabindex="0" class="focus:outline-none h-10 rounded">
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
            <div class="flex">
              <p class="font-semibold ml-2">Валюта</p>
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
        </tr>
        </thead>
        <tbody>
        <tr v-for="(order, index) in orders" :key="order.id" tabindex="0" @click="showModalOrderDetail(order, order.user)"
            class="inside_table h-12 hover:bg-gray-100">
          <td v-if="order.is_message && order.status !== 'success'" class="pl-5">
            <div @click="showModalChat(order.id)" class="flex items-center">
              <Icon icon="wpf:message-outline" width="26" height="26" class="flashing-icon"/>
            </div>
          </td>
          <td v-else-if="!order.is_message && order.status !== 'success'" class="pl-4">
            <div class="flex items-center">
              <p class="text-sm leading-none text-gray-600 ml-3">{{ index + 1 }}</p>
<!--              <p class="text-sm leading-none text-gray-600 ml-2">{{ order.id }}</p>-->
            </div>
          </td>
          <td v-else class="pl-4">
            <div class="flex items-center ml-2">
              <Icon icon="mdi:success" width="26" height="26" class="icon-success" />
            </div>
          </td>

          <td class="pl-2">
            <div class="flex items-center">
<!--            <span v-if="order.status !== 'success'" :class="getStatusColor(order.status)" class="text-base font-medium leading-none mr-2">{{ translateStatus(order.status) }}</span>-->
              <span :class="getStatusColor(order.status)" class="text-sm font-medium leading-none mr-2">{{ translateStatus(order.status) }}</span>
            </div>
          </td>
          <td class="">
            <div class="flex items-center">
              <span class="px-4 bg-gray-50 rounded-md shadow-md">
  {{ order.client.first_name }}
</span>
            </div>
          </td>
          <td class="">
            <div class="flex items-center">
              <p class="text-sm leading-none text-gray-600 ml-2">{{ order.amount }}</p>
            </div>
          </td>
          <td class="pl-3">
            <div class="flex items-center">
              <p class="text-sm leading-none text-gray-600 ml-2">{{ order.currency_name }}</p>
            </div>
          </td>
          <td class="pl-5">
            <div class="flex items-center">
              <img @click="showModal(order.media[0]['original_url'])" :src="order.media[0]['original_url']"
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

        </tr>
        <tr class="h-3">
        </tr>
        </tbody>
      </table>
    </div>


    <ModalShowOrderScreenshot
      :is-active="isModalShow"
      :currentImageUrl="currentImageUrl"
      @close="closeModalShowOrderScreenshot"
    />
    <ModalShowChat
      :is-active="isModalChatShow"
      :orderId="orderId"
      @close="closeModalShowChat"
    />
    <ModalShowOrderDetail
      :is-active="isModalShowOrderDetail"
      :selectedOrder="selectedOrder"
      :selectedUser="selectedUser"
      @close="closeModalShowOrderDetail"
    />


<!--                    <Pagination @click="update(page)" url="users" :current-page="page" :limit="limit" :total="total"/>-->



  </div>
</template>

<script>
import Pusher from 'pusher-js'
import { OrdersService } from '@/services/OrdersService.js'
import ModalShowOrderScreenshot from './Modal/ModalShowOrderScreenshot.vue'
import ModalShowChat from './Chats/Modal/ModalShowChat.vue'
import ModalShowOrderDetail from './Modal/ModalShowOrderDetail.vue'
import { Icon } from '@iconify/vue'

export default {
  components: { ModalShowOrderScreenshot, ModalShowChat, ModalShowOrderDetail, Icon },
  data: function() {
    return {
      orders: '',
      oldOrders: [],
      orderId: '',
      selectedOrder: '',
      selectedUser: '',
      form: {
        dateFrom: '',
        dateTo: '',
      },
      statusTranslations: {
        new: 'Новый',
        active: 'Активный',
        success: 'Завершен',
        deleted: 'Удален',
      },
      query: '',
      limit: 5,
      total: 1,
      errors: '',
      message: null,
      loading: false,
      showFilter: false,
      isModalShow: false,
      isModalChatShow: false,
      isModalShowOrderDetail: false,
      currentImageUrl: '',
    }
  },
  mounted() {
    this.getOrders()
    this.checkOrders()
    this.checkOrdersUpdate()
  },
  created: function() {
    // this.update(this.page)
  },
  computed: {
    page() {
      // return Number(this.$route.query.page) ?? 1
    },
    hasFilters() {
      return Object.values(this.form).some(value => value !== '')
    },
  },
  methods: {
    // changeStatus(newStatus) {
    //     this.$emit('status-updated', newStatus);
    // },
    getOrders: function() {
      OrdersService.getOrders().then(response => {
        this.orders = response.data.orders
      })
    },
    hasNewOrders(newOrders) {
      return newOrders.length > this.oldOrders.length
    },
    getStatusColor(status) {
      switch (status) {
        case 'new':
          return 'text-[#38b0b0]'
        case 'active':
          return 'text-[#F93827]'
        case 'success':
          return 'text-[#DBDBDB]'
        case 'deleted':
          return 'text-[#FF0000]'
        default:
          return 'text-black'
      }
    },
    checkOrders() {
      const pusher = new Pusher('6c99314bac482dfe845e', {
        cluster: 'eu', logToConsole: true,
      })
      const channel = pusher.subscribe('check_amount')

            channel.bind('my-event', (data) => {
                this.getOrders()
            })
        },
        checkOrdersUpdate() {
            const pusher = new Pusher('6c99314bac482dfe845e', {
                cluster: 'eu', logToConsole: true,
            })
            const channel = pusher.subscribe('update_order');

      channel.bind('order-updated', (data) => {
        this.new_sms()
        this.getOrders()
        // if (data.order.is_message !== this.orders.is_message) {
        //     this.orders = data.order;
        // }
      })
    },
    translateStatus(status) {
      return this.statusTranslations[status] || status
    },
    reset() {
      this.form = mapValues(this.form, () => '')
    },
    showModal(image) {
      this.currentImageUrl = image
      this.isModalShow = true
    },
    showModalChat($orderId) {
      this.isModalChatShow = true
      this.orderId = $orderId
    },
    showModalOrderDetail($selectedOrder, $selectedUser) {
      if (!this.isModalChatShow && !this.isModalShow) {
        this.isModalShowOrderDetail = true
        // this.selectedOrder = $selectedOrder
        this.selectedOrder = { ...$selectedOrder, orderStatus: this.translateStatus($selectedOrder.status) }
        this.selectedUser = $selectedUser
      }
    },
    closeModalShowOrderScreenshot() {
      this.isModalShow = false
    },
    closeModalShowChat() {
      this.getOrders()
      this.isModalChatShow = false
    },
    closeModalShowOrderDetail(data) {
      if (data.openChat) {
        this.showModalChat(data.orderId)
      }
      this.getOrders()
      this.isModalShowOrderDetail = false
    },
    new_sms() {
      let audio = new Audio('/audio/sms_new.wav');
      audio.play();
    }
  },
  // watch: {
  //     'query': _.debounce(function () {
  //         this.update()
  //     }, 500)
  // }
}
</script>
<style lang="scss">
.main {
  min-height: 93vh; /* Устанавливаем минимальную высоту на весь экран */
  display: flex;
  flex-direction: column;
}

.table-container {
  flex-grow: 1; /* Заставит таблицу занимать оставшееся пространство */
  overflow-y: auto; /* Добавим прокрутку, если таблица слишком велика */
  background-color: white; /* Устанавливаем непрозрачный фон */
}

.filters_block {
  margin-bottom: 10px;

  span {
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

.inside_table:hover {
  border-top: 2px solid rgba(0, 0, 0, 0.2);
  border-bottom: 2px solid rgba(0, 0, 0, 0.2);
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
</style>
