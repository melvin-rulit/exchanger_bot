<template>
    <transition name="slide-down">
        <div v-if="isVisible" :class="['alert', alertTypeClass]">
            <div class="content">
                <Icon :icon="icon" class="alert-icon" :class="iconColorClass" />
                <p>{{ message }}</p>
            </div>
            <div class="progress-bar" :style="progressBarStyle"></div>
        </div>
    </transition>
</template>

<script>
import { Icon } from "@iconify/vue";

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
            progressBarWidth: 100,
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
                    return 'mdi:cancel-bold';
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
        progressBarStyle() {
            return {
                width: `${this.progressBarWidth}%`,
            };
        },
    },
    methods: {
        showAlert() {
            this.isVisible = true;
            this.startProgressBar();
            setTimeout(() => {
                this.isVisible = false;
            }, this.duration);
        },
        startProgressBar() {
            this.progressBarWidth = 100;
            const interval = setInterval(() => {
                if (this.progressBarWidth > 0) {
                    this.progressBarWidth -= 100 / (this.duration / 100);
                } else {
                    clearInterval(interval);
                }
            }, 100);
        },
    },
    components: {
        Icon,
    },
};
</script>

<style lang="scss" scoped>
.alert {
    position: fixed;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80%;
    max-width: 600px;
    padding: 15px;
    border-radius: 6px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    text-align: center;
    font-size: 16px;
    opacity: 0.9;
    margin-top: 2em;
    background-color: #fff;

    p {
        margin: 0 0 0 1em;
        font-weight: 500;
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
