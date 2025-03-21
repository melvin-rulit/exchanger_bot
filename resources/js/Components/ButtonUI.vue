<template>
    <!-- Компонент кнопки с динамическими классами и стилями -->
    <button :class="[colorClass]" :style="{ width: width, padding: padding, fontSize: fontSize, fontWeight: fontWeight }" @click="handleClick" :disabled="isDisabled">
        {{ buttonText }} <slot></slot>
    </button>
</template>

<script>
export default {
    props: {
        // Текст кнопки, обязательный параметр
        buttonText: {
            type: String,
            required: false,
        },
        // Цвет по умолчанию
        color: {
            type: String,
            default: "blue",
            validator: function (value) {
                return ['blue', 'white', 'grey', 'red', 'green'].includes(value);
            }
        },
        // Ширина по умолчанию
        width: {
            type: String,
            default: "auto",
        },
        // Настройка padding по умолчанию
        padding: {
            type: String,
            default: "10px 40px",
        },
        // Размер шрифта по умолчанию
        fontSize: {
            type: String,
            default: "12px",
        },
        // Настройка font-weight по умолчанию
        fontWeight: {
            type: String,
            default: "600",
        },
      isDisabled: {
        type: Boolean,
        default: false
      },
    },
    // Определяет класс кнопки в зависимости от выбранного цвета
    computed: {
        colorClass() {
            return this.color === "blue" ? "blue-button" :
                this.color === "white" ? "white-button" :
                    this.color === "grey" ? "grey-button" :
                        this.color === "green" ? "green-button" :
                        "red-button";
        },
    },
    // Обработчик клика на кнопку, генерирует событие "click"
    methods: {
        handleClick() {
            this.$emit("click");
        },
    },
};
</script>

<style lang="scss" scoped>
/* Стили для всех кнопок */
button {
    cursor: pointer;
    border-radius: 5px;
    border: 1px solid transparent;
    color: #f1f1f2;
    display: flex;
    align-items: center;

    /* Стили для синей кнопки */
    &.blue-button {
        background: #2c7be5;
        transition: background-image 0.5s, border-color 0.5s;
        &:hover {
            background: #2362b7;
            transition: 0.5s;
        }
    }

    /* Стили для белой кнопки */
    &.white-button {
        background: #ffffff;
        color: black;
        border: 1px solid #b1c2d9;
        transition: background-image 0.5s, border-color 0.5s;
        &:hover {
            background-color: #f9fbfd;
            border-color: #d2ddec;
            transition: 0.5s;
        }
    }

    /* Стили для серой кнопки */
    &.grey-button {
        background: #0d121c40;
        border-color: #b9b9b950;
        transition: background-image 1s, border-color 0.5s;
        &:hover {
            border-color: #b9b9b990;
            background-image: radial-gradient(15em circle at 50% 60px,#b9b9b950,transparent 40%);
            transition: 0.5s;
        }
    }

    /* Стили для красной кнопки */
    &.red-button {
        background: #ff4d4d; /* Красный цвет */
        transition: background-image 0.5s, border-color 0.5s;
        &:hover {
            background: #e60000; /* Темнее при наведении */
            transition: 0.5s;
        }
    }

    /* Стили для зеленой кнопки */
    &.green-button {
        background: #4caf50; /* Зеленый цвет */
        transition: background-image 0.5s, border-color 0.5s;
        &:hover {
            background: #43A047; /* Темнее при наведении */
            transition: 0.5s;
        }
    }
}
</style>
