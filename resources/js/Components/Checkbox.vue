<template>
  <label class="inline-flex items-center gap-2">
    <input
      type="checkbox"
      class="rounded"
      :checked="isChecked"
      @change="toggleCheck"
    />
    <span class="text-white">{{ label }}</span>
  </label>
</template>

<script setup>
import { computed } from 'vue'

const emit = defineEmits(['update:checked'])

const props = defineProps({
  checked: {
    type: [Array, Boolean],
    required: false,
  },
  value: {
    type: [String, Number, Boolean],
    default: null,
  },
  label: {
    type: String,
    required: true,
  },
})

const isChecked = computed(() => {
  if (Array.isArray(props.checked)) {
    return props.checked.includes(props.value)
  } else {
    return props.checked
  }
})

function toggleCheck(e) {
  const isChecked = e.target.checked

  if (Array.isArray(props.checked)) {
    let newValue = [...props.checked]
    if (isChecked && !newValue.includes(props.value)) {
      newValue.push(props.value)
    } else if (!isChecked && newValue.includes(props.value)) {
      newValue = newValue.filter(v => v !== props.value)
    }
    emit('update:checked', newValue)
  } else {
    emit('update:checked', isChecked)
  }
}
</script>
