<template>
  <component :is="transitionComponent">
    <slot />
  </component>
</template>

<script>
export default {
  props: {
    disableTransition: {
      type: Boolean,
      default: false,
    },
    name: {
      type: String,
      default: 'fade',
    },
  },
  computed: {
    transitionComponent() {
      return this.disableTransition ? 'div' : 'transition';
    },
  },
};
</script>
