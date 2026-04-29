<template>
  <div class="auth-page">
    <div class="auth-card">
      <!-- Brand -->
      <div class="auth-brand">
        <div class="brand-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
          </svg>
        </div>
        <span>WorkflowOS</span>
      </div>

      <h1 class="auth-title">Welcome back</h1>
      <p class="auth-sub">Sign in to your account to continue</p>

      <form @submit.prevent="handleLogin" class="auth-form">
        <div class="form-group">
          <label for="email">Email address</label>
          <input id="email" v-model="email" type="email" required placeholder="you@example.com" autocomplete="email" />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input id="password" v-model="password" type="password" required placeholder="••••••••" autocomplete="current-password" />
        </div>

        <div v-if="error" class="form-error">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          {{ error }}
        </div>

        <button type="submit" class="btn-primary" :disabled="loading">
          <LoadingSpinner v-if="loading" size="small" />
          <span v-else>Sign In</span>
        </button>
      </form>

      <div class="auth-divider"><span>Demo credentials</span></div>
      <div class="demo-creds">
        <div class="cred-item" @click="fillDemo('admin')">
          <div class="cred-badge admin">Admin</div>
          <div class="cred-info">
            <div>admin@example.com</div>
            <div class="cred-pass">password</div>
          </div>
        </div>
        <div class="cred-item" @click="fillDemo('user')">
          <div class="cred-badge user">User</div>
          <div class="cred-info">
            <div>user@example.com</div>
            <div class="cred-pass">password</div>
          </div>
        </div>
      </div>

      <div v-if="showRegisterLink" class="auth-footer">
        Don't have an account?
        <router-link to="/register">Create one</router-link>
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

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)
const showRegisterLink = ref(false)

const fillDemo = (role) => {
  email.value = role === 'admin' ? 'admin@example.com' : 'user@example.com'
  password.value = 'password'
}

const checkRegistrationMode = async () => {
  try {
    const res = await api.get('/registration-mode')
    showRegisterLink.value = res.data.is_public
  } catch {}
}

const handleLogin = async () => {
  error.value = ''
  loading.value = true
  try {
    await authStore.login(email.value, password.value)
    router.push('/dashboard')
  } catch (err) {
    error.value = err.response?.data?.message || 'Invalid credentials'
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
  background-image: radial-gradient(ellipse at 20% 50%, rgba(79,142,247,0.06) 0%, transparent 60%),
                    radial-gradient(ellipse at 80% 20%, rgba(168,85,247,0.05) 0%, transparent 50%);
}

.auth-card {
  width: 100%;
  max-width: 400px;
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

.auth-brand span {
  font-size: 1rem;
  font-weight: 700;
  color: var(--text-primary);
}

.auth-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.35rem;
}

.auth-sub {
  font-size: 0.875rem;
  color: var(--text-muted);
  margin-bottom: 1.75rem;
}

.auth-form { display: flex; flex-direction: column; gap: 1rem; }

.form-group { display: flex; flex-direction: column; gap: 0.4rem; }

.form-group label {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-secondary);
}

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

.btn-primary:hover:not(:disabled) {
  background: var(--accent-hover);
  box-shadow: 0 0 0 3px var(--accent-glow);
}

.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.auth-divider {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin: 1.5rem 0 0.75rem;
  color: var(--text-muted);
  font-size: 0.75rem;
}

.auth-divider::before, .auth-divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--border);
}

.demo-creds {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
}

.cred-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.65rem 0.85rem;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: border-color var(--transition), background var(--transition);
}

.cred-item:hover {
  border-color: var(--accent);
  background: var(--bg-hover);
}

.cred-badge {
  font-size: 0.65rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 2px 8px;
  border-radius: 20px;
  flex-shrink: 0;
}

.cred-badge.admin { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
.cred-badge.user  { background: var(--accent-glow); color: var(--accent); border: 1px solid var(--accent-border); }

.cred-info { font-size: 0.8rem; color: var(--text-secondary); }
.cred-pass { font-size: 0.75rem; color: var(--text-muted); margin-top: 1px; }

.auth-footer {
  text-align: center;
  font-size: 0.85rem;
  color: var(--text-muted);
  padding-top: 1rem;
  border-top: 1px solid var(--border);
}

.auth-footer a { color: var(--accent); font-weight: 500; }
.auth-footer a:hover { color: var(--accent-hover); }
</style>
