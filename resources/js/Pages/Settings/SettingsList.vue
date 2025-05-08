<template>
  <div class="main h-[600px] relative">
    <div class="flex justify-center space-x-14 border-b mt-5 mb-4 tabs">
      <button
        v-for="tab in tabs"
        :key="tab"
        @click="activeTab = tab"
        :class="['pb-2 font-semibold', activeTab === tab ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500']">
        {{ tab }}
      </button>
    </div>

    <div class="tab-content p-10 rounded h-[calc(100%-4rem)] overflow-y-auto pb-24">
      <div v-if="activeTab === 'Личные данные'">
        <p>Форма редактирования профиля и пароля...</p>
      </div>

      <div v-else-if="activeTab === 'Шаблоны сообщений'">
        <ul>
          <li
            v-for="(template, index) in templates"
            :key="index"
            class="flex justify-between items-center border-b py-2 px-3 cursor-pointer hover:bg-gray-100">
            <span>{{ template.text }}</span>
            <button @click="removeTemplate(template.id)" class="text-red-500 hover:underline">Удалить</button>
          </li>
        </ul>
      </div>

      <div v-else-if="activeTab === 'Настройки уведомлений'">
        <p>Здесь настройки уведомлений и предпочтений.</p>
      </div>
    </div>

    <!-- Закрепленный блок ввода -->
    <div
      v-if="activeTab === 'Шаблоны сообщений'"
      class="absolute bottom-0 left-0 w-full bg-white p-4"
    >
      <div class="flex w-full rounded overflow-hidden border">
        <input
          v-model="newTemplate"
          type="text"
          placeholder="Введите новый шаблон сообщения"
          class="border p-2 w-full rounded-l-md custom-input"
        />
        <button
          @click="addTemplate"
          class="bg-blue-500 text-white p-2 rounded-r-md"
        >
          Добавить
        </button>
      </div>
    </div>
  </div>
</template>


<script>
import { TemplateService } from '@/services/TemplateMessagesService.js'
import { handleApiError } from '@/helpers/errors.js'

export default {
  data() {
    return {
      tabs: ['Личные данные', 'Шаблоны сообщений', 'Настройки уведомлений'],
      activeTab: 'Личные данные',
      newTemplate: '',
      templates: [],
      alertMessage: '',
      alertType: 'success',
      errors: '',
    };
  },
  mounted() {
    this.getTemplatesMessages()
  },
  methods: {
    async getTemplatesMessages() {
      try {
        const response = await TemplateService.getTemplateMessages();
        this.templates = Array.isArray(response.data.template.messages) ? response.data.template.messages: [];
      } catch (error) {
        this.templates = [];
        if (error.response && error.response.data) {this.errors = handleApiError(error)}
      }
    },
    async addTemplate() {
      if (!this.newTemplate.trim()) {
        return;
      }
      try {
        const response = await TemplateService.storeNewTemplate(this.newTemplate);
        this.templates.push(response.data.template.messages);
      } catch (error) {
        if (error.response && error.response.data) {this.errors = handleApiError(error)}
      }finally {
        this.newTemplate = '';
      }
    },
    async removeTemplate($templateId) {
      try {
        const response = await TemplateService.delete($templateId);
        this.templates = Array.isArray(response.data.template.messages) ? response.data.template.messages: [];
      } catch (error) {
        if (error.response && error.response.data) {this.errors = handleApiError(error)}
      }
    },
  }
}
</script>

<style scoped>
.main {
  min-height: 93vh;
  display: flex;
  flex-direction: column;
  background-color: white;
}

/* Поле ввода сообщения */
input {
  border: 1px solid #ddd;
}
.custom-input {
  color: black;
  background-color: white;
  -webkit-text-fill-color: initial;
}
input:focus {
  outline: none;
  box-shadow: none;
  border-color: inherit;
}

.settings-wrapper {
  width: 100%;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
  padding-bottom: 0.5rem;
}

.setting-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}

.setting-row label {
  flex: 1;
  font-weight: 500;
}

.setting-input {
  flex: 2;
  padding: 0.5rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  min-width: 250px;
}

.btn {
  padding: 0.5rem 1rem;
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.btn:hover {
  background-color: #2563eb;
}
</style>
