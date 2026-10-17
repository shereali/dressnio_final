<script setup lang="ts">
// Shows the uploaded photo when there is one that loads, otherwise the drawn garment for that item.
const props = withDefaults(
  defineProps<{
    src?: string | null;
    alt?: string;
    kind?: GarmentKind;
    color?: string;
    fit?: "cover" | "contain";
    lazy?: boolean;
  }>(),
  { src: null, alt: "", kind: "dress", color: "#4D4DDB", fit: "cover", lazy: true }
);

const failed = ref(false);
const img = ref<HTMLImageElement | null>(null);
const usable = computed(() => !failed.value && !isDrawableOnly(props.src));

watch(() => props.src, () => (failed.value = false));
onMounted(() => {
  const el = img.value;
  if (el && el.complete && el.naturalWidth === 0) failed.value = true;
});
</script>

<template>
  <img
    v-if="usable"
    ref="img"
    :src="src!"
    :alt="alt"
    :loading="lazy ? 'lazy' : 'eager'"
    decoding="async"
    class="art-photo"
    :style="{ objectFit: fit }"
    @error="failed = true"
  />
  <GarmentArt v-else :kind="kind" :color="color" :label="alt" class="art-drawn" />
</template>

<style scoped>
.art-photo {
  width: 100%;
  height: 100%;
  object-position: 50% 15%;
}
.art-drawn {
  padding: 6% 12% 2%;
}
</style>
