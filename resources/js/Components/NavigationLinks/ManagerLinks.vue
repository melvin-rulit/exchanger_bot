<template>
  <div class="mt-2">
    <div class="hidden space-x-2 sm:-my-px sm:ms-40 sm:flex">
      <div class="flex items-center gap-3">
        <!-- Иконка сообщений -->
        <div v-show="ordersStore.unreadMessagesCount > 0" class="relative">
          <Icon icon="wpf:message-outline" width="24" height="24" class="text-white" />
          <span
            class="absolute -top-1.5 -right-1.5 bg-green-600 text-xs font-bold px-1.5 py-0.3 rounded-full">
      {{ ordersStore.unreadMessagesCount }}
    </span>
        </div>

        <!-- Иконка новых заказов -->
        <div v-show="ordersStore.unreadNewOrdersCount > 0" class="relative">
          <Icon icon="mdi:cart-outline" width="24" height="24" class="text-white" />
          <span
            class="absolute -top-1.5 -right-1.5 bg-green-600 text-xs font-bold px-1.5 py-0.3 rounded-full">
      {{ ordersStore.unreadNewOrdersCount }}
    </span>
        </div>
      </div>

      <NavLink
        :href="route('dashboard')"
        :active="route().current('dashboard')"
        :class="{'pulse-green': hasNewConsultMessages && !route().current('dashboard') && ordersStore.unreadMessagesCount > 0}">
        Заказы
      </NavLink>


      <div class="pl-5">
                                    <span v-if="consultationStore.unreadMessagesCount > 0"
                                          :class="['badge',route().current('consultation') ? 'badge-active' : 'badge-inactive']">
                                          {{ consultationStore.unreadMessagesCount }}
                                    </span>
      </div>
      <NavLink
        :href="route('consultation')"
        :active="route().current('consultation')"
        :class="{'pulse-green': hasNewConsultMessages && !route().current('consultation') && consultationStore.unreadMessagesCount > 0}">
        Консультация
      </NavLink>
    </div>

  </div>
</template>

<script>
import NavLink from '@/Components/NavLink.vue'
import { useOrdersStore } from '@/stores/ordersStore.js'
import { useConsultationStore } from '@/stores/consultationStore'
import { Icon } from '@iconify/vue'

export default {
  components: { Icon, NavLink, useConsultationStore },
  data: function() {
    return {
      hasNewConsultMessages: true,
    }
  },
  setup() {
    const ordersStore = useOrdersStore()
    const consultationStore = useConsultationStore()
    return { ordersStore, consultationStore }
  },
}

</script>

<style scoped>
.badge {
  display: inline-block;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  color: black;
  font-size: 14px;
  font-weight: bold;
  text-align: center;
  line-height: 18px;
}

.badge-active {
  background-color: #d1d5db;
}

.badge-inactive {
  background-color: #22c55e;
}
.pulse-green {
  animation: pulse-text 1s infinite;
}
@keyframes pulse-text {
  0%, 100% {
    color: #6b7280;
  }
  50% {
    color: #22c55e;
    text-shadow: 0 0 0px #22c55e;
  }
}
</style>
