import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '../services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token'))

  const login = async (email, password) => {
    const response = await api.post('/login', { email, password })
    user.value = response.data.user
    token.value = response.data.token
    localStorage.setItem('token', response.data.token)
  }

  const logout = async () => {
    try {
      await api.post('/logout')
    } catch (error) {
      console.error('Logout error:', error)
    }
    user.value = null
    token.value = null
    localStorage.removeItem('token')
  }

  const fetchUser = async () => {
    if (!token.value) return
    try {
      const response = await api.get('/me')
      user.value = response.data
    } catch (error) {
      logout()
    }
  }

  const isAuthenticated = () => !!token.value

  return { user, token, login, logout, fetchUser, isAuthenticated }
})
