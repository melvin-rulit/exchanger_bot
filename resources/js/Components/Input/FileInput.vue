<template>
  <div>
    <input
      ref="file"
      type="file"
      :accept="accept"
      class="hidden"
      @change="change"
    />
  </div>
</template>


<script>
export default {
  props: {
    modelValue: File,
    label: String,
    accept: String,
    errors: {
      type: Array,
      default: () => [],
    },
  },
  emits: ['update:modelValue'],
  watch: {
    modelValue(value) {
      if (!value) {
        this.$refs.file.value = ''
      }
    },
  },
  methods: {
    filesize(size) {
      var i = Math.floor(Math.log(size) / Math.log(1024))
      return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i]
    },
    browse() {
      this.$refs.file.click()
    },
    change(e) {
      this.$emit('update:modelValue', e.target.files[0])
      this.$emit('change', e.target.files[0])
    },
    remove() {
      this.$emit('update:modelValue', null)
    },
  },
}
</script>
