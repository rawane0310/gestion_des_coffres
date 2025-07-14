import { createRouter, createWebHistory } from 'vue-router'
import LoginPage from '../views/LoginPage.vue'
import SearchPage from '../views/SearchPage.vue'
import HistoryPage from '../views/HistoryPage.vue'

const routes = [
  { path: '/', name: 'Login', component: LoginPage },
  { path: '/search', name: 'Search', component: SearchPage },
  { path: '/history/:id', component: HistoryPage }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
