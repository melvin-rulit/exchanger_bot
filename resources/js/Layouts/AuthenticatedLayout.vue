<template>
    <div class="min-h-screen" style="background-image: url('Images/background.jpg'); background-size: cover; background-position: center;">

      <nav class="bg-transparent">
        <!-- Primary Navigation Menu -->
        <div class="mx-auto with-custom">
          <div class="flex h-13 justify-between">
            <div class="flex">
              <div class="date_block mt-2">
                <h1>{{ today }}</h1>
              </div>

              <!-- Navigation Links -->
              <div class="mt-2">
                <div class="hidden space-x-2 sm:-my-px sm:ms-40 sm:flex">
                  <div>
                  <span v-if="ordersStore.unreadMessagesCount > 0"
                        :class="['badge',route().current('dashboard') ? 'badge-active' : 'badge-inactive']">
                        {{ ordersStore.unreadMessagesCount }}
                  </span>

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
            </div>

            <div class="sm:ms-6 sm:flex sm:items-center">

              <div class="relative group">
                <div class="text-white cursor-pointer sm:ms-6 sm:flex sm:items-center">
<!--                  <span class="text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity"> Настройки </span>-->
                  <Icon @click="settings" icon="material-symbols-light:settings-b-roll-outline" width="34" height="34" class="hover:text-gray-400" />
                </div>
              </div>

              <span v-if="userStore.currentUser" class="text-white font-weight-bolder font-size-base d-none d-md-inline mr-3 uppercase"
                    type="button" data-drawer-target="drawer-right-example"
                    data-drawer-show="drawer-right-example" data-drawer-placement="right"
                    aria-controls="drawer-right-example">{{ userStore.currentUser.name }}</span>
              <span v-if="userStore.currentUser" class="d-none d-md-block">
                            <span class="px-2 py-0.5 text-xs font-bold bg-green-100 rounded-md shadow-md">
                        {{ userStore.currentUser.role[0] }}
                            </span>
              </span>
              <div class="relative group">
                <div class="text-white cursor-pointer sm:ms-6 sm:flex sm:items-center">
                  <Icon @click="logout" icon="guidance:exit" width="24" height="24" class="hover:text-gray-400" />
<!--                  <span class="absolute ml-5 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity"> Выйти </span>-->
                </div>
              </div>


            </div>

          </div>
        </div>
      </nav>

      <!-- Page Heading -->
      <header class="bg-white shadow" v-if="$slots.header">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>

      <!-- Page Content -->
      <main>
        <ConsultationListWatcher />
        <slot />
      </main>
    </div>

</template>

<script setup>
import { ref, onMounted } from 'vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import { Link } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import { router } from '@inertiajs/vue3'
import { useOrdersStore } from '@/stores/ordersStore'
import { UserService } from '@/services/UserService.js'
import { useUserStore } from '@/stores/userStore.js'
import { useConsultationStore } from '@/stores/consultationStore'
import ConsultationListWatcher from '@/Pages/Consultation/Watcher/ConsultationListWatcher.vue'
import { getFormattedDate } from '@/utils/dateFormatter.js'
import { handleApiError } from '@/helpers/errors.js'

const currentUser = ref(null)
const errors = ref(null)
const logout = () => {
  router.post(route('logout'))
}
const settings = () => {
  router.get(route('settings'))
}

const hasNewConsultMessages = ref(true)
const today = ref(getFormattedDate())
const ordersStore = useOrdersStore()
const userStore = useUserStore()
const consultationStore = useConsultationStore()

async function getUser() {
  try {
    const response = await UserService.currentUser()
    userStore.setCurrentUser(response.data.data)
  } catch (error) {
    errors.value = handleApiError(error)
  }
}

onMounted(() => {
  getUser()
})
</script>

<style scoped>
.with-custom {
  width: 95%;
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

.pulse-green {
  animation: pulse-text 1s infinite;
}

.date_block {
  color: ghostwhite;
}
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
</style>
