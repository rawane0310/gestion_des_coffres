<template>
  <div class="search-page">
    <div class="header">
      <h1 class="header-title">VaultMaster</h1>

      <SearchBar @search="handleSearch" />

      <div class="header-buttons">
        <div class="dropdown" @mouseleave="showMenu = false">
          <button 
            class="dropdown-toggle" 
            @mouseenter="showMenu = true"
          >
            Add
          </button>
          <div v-if="showMenu" class="dropdown-menu">
            <button @click="triggerAdd('user')">Add User</button>
            <button @click="triggerAdd('coffre')">Add Coffre</button>
          </div>
        </div>
        <AddUserDialog 
          v-if="showUserDialog" 
          @close="showUserDialog = false" 
          @submit="handleAddUser"
        />
        <CoffreDialog
          v-if="showCoffreDialog"
          :title="dialogTitle"
          :initialName="dialogInitialName"
          @submit="handleCoffreSubmit"
          @close="showCoffreDialog = false"
/>

        <LogoutButton />
      </div>
    </div>

    <div class="coffre-container">
      <div class="coffre-list">
        <CoffreCard
          v-for="c in paginatedCoffres"
          :key="c.id"
          :coffre="c"
          @changeName="handleChangeName"
          @changeCode="handleChangeCode"
          @history="handleHistory"
        />
        <p v-if="coffres.length === 0" class="empty-text">No coffres found.</p>
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
import SearchBar from '../components/SearchBar.vue'
import CoffreCard from '../components/CoffreCard.vue'

import LogoutButton from '../components/LogoutButton.vue'
import AddUserDialog from '../components/AddUserDialog.vue'
import CoffreDialog from '../components/CoffreDialog.vue'


import { ref , computed , onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const coffres = ref([])
const showMenu = ref(false)
const showUserDialog = ref(false)
const showCoffreDialog = ref(false)
const dialogTitle = ref('')
const dialogInitialName = ref('')
const selectedCoffreId = ref<number | null>(null)

// Pagination
const currentPage = ref(1)
const itemsPerPage = 3

const token  = localStorage.getItem('authToken')
const user = localStorage.getItem('email')

// Computed pour la pagination
const totalPages = computed(() => {
  return Math.ceil(coffres.value.length / itemsPerPage)
})

const paginatedCoffres = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return coffres.value.slice(start, end)
})

onMounted(() => {
  fetchAllCoffres();
});

async function fetchAllCoffres() {
  const response = await fetch('http://127.0.0.1:8000/api/coffre/', {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });

  if (response.ok) {
    const result = await response.json();
    coffres.value = result.data;
    console.log(coffres.value)
  } else {
    console.error('Erreur fetching coffres.');
  }
}

// Search
async function handleSearch(type: string, query: string) {
  if (!query) return

  let url = '';
    
  if (type === 'name') {
    url = `http://127.0.0.1:8000/api/coffre/searchByNom?name=${encodeURIComponent(query)}`;
    console.log(url)
  } else {
    url = `http://127.0.0.1:8000/api/coffre/searchByCode?code=${encodeURIComponent(query)}`;
    console.log(url)
  }

  const response = await fetch(url, {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });

  if (response.ok) {
    const result = await response.json();
    coffres.value = result.data;
    console.log(result)
    currentPage.value = 1;
  } else {
    console.error('Erreur de recherche.');
  }
}

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

function triggerAdd(type: 'user' | 'coffre') {
  showMenu.value = false
  if (type === 'user') {
    showUserDialog.value = true
  } else {
    dialogTitle.value = 'Add New Coffre'
    dialogInitialName.value = ''
    showCoffreDialog.value = true
  }
}


async function handleAddUser(user: { email: string; password: string }) {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/user/create', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(user)
    });

    const data = await response.json();

    if (response.ok) {
      
      console.log("user created succefully")
    } else {
      alert(data.message?.error || 'Error while creating.');
    }
  } catch (error) {
    console.error('Error:', error);
    
  }
  showUserDialog.value = false
}


async function handleCoffreSubmit(coffre: { name: string }) {
  
  const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  };

  if (dialogTitle.value === 'Add New Coffre') {
    try {
      const response = await fetch('http://127.0.0.1:8000/api/coffre/create', {
        method: 'POST',
        headers,
        body: JSON.stringify({
          nom: coffre.name,
          user: user
        })
      });

      const data = await response.json();
      console.log(data)
      if (response.ok) {
        
        window.location.reload();
      } else {
        alert(data.message?.error || "Error while creating coffre.");
      }

    } catch (error) {
      console.error("Error POST :", error);
      
    }
  } else if (dialogTitle.value === 'Change Name' && selectedCoffreId.value !== null) {
    
    try {
      const response = await fetch('http://127.0.0.1:8000/api/coffre/updateNom', {
        method: 'PUT',
        headers,
        body: JSON.stringify({
          id: selectedCoffreId.value,
          nom: coffre.name
        })
      });

      const data = await response.json();

      if (response.ok) {
        
        window.location.reload();
      } else {
        alert(data.message?.error || "Error while updating.");
      }

    } catch (error) {
      console.error("Error PUT :", error);
      
    }
  }

  showCoffreDialog.value = false
}

function handleChangeName(id: number) {
  const coffre = (coffres.value as any[]).find(c => c.id === id);
  if (!coffre) return;
  selectedCoffreId.value = id
  dialogTitle.value = 'Change Name'
  dialogInitialName.value = coffre.name;
  showCoffreDialog.value = true
} 

async function handleChangeCode(id: number) {
  try {
    const response = await fetch(`http://127.0.0.1:8000/api/coffre/updateCode`, {
      method: 'PUT',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        id: id,
        user: user
      }),
    });

    const result = await response.json();

    if (response.ok) {
      
      window.location.reload(); 
    } else {
      alert(result.message?.error || "Error while changing the code.");
    }

  } catch (error) {
    console.error("Error :", error);
    
  }
}

function handleHistory(id: number) {
  console.log('Show history for:', id)
  router.push(`/history/${id}`)
}
</script>

<style scoped>
.search-page {
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

/* DROPDOWN STYLING */
.dropdown {
  position: relative;
}

.dropdown-toggle {
  background-color: #fc86da; 
  color: rgb(8, 8, 8); 
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.3s ease;
}

.dropdown-toggle:hover {
  background-color: #fd369a;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  background-color: #f0dbea; 
  z-index: 100;
  padding: 5px 0;
  border-radius: 5px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  min-width: 120px;
}

.dropdown-menu button {
  display: block;
  width: 100%;
  padding: 10px 15px;
  background: none;
  border: none;
  text-align: left;
  cursor: pointer;
  color: #000; /* Noir */
  transition: background-color 0.3s ease;
}

.dropdown-menu button:hover {
  background-color: #fd369a; 
  color: white;
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
  gap: 15px;
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