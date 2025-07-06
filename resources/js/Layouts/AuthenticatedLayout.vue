<template>
    <div class="min-h-screen bg-main">

      <nav v-if="!lockStore.isModalLockShow" class="bg-transparent">

        <div class="mx-auto with-custom">
          <div class="flex h-13 justify-between">
            <div class="flex">
              <div class="date_block mt-2">
                <h1>{{ today }}</h1>
              </div>

                <ManagerLinks v-if="userStore.currentUser && userStore.currentUser.role === 'Менеджер'"/>
                <AdminLinks v-if="userStore.currentUser && userStore.currentUser.role === 'Администратор'"/>

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
              <span v-if="userStore.currentUser && userStore.currentUser.role && userStore.currentUser.role.length" class="d-none d-md-block">
                            <span class="px-2 py-0.5 text-xs font-bold bg-green-100 rounded-md shadow-md">
                         {{ userStore.currentUser.role }}
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

      <ModalLock
        :is-active="lockStore.isModalLockShow"
        @unlocked="unLockScreen"
      />

      <!-- Page Heading -->
      <header class="bg-white shadow" v-if="$slots.header">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>

      <!-- Page Content -->
      <main v-if="!lockStore.isModalLockShow">
        <ConsultationListWatcher />
        <OrderListWatcher />
        <slot />
      </main>
    </div>

</template>

<script setup>
import { ref } from 'vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { Icon } from '@iconify/vue'
import { router } from '@inertiajs/vue3'
import { useOrdersStore } from '@/stores/ordersStore'
import { useUserStore } from '@/stores/userStore.js'
import { useLockScreenStore } from '@/stores/lockScreenStore.js'
import { useConsultationStore } from '@/stores/consultationStore'
import ConsultationListWatcher from '@/Pages/Consultation/Watcher/ConsultationListWatcher.vue'
import OrderListWatcher from '@/Pages/Order/Watcher/OrderListWatcher.vue'
import { getFormattedDate } from '@/utils/dateFormatter.js'
import ManagerLinks from '@/Components/NavigationLinks/ManagerLinks.vue'
import AdminLinks from '@/Components/NavigationLinks/AdminLinks.vue'
import ModalLock from '@/Components/Modal/ModalLock.vue'

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
const lockStore = useLockScreenStore()

function unLockScreen() {
  lockStore.hideLockModal()
}

</script>

<style scoped>
.bg-main {
  background-image: url('/Images/background.jpg');
  background-size: cover;
  background-position: center;
}
.with-custom {
  width: 95%;
}
.date_block {
  color: ghostwhite;
}
</style>
