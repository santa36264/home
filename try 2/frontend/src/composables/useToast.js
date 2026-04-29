import { ref } from 'vue'

const toasts = ref([])

export function useToast() {
  const show = (type, title, message = '', duration = 3000) => {
    const id = Date.now()
    toasts.value.push({ id, type, title, message, duration })
    
    if (duration > 0) {
      setTimeout(() => {
        remove(id)
      }, duration + 300)
    }
  }

  const remove = (id) => {
    const index = toasts.value.findIndex(t => t.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  const success = (title, message = '') => show('success', title, message)
  const error = (title, message = '') => show('error', title, message)
  const warning = (title, message = '') => show('warning', title, message)
  const info = (title, message = '') => show('info', title, message)

  return {
    toasts,
    show,
    remove,
    success,
    error,
    warning,
    info
  }
}
