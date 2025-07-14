<!-- components/CoffreCard.vue -->
<template>
  <div class="coffre-card">
    <div class="coffre-info">
      <div class="coffre-name">{{ coffre.name }}</div>
      <div class="coffre-code">{{ coffre.code }}</div>
    </div>
    
    <div class="coffre-actions">
      <button 
        @click="$emit('changeName', coffre.id)" 
        class="action-btn change-name-btn"
      >
        Change Name
      </button>
      <button 
        @click="$emit('changeCode', coffre.id)" 
        class="action-btn change-code-btn"
      >
        Change Code
      </button>
      <button 
        @click="$emit('history', coffre.id)" 
        class="action-btn history-btn"
      >
        History
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Coffre {
  id: number
  name: string
  code: string
}

defineProps<{
  coffre: Coffre
}>()

defineEmits<{
  (e: 'changeName', id: number): void
  (e: 'changeCode', id: number): void
  (e: 'history', id: number): void
}>()
</script>

<style scoped>
.coffre-card {
  background-color: white;
  border: 1px solid #aaa;
  border-radius: 12px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  padding: 20px;
  margin-bottom: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 800px;
  width: 100%;
  transition: all 0.3s ease;
}

.coffre-card:hover {
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

.coffre-info {
  flex: 1;
}

.coffre-name {
  font-size: 18px;
  font-weight: bold;
  color: #333;
  margin-bottom: 8px;
}

.coffre-code {
  font-size: 14px;
  color: #666;
  font-family: monospace;
  background-color: #f5f5f5;
  padding: 4px 8px;
  border-radius: 4px;
  display: inline-block;
}

.coffre-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.action-btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.change-name-btn {
  background-color: #f368ad;
  color: white;
}

.change-name-btn:hover {
  background-color: #fd0983;
  transform: scale(1.05);
}

.change-code-btn {
  background-color: #f368ad;
  color: white;
}

.change-code-btn:hover {
  background-color: #fd0983;
  transform: scale(1.05);
}

.history-btn {
  background-color: #f368ad;
  color: white;
}

.history-btn:hover {
  background-color: #fd0983;
  transform: scale(1.05);
}

/* Responsive */
@media (max-width: 768px) {
  .coffre-card {
    flex-direction: column;
    align-items: flex-start;
    padding: 15px;
  }
  
  .coffre-actions {
    margin-top: 15px;
    width: 100%;
    justify-content: center;
  }
  
  .action-btn {
    flex: 1;
    min-width: 100px;
  }
}
</style>