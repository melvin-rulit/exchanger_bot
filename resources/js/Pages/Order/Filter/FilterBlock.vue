<template>
    <div v-if="isActive" class="block">

      <div class="flex gap-10">
        <label class="flex items-center gap-2">
          <Checkbox v-model:checked="filters" value="new" label="Новые"/>
        </label>
        <label class="flex items-center gap-2">
          <Checkbox v-model:checked="filters" value="active" label="Активные"/>
        </label>
        <label class="flex items-center gap-2">
          <Checkbox v-model:checked="filters" value="success" label="Завершенные"/>
        </label>
        <label class="flex items-center gap-2">
          <Checkbox v-model:checked="filters" value="closed" label="Отмененные"/>
        </label>

        <button @click.stop="closeFilterBlock" class="close-btn h-10 w-10 flex items-center justify-center">
          <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" class="icon-error" />
        </button>
      </div>

    </div>
</template>

<script>
import ButtonUI from '@/Components/Button/ButtonUI.vue'
import { Icon } from '@iconify/vue'
import Checkbox from '@/Components/Checkbox.vue'
import { useOrdersStore } from '@/stores/ordersStore'

export default {
  components: { Checkbox, ButtonUI, Icon },
  props: {
    isActive: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    const store = useOrdersStore()

    return {
      store,
      filters: [...store.selectedFilters],
    };
  },
  methods: {
    closeFilterBlock() {
      this.$emit('close');
    },
  },
  watch: {
    filters: {
      handler(newFilters) {
        this.store.setSelectedFilters(newFilters)
        this.$emit('filter-change', newFilters)
      },
      deep: true,
    },
  },
}

</script>

<style>
.block {
  position: fixed;
  bottom: 35px;
  left: 37%;
}
</style>

