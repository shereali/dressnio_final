<script setup lang="ts">
// Draws one garment as a catalogue-style illustration. Used for category, style and product artwork.
const props = withDefaults(
  defineProps<{
    kind?: GarmentKind;
    color?: string;
    pattern?: "floral" | "dots" | "stripes" | "none";
    hanger?: boolean;
    label?: string;
  }>(),
  { kind: "dress", color: "#4D4DDB", pattern: undefined, hanger: undefined, label: "" }
);

const uid = useId().replace(/[^a-zA-Z0-9]/g, "");
const garment = computed(() => GARMENTS[props.kind] || GARMENTS.dress);
const tone = computed(() => toneFromHex(props.color));
const pattern = computed(() => props.pattern ?? garment.value.pattern);
const showHanger = computed(() => props.hanger ?? garment.value.hanger);
const fill = (key: string) => (key === "main" ? `url(#g${uid})` : (tone.value as any)[key]);
const bodyLayer = computed(() => garment.value.layers.find((l) => l.body));
</script>

<template>
  <svg
    viewBox="0 0 240 330"
    xmlns="http://www.w3.org/2000/svg"
    role="img"
    :aria-label="label || garment.label"
    class="garment-art"
  >
    <defs>
      <linearGradient :id="`g${uid}`" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0" :stop-color="tone.main" />
        <stop offset="1" :stop-color="tone.shade" />
      </linearGradient>
      <linearGradient :id="`s${uid}`" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0" stop-color="#fff" stop-opacity=".18" />
        <stop offset="1" stop-color="#000" stop-opacity=".1" />
      </linearGradient>
      <pattern v-if="pattern === 'floral'" :id="`p${uid}`" width="26" height="26" patternUnits="userSpaceOnUse">
        <g fill="#fff" opacity=".34">
          <circle cx="13" cy="13" r="2" />
          <circle cx="13" cy="7" r="2.6" /><circle cx="13" cy="19" r="2.6" />
          <circle cx="7" cy="13" r="2.6" /><circle cx="19" cy="13" r="2.6" />
        </g>
        <circle cx="0" cy="0" r="1.6" fill="#fff" opacity=".28" /><circle cx="26" cy="26" r="1.6" fill="#fff" opacity=".28" />
      </pattern>
      <pattern v-else-if="pattern === 'dots'" :id="`p${uid}`" width="14" height="14" patternUnits="userSpaceOnUse">
        <circle cx="7" cy="7" r="1.7" fill="#fff" opacity=".4" />
      </pattern>
      <pattern v-else-if="pattern === 'stripes'" :id="`p${uid}`" width="9" height="9" patternUnits="userSpaceOnUse" patternTransform="rotate(32)">
        <rect width="3" height="9" fill="#fff" opacity=".24" />
      </pattern>
      <clipPath v-if="bodyLayer" :id="`c${uid}`"><path :d="bodyLayer.d" /></clipPath>
    </defs>

    <ellipse cx="120" cy="324" rx="74" ry="4.5" fill="#151552" opacity=".12" />

    <g v-if="showHanger" fill="none" stroke="#A7ACC2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
      <path d="M120 38 V30 C120 24 128 22 128 15 C128 8 118 7 115 12" />
      <path d="M120 38 L48 68 C42 71 44 76 51 76 H189 C196 76 198 71 192 68 Z" />
    </g>

    <template v-for="(layer, i) in garment.layers" :key="i">
      <path :d="layer.d" :fill="fill(layer.f)" />
      <path v-if="layer.pat && pattern !== 'none'" :d="layer.d" :fill="`url(#p${uid})`" />
      <path v-if="layer.f !== 'skin' && layer.f !== 'form'" :d="layer.d" :fill="`url(#s${uid})`" />
    </template>

    <g v-if="bodyLayer" :clip-path="`url(#c${uid})`">
      <rect v-for="(b, i) in garment.bands" :key="i" x="0" :y="b.y" width="240" :height="b.h" :fill="fill(b.f)" />
      <g v-for="(b, i) in garment.bands.filter((x) => x.f === 'trim' && x.h > 8)" :key="`bd${i}`" fill="#fff" opacity=".55">
        <circle v-for="n in 20" :key="n" :cx="n * 12" :cy="b.y + b.h / 2" r="1.6" />
      </g>
    </g>

    <g fill="none" stroke-linecap="round" stroke-linejoin="round">
      <path v-for="(d, i) in garment.folds" :key="`f${i}`" :d="d" :stroke="tone.dark" stroke-width="2.4" opacity=".22" />
      <path v-for="(t, i) in garment.trims" :key="`t${i}`" :d="t.d" :stroke="fill(t.f)" :stroke-width="t.w" />
    </g>
    <circle v-for="(d, i) in garment.dots" :key="`d${i}`" :cx="d[0]" :cy="d[1]" :r="d[2]" :fill="tone.trim" />
  </svg>
</template>

<style scoped>
.garment-art {
  display: block;
  width: 100%;
  height: 100%;
}
</style>
