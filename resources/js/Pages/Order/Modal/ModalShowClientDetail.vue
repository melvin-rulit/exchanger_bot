<template>
  <Alert ref="alertComponent" :message="alertMessage" :type="alertType" />

  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div @click.stop class="modal-container">

      <div class="modal-header">
        <div class="flex items-center">

          <h2 class="text-lg font-semibold">Информация о клиенте </h2>
          <span v-if="selectedClient" class="ml-3 mt-1 uppercase">{{ selectedClient.first_name}}</span>

        </div>

        <button @click="close" class="close-btn" aria-label="Close modal">
          <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" />
        </button>
      </div>

      <div class="modal-body">
        <div v-if="currentUser.role === 'Администратор'" class="flex items-center gap-2">
          <div class="justify-center w-full pt-6 bg-white mt-8">
            <div class="flex justify-center flex-wrap gap-20">

              <div class="w-[336px] h-[100px]">
                <div class="h-full rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-white p-5 flex flex-col border-t">
                  <h6 class="text-sm font-semibold text-gray-500 uppercase mx-auto">Имя/Ник</h6>
                  <div class="flex items-center gap-2 mt-1 flex-col">
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-md shadow-md cursor-pointer w-[200px]">{{ selectedClient.first_name }}</span>
                  </div>
                </div>
              </div>


              <div class="w-[336px] h-[100px]">
                <div class="h-full rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-white p-5 flex flex-col border-t">
                  <h6 class="text-sm font-semibold text-gray-500 uppercase mx-auto">Bot name</h6>
                  <div class="flex items-center gap-2 mt-1 flex-col">
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-md shadow-md cursor-pointer w-[200px]">@{{ selectedClient.bot_name }}</span>
                  </div>
                </div>
              </div>

              <div class="w-[336px] h-[100px]">
                <div class="h-full rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-white p-5 flex flex-col border-t">
                  <h6 class="text-sm font-semibold text-gray-500 uppercase mx-auto">Bot id</h6>
                  <div class="flex items-center gap-2 mt-1 flex-col">
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-md shadow-md cursor-pointer w-[200px]">{{ selectedClient.bot_id }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div v-if="currentUser.role === 'Администратор'" class="flex items-center gap-2">
          <div class="justify-center w-full pt-6 bg-white mt-8">
            <div class="flex justify-center flex-wrap gap-20">

              <div class="w-[336px] h-[100px]">
                <div class="h-full rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-white p-5 flex flex-col border-t">
                  <h6 class="text-sm font-semibold text-gray-500 uppercase mx-auto">Дата записи в базу</h6>
                  <div class="flex items-center justify-center mt-1 w-full">
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-md shadow-md cursor-default w-[200px] text-center">{{ formatDateTime(selectedClient.created_at) }}</span>
                  </div>

                </div>
              </div>


              <div class="w-[736px] h-[200px]">
                <div class="h-full rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-white p-5 flex flex-col border-t">
                  <h6 class="text-sm font-semibold text-gray-500 uppercase mx-auto">Комментарий к клиенту</h6>
                  <div class="flex items-center justify-center mt-1 w-full">
                    <span class="text-center">{{ selectedClient.comment }}</span>
                  </div>

                </div>

              </div>

            </div>
          </div>
        </div>

        <div v-if="currentUser.role !== 'Администратор'" class="flex items-center gap-2">
          <div class="justify-center w-full pt-6 bg-white mt-8">
            <div class="flex justify-center flex-wrap gap-20">
              <div class="w-[1100px] h-[300px]">
                <div class="h-full rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-white p-5 flex flex-col border-t">
                  <h6 class="text-sm font-semibold text-gray-500 uppercase mx-auto">Комментарий к клиенту</h6>
                  <div class="flex items-center justify-center mt-10 cursor-pointer w-[1000px]">
                    <span v-if="editableClientId !== selectedClient.id && selectedClient.comment && selectedClient.comment.trim().length > 0" @click="enableEdit(selectedClient)" >{{ selectedClient.comment }}</span>
                    <div
                      v-else-if="editableClientId !== selectedClient.id && (!selectedClient.comment || selectedClient.comment.trim().length === 0)"
                      @click="enableEdit(selectedClient)" >
                      <Icon icon="lets-icons:add-ring-duotone" width="64" height="64" />
                    </div>
                  </div>

                  <div  v-if="editableClientId === selectedClient.id" class="flex items-center gap-2">
                    <div>
                      <hollow-dots-spinner
                        :animation-duration="1000"
                        :dot-size="15"
                        :dots-num="1"
                        color="#4caf50"
                      />
                    </div>
                    <TextInput v-model="form.editableClientComment" width-class="w-[1000px]" height-class="h-[100px]" :multiline="true"/>
                    <button @click.stop="closeUpdateInput('userType')" class="close-btn h-8 w-8 flex items-center justify-center">
                      <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" class="icon-error"/>
                    </button>
                  </div>

                  <ButtonUI v-if="editableClientId === selectedClient.id" @click="updateClientComment" type="submit" color="green" class="mt-6">Сохранить</ButtonUI>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

