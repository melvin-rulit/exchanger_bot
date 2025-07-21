<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <div class="py-2">
      <div class="mx-auto w-full lg:px-4">
        <div class="overflow-hidden  shadow-lg sm:rounded-lg dark:bg-gray-800">
          <div class="text-gray-900 dark:text-gray-100">
            <ConsultationList></ConsultationList>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ConsultationList from '@/Pages/Consultation/ConsultationList.vue'
</script>

