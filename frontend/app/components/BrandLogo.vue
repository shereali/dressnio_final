<script setup>
// Dressnio mark: a needle drawing a looping thread, with the wordmark.
// Magenta-to-violet gradient matches the brand logo; `dark` lightens it for dark backgrounds.
defineProps({
  word: { type: Boolean, default: true },
  size: { type: Number, default: 40 },
  dark: { type: Boolean, default: false },
});
const uid = useId();
</script>

<template>
  <span class="bl" :class="{ 'bl-dark': dark }" :style="{ '--s': `${size}px` }">
    <svg class="bl-mark" viewBox="0 0 150 105" role="img" aria-label="Dressnio" fill="none">
      <defs>
        <linearGradient :id="`g${uid}`" x1="0" y1="1" x2="1" y2="0">
          <stop offset="0" stop-color="#7a1fd1" />
          <stop offset="1" stop-color="#e01cf2" />
        </linearGradient>
      </defs>
      <path d="M8 18C42 4 82 6 98 24c14 17 8 46-6 62-8 9-20 4-18-10 3-17 24-30 72-26" :stroke="`url(#g${uid})`" stroke-width="5.5" stroke-linecap="round" />
      <path d="M96 5c4-3 9-4 12-3 3 8-1 20-7 31L21 100c-3 3-6 4-9 4Z" :fill="`url(#g${uid})`" />
      <ellipse cx="104" cy="17" rx="2.6" ry="9" transform="rotate(24 104 17)" fill="var(--bl-eye, #fff)" />
    </svg>
    <span v-if="word" class="bl-word">DRESSNIO</span>
  </span>
</template>

<style scoped>
.bl {
  display: inline-flex;
  align-items: center;
  gap: calc(var(--s) * 0.3);
  line-height: 1;
}
.bl-mark {
  width: calc(var(--s) * 1.2);
  height: auto;
  flex-shrink: 0;
}
.bl-word {
  font-family: var(--font-body);
  font-size: calc(var(--s) * 0.5);
  font-weight: 500;
  letter-spacing: 0.18em;
  background: linear-gradient(100deg, #7a1fd1, #c21ce0);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
.bl-dark .bl-word {
  background: none;
  color: #fff;
}
</style>
