<template>
  <input
    :placeholder="placeholder"
    :autofocus="autofocus"
    :type="type"
    :readonly="readonly"
    :disabled="disabled"
    class="text-sm px-2 py-1 rounded border border-gray-300 focus:outline-none focus:ring-0 focus:border-gray-400"
    :class="widthClass"
    :value="model"
    ref="input"
    @keydown="onKeyDown"
    @input="handleInput"
    @beforeinput="preventInvalidInput"
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

function handleInput(e: Event) {
  const target = e.target as HTMLInputElement
  let value = target.value.replace(/\s/g, '')
  if (value.length > 10) value = value.slice(0, 10)
  model.value = value
}

function preventInvalidInput(e: InputEvent) {
  const inputEl = e.target as HTMLInputElement

  // Разрешаем все input-типы кроме вставки текста
  if (e.inputType !== 'insertText') return

  const current = inputEl.value
  const selectionStart = inputEl.selectionStart || 0
  const selectionEnd = inputEl.selectionEnd || 0
  const inserted = e.data || ''
  const next = current.slice(0, selectionStart) + inserted + current.slice(selectionEnd)

  // Блокируем пробелы и длину > 9
  if (/\s/.test(inserted) || next.length > 10) {
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
