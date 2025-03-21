<template>
    <div
        v-if="isActive"
        @click="close"
        class="fixed inset-0 bg-black shadow-lg flex items-center justify-center z-50"
    >
        <!-- Модальное окно -->
        <div class="relative">
            <!-- Картинка -->
            <img
                :src="currentImageUrl"
                class="max-w-full max-h-full object-contain"
                alt="Image preview"
            />

            <!-- Кнопка закрытия -->
<!--            <button-->
<!--                @click.stop="close"-->
<!--                class="absolute top-4 right-4 bg-white p-2 rounded-full text-black hover:bg-gray-300"-->
<!--                aria-label="Close modal"-->
<!--            >-->
<!--                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">-->
<!--                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>-->
<!--                </svg>-->
<!--            </button>-->
        </div>
    </div>
</template>

<script>
export default {
    props: {
        isActive: {
            type: Boolean,
            default: false
        },
        currentImageUrl: {
            type: String,
        },
    },
    methods: {
        close() {
            this.$emit('close');
        },
    },
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
  backdrop-filter: blur(4px);
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

.object-contain {
    object-fit: contain;
}
.relative {
    position: relative;
}
.absolute {
    position: absolute;
}
.top-4 {
    top: 1rem;
}
.right-4 {
    right: 1rem;
}
</style>
