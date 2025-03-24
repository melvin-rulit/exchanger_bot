<template>
  <Alert ref="alertComponent" :message="alertMessage" :type="alertType" />

  <div
    v-if="isActive"
    @click="close"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >

    <div @click.stop class="modal-container">
      <!-- Заголовок -->
      <div class="modal-header">
        <div class="flex items-center">
          <h2 v-if="selectedOrder.status !== 'success'" class="text-lg font-semibold">Обработка заказа </h2>
          <h2 v-else class="text-lg font-semibold">Заказ </h2>
          <span class="ml-2">№{{ selectedOrder.id }}</span>

          <span :class="getStatusColor(selectedOrder.status )" class="text-base font-medium leading-none ml-2">{{ translateStatus(selectedOrder.status) }}</span>
        </div>

        <div v-if="selectedOrder.status !== 'success'" class="flex items-center cursor-pointer">
          <div class="relative group">
            <div @click="openChat" class="cursor-pointer sm:ms-6 sm:flex sm:items-center">
              <Icon icon="wpf:message-outline" width="26" height="26"/>
              <span class="text-xs rounded ml-3 hover:underline"> Отправить сообщение </span>
            </div>
          </div>
        </div>

        <button @click="close" class="close-btn" aria-label="Close modal">
          <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" />
        </button>
      </div>


      <!-- Контент -->
      <div class="modal-body">
        <div class="left-panel">
          <div v-if="selectedOrder.status !== 'success'" class="select_user">
            <label class="block text-sm font-medium text-gray-700 mb-1">Ответственный</label>
            <multiselect v-model="form.selectedUser" :options="users" placeholder="Назначить ответственного"
                         :close-on-select="true" :show-labels="false" label="name"></multiselect>
          </div>

          <!-- Блок информации о закрытом заказе -->
          <div v-else class="order-info">
            <h2 class="order-title">Информация о заказе</h2>

            <div class="info-item">
              <span class="label">Клиент:</span>
<!--              <span class="value">{{ selectedOrder.client_name }}</span>-->
              <span class="value">dfd</span>
            </div>

            <div class="info-item">
              <span class="label">Закрыл заказ:</span>
              <span class="value">{{ selectedOrder.closed_by }}</span>
            </div>

            <div class="info-item">
              <span class="label">Сумма заказа:</span>
              <span class="value">{{ selectedOrder.amount }} {{ selectedOrder.currency }}</span>
            </div>

            <div class="info-item">
              <span class="label">Дата закрытия:</span>
<!--              <span class="value">{{ formatDate(selectedOrder.closed_at) }}</span>-->
            </div>
          </div>

        </div>

        <div class="right-panel">
          <img v-if="selectedOrder.media[0]['original_url']" :src="selectedOrder.media[0]['original_url']" alt="Фото заказа" class="order-image">
          <div v-else class="no-image">Фото отсутствует</div>
        </div>
      </div>

      <!-- Фиксированный футер -->
      <div class="modal-footer">

        <transition name="fade">
          <div v-if="!confirmationOrder && selectedOrder.status !== 'success'">
            <div class="btn_save">
              <ButtonUI @click="store" type="submit" color="green">Сохранить</ButtonUI>
            </div>

            <div class="btn_complete">
              <ButtonUI @click="prepareCompleted" type="submit">Завершить</ButtonUI>
            </div>
          </div>
        </transition>

        <transition name="fade">
          <div v-if="confirmationOrder">
            <div class="content">
              <p class="message_complete">Вы уверены, что хотите завершить этот заказ?</p>
            </div>

            <div class="buttons">
              <ButtonUI @click="successCloseOrder" type="submit" color="green">Да</ButtonUI>
              <ButtonUI @click="cancelCloseOrder" type="submit" color="red">Отмена</ButtonUI>
            </div>
          </div>
        </transition>

      </div>
    </div>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import { UserService } from '@/services/UserService.js'
import { OrdersService } from '@/services/OrdersService.js'
import Alert from "../../../Components/Alert.vue";
import { Icon } from '@iconify/vue';
import ButtonUI from "../../../Components/ButtonUI.vue";

