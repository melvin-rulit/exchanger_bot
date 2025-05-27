<template>
  <transition name="slide-down">
    <div v-if="isActive" class="block">
      <div v-if="showInput" class="flex items-center">
        <hollow-dots-spinner
          :animation-duration="1000"
          :dot-size="15"
          :dots-num="1"
          color="#4caf50"
        />
        <TextInput @enter="sendPassword" v-model="password" class="h-8 text-sm px-2 py-1 ml-4" width-class="w-[400px]" placeholder="Введите пароль. И по завершению клавишу Enter"/>
        <Icon @click="closeAlert" icon="material-symbols-light:close-small-rounded" width="34" height="34" class="icon-error cursor-pointer"/>
      </div>
      <div v-if="successMessage" class="flex items-center">
        <Icon icon="mdi:success" width="26" height="26" class="icon-success" />
        <span class="icon-success">Пароль установлен. Можете воспользоваться функцией</span>
      </div>
    </div>
  </transition>
</template>

<script>
import TextInput from '@/Components/TextInput.vue'
import ButtonUI from '@/Components/ButtonUI.vue'
import { Icon } from '@iconify/vue'
import { UserService } from '@/services/UserService.js'
import { HollowDotsSpinner } from 'epic-spinners'
export default {
  components: { ButtonUI, TextInput, Icon, HollowDotsSpinner },
  props: {
    isActive: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      password: '',
      showInput: true,
      successMessage: false,
    };
  },
  methods: {
    async sendPassword() {
      if (!this.password.trim()) {
        return;
      }

      try {
        const response = await UserService.saveIsLockPassword(
          this.password,
        );
        if (response && response.status === 200) {
          this.showInput = false
          this.successMessage = true

          setTimeout(() => {
            this.successMessage = false
            this.$emit('closeModal');
          }, 2000);
        }

      } catch (error) {

      }
    },
    closeAlert() {
      this.$emit('closeModal');
    },
  }
}

</script>

<style>
.block {
  position: fixed;
  bottom: 35px;
  left: 37%;
}
.slide-down-enter-active, .slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter, .slide-down-leave-to {
  transform: translateX(100%);
  opacity: 0;
}
</style>
