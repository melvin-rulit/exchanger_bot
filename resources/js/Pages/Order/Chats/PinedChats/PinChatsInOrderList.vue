<template>
  <div class="relative w-[78px] h-[68px] cursor-pointer hover:text-gray-400 hover:shadow-lg" @click="handleClick">
    <span
      class="absolute top-1 right-0 z-10 text-xs rounded-full px-2 py-0.2 text-white"
      style="background-color: rgba(220, 38, 38, 0.8);">
      <span v-if="order?.client"> {{ order.client.first_name }} </span>
    </span>
    <Icon icon="mdi:message-reply" width="76" height="66" class="absolute inset-0" />
    <div class="absolute inset-0 flex flex-col items-center justify-center text-md leading-tight text-black">
      <span v-if="order"> № {{ order.id }} </span>
    </div>
  </div>
</template>

<script>
import { Icon } from '@iconify/vue'

export default {
  name: 'ChatIcon',
  components: { Icon },
  props: {
    orderFullInfo: {
      type: Object,
      required: false,
      default: () => null,
    },
    order: {
      type: Object,
      required: false,
      default: () => null,
    },
    onClick: Function,
  },
  methods: {
    handleClick() {
      if (typeof this.onClick === 'function') {
        this.onClick()
      }
    },
  },
}
</script>
