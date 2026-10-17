<script setup>
// Spotlight: the one product flagged "highlight" in admin. Add to bag works in place.
import { useCartStore } from "~/stores/cart";

const props = defineProps({
  highlight: { type: Object, default: null },
});

const cartStore = useCartStore();
const snackbar = useSnackbar();

// Falls back to the dedicated endpoint when the homepage payload has no highlight
const { data } = useApiFetch("/client/highlight-product", { key: "highlight-product", lazy: true });
const product = computed(() => props.highlight || data.value?.data || null);

const sizes = computed(() => (product.value?.sizes || []).map((s) => s.standard || s.name).filter(Boolean));
const colors = computed(() =>
  (product.value?.colors || [])
    .map((c) => c.color_code)
    .filter((c) => c && /^#?[0-9a-f]{3,6}$/i.test(String(c).replace("#", "")))
    .map((c) => (String(c).startsWith("#") ? c : `#${c}`))
);

const size = ref("");
const color = ref("");
const qty = ref(1);

watch(
  product,
  (p) => {
    if (p && !color.value && colors.value.length) color.value = colors.value[0];
  },
  { immediate: true }
);

const info = computed(() => priceInfo(product.value || {}, size.value));
const kind = computed(() => garmentKindFor(product.value?.categories?.[0]?.name, product.value?.name));
const artColor = computed(() => color.value || toneHexAt(Number(product.value?.id) || 3));
const href = computed(() => `/product/${product.value?.slug || product.value?.id}`);
const plainText = computed(() =>
  (product.value?.description || "").replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim()
);

const add = () => {
  const p = product.value;
  if (!p) return;
  if (sizes.value.length && !size.value) {
    snackbar.add({ type: "error", text: "Choose a size first" });
    return;
  }
  const ok = cartStore.addToCart({
    id: p.id,
    name: p.name,
    image: p.image || null,
    size: sizes.value.length ? size.value : null,
    color: color.value || null,
    quantity: qty.value,
    price: info.value.sale,
    is_size_visible: sizes.value.length > 0,
  });
  if (ok) snackbar.add({ type: "success", text: `${p.name} added to your bag` });
};
</script>

<template>
  <section v-if="product && product.name" class="pm-section hp" aria-labelledby="hp-title">
    <div class="pm-wrap hp-grid">
      <div class="hp-stage">
        <div class="hp-arch arch" :style="{ background: toneFromHex(artColor).tintDeep }">
          <ArtImage :src="product.image" :alt="product.name" :kind="kind" :color="artColor" />
        </div>
        <div class="hp-floor"></div>
      </div>

      <div class="hp-copy">
        <p class="hp-kicker">Featured piece</p>
        <h2 id="hp-title" class="hp-title">{{ product.name }}</h2>
        <p v-if="plainText" class="hp-desc">{{ plainText }}</p>

        <div class="hp-price">
          <span class="hp-sale">{{ formatBDT(info.sale * qty) }}</span>
          <span v-if="info.percent > 0" class="hp-list">{{ formatBDT(info.list * qty) }}</span>
          <span v-if="info.percent > 0" class="hp-off">{{ Math.round(info.percent) }}% off</span>
        </div>

        <div v-if="colors.length > 1" class="hp-row">
          <span class="hp-label">Colour</span>
          <div class="hp-sw" role="radiogroup" aria-label="Colour">
            <button
              v-for="c in colors"
              :key="c"
              type="button"
              role="radio"
              :aria-checked="color === c"
              :aria-label="`Colour ${c}`"
              :class="{ on: color === c }"
              :style="{ '--sw': c }"
              @click="color = c"
            ></button>
          </div>
        </div>

        <div v-if="sizes.length" class="hp-row">
          <span class="hp-label">Size</span>
          <div class="hp-sizes" role="radiogroup" aria-label="Size">
            <button v-for="s in sizes" :key="s" type="button" role="radio" :aria-checked="size === s" :class="{ on: size === s }" @click="size = s">{{ s }}</button>
          </div>
        </div>

        <div class="hp-actions">
          <div class="hp-qty" aria-label="Quantity">
            <button type="button" aria-label="Decrease quantity" @click="qty = Math.max(1, qty - 1)"><Icon name="solar:minus-circle-linear" class="w-5 h-5" /></button>
            <span aria-live="polite">{{ qty }}</span>
            <button type="button" aria-label="Increase quantity" @click="qty++"><Icon name="solar:add-circle-linear" class="w-5 h-5" /></button>
          </div>
          <button type="button" class="pm-btn pm-btn-primary hp-add" @click="add">
            <Icon name="solar:bag-3-linear" class="w-5 h-5" />
            Add to bag
          </button>
          <NuxtLink :to="href" class="pm-btn pm-btn-ghost">View details</NuxtLink>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.hp {
  background: #fff;
}
.hp-grid {
  display: grid;
  gap: 2.5rem;
  align-items: center;
}
@media (min-width: 900px) {
  .hp-grid {
    grid-template-columns: 0.9fr 1.1fr;
    gap: 5rem;
  }
}
.hp-stage {
  position: relative;
  max-width: 440px;
  width: 100%;
  margin: 0 auto;
}
.hp-arch {
  aspect-ratio: 4 / 5;
}
.hp-arch :deep(svg) {
  padding: 14% 12% 4%;
}
.hp-floor {
  height: 2px;
  background: var(--brand-deep);
  opacity: 0.35;
}
.hp-kicker {
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--brand);
}
.hp-title {
  margin-top: 0.5rem;
  font-size: clamp(2rem, 3.6vw, 3.1rem);
  line-height: 1.1;
  font-weight: 600;
  color: var(--brand-deep);
  text-wrap: balance;
}
.hp-desc {
  margin-top: 1rem;
  max-width: 52ch;
  line-height: 1.7;
  color: var(--ink-soft);
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.hp-price {
  display: flex;
  align-items: baseline;
  flex-wrap: wrap;
  gap: 0.7rem;
  margin-top: 1.5rem;
}
.hp-sale {
  font-family: var(--font-display);
  font-size: 2.2rem;
  font-weight: 600;
  color: var(--brand);
}
.hp-list {
  color: #8b8ba3;
  text-decoration: line-through;
}
.hp-off {
  padding: 0.2rem 0.6rem;
  border-radius: 999px;
  background: #fde7ee;
  color: #c0285a;
  font-size: 0.82rem;
  font-weight: 600;
}
.hp-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-top: 1.3rem;
}
.hp-label {
  width: 3.5rem;
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--ink-soft);
}
.hp-sw,
.hp-sizes {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}
.hp-sw button {
  width: 1.9rem;
  height: 1.9rem;
  border-radius: 999px;
  background: var(--sw);
  box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.12);
}
.hp-sw button.on {
  outline: 2px solid var(--brand);
  outline-offset: 3px;
}
.hp-sizes button {
  min-width: 2.8rem;
  height: 2.6rem;
  padding: 0 0.8rem;
  border: 1.5px solid var(--line);
  border-radius: 10px;
  font-weight: 500;
}
.hp-sizes button:hover {
  border-color: var(--brand);
}
.hp-sizes button.on {
  background: var(--brand);
  border-color: var(--brand);
  color: #fff;
}
.hp-actions {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.8rem;
  margin-top: 2rem;
}
.hp-qty {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  height: 3rem;
  padding: 0 0.4rem;
  border: 1.5px solid var(--line);
  border-radius: 999px;
}
.hp-qty button {
  display: grid;
  place-items: center;
  width: 2.3rem;
  height: 2.3rem;
  border-radius: 999px;
  color: var(--brand);
}
.hp-qty span {
  min-width: 1.6rem;
  text-align: center;
  font-weight: 700;
}
</style>
