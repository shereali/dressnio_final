<template>
  <header class="text-center" :class="class">
    <h2 
      class="text-3xl md:text-4xl lg:text-5xl font-extrabold uppercase tracking-tight mb-3" 
      :class="isWhiteText ? 'text-white' : 'text-gray-900'"
    >
      {{ title }}
    </h2>
    <div class="flex items-center justify-center gap-3 mb-2">
      <div 
        class="h-0.5 w-12 bg-gradient-to-r" 
        :class="isWhiteText ? 'from-transparent to-white' : 'from-transparent to-brand'"
      ></div>
      <div 
        class="h-1 w-1 rounded-full" 
        :class="isWhiteText ? 'bg-white' : 'bg-brand'"
      ></div>
      <div 
        class="h-0.5 w-12 bg-gradient-to-l" 
        :class="isWhiteText ? 'from-transparent to-white' : 'from-transparent to-brand'"
      ></div>
    </div>
    <h3 
      class="text-base md:text-lg font-light italic" 
      :class="isWhiteText ? 'text-white/90' : 'text-gray-600'"
    >
      {{ subtitle }}
    </h3>
  </header>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps(['title', 'subtitle', 'class']);

const isWhiteText = computed(() => {
  return props.class && props.class.includes('text-white');
});
</script>
