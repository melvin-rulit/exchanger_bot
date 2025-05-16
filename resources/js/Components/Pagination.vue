<template>
  <nav class="my-3 pt-6">
    <ul class="inline-flex -space-x-px">
      <li>
        <button
          :disabled="stopPrev"
          @click="changePage(currentPage - 1)"
          class="px-3 py-2 ml-0 leading-tight border rounded-l-lg"
          :class="buttonClass(stopPrev, false, true)">  <!-- isNavButton = true -->
          Предыдущая
        </button>
      </li>
      <li v-for="page in pages" :key="page">
        <button
          @click="changePage(page)"
          :class="buttonClass(false, page === currentPage)"
          class="px-3 py-2 leading-tight border">
          {{ page }}
        </button>
      </li>
      <li>
        <button
          :disabled="stopForward"
          @click="changePage(currentPage + 1)"
          class="px-3 py-2 leading-tight border rounded-r-lg"
          :class="buttonClass(stopForward, false, true)">  <!-- isNavButton = true -->
          Следующая
        </button>
      </li>
    </ul>
  </nav>
</template>

<script>
import { range } from '@/helpers/utils'

export default {
  name: 'Pagination',
  props: {
    total: { type: Number, required: true },
    limit: { type: Number, required: true },
    currentPage: { type: Number, required: true },
  },
  emits: ['page-change'],
  computed: {
    pages() {
      const pagesCount = Math.ceil(this.total / this.limit)
      return range(1, pagesCount)
    },
    stopForward() {
      return this.currentPage >= this.pages.length
    },
    stopPrev() {
      return this.currentPage <= 1
    },
  },
  methods: {
    changePage(page) {
      if (page !== this.currentPage) {
        this.$emit('page-change', page)
      }
    },
    buttonClass(disabled, active = false, isNavButton = false) {
      if (isNavButton) {
        return {
          'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700': !disabled,
          'opacity-100 cursor-not-allowed bg-white': disabled,
          'dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': true,
        }
      }

      return {
        'text-blue-600 bg-white hover:bg-gray-100 text-gray-700': !disabled && !active,
        'bg-gray-100 font-bold': active,
        'opacity-50 cursor-not-allowed': disabled,
        'dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': true,
      }
    }

  }
}
</script>
