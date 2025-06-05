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
      <div class="flex flex-col" v-if="activeTab === 'Личные данные'">

        <div class="flex items-center gap-2">
          <div class="justify-center w-full pt-6 bg-white mt-8">
            <div class="flex justify-center flex-wrap gap-20">

              <div class="w-[336px] h-[140px]">
                {{}}
                <div class="h-full rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-white p-5 flex flex-col border-t">
                  <h6 class="text-sm font-semibold text-gray-500 uppercase mb-2 mx-auto">Ваша фотография в системе</h6>
                  <img v-if="userStore.currentUser.image_url" @click="triggerFileInput" :src="userStore.currentUser.image_url" class="w-20 mt-3 mx-auto cursor-pointer"  alt=""/>
                  <img v-else @click="triggerFileInput" src="/Images/User/no_avatar.svg" alt="Аватар по умолчанию" class="w-20 mx-auto cursor-pointer">

                  <file-input
                    ref="fileInputRef"
                    v-model="form.photo_path"
                    :error="errors"
                    type="file"
                    accept="image/*"
                    @change="onFileChange"
                  />
                </div>
              </div>


              <div class="w-[336px] h-[140px]">
                <div class="h-full rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-white p-5 flex flex-col border-t">
                  <h6 class="text-sm font-semibold text-gray-500 uppercase mb-2 mx-auto">Ваше имя в системе</h6>
                  <div class="flex items-center gap-2 mt-3 flex-col">
                    <span v-if="editableUserId !== userStore.currentUser.id" @click="enableEdit(userStore.currentUser, 'userType')" class="px-5 py-2 bg-gray-100 text-gray-800 rounded-md shadow-md cursor-pointer w-[200px]">{{ userStore.currentUser.name }}</span>
                    <div  v-if="editableUserId === userStore.currentUser.id" class="flex items-center gap-2">
                      <div>
                        <hollow-dots-spinner
                          :animation-duration="1000"
                          :dot-size="15"
                          :dots-num="1"
                          color="#4caf50"
                        />
                      </div>
                      <TextInput @enter="updateUser(form)" v-model="form.editableUserName" class="text-xl" width-class="w-[180px]"/>
                      <button @click.stop="closeUpdateInput('userType')" class="close-btn h-8 w-8 flex items-center justify-center">
                        <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" class="icon-error"/>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="w-[336px] h-[140px]">
                <div class="h-full rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-white p-5 border-t">
                  <h6 class="text-sm font-semibold text-gray-500 uppercase mb-2 mx-auto">Ваше имя в системе:</h6>
                  <p class="text-gray-800 text-base">Еще одна карточка</p>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <div v-else-if="activeTab === 'Шаблоны сообщений'">
        <ul v-if="templates.length">
          <li
            v-for="(template, index) in templates"
            :key="index"
            class="flex justify-between items-center py-2 px-3 cursor-pointer hover:bg-gray-100">
            <span v-if="editableTemplateId !== template.id" @click="enableEdit(template, 'templateType')" class="px-4 bg-gray-100 text-gray-800 rounded-md shadow-md cursor-pointer">{{ template.text }}</span>
            <div  v-if="editableTemplateId === template.id" class="flex items-center gap-2">
              <div class="" >
                <hollow-dots-spinner
                  :animation-duration="1000"
                  :dot-size="15"
                  :dots-num="1"
                  color="#4caf50"
                />
              </div>
              <TextInput @enter="updateTemplate(template)" v-model="editableTemplateName" class="h-8 text-sm px-2 py-1" width-class="w-[1680px]"/>
              <button @click.stop="closeUpdateInput('templateType')" class="close-btn h-8 w-8 flex items-center justify-center">
                <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" class="icon-error"/>
              </button>
            </div>

            <span
              v-if="editableTemplateId !== template.id"
              class="flex items-center space-x-1 cursor-pointer text-gray-800"
              @click="removeTemplate(template.id)"
              @mouseenter="hoveredTemplateId = template.id"
              @mouseleave="hoveredTemplateId = null">
              <Icon :icon="hoveredTemplateId === template.id ? 'fluent-mdl2:remove-from-trash' : 'cil:trash'" width="25" height="25"/>
              <button class="text-red-500">Удалить</button>
            </span>


          </li>
        </ul>
        <div v-else class="no-messages">Нет ни одного шаблона</div>
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
          @keydown.enter="addTemplate"
          type="text"
          placeholder="Введите новый шаблон сообщения"
          class="border p-2 w-full rounded-l-md custom-input"
        />
        <button
          @click="addTemplate"
          class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-r-md">
          Добавить
        </button>
      </div>
    </div>
  </div>
