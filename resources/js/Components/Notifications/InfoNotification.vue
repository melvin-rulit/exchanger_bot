<template>
  <div class="flex justify-center items-center shadow-sm">
    <div class="flex items-center gap-2 p-1 text-md">
      <Icon :icon="icon" class="alert-icon" width="26" height="26" :class="[iconColorClass, animateIcon ? 'animate-fade-bounce' : '']"/>
      <div>
        <span class="font-medium text-lg">{{ message }}</span>
      </div>
    </div>
  </div>

</template>

<script>
import { Icon } from "@iconify/vue";
import ButtonUI from '@/Components/ButtonUI.vue'

export default {
  components: {ButtonUI, Icon},
    props: {
        message: {
            type: String,
            required: true,
        },
        type: {
            type: String,
            default: 'success', // Возможные значения: 'success', 'error', 'danger', 'info'
        },
      animateIcon: {
        type: Boolean,
        default: true,
      },
    },
    data() {
        return {

        };
    },
    computed: {
        alertTypeClass() {
            switch (this.type) {
                case 'success':
                    return 'alert-success';
                case 'error':
                    return 'alert-error';
                case 'danger':
                    return 'alert-danger';
                case 'info':
                    return 'alert-info';
                default:
                    return 'alert-success';
            }
        },
        icon() {
            switch (this.type) {
                case 'success':
                    return 'mdi:success-bold';
                case 'error':
                    return 'carbon:close-outline';
                case 'danger':
                    return 'pajamas:warning';
                case 'info':
                    return 'zondicons:information-outline';
                default:
                    return 'mdi:success-bold';
            }
        },
        iconColorClass() {
            switch (this.type) {
                case 'success':
                    return 'icon-success';
                case 'error':
                    return 'icon-error';
                case 'danger':
                    return 'icon-danger';
                case 'info':
                    return 'icon-info';
                default:
                    return 'icon-success';
            }
        },
    },
};
</script>

<style lang="scss" scoped>
.alert {

    &-success {
      background-color: #4caf50;
    }

    &-error {
      background-color: #f44336;
    }

    &-danger {
      border-color: #ff9800;
    }

    &-info {
      background-color: #2196f3;
    }
}
</style>