export default {
  components: {Multiselect, Alert, Icon, ButtonUI},
  props: {
    isActive: {
      type: Boolean,
      default: false
    },
    selectedUser: {
      type: Object,
      default: null,
    },
    selectedOrder: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      users: '',
      form: {
        selectedUser: null,
        selectedOrder: null,
      },
      statusTranslations: {
        new: 'Новый',
        active: 'Активный',
        success: 'Завершен',
        deleted: 'Удален',
      },
      errors: '',
      alertMessage: '',
      alertType: 'success',
      loading: false,
      confirmationOrder: false,
    }
  },
  mounted() {
    this.getUsers()
  },
  methods: {
    getUsers() {
      UserService.getUsers().then(response => {
        this.users = response.data.users
      })
    },
      store: async function (event) {
        event.preventDefault()
        this.errors = null
        if (!this.form.selectedUser || !this.form.selectedUser.id) {
          this.triggerErrorAlert('Выберите ответственного сотрудника!')
          return;
        }

        OrdersService.store(this.form)
          .then(response => {
            this.triggerSuccessAlert('Изменения сохранены');
          })
          .catch(error => {
            this.errors = error.response.data.message
          })
    },
    closeOrder: async function () {
        // event.preventDefault()
        this.errors = null
        OrdersService.close_order(this.form)
          .then(response => {
            this.selectedOrder.status  = response.data.order.status
            this.triggerSuccessAlert('Заказ успешно завершен');
          })
          .catch(error => {
            this.errors = error.response.data.message
          })
    },
    prepareCompleted() {
      this.confirmationOrder = true
    },
    successCloseOrder() {
      this.closeOrder()
      this.confirmationOrder = false
    },
    cancelCloseOrder() {
      this.confirmationOrder = false
    },
    openChat() {
      this.$emit('close', { openChat: true, orderId: this.selectedOrder.id});
    },
    translateStatus(status) {
      return this.statusTranslations[status] || status;
    },
    getStatusColor(status) {
      switch (status) {
        case 'new':
          return 'text-[#38b0b0]';
        case 'active':
          return 'text-[#F93827]';
        case 'success':
          return 'text-[#FFA500]';
        case 'deleted':
          return 'text-[#FF0000]';
        default:
          return 'text-black';
      }
    },
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
    close() {
      this.form.selectedUser = ''
      this.confirmationOrder = false
      this.$emit('close', { openChat: false, orderId: 0});
    },
  },
  watch: {
    selectedUser: {
      immediate: true,
      handler(newUser) {
        this.form.selectedUser = newUser;
      }
    },
    selectedOrder: {
      immediate: true,
      handler(newOrder) {
        this.form.selectedOrder = newOrder;
      }
    }
  },
};
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

<style lang="scss" scoped>
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
  margin-top: 50px;
  display: flex;
  gap: 20px;

    .select_user {
      display: flex;
      margin-top: 20px;
      margin-left: 6%;
      align-items: center;
      width: 70%;
      gap: 10px;
    }
}

.left-panel {
  flex: 1;
}

.right-panel {
  flex: 1;
  display: flex;
}

.order-image {
  max-width: 100%;
  max-height: 95%;
  border-radius: 8px;
  margin-right: 20%;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.no-image {
  text-align: center;
  color: #999;
  font-size: 14px;
}
.modal-footer {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  border-radius: 0 0 10px 10px;
}
.btn_save {
  position: absolute;
  left: 10%;
  bottom: 40px;
}
.btn_complete {
  position: absolute;
  left: 22%;
  bottom: 40px;
}
.btn_close_order {
  position: absolute;
  left: 10%;
  bottom: 30px;
}
.buttons {
  display: flex;
  position: absolute;
  left: 14%;
  bottom: 40px;
  gap: 10px;
}
.message_complete {
  position: absolute;
  left: 10%;
  font-size: 16px;
  font-weight: bold;
  bottom: 90px;
}
.close-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.9s ease;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
</style>
