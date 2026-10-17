<script setup>
import { useCartStore } from "~/stores/cart";
import { useWishlistStore } from "~/stores/wishlist";

const props = defineProps({
  product: { type: Object, required: true },
});

const cartStore = useCartStore();
const wishlist = useWishlistStore();
const snackbar = useSnackbar();

const sizes = computed(() =>
  (props.product.sizes || []).map((s) => s.standard || s.name).filter(Boolean)
);
const colors = computed(() =>
  (props.product.colors || [])
    .map((c) => ({ code: c.color_code || c.name, id: c.id }))
    .filter((c) => c.code && /^#?[0-9a-f]{3,6}$/i.test(String(c.code).replace("#", "")))
    .map((c) => ({ ...c, code: String(c.code).startsWith("#") ? c.code : `#${c.code}` }))
);

const size = ref("");
const color = ref("");
watchEffect(() => {
  if (!color.value && colors.value.length) color.value = colors.value[0].code;
});

const price = computed(() => priceInfo(props.product, size.value));
const photo = computed(
  () => props.product.image || props.product.media?.[0]?.media_url || props.product.media?.[0]?.url || null
);
const kind = computed(() =>
  garmentKindFor(props.product.categories?.[0]?.name, props.product.cloth_type, props.product.name)
);
const artColor = computed(() => color.value || toneHexAt(Number(props.product.id) || 0));
const soldOut = computed(
  () => props.product.stock_available === 0 || props.product.stock_status === "out_of_stock"
);
const href = computed(() => `/product/${props.product.slug || props.product.id}`);
const saved = computed(() => wishlist.ids.includes(props.product.id));

const badge = computed(() => {
  if (soldOut.value) return { text: "Sold out", cls: "b-out" };
  if (price.value.percent > 0) return { text: `${Math.round(price.value.percent)}% off`, cls: "b-sale" };
  if (props.product.best_seller) return { text: "Best seller", cls: "b-best" };
  if (props.product.new_arrival) return { text: "New", cls: "b-new" };
  return null;
});

const toggleSave = async () => {
  const added = await wishlist.toggle(props.product.id);
  snackbar.add({ type: "success", text: added ? "Saved to your wishlist" : "Removed from your wishlist" });
};

const addToBag = () => {
  const p = props.product;
  if (soldOut.value) return;
  const needsSize = !!p.is_size_visible && sizes.value.length > 0;
  if (needsSize && !size.value) {
    snackbar.add({ type: "error", text: "Choose a size first" });
    return;
  }
  const ok = cartStore.addToCart({
    id: p.id,
    name: p.name,
    image: photo.value,
    size: needsSize ? size.value : null,
    color: color.value || null,
    quantity: 1,
    price: price.value.sale,
    is_size_visible: needsSize,
  });
  if (ok) snackbar.add({ type: "success", text: `${p.name} added to your bag` });
};
</script>

<template>
  <article v-if="product" class="pc">
    <div class="pc-media">
      <NuxtLink :to="href" class="pc-photo" :style="{ background: toneFromHex(artColor).tint }" :aria-label="product.name">
        <ArtImage :src="photo" :alt="product.name" :kind="kind" :color="artColor" class="pc-img" />
      </NuxtLink>
      <span v-if="badge" class="pc-badge" :class="badge.cls">{{ badge.text }}</span>
      <button
        type="button"
        class="pc-heart"
        :class="{ on: saved }"
        :aria-pressed="saved"
        :aria-label="saved ? 'Remove from wishlist' : 'Save to wishlist'"
        @click="toggleSave"
      >
        <Icon :name="saved ? 'solar:heart-bold' : 'solar:heart-linear'" class="w-5 h-5" />
      </button>
    </div>

    <div class="pc-body">
      <p v-if="product.categories?.[0]?.name" class="pc-cat">{{ product.categories[0].name }}</p>
      <NuxtLink :to="href" class="pc-title">{{ product.name }}</NuxtLink>

      <div class="pc-price">
        <span class="pc-sale">{{ formatBDT(price.sale) }}</span>
        <span v-if="price.percent > 0" class="pc-list">{{ formatBDT(price.list) }}</span>
      </div>

      <div v-if="colors.length > 1" class="pc-swatches" role="radiogroup" aria-label="Colour">
        <button
          v-for="c in colors"
          :key="c.code"
          type="button"
          role="radio"
          :aria-checked="color === c.code"
          :aria-label="`Colour ${c.code}`"
          class="pc-swatch"
          :class="{ on: color === c.code }"
          :style="{ '--sw': c.code }"
          @click="color = c.code"
        ></button>
      </div>

      <div v-if="sizes.length" class="pc-sizes" role="radiogroup" aria-label="Size">
        <button
          v-for="s in sizes"
          :key="s"
          type="button"
          role="radio"
          :aria-checked="size === s"
          class="pc-size"
          :class="{ on: size === s }"
          @click="size = s"
        >
          {{ s }}
        </button>
      </div>

      <button type="button" class="pc-add" :disabled="soldOut" @click="addToBag">
        <Icon name="solar:bag-3-linear" class="w-[1.1rem] h-[1.1rem]" />
        {{ soldOut ? "Sold out" : "Add to bag" }}
      </button>
    </div>
  </article>
</template>

<style scoped>
.pc {
  background: #fff;
  border-radius: 20px;
  border: 1px solid var(--line);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  text-align: left;
  transition: box-shadow 0.3s ease, transform 0.3s ease;
}
.pc:hover {
  box-shadow: 0 22px 44px -22px rgba(21, 21, 82, 0.35);
  transform: translateY(-3px);
}
.pc-media {
  position: relative;
}
.pc-photo {
  display: block;
  aspect-ratio: 4 / 5;
  overflow: hidden;
  transition: background-color 0.3s;
}
.pc-img {
  transition: transform 0.5s cubic-bezier(0.2, 0.7, 0.2, 1);
}
.pc:hover .pc-img {
  transform: scale(1.04);
}
.pc-badge {
  position: absolute;
  top: 0.8rem;
  left: 0.8rem;
  padding: 0.3rem 0.65rem;
  border-radius: 999px;
  font-size: 0.74rem;
  font-weight: 600;
  line-height: 1.2;
}
.b-new {
  background: var(--brand-deep);
  color: #fff;
}
.b-best {
  background: #fff;
  color: var(--brand);
  box-shadow: 0 2px 8px rgba(21, 21, 82, 0.12);
}
.b-sale {
  background: #e0396b;
  color: #fff;
}
.b-out {
  background: #6b6b80;
  color: #fff;
}
.pc-heart {
  position: absolute;
  top: 0.6rem;
  right: 0.6rem;
  width: 2.5rem;
  height: 2.5rem;
  display: grid;
  place-items: center;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.92);
  color: var(--ink-soft);
  transition: color 0.2s, transform 0.2s;
}
.pc-heart:hover {
  color: #e0396b;
  transform: scale(1.08);
}
.pc-heart.on {
  color: #e0396b;
}
.pc-body {
  padding: 1rem 1.1rem 1.15rem;
  display: flex;
  flex-direction: column;
  gap: 0.55rem;
  flex: 1;
}
.pc-cat {
  font-size: 0.8rem;
  color: var(--ink-soft);
}
.pc-title {
  font-family: var(--font-display);
  font-size: 1.08rem;
  line-height: 1.3;
  font-weight: 500;
  color: var(--brand-deep);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.pc-title:hover {
  color: var(--brand);
}
.pc-price {
  display: flex;
  align-items: baseline;
  gap: 0.55rem;
}
.pc-sale {
  font-weight: 700;
  font-size: 1.1rem;
  color: var(--brand);
}
.pc-list {
  font-size: 0.88rem;
  color: #8b8ba3;
  text-decoration: line-through;
}
.pc-swatches {
  display: flex;
  gap: 0.45rem;
  flex-wrap: wrap;
}
.pc-swatch {
  width: 1.35rem;
  height: 1.35rem;
  border-radius: 999px;
  background: var(--sw);
  box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.12);
  transition: transform 0.15s;
}
.pc-swatch.on {
  outline: 2px solid var(--brand);
  outline-offset: 2px;
}
.pc-swatch:hover {
  transform: scale(1.12);
}
.pc-sizes {
  display: flex;
  gap: 0.4rem;
  flex-wrap: wrap;
}
.pc-size {
  min-width: 2.2rem;
  height: 2.1rem;
  padding: 0 0.6rem;
  border-radius: 8px;
  border: 1px solid var(--line);
  font-size: 0.82rem;
  font-weight: 500;
  color: var(--ink);
  transition: border-color 0.15s, background-color 0.15s, color 0.15s;
}
.pc-size:hover {
  border-color: var(--brand);
}
.pc-size.on {
  background: var(--brand);
  border-color: var(--brand);
  color: #fff;
}
.pc-add {
  margin-top: auto;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.55rem;
  min-height: 2.75rem;
  border-radius: 12px;
  background: var(--brand-tint);
  color: var(--brand);
  font-weight: 600;
  font-size: 0.92rem;
  transition: background-color 0.2s, color 0.2s;
}
.pc-add:hover:not(:disabled) {
  background: var(--brand);
  color: #fff;
}
.pc-add:disabled {
  background: #ececf3;
  color: #9a9ab0;
  cursor: not-allowed;
}
</style>
