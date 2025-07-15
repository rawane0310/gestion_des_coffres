<script setup lang="ts">
import { ref , computed } from 'vue'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')

const passwordVisible = ref(false)

const errorMessage = ref('')


const router = useRouter()

function togglePassword() {
  console.log("cliked")
  passwordVisible.value = !passwordVisible.value
}

const isEmailValid = computed(() => {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)
})


async function handleLogin() {

  

  try {
    const response = await fetch('http://127.0.0.1:8000/api/login_check', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        username: email.value,
        password: password.value
      })
    })

    if (!response.ok) {
      throw new Error('Login failed')
    }

    const data = await response.json()
    
    const token = data.token
    
    if (!token) {
      throw new Error('No token received')
    }

    // Save token
    localStorage.setItem('authToken', token)
    localStorage.setItem('email', email.value)
    localStorage.setItem('isAuthenticated', 'true')

    //Redirect to another page 
    router.push('/search')
  } catch (err) {
    errorMessage.value = 'Email or password incorrect.'
    localStorage.setItem('isAuthenticated', 'false')
  }
}
</script>



<template>
  
  <form class="login-form" @submit.prevent="handleLogin">
    
    <div class="input-group">
      <label for="email">Email</label>
      <input
        id="email"
        type="email"
        v-model="email"
        placeholder="Enter email"
        required
      />
      <p v-if="email && !isEmailValid" class="error">Invalid email format</p>
    </div>

    <div class="input-group">
      <label for="password">Password</label>
      <div class="password-wrapper">
        <input
      :type="passwordVisible ? 'text' : 'password'"
      id="password"
      v-model="password"
      placeholder="Enter password"
      required
    />
    <img
      src="@/assets/view.png"
      class="toggle-icon"
      @click="togglePassword"
      alt="Show password"
    />
      </div>
    </div>

    <button type="submit">Login</button>
    <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
  </form>
</template>


<style scoped>
.login-form {
  width: 300px;
  display: flex;
  flex-direction: column;
}

.login-form h2 {
  margin-bottom: 20px;
  text-align: center;
  color: #333;
}

.input-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 25px;
  align-items: flex-start;
  position: relative;
}

.input-group label {
  margin-bottom: 5px;
  font-weight: bold;
  color: #444;
  text-align: left;
}

.input-group input {
  width: 100%;
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 13px; 
  background-color: white; 
  color: black; 
  outline: none;
  box-sizing: border-box;
}

.input-group input::placeholder {
  color: #999;
  opacity: 0.8;
}

.input-group input:focus {
  border-color: #df4c84;
  box-shadow: 0 0 5px rgba(223, 76, 132, 0.4);
}

.password-wrapper {
  position: relative;
  width: 100%;
}

.password-wrapper input {
  width: 100%;
  padding: 10px 35px 10px 10px; 
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 13px;
  background-color: white;
  color: black;
  outline: none;
  box-sizing: border-box;
}

.password-wrapper input::placeholder {
  color: #999;
  opacity: 0.8;
}

.password-wrapper input::-ms-reveal,
.password-wrapper input::-ms-clear {
  display: none;
}

.password-wrapper input::-webkit-credentials-auto-fill-button {
  visibility: hidden;
  position: absolute;
  right: 0;
}

.toggle-icon {
  position: absolute;
  right: 13px;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  cursor: pointer;
  opacity: 0.6;
}

.toggle-icon:hover {
  opacity: 1;
}

.login-form button {
  width: 50%;
  padding: 10px;
  background-color: #ff3caee5;
  border: none;
  color: white;
  font-size: 16px;
  border-radius: 10px;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 15px;
  margin: 22px auto 0 auto;
}

.login-form button:hover {
  background-color: #ff0080;
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
</style>
