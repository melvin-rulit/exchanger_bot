<template>
  <div v-if="isActive" class="statistic_search_block">
    <div class="flex gap-10 items-center">
      <div style="width: 20px; margin-right:180px">
      <flat-pickr @on-change="searchFrom" v-model="form.dateFrom" :config="flatpickrConfig" class="flatpickr-input" placeholder="выберите дату от" />
        <button
          v-if="form.dateFrom"
          @click="form.dateFrom = null"
          class="absolute top-1/3 left-[190px] -translate-y-1/2 text-gray-400">
          <Icon icon="material-symbols:close-small-rounded" width="20" height="20" />
        </button>
      </div>
      <div style="width: 20px; margin-right:200px">
      <flat-pickr @on-change="searchFrom" v-model="form.dateTo" :config="flatpickrConfig" class="flatpickr-input" placeholder="выберите дату до" />
        <button
          v-if="form.dateTo"
          @click="form.dateTo = null"
          class="absolute top-1/3 left-[430px] -translate-y-1/2 text-gray-400">
          <Icon icon="material-symbols:close-small-rounded" width="20" height="20" />
        </button>
      </div>

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
import flatPickr from 'vue-flatpickr-component'
import { Russian } from "flatpickr/dist/l10n/ru.js"

export default {
  components: { flatPickr, Checkbox, ButtonUI, Icon },
  props: {
    isActive: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      filters: [],
      form: {
        dateFrom: null,
        dateTo: null,
      },
      flatpickrConfig: {
        onOpen: () => {
          //this.isCalendarOpen = true;
        },
        onClose: () => {
          this.isCalendarOpen = false;
        },
        allowInput: false,
        altFormat: 'd.m.Y',
        dateFormat: 'd.m.Y',
        locale: Russian,
        altInputClass: 'custom-alt-input',
      },
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
    searchFrom() {
      this.$emit('filter-change', this.form)
    },
    closeFilterBlock() {
      Object.keys(this.form).forEach(key => this.form[key] = null)
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
.statistic_search_block {
  position: fixed;
  bottom: 12px;
  left: 38%;
}
::v-deep(.flatpickr-input) {
  height: 36px;
  font-size: 13px;
  margin-bottom: 6px;
}
:deep(.custom-alt-input::placeholder) {
  color: #adadad;
}
::v-deep(.flatpickr-input),
:deep(.custom-alt-input) {
  border-radius: 4px;
  border: 1px solid #ccc;
}
</style>

