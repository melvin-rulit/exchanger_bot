<template>
  <div class="mt-2">
    <div class="hidden space-x-2 sm:-my-px sm:ms-40 sm:flex">
      <div>
                  <span v-if="ordersStore.unreadMessagesCount > 0"
                        :class="['badge',route().current('adminOrders') ? 'badge-active' : 'badge-inactive']">
                        {{ ordersStore.unreadMessagesCount }}
                  </span>

      </div>
      <NavLink
        :href="route('adminOrders')"
        :active="route().current('adminOrders')"
        @click="userStore.setIsOnUserStatistics(false)">
        Заказы
      </NavLink>

      <div class="pl-5">

      </div>
      <NavLink
        :href="route('adminUsers')"
        :active="route().current('adminUsers')">
        {{ userStore.isOnUserStatistics ? 'Статистика сотрудников' : 'Сотрудники' }}
      </NavLink>
    </div>

  </div>
</template>

<script>
import NavLink from '@/Components/NavLink.vue'
import { useOrdersStore } from '@/stores/ordersStore.js'
import { useUserStore } from '@/stores/userStore.js'
import { useConsultationStore } from '@/stores/consultationStore'

export default {
  components: { NavLink, useConsultationStore},
  data: function() {
    return {
      hasNewConsultMessages: true,
    }
  },
  setup() {
    const ordersStore = useOrdersStore()
    const userStore = useUserStore()
    const consultationStore = useConsultationStore()
    return {ordersStore, userStore, consultationStore}
  },
}

</script>
