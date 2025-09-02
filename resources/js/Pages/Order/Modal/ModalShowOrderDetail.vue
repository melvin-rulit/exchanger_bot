<template>
  <Alert ref="alertComponent" :message="alertMessage" :type="alertType" />

  <div
    v-if="isActive"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div @click.stop class="modal-container">

      <div class="modal-header">
        <div class="flex items-center">
          <h2 v-if="selectedOrder.status !== 'success'" class="text-lg font-semibold">Обработка заказа </h2>
          <h2 v-else class="text-lg font-semibold">Заказ </h2>
          <span class="ml-2">№{{ selectedOrder.id }}</span>

          <span :class="getStatusColor(selectedOrder.status )" class="text-base font-medium leading-none ml-2">{{ translateStatus(selectedOrder.status) }}</span>
        </div>

        <div v-if="selectedOrder.status !== 'success' && selectedOrder.status !== 'closed'" class="flex items-center cursor-pointer space-x-4">
          <div @click="openChat" class="cursor-pointer flex items-center">
            <Icon icon="wpf:message-outline" width="26" height="26"/>
            <span class="text-xs rounded ml-2 hover:underline">Отправить сообщение</span>
          </div>

          <div @click="fixTheOrder(selectedOrder.id)">
            <div v-if="!selectedOrder.is_pinned" class="cursor-pointer flex items-center">
              <Icon icon="bi:pin-angle" width="26" height="26" class="cursor-pointer"/>
              <span class="text-xs rounded ml-1 hover:underline">Закрепить заказ</span>
            </div>
            <div v-else class="cursor-pointer flex items-center">
              <Icon icon="bi:pin-angle-fill" width="26" height="26" class="cursor-pointer"/>
              <span class="text-xs rounded ml-1 hover:underline">Заказ закреплен</span>
            </div>
          </div>
        </div>

        <button @click="close" class="close-btn" aria-label="Close modal">
          <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" />
        </button>
      </div>

      <div class="modal-body">
        <div class="left-panel">
          <div v-if="selectedOrder.status !== 'success' && selectedOrder.status !== 'closed'" class="select_user">
            <label class="text-sm font-medium text-gray-700 mb-1">Ответственный</label>

            <multiselect
              v-model="form.selectedUser"
              :options="managers"
              placeholder="список менеджеров"
              :close-on-select="true"
              :show-labels="false"
              :searchable="false"
              :append-to-body="false"
              label="name" />
            <Icon v-if="!form.selectedUser" icon="pajamas:warning" class="animate-fade-bounce ml-3 icon-danger" width="36" height="36" />
          </div>


          <div v-if="selectedOrder.status === 'success' && selectedOrder.status !== 'closed'" class="order-info-wrapper">
            <div class="order-info border-t">
              <h1 class="order-title">Информация о заказе</h1>

              <div class="info-item mb-2">
                <span class="label">Клиент: </span>
                <span class="px-4 bg-gray-50 rounded-md shadow-md ml-2">{{ selectedOrder.client.first_name }}</span>
              </div>

              <div class="info-item mb-2">
                <span class="label">Завершил заказ: </span>
                <span class="px-4 bg-gray-50 rounded-md shadow-md ml-2">{{ selectedOrder.user?.name || '' }}</span>
              </div>

              <div class="info-item mb-2">
                <span class="label">Сумма заказа: </span>
                <span class="px-4 bg-gray-50 rounded-md shadow-md ml-2">{{ selectedOrder.amount }} {{ selectedOrder.currency_name }}</span>
              </div>

              <div class="info-item">
                <span class="label">Дата завершения:</span>
                <span class="px-4 bg-gray-50 rounded-md shadow-md ml-2">{{ formatDateTime(selectedOrder.end_at) }}</span>
              </div>
            </div>
          </div>

          <div v-if="selectedOrder.status !== 'success' && selectedOrder.status === 'closed'" class="order-info-wrapper">
            <div class="order-info border-t">
              <h1 class="order-title">Информация о заказе</h1>

              <div class="info-item mb-2">
                <span class="label">Клиент: </span>
                <span class="px-4 bg-gray-50 rounded-md shadow-md ml-2">{{ selectedOrder.client.first_name }}</span>
              </div>

              <div class="info-item mb-2">
                <span class="label">Отменил заказ: </span>
                <span class="px-4 bg-gray-50 rounded-md shadow-md ml-2">{{ selectedOrder.client.first_name }}</span>
              </div>

              <div class="info-item mb-2">
                <span class="label">Сумма заказа: </span>
                <span class="px-4 bg-gray-50 rounded-md shadow-md ml-2">{{ selectedOrder.amount }} {{ selectedOrder.currency_name }}</span>
              </div>

              <div class="info-item mb-2">
                <span class="label">Дата отмены:</span>
                <span class="px-4 bg-gray-50 rounded-md shadow-md ml-2">{{ formatDateTime(selectedOrder.close_at) }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="right-panel">
          <img  v-if="selectedOrder?.image_url && selectedOrder.status !== 'closed'" @click="showModalScreenshot(selectedOrder.image_url)" :src="selectedOrder.image_url" alt="Фото заказа" class="order-image">
          <div v-else-if="selectedOrder.status !== 'closed'" class="no-image">Чек отсутствует</div>
        </div>
      </div>

      <!-- Фиксированный футер -->
      <div class="modal-footer">
        <div class="spinner-wrapper" v-if="showSpinner">
          <hollow-dots-spinner
            :animation-duration="1000"
            :dot-size="20"
            :dots-num="3"
            color="#ff1d5e"
          />
        </div>

        <FadeOrInstant :disable-transition="checkCloseOrder" name="fade">
          <div v-if="!confirmationOrder && !confirmationCloseOrder && selectedOrder.status !== 'success' && !checkCloseOrder && selectedOrder.status !== 'closed' && form.selectedUser">

            <div v-if="form.selectedUser && form.selectedUser.id !== $page.props.auth.user.id || selectedOrder.status === 'new'" class="spiner_wait">
              <hollow-dots-spinner
                :animation-duration="1000"
                :dot-size="15"
                :dots-num="1"
                color="#4caf50"
              />
            </div>
              <div class="btn_assign">
                <div v-if="selectedOrder.status !== 'new' && form.selectedUser && form.selectedUser.id === $page.props.auth.user.id" class="mb-1">
                  <InfoNotification message="Вы уже ответственный" type="success" :animateIcon="false" />
                </div>
                <ButtonUI v-if="selectedOrder.status === 'new'" @click="assignExecutor" type="submit" color="green">Закрепить менеджера</ButtonUI>
                <ButtonUI v-if="selectedOrder.status !== 'new' && form.selectedUser && form.selectedUser.id !== $page.props.auth.user.id" @click="assignExecutor" type="submit" color="green">Передать заказ другому</ButtonUI>
              </div>

            <div class="btn_success">
              <ButtonUI @click="prepareEnd" type="submit" :isDisabled="selectedOrder.status !== 'active'">Завершить заказ</ButtonUI>
            </div>
            <div class="btn_close pl-3">
              <ButtonUI @click="prepareClosed" type="submit" color="red" :isDisabled="selectedOrder.status !== 'active'">Отменить заказ</ButtonUI>
            </div>
          </div>

          <div v-if="!confirmationOrder && !form.selectedUser && selectedOrder.status !== 'closed' && selectedOrder.status !== 'success'" class="mb-7 border">
            <InfoNotification message="Чтобы начать обработку заказа назначьте менеджера" type="danger"/>
          </div>
        </FadeOrInstant>


          <div v-if="confirmationOrder">
            <div class="content">
              <p class="message_complete">Вы уверены, что хотите завершить этот заказ?</p>
            </div>

            <div class="buttons">
              <ButtonUI @click="successEndOrder" type="submit" color="green">Да</ButtonUI>
              <ButtonUI @click="cancelEndOrder" type="submit" color="red">Отмена</ButtonUI>
            </div>
          </div>
        <div v-if="confirmationCloseOrder">
            <div class="content">
              <p class="message_complete">Вы уверены, что хотите отменить этот заказ?</p>
            </div>

            <div class="buttons">
              <ButtonUI @click="successCloseOrder" type="submit" color="green">Да</ButtonUI>
              <ButtonUI @click="cancelCloseOrder" type="submit" color="red">Отмена</ButtonUI>
            </div>
          </div>

      </div>
    </div>

    <ModalShowOrderScreenshot
      :is-active="isModalShow"
      :currentImageUrl="currentImageUrl"
      :clientName="clientName"
      @close="closeModalShowOrderScreenshot"
    />

  </div>
</template>

<script>
import { handleApiError } from '@/helpers/errors.js'
import { getStatusColorClass } from '@/helpers/statusColorClass.js'
import { translateStatus } from '@/helpers/statusTranslationClass.js'
import Multiselect from 'vue-multiselect'
import { UserService } from '@/services/UserService.js'
import { OrdersService } from '@/services/OrdersService.js'
import { useOrdersStore } from '@/stores/ordersStore'
import FadeOrInstant from "../../../Components/FadeOrInstant.vue";
import Alert from "../../../Components/Notifications/Alert.vue";
import { Icon } from '@iconify/vue';
import ButtonUI from "../../../Components/Button/ButtonUI.vue";
import { HollowDotsSpinner } from 'epic-spinners'
import ModalShowOrderScreenshot from '@/Pages/Order/Modal/ModalShowOrderScreenshot.vue'
import InfoNotification from '@/Components/Notifications/InfoNotification.vue'
import { REMINDER_TIMEOUT_MS } from '@/helpers/constants.js'

export default {
  components: {InfoNotification, ModalShowOrderScreenshot, Multiselect, FadeOrInstant, Alert, Icon, ButtonUI, HollowDotsSpinner},
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
    clientName: {
      required: true
    },
  },
  data() {
    return {
      managers: '',
      clientName: '',
      form: {
        selectedUser: null,
        selectedOrder: null,
      },
      localOrder: { ...this.selectedOrder },
      showSpinner: false,
      errors: {},
      alertMessage: '',
      alertType: 'success',
      loading: false,
      confirmationOrder: false,
      confirmationCloseOrder: false,
      checkCloseOrder: false,
      isClosing: false,
      isModalShow: false,
      currentImageUrl: '',
    }
  },
  setup() {
    const ordersStore = useOrdersStore()
    return { ordersStore, translateStatus}
  },
  mounted() {
    this.getManagers()
  },
  methods: {
    getManagers() {
      UserService.getManagers().then(response => {
        this.managers = response.data.data
      })
    },
    assignExecutor: async function () {
      this.errors = null

      if (!this.form.selectedUser || !this.form.selectedUser.id) {
        this.triggerErrorAlert('Назначьте ответственного менеджера!')
        return;
      }

      if (this.selectedOrder.user?.id === this.$page.props.auth.user.id && this.form.selectedUser.id === this.$page.props.auth.user.id) {
        this.triggerSuccessAlert('Этот заказ уже закреплен за вами!')
        return;
      }

      this.updateStatus('active');

      OrdersService.assignExecutor(this.form)
        .then(response => {
          this.localOrder.user = response.data.assigned_user.user;
          this.ordersStore.markAsReadNewOrder(this.selectedOrder.id)

          if (this.$page.props.auth.user.id !== response.data.assigned_user.user.id) {
            this.$emit('executorChanged', this.selectedOrder.id);
            //eventBus.emit('order-assigned-to-other', this.selectedOrder.id)
            //this.unPinChat()
            //this.close();
          }
        })
        .catch(error => {
          this.errors = handleApiError(error)
        }).finally(() => {
        if (this.selectedOrder.status === 'new') {
          this.triggerSuccessAlert('Менеджер успешно закреплен ');
        }else if (this.selectedOrder.status === 'active' && this.form.selectedUser.id !== this.$page.props.auth.user.id) {
          this.triggerSuccessAlert('Заказ передается другому менеджеру');
        }
      });
    },
    unPinChat(pinedChatId) {
      UserService.unPinChat(pinedChatId)
        .then(response => {
          if (response.data.data) {
            this.pinedChat = response.data.data;
            this.getPinedChat()
          }
        })
        .catch(error => {
          this.errors = handleApiError(error)
        })
    },
    updateStatus($status) {
      OrdersService.updateStatus(this.selectedOrder.id, $status)
        .then(response => {
          this.selectedOrder.status  = response.data.order.status
        })
        .catch(error => {
          this.errors = handleApiError(error)
        })
    },
    endOrder: async function () {
      if (this.isClosing) return;
      this.isClosing = true;
      this.errors = {};

      OrdersService.end_order(this.form)
        .then(response => {
          Object.assign(this.selectedOrder, response.data.update.order);
          this.triggerSuccessAlert('Заказ успешно завершен');
        })
        .catch(error => {
          this.errors = handleApiError(error)
        })
        .finally(() => {
          this.showSpinner = false
          this.isClosing = false;
          if (this.errors.success === false){
            this.checkCloseOrder = false
            this.triggerErrorAlert('Невозможно завершить заказ. Обратитесь к администратору')
          }else {
            this.updateStatus('success');
          }
        });
    },
    closeOrder: async function () {
      if (this.isClosing) return;
      this.isClosing = true;
      this.errors = {};

      OrdersService.close_order(this.form)
        .then(response => {
          Object.assign(this.selectedOrder, response.data.update.order);
          this.triggerSuccessAlert('Заказ успешно отменен');
        })
        .catch(error => {
          this.errors = handleApiError(error)
        })
        .finally(() => {
          this.showSpinner = false
          this.isClosing = false;
          if (this.errors.success === false){
            this.checkCloseOrder = false
            this.triggerErrorAlert('Невозможно отменить заказ. Обратитесь к администратору')
          }
        });
    },
    prepareEnd() {
      // if (!this.form.selectedUser || !this.form.selectedUser.id) {
      //   this.triggerErrorAlert('Назначьте ответственного менеджера')
      //   return;
      // }
      // if (!this.form.selectedUser || !this.form.selectedUser.id || this.selectedOrder.status === 'new') {
      //   this.triggerErrorAlert('Менеджер еще не назначен!')
      //   return;
      // }
      this.confirmationOrder = true
    },
    prepareClosed() {
      // if (!this.form.selectedUser || !this.form.selectedUser.id) {
      //   this.triggerErrorAlert('Назначьте ответственного менеджера')
      //   return;
      // }
      // if (!this.form.selectedUser || !this.form.selectedUser.id || this.selectedOrder.status === 'new') {
      //   this.triggerErrorAlert('Менеджер еще не назначен!')
      //   return;
      // }
      this.confirmationCloseOrder = true
    },
    successEndOrder() {
      this.endOrder()
      this.confirmationOrder = false
      this.checkCloseOrder = true
      this.showSpinner = true
      this.$emit('successEndOrder');
    },
    successCloseOrder() {
      this.closeOrder()
      this.confirmationCloseOrder = false
      this.checkCloseOrder = true
      this.showSpinner = true
      this.$emit('successCloseOrder');
    },
    cancelEndOrder() {
      this.confirmationOrder = false
    },
    cancelCloseOrder() {
      this.confirmationCloseOrder = false
    },
    openChat() {
      //console.log(this.localOrder.status)
      if (String(this.selectedOrder.status).trim() === 'new') {
      //if (!this.form.selectedUser) {
      //if (!this.selectedOrder.user && !this.form.selectedUser) {
        this.triggerErrorAlert('Назначьте ответственного менеджера')
        return;
      }

      this.$emit('close', { openChat: true, order: this.selectedOrder, orderId: this.selectedOrder.id});
    },
    fixTheOrder($orderId) {
      if (!this.form.selectedUser || !this.localOrder.user) {
        this.triggerErrorAlert('Назначьте ответственного менеджера!')
        return;
      }

      OrdersService.fix_order($orderId)
        .then(response => {
          if (response.data.order.fixed) {
            this.selectedOrder.is_pinned = response.data.order.fixed.is_pinned;
          } else {
            this.selectedOrder.is_pinned = !this.selectedOrder.is_pinned;
          }
        })
        .catch(error => {
          this.errors = handleApiError(error)
        })
    },
    getStatusColor(status) {
      return getStatusColorClass(status)
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
    close() {
      this.form.selectedUser = ''
      this.confirmationOrder = false
      this.checkCloseOrder = false
      this.$emit('close', { openChat: false, orderId: 0});
    },
    showModalScreenshot(image) {
      this.currentImageUrl = image
      this.isModalShow = true
    },
    closeModalShowOrderScreenshot() {
      this.isModalShow = false
    },
  },
  watch: {
    isActive: {
      immediate: true,
      handler(newVal) {
        if (newVal && this.selectedUser) {
          this.form.selectedUser = this.selectedUser
        }
      }
    },
    // selectedUser: {
    //   immediate: true,
    //   handler(newUser) {
    //     if (this.isActive && newUser) {
    //       this.form.selectedUser = newUser
    //     }
    //   }
    // },
    selectedOrder: {
      handler(newOrder) {
        this.form.selectedOrder = newOrder ? { ...newOrder } : null
      },
      immediate: true
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
  justify-content: end;
  align-items: center;
  max-height: 60vh;
  overflow: hidden;
}
.order-info-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 50vh;
}
.order-info {
  background: white;
  padding: 60px;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  text-align: center;

  .order-title {
    margin-bottom: 20px;
    font-weight: bold;
    font-size: 20px;
  }
}
.order-image {
  max-width: 100%;
  max-height: 100%;
  width: auto;
  height: auto;
  object-fit: contain;
  border-radius: 8px;
  margin-right: 20%;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  cursor: pointer;
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
.btn_assign {
  position: absolute;
  left: 4%;
  bottom: 40px;
}
.btn_success {
  position: absolute;
  left: 23%;
  bottom: 40px;
}
.btn_close {
  position: absolute;
  left: 38%;
  bottom: 40px;
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
.spiner_wait {
  position: absolute;
  left: 4%;
  bottom: 53px;
}
.spinner-wrapper {
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  left: 18%;
  bottom: 70px;
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
