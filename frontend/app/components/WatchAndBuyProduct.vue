<script setup>
import VuePlayerVideo from "vue3-player-video";
import { useCartStore } from "~/stores/cart";

const props = defineProps({
  product: { type: Object, default: () => ({}) },
  media: { type: String, default: "" },
  itemIndex: { type: Number, default: 0 },
});

const cartStore = useCartStore();
const snackbar = useSnackbar();

const sizes = computed(() => (props.product.sizes || []).map((s) => s.standard || s.name).filter(Boolean));
const size = ref("");
const picking = ref(false);

const info = computed(() => priceInfo(props.product, size.value));
const isVideo = computed(() => /\.(mp4|webm)(\?|$)/i.test(props.media || ""));
const photo = computed(() => (!isVideo.value && props.media) || props.product.image || null);
const kind = computed(() => garmentKindFor(props.product.categories?.[0]?.name, props.product.name));
const color = computed(() => toneHexAt(props.itemIndex + 1));
const href = computed(() => `/product/${props.product.slug || props.product.id}`);

const onAdd = () => {
  if (sizes.value.length && !picking.value) picking.value = true;
  else add();
};

const add = () => {
  const p = props.product;
  if (sizes.value.length && !size.value) {
    picking.value = true;
    snackbar.add({ type: "error", text: "Choose a size first" });
    return;
  }
  const ok = cartStore.addToCart({
    id: p.id,
    name: p.name,
    image: props.product.image || null,
    size: sizes.value.length ? size.value : null,
    color: p.colors?.[0]?.color_code || null,
    quantity: 1,
    price: info.value.sale,
    is_size_visible: sizes.value.length > 0,
  });
  if (ok) {
    picking.value = false;
    snackbar.add({ type: "success", text: `${p.name} added to your bag` });
  }
};
</script>

<template>
  <article class="wbp">
    <div class="wbp-stage" :style="{ background: toneFromHex(color).tint }">
      <VuePlayerVideo v-if="isVideo" theme="gradient" :controls="true" :src="media" class="wbp-video" />
      <ArtImage v-else :src="photo" :alt="product.name" :kind="kind" :color="color" class="wbp-art" />
    </div>

    <div class="wbp-bar">
      <div class="wbp-info">
        <NuxtLink :to="href" class="wbp-name">{{ product.name }}</NuxtLink>
        <p class="wbp-price">{{ formatBDT(info.sale) }}</p>
      </div>
      <button type="button" class="wbp-add" :aria-label="`Add ${product.name} to bag`" @click="onAdd">
        <Icon name="solar:bag-3-bold" class="w-5 h-5" />
      </button>
    </div>

    <div v-if="picking && sizes.length" class="wbp-sizes" role="radiogroup" aria-label="Size">
      <button
        v-for="s in sizes"
        :key="s"
        type="button"
        role="radio"
        :aria-checked="size === s"
        :class="{ on: size === s }"
        @click="size = s"
      >
        {{ s }}
      </button>
      <button type="button" class="wbp-confirm" @click="add">Add to bag</button>
    </div>
  </article>
</template>

<style scoped>
.wbp {
  background: #fff;
  border-radius: 22px;
  overflow: hidden;
  color: var(--ink);
}
.wbp-stage {
  aspect-ratio: 9 / 14;
  position: relative;
  overflow: hidden;
}
.wbp-video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.wbp-bar {
  display: flex;
  align-items: center;
  gap: 0.8rem;
  padding: 0.9rem 1rem;
}
.wbp-info {
  flex: 1;
  min-width: 0;
}
.wbp-name {
  display: block;
  font-family: var(--font-display);
  font-size: 1rem;
  line-height: 1.3;
  color: var(--brand-deep);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.wbp-price {
  margin-top: 0.15rem;
  font-weight: 700;
  color: var(--brand);
}
.wbp-add {
  flex-shrink: 0;
  display: grid;
  place-items: center;
  width: 2.8rem;
  height: 2.8rem;
  border-radius: 999px;
  background: var(--brand);
  color: #fff;
  transition: background-color 0.2s, transform 0.2s;
}
.wbp-add:hover {
  background: #3e3ebe;
  transform: scale(1.06);
}
.wbp-sizes {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
  padding: 0 1rem 1rem;
}
.wbp-sizes button {
  min-width: 2.3rem;
  height: 2.2rem;
  padding: 0 0.6rem;
  border: 1px solid var(--line);
  border-radius: 8px;
  font-size: 0.84rem;
  font-weight: 500;
}
.wbp-sizes button.on {
  background: var(--brand);
  border-color: var(--brand);
  color: #fff;
}
.wbp-sizes .wbp-confirm {
  margin-left: auto;
  border-color: var(--brand);
  color: var(--brand);
  font-weight: 600;
}
</style>
