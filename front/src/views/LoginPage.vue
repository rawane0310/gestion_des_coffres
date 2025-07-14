<template>
  <div class="login-container">
    <div class="left-panel">
      <h1 class="app-title">Welcome to VaultMaster</h1>
      <LoginForm />
    </div>
    <div class="right-panel">
      <img src="@/assets/login-image.jpg" alt="Login Image" />
    </div>
  </div>
</template>

<script setup lang="ts">
import LoginForm from '../components/LoginForm.vue'
</script>

<style scoped>
.login-container {
  display: flex;
  height: 100vh;
}

.left-panel, .right-panel {
  flex: 1;
}

.left-panel {
  flex: 1;
  padding: 60px 40px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-color: #ecbbde;
}
.app-title {
  font-size: 40px;
  color: #f8f8f8;
  font-weight: 900;
  margin-bottom: 80px;
  text-align: center;
}

.right-panel {
  padding: 40px; 
  background-color: #ecbbde; 
  display: flex;
  justify-content: center;
  align-items: center;
}

.right-panel img {
  width: 100%;
  height: 90%;
  max-width: 90%;
  max-height: 90%;
  object-fit: cover;
  border-radius: 20px; 
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); 
}

</style>
