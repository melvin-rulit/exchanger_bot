<template>
    <!-- Компонент кнопки с динамическими классами и стилями -->
    <button :class="[colorClass]" :style="{ width: width, padding: padding, fontSize: fontSize, fontWeight: fontWeight }" @click="handleClick" :disabled="isDisabled">
        {{ buttonText }} <slot></slot>
    </button>
</template>

<script>
export default {
    props: {
        buttonText: {
            type: String,
            required: false,
        },
        color: {
            type: String,
            default: "blue",
            validator: function (value) {
                return ['blue', 'white', 'grey', 'red', 'green'].includes(value);
            }
        },
        width: {
            type: String,
            default: "auto",
        },
        padding: {
            type: String,
            default: "10px 40px",
        },
        fontSize: {
            type: String,
            default: "13px",
        },
        fontWeight: {
            type: String,
            default: "600",
        },
      isDisabled: {
        type: Boolean,
        default: false
      },
    },
    computed: {
        colorClass() {
          const baseClass = this.color === "blue" ? "blue-button" :
            this.color === "white" ? "white-button" :
              this.color === "grey" ? "grey-button" :
                this.color === "green" ? "green-button" :
                  "red-button";

          return [baseClass, this.isDisabled ? 'disabled-button' : ''].join(' ');
        },
    },
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
        transition: background-image 0.3s, border-color 0.3s;
        &:hover {
            background: #2362b7;
            transition: 0.3s;
        }
    }

    /* Стили для белой кнопки */
    &.white-button {
        background: #ffffff;
        color: black;
        border: 1px solid #b1c2d9;
        transition: background-image 0.3s, border-color 0.3s;
        &:hover {
            background-color: #f9fbfd;
            border-color: #d2ddec;
            transition: 0.3s;
        }
    }

    /* Стили для серой кнопки */
    &.grey-button {
        background: #0d121c40;
        border-color: #b9b9b950;
        transition: background-image 0.3s, border-color 0.3s;
        &:hover {
            border-color: #b9b9b990;
            background-image: radial-gradient(15em circle at 50% 60px,#b9b9b950,transparent 40%);
            transition: 0.3s;
        }
    }

    /* Стили для красной кнопки */
    &.red-button {
        background: #ff4d4d; /* Красный цвет */
        transition: background-image 0.3s, border-color 0.3s;
        &:hover {
            background: #e60000; /* Темнее при наведении */
            transition: 0.3s;
        }
    }

    /* Стили для зеленой кнопки */
    &.green-button {
        background: #4caf50; /* Зеленый цвет */
        transition: background-image 0.3s, border-color 0.3s;
        &:hover {
            background: #43A047; /* Темнее при наведении */
            transition: 0.3s;
        }
    }
}
.disabled-button {
  opacity: 0.6;
  pointer-events: none;
  filter: grayscale(30%);
}
</style>
