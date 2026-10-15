<template>
  <div class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 max-w-sm pointer-events-none">
    <div
      v-for="msg in snackbar.messages.value"
      :key="msg.id"
      class="pointer-events-auto p-4 rounded-xl shadow-xl flex items-center justify-between gap-3 text-sm font-semibold transition-all transform duration-300 ease-out"
      :class="[
        msg.type === 'error' ? 'bg-rose-600 text-white' :
        msg.type === 'info' ? 'bg-indigo-600 text-white' :
        'bg-emerald-600 text-white'
      ]"
    >
      <div class="flex items-center gap-2">
        <Icon
          :name="
            msg.type === 'error' ? 'solar:danger-circle-bold' :
            msg.type === 'info' ? 'solar:info-circle-bold' :
            'solar:check-circle-bold'
          "
          class="w-5 h-5 flex-shrink-0"
        />
        <span>{{ msg.text }}</span>
      </div>
      <button
        @click="snackbar.remove(msg.id)"
        class="opacity-75 hover:opacity-100 p-1"
      >
        <Icon name="solar:close-circle-bold" class="w-4 h-4" />
      </button>
    </div>
  </div>
</template>

<script setup>
const snackbar = useSnackbar();
</script>
