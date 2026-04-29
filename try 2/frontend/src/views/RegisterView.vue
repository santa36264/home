<template>
  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-brand">
        <div class="brand-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
        </div>
        <span>WorkflowOS</span>
      </div>

      <h1 class="auth-title">Create account</h1>
      <p class="auth-sub">Fill in the details below to get started</p>

      <div v-if="!isPublicRegistration && !loading" class="info-banner">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
          <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <div>
          <strong>Registration disabled</strong>
          <p>Contact an administrator to create an account. Redirecting to login…</p>
        </div>
      </div>

      <form v-else @submit.prevent="handleRegister" class="auth-form">
        <div class="form-group">
          <label for="name">Full name</label>
          <input id="name" v-model="form.name" type="text" required placeholder="John Doe" />
        </div>
        <div class="form-group">
          <label for="email">Email address</label>
          <input id="email" v-model="form.email" type="email" required placeholder="you@example.com" />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input id="password" v-model="form.password" type="password" required minlength="8" placeholder="Min. 8 characters" />
        </div>
        <div class="form-group">
          <label for="confirm">Confirm password</label>
          <input id="confirm" v-model="form.password_confirmation" type="password" required minlength="8" placeholder="Re-enter password" />
        </div>

        <div v-if="error" class="form-error">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          {{ error }}
        </div>

        <button type="submit" class="btn-primary" :disabled="loading">
          <LoadingSpinner v-if="loading" size="small" />
          <span v-else>Create Account</span>
        </button>
      </form>

      <div class="auth-footer">
        Already have an account?
        <router-link to="/login">Sign in</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import LoadingSpinner from '../components/LoadingSpinner.vue'
import api from '../services/api'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({ name: '', email: '', password: '', password_confirmation: '' })
const error = ref('')
const loading = ref(false)
const isPublicRegistration = ref(true)

const checkRegistrationMode = async () => {
  loading.value = true
  try {
    const res = await api.get('/registration-mode')
    isPublicRegistration.value = res.data.is_public
    if (!res.data.is_public) setTimeout(() => router.push('/login'), 3000)
  } catch { router.push('/login') }
  finally { loading.value = false }
}

const handleRegister = async () => {
  error.value = ''
  if (form.value.password !== form.value.password_confirmation) {
    error.value = 'Passwords do not match'
    return
  }
  loading.value = true
  try {
    const res = await api.post('/register', form.value)
    authStore.user = res.data.user
    authStore.token = res.data.token
    localStorage.setItem('token', res.data.token)
    router.push('/dashboard')
  } catch (err) {
    error.value = err.response?.data?.message || 'Registration failed'
  } finally {
    loading.value = false
  }
}

onMounted(checkRegistrationMode)
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  background: var(--bg-base);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  background-image: radial-gradient(ellipse at 20% 50%, rgba(79,142,247,0.06) 0%, transparent 60%);
}

.auth-card {
  width: 100%;
  max-width: 420px;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 2rem;
  box-shadow: var(--shadow-lg);
}

.auth-brand {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  margin-bottom: 1.75rem;
}

.brand-icon {
  width: 34px;
  height: 34px;
  background: var(--accent);
  border-radius: var(--radius-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
}

.auth-brand span { font-size: 1rem; font-weight: 700; color: var(--text-primary); }
.auth-title { font-size: 1.4rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.35rem; }
.auth-sub { font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.75rem; }

.info-banner {
  display: flex;
  gap: 0.75rem;
  padding: 1rem;
  background: var(--warning-bg);
  border: 1px solid rgba(245,158,11,0.3);
  border-radius: var(--radius-sm);
  color: var(--warning);
  font-size: 0.85rem;
  margin-bottom: 1rem;
}

.info-banner strong { display: block; margin-bottom: 0.25rem; }
.info-banner p { color: var(--text-muted); margin: 0; }

.auth-form { display: flex; flex-direction: column; gap: 1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.4rem; }

.form-group label { font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); }

.form-group input {
  padding: 0.65rem 0.85rem;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  color: var(--text-primary);
  font-size: 0.9rem;
  transition: border-color var(--transition), box-shadow var(--transition);
}

.form-group input::placeholder { color: var(--text-muted); }
.form-group input:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px var(--accent-glow);
}

.form-error {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.65rem 0.85rem;
  background: var(--danger-bg);
  border: 1px solid rgba(239,68,68,0.3);
  border-radius: var(--radius-sm);
  color: #f87171;
  font-size: 0.85rem;
}

.btn-primary {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.7rem;
  background: var(--accent);
  border: none;
  border-radius: var(--radius-sm);
  color: #fff;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: background var(--transition), box-shadow var(--transition);
  margin-top: 0.25rem;
}

.btn-primary:hover:not(:disabled) { background: var(--accent-hover); box-shadow: 0 0 0 3px var(--accent-glow); }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.auth-footer {
  text-align: center;
  font-size: 0.85rem;
  color: var(--text-muted);
  padding-top: 1.25rem;
  margin-top: 1.25rem;
  border-top: 1px solid var(--border);
}

.auth-footer a { color: var(--accent); font-weight: 500; }
</style>
