<template>
  <div class="page">
    <div v-if="!feedbackSent">
      <button @click="showModal = true">Оставить отзыв</button>
    </div>
    <div v-else class="success-message">
      Ваш отзыв отправлен
    </div>

    <FeedbackModal
      v-model="showModal"
      @sent="onFeedbackSent"
    />

    <ToastNotification :visible="showToast" />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import FeedbackModal from './components/FeedbackModal.vue';
import ToastNotification from './components/ToastNotification.vue';

const showModal = ref(false);
const showToast = ref(false);
const feedbackSent = ref(false);

function onFeedbackSent() {
  showToast.value = true;
  setTimeout(() => {
    showToast.value = false;
  }, 3000);
  
  feedbackSent.value = true;
}
</script>

<style>
.page {
  padding: 20px;
  font-family: sans-serif;
}
button {
  padding: 10px 20px;
  background: #4a90d9;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}
.success-message {
  padding: 12px 0;
  font-size: 18px;
  color: #2e7d32;
  font-weight: 600;
}
</style>