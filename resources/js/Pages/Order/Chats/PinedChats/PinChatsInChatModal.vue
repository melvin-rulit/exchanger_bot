<template>
  <div v-if="chats" @click="handleClick" class="relative w-[30px] h-[30px] cursor-pointer">
    <Icon
      icon="circum:chat-1"
      width="50"
      height="50"
      class="absolute inset-0"
      :class="isSelected ? 'text-green-600' : 'text-gray-400'"
    />
    <div v-if="chats.order" class="absolute inset-0 flex top-3 left-3 items-center justify-center text-[14px] text-white leading-none">
      {{chats.order.id}}
    </div>
    <div v-else-if="chats.order === null" class="absolute inset-0 flex items-center justify-center text-[10px] text-white leading-none">
      {{chats.id}}
    </div>
  </div>
</template>

<script>
import { Icon } from '@iconify/vue'

export default {
  components: { Icon },
  props: {
    chats: {
      type: Object,
      required: true
    },
    selectedOrderId: {
      type: [Number, String],
      required: false
    },
    onClick: Function,
  },
  computed: {
    isSelected() {
      return this.chats?.order?.id === this.selectedOrderId
    }
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

