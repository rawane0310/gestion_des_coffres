<template>
  <div class="history-page">
    <div class="header">
      <h1 class="header-title">VaultMaster</h1>

      <div class="header-buttons">
        <button class="dropdown-toggle" @click="goBack" >Return</button>
        <LogoutButton />
      </div>
    </div>

    <div class="coffre-container">
      <div class="coffre-list">
        <div 
        v-for="c in paginatedCoffres" 
        :key="c.id" 
        class="coffre-item"
        >
        
        <span><strong>Code:</strong> {{ c.old_code }}</span>
        <span><strong>Time:</strong> {{ c.date }}</span>
        <span><strong>Changed by:</strong> {{ c.changed_by }}</span>
  </div>
        <p v-if="coffres.length === 0" class="empty-text">No history found.</p>
      </div>
      <!-- Pagination -->
      <div v-if="totalPages > 1" class="pagination">
        <button 
          @click="previousPage" 
          :disabled="currentPage === 1"
          class="pagination-btn"
          :class="{ disabled: currentPage === 1 }"
        >
          Previous
        </button>
        
        <span class="pagination-info">
          Page {{ currentPage }} of {{ totalPages }}
        </span>
        
        <button 
          @click="nextPage" 
          :disabled="currentPage === totalPages"
          class="pagination-btn"
          :class="{ disabled: currentPage === totalPages }"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">

import { ref , computed , onMounted} from 'vue'
import { useRoute, useRouter } from 'vue-router'

import LogoutButton from '../components/LogoutButton.vue'

const router = useRouter()
const route = useRoute()

const coffres = ref<any[]>([]);
const coffreId = route.params.id

// Pagination
const currentPage = ref(1)
const itemsPerPage = 4

async function fetchCoffreHistory() {
  const token = localStorage.getItem('authToken');
  if (!token) {
    alert("Non authentifié");
    router.push('/login');
    return;
  }

  try {
    const response = await fetch(`http://127.0.0.1:8000/api/historique/searchByCoffre?id=${coffreId}`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });

    const result = await response.json();

    if (response.ok) {
      coffres.value = result.data; 
    } else {
      coffres.value = [];
      alert(result.message?.error || "Error");
    }

  } catch (err) {
    
    console.error(err);
  }
}

onMounted(() => {
  fetchCoffreHistory();
});


//return
function goBack() {
  router.push('/search')
}


// Computed pour la pagination
const totalPages = computed(() => {
  return Math.ceil(coffres.value.length / itemsPerPage)
})

const paginatedCoffres = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return coffres.value.slice(start, end)
})

function nextPage() {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

function previousPage() {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}
</script>

<style scoped>
.history-page {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background-color: #f5f5f5;
}

/* HEADER STYLING */
.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background-color: #ecbbde; 
  padding: 25px 5px;
  width: 100vw;    
  max-width: 100%;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}


.header-title {
  font-size: 40px;
  font-weight: bold;
  color: white;
  margin: 0;
  margin-left: 20px;
}



.header-buttons {
  display: flex;
  flex-direction: row; 
  gap: 10px;
  align-items: center;
  
}

.dropdown-toggle {
  background-color: #fc86da; 
  color: rgb(8, 8, 8); 
  border: none;
  padding: 10px 15px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.dropdown-toggle:hover {
  background-color: #fd369a;
}

/* COFFRE CONTAINER */
.coffre-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 70px;
  overflow-y: auto;
}

.coffre-list {
  width: 100%;
  max-width: 800px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 30px;
}

.coffre-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background-color: white;
  border: 1px solid #aaa;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  padding: 15px 20px;
  width: 100%;
  max-width: 800px;
  font-size: 16px;
  gap: 20px;
  transition: all 0.3s ease;
}

.coffre-item:hover {
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

.empty-text {
  text-align: center;
  color: #999;
  font-style: italic;
  margin-top: 50px;
}

/* PAGINATION STYLING */
.pagination {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-top: auto;
  padding: 20px;
}

.pagination-btn {
  background-color: #f8bbd9;
  color: #000;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.pagination-btn:hover:not(.disabled) {
  background-color: #e91e63;
  color: white;
  transform: scale(1.05);
}

.pagination-btn.disabled {
  background-color: #ddd;
  color: #999;
  cursor: not-allowed;
  opacity: 0.6;
}

.pagination-info {
  font-size: 14px;
  color: #666;
  font-weight: 500;
}

</style>
