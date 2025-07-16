<template>
  <Alert ref="alertComponent" :message="alertMessage" :type="alertType" />

  <div class="main">
    <div class="table-container rounded-xl">
      <table v-if="!statisticBlock" class="w-full">
        <thead class="head_table">
        <tr tabindex="0" class="focus:outline-none h-7 rounded sticky top-0 bg-white">
          <td>
            <div class="flex pl-6">
              <p class="font-semibold mr-2">№</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-3">Имя</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-2">Почта</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-2">Пароль</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-2">Роль</p>
            </div>
          </td>
          <td class="pl-3">
            <p class="font-semibold ml-2">Дата регистрации</p>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-2">Статус</p>
            </div>
          </td>
          <td>
            <div class="flex">

            </div>
          </td>
        </tr>
        </thead>

        <tbody>
        <template v-for="(user, index) in filteredUsers" :key="user?.id">
          <tr v-if="user && user.id" @click="showModalManagerDetail(user)" class="h-12 inside_table">
            <td class="pl-6">{{ index + 1 }}</td>
            <td class="pl-1">{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td class="pl-4">{{ user.password_show }}</td>
            <td>{{ Array.isArray(user.role) ? user.role[0] : user.role }}</td>
            <td style="padding-left: 45px;">{{ new Date(user.created_at).toLocaleDateString() }}</td>
            <td style="padding-left: 10px;" class="text-sm text-gray-700">
        <span :class="[getStatusColor(user.enabled === 1 ? 'new' : 'active')]">
          {{ user.enabled === 1 ? 'Активен' : 'Отключен' }}
        </span>
            </td>
          </tr>
        </template>
        </tbody>
      </table>

      <table v-if="statisticBlock" class="w-full">
        <thead class="head_table">
        <tr tabindex="0" class="focus:outline-none h-7 rounded sticky top-0 bg-white">
          <td>
            <div class="flex pl-6">
              <p class="font-semibold mr-2">№</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-3">Имя</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-2">Активные заказы</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-2">Выполненные заказы</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-2">Отмененные заказы</p>
            </div>
          </td>
          <td class="pl-3">

          </td>
          <td>
            <div class="flex">

            </div>
          </td>
          <td>
            <div class="flex">

            </div>
          </td>
        </tr>
        </thead>

        <tbody>
        <tr v-for="(user, index) in filteredUsers" :key="user.id" @click="showModalManagerDetail(user)" class="h-12 inside_table">
          <td class="pl-6">
            {{ index + 1 }}
          </td>
          <td class="pl-1">
            {{ user.name }}
          </td>
          <td class="pl-16">
            {{user.active_orders_count}}
          </td>
          <td class="pl-16">
            {{user.success_orders_count}}
          </td>
          <td class="pl-16">
            {{user.closed_orders_count}}
          </td>
          <td style="padding-left: 45px;">

          </td>

        </tr>
        </tbody>
      </table>

      <div v-if="!statisticBlock" class="pagination-wrapper">
        <Pagination :total="ordersMeta.total"
        :limit="ordersMeta.per_page"
        :currentPage="ordersMeta.current_page"
        @page-change="getUsers"/>
      </div>


      <ModalShowManagerDetail
        v-if="isModalShowManagerDetail"
        :is-active="isModalShowManagerDetail"
        :selectedManager="selectedManager"
        @close="closeModalShowManagerDetail"
        @successDeleteUser="successDeleteUser"
        @userUpdated="onUserUpdated"
      />

    </div>

    <div class="flex justify-between items-center rounded-lg shadow h-[20px]">
<!--      <div class="w-[440px] text-sm sm:text-base font-medium text-gray-700 mt-14">-->
        <div v-if="!statisticBlock" class="w-[420px] text-sm sm:text-base font-medium text-white mt-[67px] ml-6">
          <span v-if="filteredUsers.length > 0">Всего сотрудников: {{filteredUsers.length}}</span>
        </div>
<!--      </div>-->

      <div v-if="startFunction" class="text-sm sm:text-base font-medium text-white">
        <div class="flex items-center gap-3 mt-14">
<!--          <Icon @click="toggleNotification(notificationSettings)" v-show="notificationSettings && notificationSettings.is_active" icon="system-uicons:bell-ringing" width="40" height="40" class="hover:text-gray-400 cursor-pointer" />-->
<!--          <Icon @click="toggleNotification(notificationSettings)" v-show="notificationSettings && !notificationSettings.is_active" icon="system-uicons:bell-disabled" width="40" height="40" class="hover:text-gray-400 cursor-pointer" />-->
          <Icon @click="openStatisticBlock" icon="akar-icons:statistic-up" width="38" height="38" class="hover:text-gray-400 cursor-pointer" />
          <Icon icon="lsicon:find-outline" width="48" height="48" class="hover:text-gray-400 cursor-pointer" />
<!--          <flat-pickr-->
<!--            v-model="date"-->
<!--            ref="calendar"-->
<!--            :config="flatpickrConfig"-->
<!--            class="invisible absolute z-10 ml-[110px] mb-4"-->
<!--          />-->
<!--          <Icon @click="openCalendar" icon="bi:calendar-date" width="38" height="38" :class="['hover:text-gray-400 cursor-pointer', isCalendarOpen ? 'text-gray-400' : '']"/>-->
          <Icon @click="openFilterBlock" icon="material-symbols-light:app-registration-outline-rounded" width="48" height="48" class="hover:text-gray-400 cursor-pointer" />
<!--          <Icon @click="clickShowLockScreen" icon="hugeicons:lock-sync-01" width="45" height="45" class="hover:text-gray-400 cursor-pointer"/>-->
        </div>
      </div>

      <FilterBlock :isActive="filterBlock" @filter-change="applyFilters" @close="closeFilterBlock" type="users"/>
      <FilterUserStatisticBlock :isActive="statisticBlock" @filter-change="applyStatisticFilters" @close="closeFilterUserStatisticBlock" />

      <div class="w-[450px] flex items-center justify-end gap-3 text-white mt-20">

      </div>

    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import { UserService } from '@/services/UserService.js'
import { getStatusColorClass } from '@/helpers/statusColorClass.js'
import ModalShowManagerDetail from '@/Pages/Admin/User/Modal/ModalShowManagerDetail.vue'
import Alert from '@/Components/Notifications/Alert.vue'
import PinChatsInOrderList from '@/Pages/Order/Chats/PinedChats/PinChatsInOrderList.vue'
import AlertForNotification from '@/Components/Notifications/AlertForNotification.vue'
import FilterBlock from '@/Components/Filter/FilterBlock.vue'
import FilterUserStatisticBlock from '@/Components/Filter/FilterUserStatisticBlock.vue'
import Pagination from '@/Components/Pagination.vue'
import ButtonUI from '@/Components/Button/ButtonUI.vue'
import flatPickr from 'vue-flatpickr-component'
import { Icon } from '@iconify/vue'
import { useUserStore } from '@/stores/userStore.js'
import { handleApiError } from '@/helpers/errors.js'
import { OrdersService } from '@/services/OrdersService.js'


export default defineComponent({
  components: {Icon, flatPickr, ButtonUI, Pagination, FilterBlock, FilterUserStatisticBlock, AlertForNotification, PinChatsInOrderList, Alert, ModalShowManagerDetail },

  data() {
    return {
      users: '',
      selectedManager: '',
      isModalShowManagerDetail: false,
      filters: JSON.parse(localStorage.getItem('selectedFiltersUsers')) || [],
      selectedFilters: [],
      filteredUsers: [],
      filterBlock: false,
      statisticBlock: false,
      startFunction: true,
      alertMessage: '',
      alertType: 'success',
      ordersMeta: {},
      searchQuery: '',
      currentPage: 1,
      query: '',
      limit: 5,
      total: 1,
    }
  },
  setup() {
    const userStore = useUserStore()
    return { userStore }
  },
  mounted() {
    this.getUsers(this.currentPage, this.searchQuery)
    this.applyFilters(this.userStore.selectedFilters)
  },
  methods: {
    async getUsers(page = 1, query = '') {
      try {
        const response = await UserService.getUsers(query, page)
        this.users = response.data.data || []
        this.userStore.setUsers(this.users)
        this.ordersMeta = response.data.meta || {}
        this.currentPage = page
        this.applyFilters(this.userStore.selectedFilters)
      } catch (error) {
        this.errors = handleApiError(error)
        this.ordersMeta = {}
      }
    },
    async getUsersWithSearch(query = '', page = 1, showSpiner = true) {
      this.isLoadingSpiner = showSpiner

      try {
        const response = await UserService.getUsersWithSearch(query, page)
        this.users = response.data.data || []
        this.userStore.setUsers(this.users)
        this.ordersMeta = response.data.meta || {}
        this.currentPage = page
        this.applyFilters(this.userStore.selectedFilters)
      } catch (error) {
        this.errors = handleApiError(error)
        this.orders = []
        this.filteredOrders = []
        this.ordersMeta = {}
      } finally {
        this.isLoadingSpiner = false
      }
    },
    showModalManagerDetail(selectedUser) {
      this.selectedManager = selectedUser
      this.isModalShowManagerDetail = true
    },
    getStatusColor(status) {
      return getStatusColorClass(status)
    },
    closeModalShowManagerDetail(data) {
      this.isModalShowManagerDetail = false

      if (data?.updateUser) {
        const index = this.users.findIndex(manager => manager.id === data.updateUser.id);

        if (index !== -1) {
          this.users[index] = {
            ...this.users[index],
            ...data.updateUser
          };
        }
      }
    },
    openStatisticBlock() {
      this.startFunction = false
      this.statisticBlock = true
      this.userStore.setIsOnUserStatistics(true)
    },
    openFilterBlock() {
      this.startFunction = false
      this.filterBlock = true
    },
    applyFilters(selectedFilters) {
      this.selectedFilters = selectedFilters

      if (!selectedFilters.length) {
        this.filteredUsers = this.userStore.users
      } else {
        this.filteredUsers = this.userStore.users.filter(user => {
          const hasStatus = selectedFilters.includes(String(Number(user.enabled)))


          const hasRole = user.role.some(role =>
            selectedFilters.includes(role)
          )

          return hasStatus || hasRole
        })
      }
    },
    // roughSearch(query) {
    //   this.searchQuery = query.toLowerCase().trim()
    //
    //   if (!this.searchQuery) {
    //     return
    //   }
    //   this.getOrdersWithElasticSearch(this.searchQuery, 1)
    // },
    // accurateSearch(form) {
    //   const params = {}
    //
    //   if (form.dateFrom) params.dateFrom = form.dateFrom
    //   if (form.dateTo) params.dateTo = form.dateTo
    //   if (form.status?.value) params.status = form.status.value
    //   if (form.selectedClient?.id) params.client_id = form.selectedClient.id
    //   if (form.selectedUser?.id) params.user_id = form.selectedUser.id
    //
    //   this.getOrdersWithSearch(params, 1)
    // },
    applyStatisticFilters(form) {
      const params = {}
      if (form.dateFrom) params.dateFrom = form.dateFrom
      if (form.dateTo) params.dateTo = form.dateTo
      this.getUsersWithSearch(params, 1)
    },
    closeFilterBlock() {
      this.startFunction = true
      this.filterBlock = false
    },
    closeFilterUserStatisticBlock() {
      this.getUsers(1, '')
      this.startFunction = true
      this.statisticBlock = false
      this.userStore.setIsOnUserStatistics(false)
    },
    successDeleteUser() {
      this.getUsers(this.currentPage, this.searchQuery);
      this.isModalShowManagerDetail = false
      this.triggerSuccessAlert(`Пользователь успешно удален`);
    },
    onUserUpdated(updatedUser) {
      this.selectedManager = updatedUser;
    },
    triggerSuccessAlert($message) {
      this.alertMessage = $message;
      this.alertType = 'success';
      this.$refs.alertComponent.showAlert();
    },
  }
})
</script>

<style scoped>
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
  bottom: 71px;
  left: 2%;
}
</style>
