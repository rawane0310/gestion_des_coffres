<template>
  <div class="dialog-overlay">
    <div class="dialog-box">
      <h2>Add New User</h2>

      <div class="form-group">
        <label class="custom-label">Email:</label>
        <input v-model="email" type="email" placeholder="Enter email" />
        <p v-if="email && !isEmailValid" class="error">Invalid email format</p>
      </div>

      <div class="form-group">
        <label class="custom-label">Password:</label>
        <input v-model="password" type="password" placeholder="Enter password" />
        <p v-if="password && password.length < 8" class="error">
          Password must be at least 8 characters long
        </p>
      </div>
      <div class="flex-spacer"></div>
      <div class="dialog-buttons">
        <button class="cancel-btn" @click="$emit('close')">Cancel</button>
        <button 
          class="add-btn" 
          :disabled="!isFormValid" 
          @click="submitUser"
        >
          Add
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

const emit = defineEmits<{
  (e: 'submit', user: { email: string; password: string }): void
  (e: 'close'): void
}>()

const email = ref('')
const password = ref('')


const isEmailValid = computed(() => {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)
})


const isFormValid = computed(() => {
  return email.value && password.value && isEmailValid.value
})

function submitUser() {
  if (!isFormValid.value) return
  emit('submit', { email: email.value.trim(), password: password.value })
}
</script>

<style scoped>
.dialog-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.dialog-box {
  background-color: #f5d7e3;
  padding: 50px 40px 60px;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
  width: 300px;
  height: 195px; 
  display: flex;
  flex-direction: column;
}

.flex-spacer {
  flex: 1; 
}


.dialog-box h2 {
  margin-top: -20px;         
  margin-bottom: 30px;       
}

.form-group {
  margin-bottom: 25px;
  display: flex;
  flex-direction: column;
  position: relative;
}

.custom-label {
  margin-bottom: 8px;
  color: #000000;
}


input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 6px;
}



.error {
  color: #ff0707;
  font-size: 12px;
  margin-top: 4px;
  font-style: italic;
  position: absolute;
  top: 100%;
  left: 0;
  height: 18 px;
}

.dialog-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 3px;
}

.cancel-btn,
.add-btn {
  padding: 8px 14px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
}

.cancel-btn {
  background-color: #ccc;
  color: #333;
}

.cancel-btn:hover {
  background-color: #a7a4a4;
}
.add-btn {
  background-color: #fa8bc2;
  color: white;
}

.add-btn:hover {
  background-color: #e91e63;
}


.add-btn[disabled] {
  background-color: #ccc;
  cursor: not-allowed;
}
</style>
