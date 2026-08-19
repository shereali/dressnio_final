<template>
  <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none">
    <TransitionGroup 
      name="snackbar-fade" 
      tag="div" 
      class="flex flex-col gap-2"
    >
      <div
        v-for="msg in messages"
        :key="msg.id"
        class="pointer-events-auto min-w-[300px] px-6 py-4 rounded-lg shadow-2xl flex items-center justify-between border-l-4 backdrop-blur-md animate-slide-in"
        :class="[
          msg.type === 'success' ? 'bg-emerald-50/90 border-emerald-500 text-emerald-900' : 
          msg.type === 'error' ? 'bg-rose-50/90 border-rose-500 text-rose-900' : 
          msg.type === 'warning' ? 'bg-amber-50/90 border-amber-500 text-amber-900' : 
          'bg-slate-50/90 border-slate-500 text-slate-900'
        ]"
      >
        <div class="flex items-center gap-3">
          <Icon 
            v-if="msg.type === 'success'" 
            name="heroicons:check-circle-20-solid" 
            class="text-emerald-500 text-xl" 
          />
          <Icon 
            v-else-if="msg.type === 'error'" 
            name="heroicons:exclamation-circle-20-solid" 
            class="text-rose-500 text-xl" 
          />
          <Icon 
            v-else-if="msg.type === 'warning'" 
            name="heroicons:exclamation-triangle-20-solid" 
            class="text-amber-500 text-xl" 
          />
          <Icon 
            v-else 
            name="heroicons:information-circle-20-solid" 
            class="text-slate-500 text-xl" 
          />
          <span class="text-sm font-medium">{{ msg.text }}</span>
        </div>
        <button 
          @click="remove(msg.id)" 
          class="ml-4 hover:opacity-70 transition-opacity"
        >
          <Icon name="heroicons:x-mark-20-solid" class="text-lg opacity-40 hover:opacity-100" />
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<script setup>
const { messages, remove } = useSnackbar()
</script>

<style scoped>
.snackbar-fade-enter-active,
.snackbar-fade-leave-active {
  transition: all 0.4s cubic-bezier(0.18, 0.89, 0.32, 1.28);
}

.snackbar-fade-enter-from {
  opacity: 0;
  transform: translateX(30px) scale(0.9);
}

.snackbar-fade-leave-to {
  opacity: 0;
  transform: translateX(100px);
}

@keyframes slide-in {
  from {
    opacity: 0;
    transform: translateX(30px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate-slide-in {
  animation: slide-in 0.4s ease-out;
}
</style>
