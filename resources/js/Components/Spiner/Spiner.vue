<template>
  <div class="loading-overlay" v-if="isLoading">
    <pulse-loader></pulse-loader>
  </div>
</template>

<script>

import PulseLoader from 'vue-spinner/src/PulseLoader.vue'

export default {
  data() {
    return {

    }
  },
  components: {
    PulseLoader
  },
  props: {
    isLoading: false
  },
  methods: {

  },
};
</script>

<style>
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 2);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}
</style>
