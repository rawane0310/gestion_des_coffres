<template>
  <div class="dialog-overlay">
    <div class="dialog-box">
      <h2>{{ title}}</h2>

      <div class="form-group">
        <label class="custom-label">Name:</label>
        <input v-model="name" type="text" placeholder="props.initialName || 'Enter name'" />
       
      </div>

      <div class="flex-spacer"></div>
      <div class="dialog-buttons">
        <button class="cancel-btn" @click="$emit('close')">Cancel</button>
        <button 
          class="add-btn" 
          :disabled="!name" 
          @click="submitName"
        >
          Add
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
    import { ref, watch } from 'vue'

    const props = defineProps<{
  title: string
  initialName: string
}>()

    const emit = defineEmits<{
    (e: 'submit', coffre:{name: string}): void
    (e: 'close'): void
    }>()

    const name = ref('')

    
    watch(() => props.initialName, val => {
  name.value = ''
  })

function submitName() {
  if (name.value.trim()) {
    emit('submit', {name: name.value.trim()})
  }
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
  height: 130px; 
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
  margin-bottom: 30px;
  display: flex;
  flex-direction: column;
}

.custom-label {
  margin-bottom: 10px;
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
  height: 18px;
}

.dialog-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 10px;
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
