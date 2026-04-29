<script setup>
import { RouterView } from 'vue-router'
import Toast from './components/Toast.vue'
import { useToast } from './composables/useToast'

const { toasts, remove } = useToast()
</script>

<template>
  <RouterView />
  <Toast
    v-for="toast in toasts"
    :key="toast.id"
    :type="toast.type"
    :title="toast.title"
    :message="toast.message"
    :duration="toast.duration"
    @close="remove(toast.id)"
  />
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

#app {
  width: 100%;
  height: 100vh;
}
</style>
