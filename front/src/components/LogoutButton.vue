<template>
  <button class="dropdown-toggle" @click="logout">Logout</button>
</template>

<script setup lang="ts">
function logout() {
  localStorage.clear();
  window.location.href = '/'
}
</script>

<style scoped>
.header-button {
  padding: 6px 10px;
  background-color: #df4c84;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 12px;
  cursor: pointer;
}

.header-button:hover {
  background-color: #c03b6e;
}
</style>
