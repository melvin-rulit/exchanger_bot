<template>
    <transition name="slide-down">
        <div v-show="isVisible" :class="['alert', alertTypeClass]">
            <div class="content">
                <Icon :icon="icon" class="alert-icon" width="26" height="26" :class="iconColorClass" />
                <p>{{ message }}</p>

              <div class="buttons pl-4">
                <slot name="buttons"></slot>
              </div>
                <div class="buttons pl-4">
                  <ButtonUI @click="closeAlert" type="submit" color="red">Закрыть</ButtonUI>
                </div>

            </div>
        </div>
    </transition>
</template>

<script>
import { Icon } from "@iconify/vue";
import ButtonUI from '@/Components/Button/ButtonUI.vue'

export default {
    props: {
        message: {
            type: String,
            required: true,
        },
        type: {
            type: String,
            default: 'success', // Возможные значения: 'success', 'error', 'danger', 'info'
        },
        duration: {
            type: Number,
            default: 2000,
        },
    },
    data() {
        return {
            isVisible: false,
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
    methods: {
        showAlert() {
            this.isVisible = true;
        },
      closeAlert() {
            this.isVisible = false;
            this.$emit('clearMessages');
        },
    },
    components: {
      ButtonUI,
        Icon,
    },
};
</script>

<style lang="scss" scoped>
.alert {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    width: 80%;
    max-width: 700px;
    padding: 15px;
    border-radius: 6px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    text-align: center;
    font-size: 16px;
    margin-top: 2em;
    background-color: #fff;

    p {
        margin: 0 0 0 1em;
        font-weight: 500;
      color: black;
    }

    &-success {
        .progress-bar {
            background-color: #4caf50;
        }
    }

    &-error {
        .progress-bar {
            background-color: #f44336;
        }
    }

    &-danger {
        .progress-bar {
            background-color: #ff9800;
        }
    }

    &-info {
        .progress-bar {
            background-color: #2196f3;
        }
    }

    .content {
        display: flex;
        justify-content: center;
        align-items: center;
    }
}

/* Цвет иконки в зависимости от типа */
.icon-success {
    color: #4caf50;
}

.icon-error {
    color: #f44336;
}

.icon-danger {
    color: #ff9800;
}

.icon-info {
    color: #2196f3;
}

.progress-bar {
    height: 4px;
    transition: width 0.1s linear;
    border-radius: 8px;
    margin-top: 1em;
}

.slide-down-enter-active, .slide-down-leave-active {
    transition: all 0.3s ease;
}

.slide-down-enter, .slide-down-leave-to {
    transform: translateX(100%);
    opacity: 0;
}
</style>
