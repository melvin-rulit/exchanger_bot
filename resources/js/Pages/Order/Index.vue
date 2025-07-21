<template>
  <Head title="Заказы" />

  <AuthenticatedLayout :user="currentUser">
    <div class="py-2">
      <div class="mx-auto w-full lg:px-4">
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
