<template>
  <div>
    <div class="min-h-screen"
         style="background-image: url('Images/background.jpg'); background-size: cover; background-position: center;">

      <nav class="bg-transparent">
        <!-- Primary Navigation Menu -->
        <div class="mx-auto with-custom">
          <div class="flex h-13 justify-between">
            <div class="flex">
<!--              🕒 <h1>03-04-2025</h1>-->
              <div class="date_block mt-2">
                <h1>{{today}}</h1>
              </div>

              <div class="flex shrink-0 items-center cursor-pointer text-white">
                <!--                                <Link :href="route('dashboard')">-->
                <!--                                    <ApplicationLogo-->
                <!--                                        class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200"-->
                <!--                                    />-->
                <!--                                </Link>-->
                <!--                              <Icon icon="flat-color-icons:search" width="30" height="30" />-->
                <div class="relative group">
                  <div class="text-white cursor-pointer sm:ms-6 sm:flex sm:items-center">
                    <span class="text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity"> Фильтр </span>
                    <Icon icon="lets-icons:chat-search-duotone" width="34" height="34" />
                  </div>
                </div>
              </div>

              <!-- Navigation Links -->
              <div
                class="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex"
              >
                <NavLink
                  :href="route('dashboard')"
                  :active="route().current('dashboard')"
                >
                  Заказы
                </NavLink>

                <NavLink
                  :href="route('consultation')"
                  :active="route().current('consultation')"
                  :class="{ 'pulse-green': hasNewConsultMessages }"
                >
                  Консультация
                </NavLink>
              </div>
            </div>


            <!--                        <div class="hidden sm:ms-6 sm:flex sm:items-center">-->
            <!--                            &lt;!&ndash; Settings Dropdown &ndash;&gt;-->
            <!--                            <div class="relative ms-3">-->
            <!--                                <Dropdown align="right" width="48">-->
            <!--                                    <template #trigger>-->
            <!--                                        <span class="inline-flex rounded-md">-->
            <!--                                            <button-->
            <!--                                                type="button"-->
            <!--                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300"-->
            <!--                                            >-->
            <!--                                                {{ $page.props.auth.user.name }}-->

            <!--                                                <svg-->
            <!--                                                    class="-me-0.5 ms-2 h-4 w-4"-->
            <!--                                                    xmlns="http://www.w3.org/2000/svg"-->
            <!--                                                    viewBox="0 0 20 20"-->
            <!--                                                    fill="currentColor"-->
            <!--                                                >-->
            <!--                                                    <path-->
            <!--                                                        fill-rule="evenodd"-->
            <!--                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"-->
            <!--                                                        clip-rule="evenodd"-->
            <!--                                                    />-->
            <!--                                                </svg>-->
            <!--                                            </button>-->
            <!--                                        </span>-->
            <!--                                    </template>-->

            <!--                                    <template #content>-->
            <!--                                        <DropdownLink-->
            <!--                                            :href="route('profile.edit')"-->
            <!--                                        >-->
            <!--                                            Profile-->
            <!--                                        </DropdownLink>-->
            <!--                                        <DropdownLink-->
            <!--                                            :href="route('logout')"-->
            <!--                                            method="post"-->
            <!--                                            as="button"-->
            <!--                                        >-->
            <!--                                            Log Out-->
            <!--                                        </DropdownLink>-->
            <!--                                    </template>-->
            <!--                                </Dropdown>-->
            <!--                            </div>-->
            <!--                        </div>-->


            <div class=" sm:ms-6 sm:flex sm:items-center">

              <div class="relative group">
                <div class="text-white cursor-pointer sm:ms-6 sm:flex sm:items-center">
                                    <span class="text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity"> Настройки </span>
                  <Icon icon="material-symbols-light:settings-b-roll-outline" width="34" height="34" />
                </div>
              </div>

              <span class="text-white font-weight-bolder font-size-base d-none d-md-inline mr-3 uppercase"
                    type="button" data-drawer-target="drawer-right-example"
                    data-drawer-show="drawer-right-example" data-drawer-placement="right"
                    aria-controls="drawer-right-example">{{ $page.props.auth.user.name }}</span>
              <span class="d-none d-md-block">
                            <span class="px-2 py-0.5 text-xs font-bold bg-green-100 rounded-md shadow-md">
                        {{ $page.props.auth.role }}
                            </span>
              </span>
              <div class="relative group">
                <div class="text-white cursor-pointer sm:ms-6 sm:flex sm:items-center">
                  <Icon @click="logout" icon="guidance:exit" width="24" height="24" />
                  <span
                    class="absolute ml-5 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity"> Выйти </span>
                </div>
              </div>


            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
              <button
                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400 dark:focus:bg-gray-900 dark:focus:text-gray-400"
              >
                <svg
                  class="h-6 w-6"
                  stroke="currentColor"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <path
                    :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                    :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div
          :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
          class="sm:hidden"
        >
          <div class="space-y-1 pb-3 pt-2">
            <ResponsiveNavLink
              :href="route('dashboard')"
              :active="route().current('dashboard')"
            >
              Dashboard
            </ResponsiveNavLink>
          </div>

          <!-- Responsive Settings Options -->
          <div
            class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-600"
          >
            <div class="px-4">
              <div
                class="text-base font-medium text-gray-800 dark:text-gray-200"
              >
                {{ $page.props.auth.user.name }}
              </div>
              <div class="text-sm font-medium text-gray-500">
                {{ $page.props.auth.user.email }}
              </div>
            </div>

            <div class="mt-3 space-y-1">
              <ResponsiveNavLink :href="route('profile.edit')">
                Profile
              </ResponsiveNavLink>
              <ResponsiveNavLink
                :href="route('logout')"
                method="post"
                as="button"
              >
                Log Out
              </ResponsiveNavLink>
            </div>
          </div>
        </div>
      </nav>

      <!-- Page Heading -->
      <header
        class="bg-white shadow dark:bg-gray-800"
        v-if="$slots.header"
      >
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
          <slot name="header" />
        </div>
      </header>

      <!-- Page Content -->
      <main>
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, defineProps } from 'vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import { Link } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import { router } from '@inertiajs/vue3';

const logout = () => {
  router.post(route('logout'));
};

const hasNewConsultMessages = ref(true)

const getFormattedDate = () => {
  const days = ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота']
  const months = [
    'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
    'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
  ]

  const now = new Date()
  const dayName = days[now.getDay()]
  const day = now.getDate().toString().padStart(2, '0')
  const month = months[now.getMonth()]
  const year = now.getFullYear()

  return `${dayName} ${day} ${month} ${year}`
}

const today = ref(getFormattedDate())

</script>

<style>
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
</style>
