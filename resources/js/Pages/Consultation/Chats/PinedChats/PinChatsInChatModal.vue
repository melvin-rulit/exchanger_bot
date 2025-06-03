<template>
  <div class="relative w-[30px] h-[30px] cursor-pointer">
    <Icon
      icon="circum:chat-1"
      width="37"
      height="37"
      class="absolute inset-0"
      :class="chats.is_active ? 'text-green-600' : 'text-gray-500'"
    />
    <div class="absolute inset-0 flex items-center justify-center text-[10px] text-black leading-none">
      {{chats.order.id}}
    </div>
  </div>
</template>

<script>
import { Icon } from '@iconify/vue'

export default {
  components: { Icon },
  props: {
    chats: Object,
    pinnedChats: Object
  },
  methods: {

  },
}
</script>

