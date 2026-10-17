<script setup>
// Section heading. `class` containing "text-white" switches to the light variant (kept for existing pages).
const props = defineProps({
  title: String,
  subtitle: String,
  class: String,
  align: { type: String, default: "center" },
  actionLabel: String,
  actionTo: String,
});

const light = computed(() => !!props.class && props.class.includes("text-white"));
</script>

<template>
  <header
    class="headline"
    :class="[props.class, { 'is-left': align === 'left', 'is-light': light }]"
  >
    <div class="headline-text">
      <h2 class="headline-title">{{ title }}</h2>
      <p v-if="subtitle" class="headline-sub">{{ subtitle }}</p>
    </div>
    <NuxtLink v-if="actionTo && actionLabel" :to="actionTo" class="headline-action">
      {{ actionLabel }}
      <Icon name="solar:arrow-right-linear" class="w-4 h-4" />
    </NuxtLink>
  </header>
</template>

<style scoped>
.headline {
  display: flex;
  align-items: flex-end;
  justify-content: center;
  text-align: center;
  gap: 1.5rem;
}
.headline.is-left {
  flex-wrap: wrap;
  justify-content: space-between;
  text-align: left;
}
.headline-title {
  font-size: clamp(1.9rem, 3.6vw, 3rem);
  line-height: 1.1;
  font-weight: 600;
  color: var(--brand-deep);
  text-wrap: balance;
}
.headline-sub {
  margin-top: 0.7rem;
  max-width: 52ch;
  color: var(--ink-soft);
  font-size: 1.02rem;
  line-height: 1.6;
}
.headline:not(.is-left) .headline-sub {
  margin-left: auto;
  margin-right: auto;
}
.headline-action {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  flex-shrink: 0;
  padding-bottom: 0.35rem;
  font-weight: 600;
  color: var(--brand);
  border-bottom: 1.5px solid currentColor;
  line-height: 1.2;
}
.headline-action:hover {
  color: var(--brand-deep);
}
.is-light .headline-title {
  color: #fff;
}
.is-light .headline-sub {
  color: rgba(255, 255, 255, 0.82);
}
.is-light .headline-action {
  color: #fff;
}
</style>
