<template>
  <input
    :placeholder="placeholder"
    :autofocus="autofocus"
    :type="type"
    :readonly="readonly"
    :disabled="disabled"
    class="text-sm px-2 py-1 rounded border border-gray-300 focus:outline-none focus:ring-0 focus:border-gray-400"
    :class="widthClass"
    v-model="model"
    ref="input"
    @keydown="onKeyDown"
  />
</template>


<script setup lang="ts">
import { onMounted, ref } from 'vue'

interface Props {
  placeholder?: string
  type?: string
  readonly?: boolean
  disabled?: boolean
  autofocus?: boolean
  widthClass?: string
}

const {
  placeholder,
  type = 'text',
  readonly = false,
  disabled = false,
  autofocus = false,
  widthClass = 'w-full'
} = defineProps<Props>()

const emit = defineEmits(['enter'])
function onKeyDown(e: KeyboardEvent) {
  if (e.key === 'Enter') emit('enter')
}

const model = defineModel<string>()
const input = ref<HTMLInputElement | null>(null)

onMounted(() => {
  if (autofocus && input.value) {
    input.value.focus()
  }
})

defineExpose({ focus: () => input.value?.focus() })
</script>


<style>
input::placeholder {
  color: #9ca3af;
  opacity: 0.8;
}
</style>
