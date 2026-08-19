export const useSnackbar = () => {
  const messages = useState('snackbar_messages', () => [])

  const add = ({ type, text, duration = 3000 }) => {
    const id = Date.now()
    messages.value.push({
      id,
      type,
      text,
      duration
    })

    setTimeout(() => {
      remove(id)
    }, duration)
  }

  const remove = (id) => {
    const index = messages.value.findIndex(m => m.id === id)
    if (index !== -1) {
      messages.value.splice(index, 1)
    }
  }

  return {
    messages,
    add,
    remove
  }
}
