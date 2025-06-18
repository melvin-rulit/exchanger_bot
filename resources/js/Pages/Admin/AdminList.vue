<template>
  <div class="main">
    <div class="table-container">
      <table class="w-full">
        <thead class="head_table">

        <tr tabindex="0" class="focus:outline-none h-10 rounded sticky top-0 bg-white">
          <td>
            <div class="flex pl-6">
              <p class="font-semibold mr-2">№</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-3">Имя</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-2">@mail</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-2">Пароль</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-2">Роль</p>
            </div>
          </td>
          <td class="pl-3">
            <p class="font-semibold ml-2">Дата регистрации</p>
          </td>
          <td>
            <div class="flex">
              <p class="font-semibold ml-2">Статус</p>
            </div>
          </td>
          <td>
            <div class="flex">
              <!-- Кнопки действий сюда можно -->
            </div>
          </td>
        </tr>
        </thead>

        <tbody>
        <tr v-for="(manager, index) in managers" :key="manager.id" class="h-10 border-b">
          <td class="pl-6">
            {{ index + 1 }}
          </td>
          <td class="pl-1">
            {{ manager.name }}
          </td>
          <td>
            {{ manager.email }}
          </td>
          <td class="pl-4">
            **** <!-- обычно пароль не показывают -->
          </td>
          <td>
            {{ Array.isArray(manager.role) ? manager.role[0] : manager.role }}
          </td>
          <td style="padding-left: 45px;">
            {{ new Date(manager.created_at).toLocaleDateString() }}
          </td>
          <td style="padding-left: 10px;" class="text-sm text-gray-700">
            {{ manager.enabled === 1 ? 'Активен' : 'Отключен' }}
          </td>

        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import { UserService } from '@/services/UserService.js'

export default defineComponent({
  name: 'AdminList',
  data() {
    return {
      managers: '',
    }
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
  }
})
</script>

<style scoped>
.main {
  min-height: 94vh;
  display: flex;
  flex-direction: column;
}
.table-container {
  flex-grow: 1;
  overflow-y: auto;
  max-height: calc(94vh - 89px);
  background-color: white;
}
</style>
