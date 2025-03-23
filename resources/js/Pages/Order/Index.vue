<template>
  <Head title="Заказы" />

  <AuthenticatedLayout :user="currentUser">
<!--            <template #header>-->
<!--                <h2-->
<!--                    class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"-->
<!--                >-->
<!--                    Dashboard-->
<!--                </h2>-->
<!--            </template>-->

    <div class="py-2">
      <div class="mx-auto max-w-9xl sm:px-6 lg:px-8">
        <div class="overflow-hidden  shadow-lg sm:rounded-lg dark:bg-gray-800">
          <div class="text-gray-900">
            <OrdersList></OrdersList>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import OrdersList from '@/Pages/Order/OrdersList.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

</script>
