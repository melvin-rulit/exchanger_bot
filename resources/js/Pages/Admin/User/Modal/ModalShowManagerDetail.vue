<template>

  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div @click.stop class="modal-container">

      <div class="modal-header">
        <div class="flex items-center">

          <h2 class="text-lg font-semibold">Редактирование пользователя </h2>
          <span v-if="selectedManager" class="ml-3 mt-1 uppercase">{{ selectedManager.name }}</span>

        </div>

        <div class="flex items-center cursor-pointer space-x-4">
          <div  class="cursor-pointer flex items-center">
            <span
              v-if="!confirmationUserDelete"
              class="flex items-center space-x-1 cursor-pointer text-gray-800"
              @click="prepareCompleted()"
              @mouseenter="deleteUserId = selectedManager.id"
              @mouseleave="deleteUserId = null">
                 <Icon
                   :icon="deleteUserId === selectedManager.id ? 'fluent-mdl2:remove-from-trash' : 'cil:trash'"
                   width="25"
                   height="25"
                 />
              <button class="text-red-500">Удалить пользователя</button>
            </span>

            <div v-if="confirmationUserDelete" class="flex items-center gap-4">
              <hollow-dots-spinner
                :animation-duration="1000"
                :dot-size="15"
                :dots-num="1"
                color="#4caf50"
              />
              <p class="whitespace-nowrap">Вы уверены, что хотите удалить пользователя?</p>

              <div class="flex gap-2">
                <ButtonUI @click="successDeleteUser(selectedManager.id)" type="submit" color="green" padding="5px 40px">Да</ButtonUI>
                <ButtonUI @click="cancelDeleteUser" type="submit" color="red" padding="5px 40px">Отмена</ButtonUI>
              </div>
            </div>

          </div>
        </div>

        <button @click="close" class="close-btn" aria-label="Close modal">
          <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" />
        </button>
      </div>

      <div class="modal-body">
        <div class="flex items-center gap-2">
          <div class="justify-center w-full pt-6 bg-white mt-8">
            <div class="flex justify-center flex-wrap gap-20">

              <div class="w-[336px] h-[100px]">
                <div class="h-full rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-white p-5 flex flex-col border-t">
                  <h6 class="text-sm font-semibold text-gray-500 uppercase mx-auto">Имя</h6>
                  <div class="flex items-center gap-2 mt-1 flex-col">
                    <span v-if="editableUserId !== selectedManager.id || editableField !== 'name'" @click="enableEdit(selectedManager, 'name')" class="px-3 py-1 bg-gray-100 text-gray-800 rounded-md shadow-md cursor-pointer w-[200px]">{{ selectedManager.name }}</span>
                    <div  v-if="editableUserId === selectedManager.id && editableField === 'name'" class="flex items-center gap-2">
                      <div>
                        <hollow-dots-spinner
                          :animation-duration="1000"
                          :dot-size="15"
                          :dots-num="1"
                          color="#4caf50"
                        />
                      </div>
                      <TextInput @enter="updateUser('name')" v-model="form.editableUserName" class="text-xl" width-class="w-[180px]"/>
                      <button @click.stop="closeUpdateInput('userType')" class="close-btn h-8 w-8 flex items-center justify-center">
                        <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" class="icon-error"/>
                      </button>
                    </div>
                  </div>
                </div>
              </div>


              <div class="w-[336px] h-[100px]">
                <div class="h-full rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-white p-5 flex flex-col border-t">
                  <h6 class="text-sm font-semibold text-gray-500 uppercase mx-auto">Почта</h6>
                  <div class="flex items-center gap-2 mt-1 flex-col">
                    <span v-if="editableUserId !== selectedManager.id || editableField !== 'email'" @click="enableEdit(selectedManager, 'email')" class="px-3 py-1 bg-gray-100 text-gray-800 rounded-md shadow-md cursor-pointer w-[200px]">{{ selectedManager.email }}</span>
                    <div  v-if="editableUserId === selectedManager.id && editableField === 'email'" class="flex items-center gap-2">
                      <div>
                        <hollow-dots-spinner
                          :animation-duration="1000"
                          :dot-size="15"
                          :dots-num="1"
                          color="#4caf50"
                        />
                      </div>
                      <TextInput @enter="updateUser('email')" v-model="form.editableUserEmail" class="text-xl" width-class="w-[180px]"/>
                      <button @click.stop="closeUpdateInput('userType')" class="close-btn h-8 w-8 flex items-center justify-center">
                        <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" class="icon-error"/>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="w-[336px] h-[100px]">
                <div class="h-full rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-white p-5 flex flex-col border-t">
                  <h6 class="text-sm font-semibold text-gray-500 uppercase mx-auto">Пароль</h6>
                  <div class="flex items-center gap-2 mt-1 flex-col">
                    <span v-if="editableUserId !== selectedManager.id || editableField !== 'password_show'" @click="enableEdit(selectedManager, 'password_show')" class="px-3 py-1 bg-gray-100 text-gray-800 rounded-md shadow-md cursor-pointer w-[200px]">{{ selectedManager.password_show }}</span>
                    <div  v-if="editableUserId === selectedManager.id && editableField === 'password_show'" class="flex items-center gap-2">
                      <div>
                        <hollow-dots-spinner
                          :animation-duration="1000"
                          :dot-size="15"
                          :dots-num="1"
                          color="#4caf50"
                        />
                      </div>
                      <TextInput @enter="updateUser('password_show')" v-model="form.editableUserPassword" class="text-xl" width-class="w-[180px]"/>
                      <button @click.stop="closeUpdateInput('userType')" class="close-btn h-8 w-8 flex items-center justify-center">
                        <Icon icon="material-symbols-light:close-small-rounded" width="34" height="34" class="icon-error"/>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="flex items-center gap-2">
          <div class="justify-center w-full pt-6 bg-white mt-8">
            <div class="flex justify-center flex-wrap gap-20">

              <div class="w-[336px] h-[100px]">
                <div class="h-full rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-white p-5 flex flex-col border-t">
                  <h6 class="text-sm font-semibold text-gray-500 uppercase mx-auto">Роль</h6>
                  <div class="flex items-center gap-2 mt-1 flex-col w-full">
                    <multiselect
                      v-model="form.selectedRole"
                      :options="roles"
                      placeholder="список ролей"
                      :close-on-select="true"
                      :show-labels="false"
                      :searchable="false"
                      :append-to-body="false"
                      label="name"
                      track-by="id"
                      />
                  </div>
                </div>
              </div>


              <div class="w-[336px] h-[100px]">
                <div class="h-full rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 bg-white p-5 flex flex-col border-t">
                  <h6 class="text-sm font-semibold text-gray-500 uppercase mx-auto">Статус</h6>
                  <div class="flex items-center gap-2 mt-1 flex-col w-full">
                    <multiselect
                      v-model="form.selectedStatus"
                      :options="statuses"
                      placeholder="Выберите статус"
                      :close-on-select="true"
                      :show-labels="false"
                      :searchable="false"
                      :append-to-body="false"
                      label="name"
                      track-by="id"
                      @select="onStatusChange"
                    />
                    </div>
                  </div>

              </div>

              <div class="w-[336px] h-[100px]">

              </div>
            </div>
          </div>

        </div>
      </div>

        </div>

    </div>

