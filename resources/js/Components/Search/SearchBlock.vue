<template>
  <div v-if="isActive" class="search_block">
    <div class="flex gap-10 items-center">
      <div style="width: 20px; margin-right:170px">
      <flat-pickr @on-change="accurateSearch" v-model="form.dateFrom" :config="flatpickrConfig" class="flatpickr-input" placeholder="выберите дату от" />
        <button
          v-if="form.dateFrom"
          @click="clearField('dateFrom')"
          class="absolute top-1/3 left-[190px] -translate-y-1/2 text-gray-400">
          <Icon icon="material-symbols:close-small-rounded" width="20" height="20" />
        </button>
      </div>
      <div style="width: 10px; margin-right:200px">
      <flat-pickr @on-change="accurateSearch" v-model="form.dateTo" :config="flatpickrConfig" class="flatpickr-input" placeholder="выберите дату до"/>
        <button
          v-if="form.dateTo"
          @click="clearField('dateTo')"
          class="absolute top-1/3 left-[420px] -translate-y-1/2 text-gray-400">
          <Icon icon="material-symbols:close-small-rounded" width="20" height="20" />
        </button>
      </div>
      <div class="relative" style="width: 20px; margin-right:250px">
        <multiselect
          v-model="form.status"
          :options="statuses"
          placeholder="список статусов"
          :close-on-select="true"
          :show-labels="false"
          :searchable="false"
          :append-to-body="false"
          :allow-empty="true"
          label="label"
          @select="accurateSearch"/>
        <button
          v-if="form.status"
          @click="clearField('status')"
          class="absolute top-1/3 left-[270px] -translate-y-1/2 text-gray-400">
          <Icon icon="material-symbols:close-small-rounded" width="20" height="20" />
        </button>
      </div>

      <div class="relative" style="width: 20px; margin-right:250px">
      <multiselect
        v-model="form.selectedClient"
        :options="clients"
        placeholder="список клиентов"
        :close-on-select="true"
        :show-labels="false"
        :searchable="false"
        :append-to-body="false"
        label="first_name"
        @select="accurateSearch"/>
        <button
          v-if="form.selectedClient"
          @click="clearField('selectedClient')"
          class="absolute top-1/3 left-[270px] -translate-y-1/2 text-gray-400">
          <Icon icon="material-symbols:close-small-rounded" width="20" height="20" />
        </button>
      </div>

      <div class="relative" style="width: 20px; margin-right:250px">
      <multiselect
        v-model="form.selectedUser"
        :options="userStore.users"
        placeholder="список пользователей"
        :close-on-select="true"
        :show-labels="false"
        :searchable="false"
        :append-to-body="false"
        label="name"
        @select="accurateSearch"/>
        <button
          v-if="form.selectedUser"
          @click="clearField('selectedUser')"
          class="absolute top-1/3 left-[270px] -translate-y-1/2 text-gray-400">
          <Icon icon="material-symbols:close-small-rounded" width="20" height="20" />
        </button>
      </div>

      <div class="mb-1 relative">
        <TextInput @enter="applySearch()" v-model="searchValue" class="text-xl" width-class="w-[300px]" height-class="h-[35px]" :placeholder="placeholder"/>
        <button
          v-if="searchValue"
          @click="clearField('searchValue')"
          class="absolute right-2 top-1/3 -translate-y-1/2 text-gray-400">
          <Icon icon="material-symbols:close-small-rounded" width="20" height="20" />
        </button>
      </div>
    <button
      @click.stop="closeSearchBlock"
      class="close-btn h-10 w-10 flex items-center justify-center">
      <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" class="icon-error" />
    </button>
  </div>
  </div>
</template>

<script>
import ButtonUI from '@/Components/Button/ButtonUI.vue'
import { Icon } from '@iconify/vue'
import TextInput from '@/Components/Input/TextInput.vue'
import Multiselect from 'vue-multiselect'
import { useUserStore } from '@/stores/userStore.js'
import { ClientService } from '@/services/ClientService.js'
import { handleApiError } from '@/helpers/errors.js'
import flatPickr from 'vue-flatpickr-component'
import { Russian } from "flatpickr/dist/l10n/ru.js"
import { eventBus } from '@/utils/eventBus.js'

export default {
  components: { Multiselect, TextInput, ButtonUI, Icon, flatPickr},
  props: {
    isActive: {
      type: Boolean,
      default: false,
    },
    type: {
      type: String,
      required: true,
    },
    placeholder: {
      type: String,
    },
  },
  data() {
    return {
      statuses: [
        { value: 'new', label: 'Новые' },
        { value: 'active', label: 'Активные' },
        { value: 'closed', label: 'Отменённые' },
        { value: 'success', label: 'Завершённые' },
      ],
      clients: [],
      users: [],
      searchValue: null,
      form: {
        dateFrom: null,
        dateTo: null,
        status: null,
        selectedClient: null,
        selectedUser: null
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
  setup() {
    const userStore = useUserStore()
    return { userStore }
  },
  mounted() {
    this.getClients()
  },
  methods: {
    async getClients() {
      try {
        const response = await ClientService.getClients()
        this.clients = response.data.data || []
      } catch (error) {
        this.errors = handleApiError(error)
      }
    },
    applySearch() {
      this.$emit('rough_search', this.searchValue)
    },
    accurateSearch() {
      this.$emit('accurate_search', this.form)
    },
    clearField(field) {
      switch (field) {
        case 'searchValue':
          this.searchValue = null
          break;
          case 'selectedUser':
          this.form.selectedUser = null
          break;
          case 'dateFrom':
          this.form.dateFrom = null
          break;
          case 'dateTo':
          this.form.dateTo = null
          break;
        case 'selectedClient':
          this.form.selectedClient = null
          break;
          case 'status':
          this.form.status = null
          break;
      }
      this.accurateSearch()
    },
    closeSearchBlock() {
      const wasSearchApplied = !!this.searchValue?.trim() || Object.values(this.form).some(val => val !== null && val !== '')

        this.$emit('close', wasSearchApplied)

      this.searchValue = null
      Object.keys(this.form).forEach(key => this.form[key] = null)
    },
  },
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>
<style scoped>
.search_block {
  position: fixed;
  bottom: 12px;
  left: 5%;
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
::v-deep(.multiselect) {
  width: 300px !important;
  min-width: 300px !important;
  height: 32px !important;
  font-size: 15px;
  margin: 0 !important;
  padding: 0 !important;
}
::v-deep(.multiselect__input) {
  height: 26px !important;
  line-height: 28px !important;
  padding-top: 0 !important;
  padding-bottom: 0 !important;
}

::v-deep(.multiselect__single) {
  line-height: 25px !important;
  padding: 0 6px !important;
}

::v-deep(.multiselect__tags) {
  min-height: 28px !important;
  padding: 0 6px 0 14px !important;
}

::v-deep(.multiselect__select) {
  height: 26px !important;
}

::v-deep(.multiselect__option--highlight) {
  background-color: #f0f8ff !important;
  color: #000000 !important;
}
</style>
