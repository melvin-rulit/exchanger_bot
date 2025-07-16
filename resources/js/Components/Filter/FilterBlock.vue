<template>
  <div v-if="isActive" class="block">
    <div class="flex gap-10 items-center">

      <template v-if="type === 'orders'">
        <label class="flex items-center gap-2">
          <Checkbox v-model:checked="filters" value="new" label="Новые" />
        </label>
        <label class="flex items-center gap-2">
          <Checkbox v-model:checked="filters" value="active" label="Активные" />
        </label>
        <label class="flex items-center gap-2">
          <Checkbox v-model:checked="filters" value="success" label="Завершенные" />
        </label>
        <label class="flex items-center gap-2">
          <Checkbox v-model:checked="filters" value="closed" label="Отмененные" />
        </label>
      </template>

      <template v-else-if="type === 'users'">
        <label class="flex items-center gap-2">
          <Checkbox v-model:checked="filters" value="1" label="Активен" />
        </label>
        <label class="flex items-center gap-2">
          <Checkbox v-model:checked="filters" value="0" label="Отключен" />
        </label>
        <label class="flex items-center gap-2">
          <Checkbox v-model:checked="filters" value="Администратор" label="Администратор" />
        </label>
        <label class="flex items-center gap-2">
          <Checkbox v-model:checked="filters" value="Менеджер" label="Менеджер" />
        </label>
      </template>

      <!-- Кнопка закрытия справа -->
      <button
        @click.stop="closeFilterBlock"
        class="close-btn h-10 w-10 flex items-center justify-center">
        <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" class="icon-error" />
      </button>

    </div>
  </div>
</template>


<script>
import ButtonUI from '@/Components/Button/ButtonUI.vue'
import { Icon } from '@iconify/vue'
import Checkbox from '@/Components/Checkbox.vue'
import { useOrdersStore } from '@/stores/ordersStore.js'
import { useUserStore } from '@/stores/userStore.js'

export default {
  components: { Checkbox, ButtonUI, Icon },
  props: {
    isActive: {
      type: Boolean,
      default: false,
    },
    type: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      filters: [],
    };
  },
  mounted() {
    if (this.activeStore) {
      this.filters = [...this.activeStore.getSelectedFilters()]
    }
  },
  computed: {
    activeStore() {
      if (this.type === 'orders') {
        return useOrdersStore()
      } else if (this.type === 'users') {
        return useUserStore()
      }
      return null
    }
  },
  methods: {
    closeFilterBlock() {
      this.$emit('close');
    },
  },
  watch: {
    filters: {
      handler(newFilters) {
        if (this.activeStore) {
          this.activeStore.setSelectedFilters(newFilters)
        }
        this.$emit('filter-change', newFilters)
      },
      deep: true,
    },

  },
}

</script>

<style scoped>
.block {
  position: fixed;
  bottom: 12px;
  left: 37%;
}
</style>

