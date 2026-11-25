<template>
  <Teleport to="body">
    <div 
      v-if="modelValue" 
      class="modal-overlay" 
      @click.self="close"
      @keydown.esc="close"
      tabindex="-1"
      ref="overlayRef"
    >
      <div class="modal-window">
        <button class="modal-close" @click="close">&times;</button>

        <h2 class="modal-title">Оцените мероприятие</h2>

        <div class="form-group">
          <div class="stars">
            <button
              v-for="s in 5"
              :key="s"
              type="button"
              class="star"
              :class="{ active: s <= rating }"
              :disabled="loading"
              @click="setRating(s)"
            >★</button>
          </div>
          <p v-if="ratingError" class="error-text">{{ ratingError }}</p>
        </div>

        <div class="form-group">
          <label for="comment" class="form-label">Комментарий</label>
          <textarea
            id="comment"
            v-model="comment"
            class="textarea"
            rows="4"
            maxlength="500"
            :disabled="loading"
            placeholder="Поделитесь впечатлениями..."
          ></textarea>
          <p class="char-counter" :class="{ 'char-counter--limit': comment.length >= 500 }">
            {{ comment.length }} / 500
          </p>
        </div>

        <p v-if="submitError" class="error-text">{{ submitError }}</p>

        <button class="btn-submit" :disabled="loading" @click="submit">
          {{ loading ? 'Отправка...' : 'Отправить отзыв' }}
        </button>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue';

const props = defineProps({
  modelValue: Boolean,
});

const emit = defineEmits(['update:modelValue', 'sent']);

const rating = ref(0);
const comment = ref('');
const ratingError = ref('');
const submitError = ref('');
const loading = ref(false);

const overlayRef = ref(null);

function saveToFakeDb(payload) {
  const KEY = 'feedback_db';
  const db = JSON.parse(localStorage.getItem(KEY) || '[]');
  const record = {
    id: Date.now(),
    rating: payload.rating,
    comment: payload.comment,
    createdAt: new Date().toISOString(),
  };
  db.push(record);
  localStorage.setItem(KEY, JSON.stringify(db));
  return record;
}

watch(() => props.modelValue, async (val) => {
  if (val) {
    await nextTick();
    overlayRef.value?.focus();
  }
});

function setRating(value) {
  rating.value = value;
  ratingError.value = '';
}

function close() {
  if (loading.value) return;
  emit('update:modelValue', false);
}

async function submit() {
  ratingError.value = '';
  submitError.value = '';

  if (rating.value === 0) {
    ratingError.value = 'Пожалуйста, поставьте оценку';
    return;
  }

  loading.value = true;

  try {
    await new Promise((resolve) => setTimeout(resolve, 400));

    const saved = saveToFakeDb({
      rating: rating.value,
      comment: comment.value,
    });

    emit('sent', saved);

    rating.value = 0;
    comment.value = '';

    emit('update:modelValue', false);
  } catch (e) {
    submitError.value = 'Не удалось отправить отзыв. Попробуйте позже.';
    console.error(e);
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  z-index: 1000;
  outline: none;
  overflow-y: auto;              
  -webkit-overflow-scrolling: touch;
}
.modal-window {
  position: relative;
  background: #fff;
  border-radius: 16px;
  padding: 28px;
  width: 100%;
  max-width: 480px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  max-height: calc(100dvh - 32px); 
  overflow-y: auto;                
  -webkit-overflow-scrolling: touch;
}
.modal-close {
  position: absolute;
  top: 8px;
  right: 8px;
  width: 44px;                     
  height: 44px;
  border: none;
  background: transparent;
  font-size: 28px;
  line-height: 1;
  cursor: pointer;
  color: #666;
  border-radius: 50%;
}
.modal-close:hover { background: #f0f0f0; }
.modal-title { margin: 0 0 20px; font-size: 22px; }
.form-group { margin-bottom: 20px; }
.form-label { display: block; margin-bottom: 8px; font-weight: 600; }
.stars { display: flex; gap: 4px; flex-wrap: wrap; }
.star {
  background: none;
  border: none;
  font-size: 40px;
  line-height: 1;
  cursor: pointer;
  color: #ddd;
  padding: 4px;
  min-width: 44px;                 
  min-height: 44px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.star.active { color: #f5b301; }
.error-text {
  margin: 6px 0 0;
  color: #d93025;
  font-size: 14px;
}
.char-counter {
  margin: 6px 0 0;
  font-size: 13px;
  color: #888;
  text-align: right;
}
.char-counter--limit { color: #d93025; }
.textarea {
  width: 100%;
  box-sizing: border-box;
  font-size: 16px;                 
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 8px;
  resize: vertical;
  font-family: inherit;
}
.btn-submit {
  width: 100%;
  padding: 14px 12px;
  min-height: 48px;                
  background: #4a90d9;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
}
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }


@media (max-width: 480px) {
  .modal-overlay {
    align-items: flex-start;       
    padding: 8px;
  }
  .modal-window {
    max-width: 100%;
    max-height: calc(100dvh - 16px); 
    padding: 20px 16px 24px;
    border-radius: 12px;
  }
  .modal-title { font-size: 20px; }
  .star {
    font-size: 36px;
    min-width: 48px;               
    min-height: 48px;
  }
  .btn-submit { min-height: 52px; font-size: 17px; }
}

@media (max-height: 500px) {
  .modal-window {
    max-height: calc(100dvh - 16px);
  }
}
</style>