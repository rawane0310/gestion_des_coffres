<!-- components/SearchBar.vue -->
<template>
  <div class="search-bar-wrapper">
    <div class="search-type-dropdown" @mouseleave="showTypeMenu = false">
      <button 
        class="search-type-toggle" 
        @mouseenter="showTypeMenu = true"
      >
        {{ searchType === 'name' ? 'Search by Name' : 'Search by Code' }}
        <span class="arrow">▼</span>
      </button>
      <div v-if="showTypeMenu" class="search-type-menu">
        <button 
          @click="selectSearchType('name')"
          :class="{ active: searchType === 'name' }"
        >
          Search by Name
        </button>
        <button 
          @click="selectSearchType('code')"
          :class="{ active: searchType === 'code' }"
        >
          Search by Code
        </button>
      </div>
    </div>
    
    <input
      v-model="searchQuery"
      type="text"
      :placeholder="searchType === 'name' ? 'Search by name...' : 'Search by code...'"
      class="search-input"
      @keyup.enter="emitSearch"
    />
    <button @click="emitSearch" class="search-btn"></button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const emit = defineEmits<{
  (e: 'search', type: string, query: string): void
}>()

const searchType = ref('name')
const searchQuery = ref('')
const showTypeMenu = ref(false)

function selectSearchType(type: 'name' | 'code') {
  searchType.value = type
  showTypeMenu.value = false
}

function emitSearch() {
  emit('search', searchType.value, searchQuery.value.trim())
}
</script>

<style scoped>
.search-bar-wrapper {
  display: flex;
  align-items: center;
  gap: 10px;
}

/* DROPDOWN SEARCH TYPE */
.search-type-dropdown {
  position: relative;
}

.search-type-toggle {
  background: rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(6px);
  border: 1px solid rgba(255, 255, 255, 0.4);
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 14px;
  color: #222;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 140px;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.search-type-toggle:hover {
  border-color: #f38ad4;
  background: rgba(255, 255, 255, 0.4);
}

.search-type-toggle:focus {
  outline: none;
  border-color: #f38ad4;
}

.arrow {
  font-size: 10px;
  transition: transform 0.3s ease;
}

.search-type-dropdown:hover .arrow {
  transform: rotate(180deg);
}

.search-type-menu {
  position: absolute;
  top: 100%;
  left: 0;
  background: #f0c6e2; 
  
  border: 1px solid rgba(255, 255, 255, 0.4);
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  min-width: 100%;
  overflow: hidden;
  margin-top: 2px;
}

.search-type-menu button {
  display: block;
  width: 100%;
  padding: 12px 16px;
  background: none;
  border: none;
  text-align: left;
  cursor: pointer;
  color: #222;
  font-size: 14px;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.search-type-menu button:hover {
  background-color: #f39dd5; 
  color: white;
}

.search-type-menu button.active {
  background-color: #f0c6e2;
  color: #222;
  
}

.search-type-menu button.active:hover {
  background-color: #f39dd5;
  color: white;
}

/* SEARCH INPUT */
.search-input {
  background: rgba(255, 255, 255, 0.3);
  backdrop-filter: blur(6px);
  border: 1px solid rgba(255, 255, 255, 0.4);
  border-radius: 8px;
  padding: 8px 35px;
  font-size: 14px;
  color: #222;
  flex: 1;
  min-width: 280px; 
  width: 100%;      
  max-width: 500px;
  transition: all 0.3s ease;
}

.search-input::placeholder {
  color: #666;
}

.search-input:focus {
  outline: none;
  border-color: #f38ad4;
  background: rgba(255, 255, 255, 0.4);
}

/* SEARCH BUTTON */
.search-btn {
  display: none;
}

/* RESPONSIVE */
@media (max-width: 768px) {
  .search-bar-wrapper {
    flex-direction: column;
    gap: 8px;
    width: 100%;
  }
  
  .search-type-toggle {
    min-width: auto;
    width: 100%;
  }
  
  .search-input {
    min-width: auto;
    width: 100%;
  }
}
</style>