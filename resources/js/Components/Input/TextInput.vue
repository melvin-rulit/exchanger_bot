<template>
  <label v-if="isTitle" class="block mb-1 text-sm font-medium text-gray-900">{{title}}</label>

  <textarea
    v-if="multiline"
    ref="input"
    :placeholder="placeholder"
    :readonly="readonly"
    :disabled="disabled"
    :autofocus="autofocus"
    class="text-sm px-2 rounded border border-gray-300 focus:outline-none focus:ring-0 focus:border-gray-400 text-gray-900 shadow-md resize-none placeholder-gray-400"
    :class="[widthClass, heightClass]"
    :value="model"
    @keydown="onKeyDown"
    @input="handleInput"
  ></textarea>

  <input
    v-else
    ref="input"
    :placeholder="placeholder"
    :readonly="readonly"
    :disabled="disabled"
    :autofocus="autofocus"
    :type="type"
    class="text-sm px-2 rounded border border-gray-300 focus:outline-none focus:ring-0 focus:border-gray-400 text-gray-900 shadow-md"
    :class="[widthClass, heightClass]"
    :value="model"
    @keydown="onKeyDown"
    @input="handleInput"
  />
</template>


<script setup lang="ts">
import { onMounted, ref } from 'vue'

interface Props {
  placeholder?: string
  title?: string
  isTitle?: boolean
  type?: string
  readonly?: boolean
  disabled?: boolean
  autofocus?: boolean
  widthClass?: string
  heightClass?: string
  limitLength?: boolean
  maxLength?: number
  trimWhitespace?: boolean
  multiline?: boolean
}

const {
  placeholder,
  title,
  isTitle = false,
  type = 'text',
  readonly = false,
  disabled = false,
  autofocus = false,
  limitLength = false,
  maxLength = 10,
  trimWhitespace = false,
  widthClass = 'w-full',
  heightClass = 'h-[32px]'
} = defineProps<Props>()

const emit = defineEmits(['enter'])

function onKeyDown(e: KeyboardEvent) {
  if (e.key === 'Enter') emit('enter')
}

const model = defineModel<string>()
const input = ref<HTMLInputElement | null>(null)

function handleInput(e: Event) {
  const target = e.target as HTMLInputElement
  let value = target.value

  if (trimWhitespace) {
    value = value.replace(/\s/g, '')
  }

  if (limitLength && value.length > maxLength) {
    value = value.slice(0, maxLength)
  }

  model.value = value
}

function preventInvalidInput(e: InputEvent) {
  if (!limitLength) return

  const inputEl = e.target as HTMLInputElement
  if (e.inputType !== 'insertText') return

  const current = inputEl.value
  const selectionStart = inputEl.selectionStart || 0
  const selectionEnd = inputEl.selectionEnd || 0
  const inserted = e.data || ''
  const next = current.slice(0, selectionStart) + inserted + current.slice(selectionEnd)

  if (/\s/.test(inserted) || next.length > maxLength) {
    e.preventDefault()
  }
}
onMounted(() => {
  if (autofocus && input.value) {
    input.value.focus()
  }
})

defineExpose({ focus: () => input.value?.focus() })
</script>
