<template>
  <div
    v-if="isActive"
    class="fixed inset-0 bg-black shadow-lg flex items-center justify-center z-50"
  >
    <!-- Модальное окно -->
    <div class="relative bg-white p-6 rounded-lg shadow-md w-80">
      <h2 class="text-xl font-semibold mb-4 text-center">Введите пароль</h2>
      <input
        type="password"
        v-model="password"
        placeholder="Пароль"
        class="w-full border border-gray-300 rounded px-3 py-2 mb-4 focus:outline-none focus:ring focus:border-blue-300"
        @keydown.enter="sendPassword"
      />

      <span class="text-xs text-center text-red-600">{{errors}}</span>

    </div>
  </div>
</template>

<script>
import { UserService } from '@/services/UserService.js'
import { useUserStore } from '@/stores/userStore.js'

export default {
  props: {
    isActive: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      password: '',
      errors: '',
    };
  },
  setup() {
    const userStore = useUserStore()
    return { userStore}
  },
  methods: {
    async sendPassword() {
      this.errors = ''

      if (!this.password.trim()) {
        return;
      }

      const user = await this.userStore.getCurrentUser(this.$page.props.auth.user.id)

      if (!user) {
        this.errors = 'Пользователь не найден'
        return
      }

      if (user.lock_password !== this.password){
        this.errors = 'Пароль не совпадает'
        return;
      }
      try {
        const response = await UserService.sendPasswordForUnlock(
          this.password,
        );
        if (response && response.status === 200) {
          this.$emit('unlocked');
          this.password = ''
        }

      } catch (error) {
        this.errors = 'Пароль не совпадает'
      }
    },
  }
};
</script>


<style scoped>
.fixed {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}
img {
  max-height: 97vh;
  object-fit: contain;
}
.shadow-lg {
  backdrop-filter: blur(9px);
  background-color: rgba(0, 0, 0, 0.9);
}
.flex {
    display: flex;
}
.items-center {
    align-items: center;
}
.justify-center {
    justify-content: center;
}
.z-50 {
    z-index: 50;
}
</style>