</template>

<script>
import { defineComponent } from 'vue'
import { getStatusColorClass } from '@/helpers/statusColorClass.js'
import { Icon } from '@iconify/vue'
import TextInput from '@/Components/Input/TextInput.vue'
import FileInput from '@/Components/Input/FileInput.vue'
import ButtonUI from '@/Components/Button/ButtonUI.vue'
import FadeOrInstant from '@/Components/FadeOrInstant.vue'
import InfoNotification from '@/Components/Notifications/InfoNotification.vue'
import { handleApiError } from '@/helpers/errors.js'
import { HollowDotsSpinner } from 'epic-spinners'
import { UserService } from '@/services/UserService.js'
import { RoleService } from '@/services/RoleService.js'
import Multiselect from 'vue-multiselect'

export default defineComponent({
  components: { Multiselect, InfoNotification, FadeOrInstant, ButtonUI, FileInput, TextInput, Icon, HollowDotsSpinner},

  props: {
    isActive: {
      type: Boolean,
      default: false,
      required: true
    },
    selectedManager: {
      type: Object,
      required: true
    },
  },
  data() {
    return {
      deleteUserId: null,
      updatedUser: null,
      editableUserId: null,
      editableField: null,
      confirmationUserDelete: false,
      roles: '',
      statuses: [
        { id: 1, name: 'Активен' },
        { id: 0, name: 'Отключен' },
      ],
      form: {
        editableUserName: null,
        editableUserEmail: null,
        editableUserPassword: null,
        selectedRole: null,
        selectedStatus: null,
      },
      errors: '',
    }
  },
  mounted() {
    this.getRoles()
    this.setInitialStatus()
  },
  methods: {
    setInitialStatus() {
      const status = this.statuses.find(s => s.id === this.selectedManager.enabled)
      this.form.selectedStatus = status ?? null
    },
    getStatusColor(status) {
      return getStatusColorClass(status)
    },
    async getRoles() {
      try {
        const response = await RoleService.getRoles();
        this.roles = response.data.data || null;
      } catch (error) {
        this.errors = handleApiError(error)
      }
    },
    enableEdit(object, field) {
      this.editableUserId = object.id;
      this.editableField = field;

      switch (field) {
        case 'name':
          this.form.editableUserName = object.name
          return
        case 'email':
          this.form.editableUserEmail = object.email
          return
        case 'password_show':
          this.form.editableUserPassword = object.password_show
          return
      }
    },
    async updateUser(fieldName) {
      let payload = {}

      if (fieldName === 'name') {
        payload.name = this.form.editableUserName
      }

      if (fieldName === 'email') {
        payload.email = this.form.editableUserEmail
      }
      if (fieldName === 'password_show') {
        payload.password_show = this.form.editableUserPassword
      }

      try {
        const response = await UserService.updateUserForAdmin(this.selectedManager.id, payload);
        this.updatedUser = response.data.data
        this.$emit('userUpdated', response.data.data);
        this.editableUserId = null

      } catch (error) {
        this.errors = handleApiError(error)
      }
    },
    async onRoleChange() {
      try {
        const response = await UserService.updateUserRole(this.selectedManager.id, this.form.selectedRole.id);
        this.updatedUser = response.data.data
        this.$emit('userUpdated', response.data.data);
      } catch (error) {
        this.errors = handleApiError(error)
      }
    },
    async onStatusChange() {
      try {
        const response = await UserService.updateUserStatus(this.selectedManager.id, this.form.selectedStatus.id);
        this.updatedUser = response.data.data
        this.$emit('userUpdated', response.data.data);
      } catch (error) {
        this.errors = handleApiError(error)
      }
    },
    prepareCompleted() {
      this.confirmationUserDelete = true
    },
    async successDeleteUser(userId) {
      this.deleteUserId = userId;
      await this.removeUser();
    },
    cancelDeleteUser() {
      this.confirmationUserDelete = false
      this.deleteUserId = null
    },
    async removeUser() {
      try {
        const response = await UserService.delete(this.deleteUserId);
        if (response){
          this.deleteUserId = null;
          this.$emit('successDeleteUser');
        }
      } catch (error) {
        this.errors = handleApiError(error)
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
    close() {
      this.$emit('close', { updateUser: this.updatedUser});
    },
  },
  watch: {
    roles(newRoles) {
      if (this.isActive && this.selectedManager && Array.isArray(newRoles)) {
        const roleName = this.selectedManager.role[0]
        const foundRole = newRoles.find(r => r.name === roleName)
        this.form.selectedRole = foundRole ?? null
      }
    },
    isActive: {
      immediate: true,
      handler(newVal) {
        if (newVal && this.selectedManager && Array.isArray(this.roles)) {
          const roleName = this.selectedManager.role[0]
          const foundRole = this.roles.find(r => r.name === roleName)
          this.form.selectedRole = foundRole ?? null
        }
      }
    },
    'form.selectedRole': {
      immediate: false,
      handler(newRole, oldRole) {
        if (newRole && (!oldRole || newRole.id !== oldRole.id)) {
          this.onRoleChange(newRole);
        }
      }
    }
  }
})
</script>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

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
::v-deep .multiselect__tags,
::v-deep .multiselect__single {
  background-color: #f5f5f5;
  border-radius: 8px;
}
</style>