</template>


<script>
import { TemplateService } from '@/services/TemplateMessagesService.js'
import { UserService } from '@/services/UserService.js'
import { handleApiError } from '@/helpers/errors.js'
import { Icon } from '@iconify/vue'
import { HollowDotsSpinner } from 'epic-spinners'
import { useUserStore } from '@/stores/userStore.js'
import TextInput from '@/Components/Input/TextInput.vue'
import FileInput from '@/Components/Input/FileInput.vue'
import { resizeImage } from '@/utils/imageResizer.js'

export default {
  components: { FileInput, TextInput, UserService, Icon, HollowDotsSpinner },
  data() {
    return {
      tabs: ['Личные данные', 'Шаблоны сообщений', 'Настройки уведомлений'],
      activeTab: 'Личные данные',
      newTemplate: '',
      templates: [],
      form: {
        editableUserName: '',
        photo_path: null,
        previewUrl: null,
      },
      alertMessage: '',
      alertType: 'success',
      hoveredTemplateId: null,
      editableUserId: null,
      editableTemplateId: null,
      editableTemplateName: '',
      errors: '',
    };
  },
  setup() {
    const userStore = useUserStore()
    return { userStore }
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
        this.errors = handleApiError(error)
      }
    },
    enableEdit(object, type) {
      switch (type) {
        case 'templateType':
          this.editableTemplateName = object.text
          this.editableTemplateId = object.id
          return
        case 'userType':
          this.form.editableUserName = object.name
          this.editableUserId = object.id
          return
      }
    },
    closeUpdateInput(type) {
      switch (type) {
        case 'templateType':
          this.editableTemplateId = null
          return
        case 'userType':
          this.editableUserId = null
          return
      }
    },
    async updateUser(userForm) {
      try {
        const response = await UserService.updateUser(this.editableUserId, userForm);
        this.editableUserId = null
        const updated = response.data.data;

        if (Array.isArray(updated.role)) {
          updated.role = updated.role[0] ?? '';
        }
        this.userStore.setCurrentUser(updated)
      } catch (error) {
        this.templates = [];
        this.errors = handleApiError(error)
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
    async updateTemplate(template) {
      await TemplateService.updateTemplateMessage(template.id, this.editableTemplateName)
      this.editableTemplateId = null
      await this.getTemplatesMessages()
    },
    async removeTemplate($templateId) {
      try {
        const response = await TemplateService.delete($templateId);
        this.templates = Array.isArray(response.data.template.messages) ? response.data.template.messages: [];
      } catch (error) {
        if (error.response && error.response.data) {this.errors = handleApiError(error)}
      }
    },
    triggerFileInput() {
      this.$refs.fileInputRef.browse()
    },

    async onFileChange(file) {
      if (!file || !file.type?.startsWith('image/')) return

      this.form.previewUrl = URL.createObjectURL(file)

      try {
        this.form.photo_path = await resizeImage(file)
        await this.savePhoto()
      } catch (error) {
        this.errors = handleApiError(error)
      }
    },
    async savePhoto() {
      try {
        const response = await UserService.sendPhoto(this.form.photo_path);
        const updated = response.data.data;

        if (Array.isArray(updated.role)) {
          updated.role = updated.role[0] ?? '';
        }
        this.userStore.setCurrentUser(updated)
        this.form.photo_path = null;
      } catch (error) {
        this.errors = handleApiError(error)
      }
    },
  },
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
.icon-error {
  color: #f44336;
}
.setting-row label {
  flex: 1;
  font-weight: 500;
}
.no-messages {
  text-align: center;
  padding: 20px;
  font-size: 16px;
  font-weight: bold;
  color: #888;
}
</style>
