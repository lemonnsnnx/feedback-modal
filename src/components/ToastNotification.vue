<template>
  <div v-if="visible" class="toast">
    Спасибо за отзыв!
  </div>
</template>

<script setup>
defineProps({
  visible: Boolean,
});
</script>

<style scoped>
.toast {
  position: fixed;
  bottom: 30px;
  left: 50%;
  transform: translateX(-50%);
  background: #333;
  color: #fff;
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 16px;
  z-index: 2000;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}
</style>