</template>

<script>
import FadeOrInstant from "../../../Components/FadeOrInstant.vue";
import Alert from "../../../Components/Notifications/Alert.vue";
import { Icon } from '@iconify/vue';
import ButtonUI from "../../../Components/Button/ButtonUI.vue";
import { HollowDotsSpinner } from 'epic-spinners'
import ModalShowOrderScreenshot from '@/Pages/Order/Modal/ModalShowOrderScreenshot.vue'
import InfoNotification from '@/Components/Notifications/InfoNotification.vue'
import TextInput from '@/Components/Input/TextInput.vue'
import { ClientService } from '@/services/ClientService.js'
import { handleApiError } from '@/helpers/errors.js'

export default {
  components: {
    TextInput,
    InfoNotification, ModalShowOrderScreenshot, FadeOrInstant, Alert, Icon, ButtonUI, HollowDotsSpinner},
  props: {
    isActive: {
      type: Boolean,
      default: false
    },
    selectedClient: {
      type: Object,
      required: true
    },
    currentUser: {
      type: Object,
      required: true
    },
  },
  data() {
    return {
      editableClientId: null,
      form: {
        editableClientComment: null
      },
      errors: {},
      alertMessage: '',
      alertType: 'success',
      loading: false,
    }
  },
  methods: {
    triggerSuccessAlert($message) {
      this.alertMessage = $message;
      this.alertType = 'success';
      this.$refs.alertComponent.showAlert();
    },
    triggerErrorAlert($message) {
      this.alertMessage = $message;
      this.alertType = 'error';
      this.$refs.alertComponent.showAlert();
    },
    formatDateTime(datetime) {
      if (!datetime) return '';
      const fixedDate = datetime.replace(' ', 'T');
      const date = new Date(fixedDate);
      return date.toLocaleString('ru-RU', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
      });
    },
    enableEdit(object) {
      this.editableClientId = object.id;
      this.form.editableClientComment = object.comment
    },
    closeUpdateInput() {
      this.editableClientId = null
    },
    async updateClientComment() {
      try {
        const response = await ClientService.updateClientComment(this.selectedClient.id, this.form.editableClientComment);
        this.selectedClient.comment = response.data.comment.comment;
        this.editableClientId = null
        this.triggerSuccessAlert('Комментарий успешно сохранен');
      } catch (error) {
        this.errors = handleApiError(error)
      }
    },
    close() {
      this.$emit('update-client-comment', {
        id: this.selectedClient.id,
        comment: this.selectedClient.comment
      });
    },
  },
};
</script>

<style scoped>
.fixed {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  backdrop-filter: blur(4px);
  background-color: rgba(0, 0, 0, 0.9);
}
.flex {
  display: flex;
  align-items: center;
  justify-content: center;
}
.modal-container {
  background: white;
  width: 84rem;
  height: 46rem;
  max-width: 90%;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  display: flex;
  flex-direction: column;
  position: relative;
}
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 2px solid rgba(0, 0, 0, 0.2);
  padding-bottom: 10px;
}
.modal-body {
  margin-top: 100px;
}
.close-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  transition: transform 0.2s ease;

  &:hover {
    transform: scale(1.2);
  }
}
</style>
