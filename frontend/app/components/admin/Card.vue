<template>
  <div 
    class="relative overflow-hidden rounded-2xl transition-all duration-300 hover:scale-[1.02] hover:shadow-2xl group border border-transparent dark:border-gray-800"
    :class="variantClasses"
  >
    <!-- Background Pattern/Overlay -->
    <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
    
    <div class="p-6 relative z-10">
      <slot></slot>
    </div>

    <!-- Decorative Soft Glow -->
    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-500"></div>
  </div>
</template>

<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'brand' // 'brand', 'emerald', 'blue', 'orange', 'purple'
  }
});

const variantClasses = computed(() => {
  const variants = {
    indigo: 'bg-gradient-to-br from-indigo-500 to-indigo-700 text-white shadow-lg shadow-indigo-500/20',
    emerald: 'bg-gradient-to-br from-emerald-500 to-emerald-700 text-white shadow-lg shadow-emerald-500/20',
    blue: 'bg-gradient-to-br from-blue-500 to-blue-700 text-white shadow-lg shadow-blue-500/20',
    orange: 'bg-gradient-to-br from-orange-500 to-orange-700 text-white shadow-lg shadow-orange-500/20',
    purple: 'bg-gradient-to-br from-purple-500 to-purple-700 text-white shadow-lg shadow-purple-500/20',
  };
  return variants[props.variant] || variants.indigo;
});
</script>

<style scoped>
</style>
