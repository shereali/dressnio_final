<script setup>
// Product page: gallery, colour and size choice, live pricing, bag, buy now and order on WhatsApp.
import { useCartStore } from "~/stores/cart";
import { useWishlistStore } from "~/stores/wishlist";

const router = useRouter();
const route = useRoute();
const config = useRuntimeConfig();
const cartStore = useCartStore();
const wishlist = useWishlistStore();
const snackbar = useSnackbar();
const { whatsappUrl, freeShippingAbove } = useStoreSettings();
const pageUrl = useRequestURL();

const { data, pending } = await useFetch(
  () => `${config.public.apiBase}/client/product-details/${route.params.slug}`,
  { key: `product-details-${route.params.slug}`, lazy: true }
);

const product = computed(() => data.value?.data || null);

const quantity = ref(1);
const size = ref("");
const color = ref("");
const activeImage = ref(0);
const guideOpen = ref(false);
const tab = ref("details");

const sizes = computed(() => (product.value?.sizes || []).map((s) => s.standard || s.name).filter(Boolean));
const colors = computed(() =>
  (product.value?.colors || [])
    .map((c) => c.color_code)
    .filter((c) => c && /^#?[0-9a-f]{3,6}$/i.test(String(c).replace("#", "")))
    .map((c) => (String(c).startsWith("#") ? c : `#${c}`))
);

watch(
  product,
  (p) => {
    if (!p) return;
    size.value = "";
    activeImage.value = 0;
    quantity.value = 1;
    color.value = colors.value[0] || "";
  },
  { immediate: true }
);

const info = computed(() => priceInfo(product.value || {}, size.value));
const kind = computed(() =>
  garmentKindFor(product.value?.categories?.[0]?.name, product.value?.cloth_type, product.value?.name)
);
const artColor = computed(() => color.value || toneHexAt(Number(product.value?.id) || 0));

const photos = computed(() => {
  const list = (product.value?.gallery || [])
    .map((g) => g.media_url || g.url || g.image)
    .filter((u) => u && !isDrawableOnly(u));
  if (product.value?.image && !isDrawableOnly(product.value.image) && !list.includes(product.value.image)) {
    list.unshift(product.value.image);
  }
  return list;
});

const stock = computed(() => Number(product.value?.stock_available ?? 0));
const soldOut = computed(() => stock.value <= 0);
const lowStock = computed(() => stock.value > 0 && stock.value <= 5);
const saved = computed(() => wishlist.ids.includes(product.value?.id));
const needsSize = computed(() => !!product.value?.is_size_visible && sizes.value.length > 0);
const total = computed(() => info.value.sale * quantity.value);
const toFreeDelivery = computed(() => Math.max(0, freeShippingAbove.value - total.value));

const cartItem = () => ({
  id: product.value.id,
  name: product.value.name,
  image: photos.value[0] || null,
  size: needsSize.value ? size.value : null,
  color: color.value || null,
  quantity: quantity.value,
  price: info.value.sale,
  is_size_visible: needsSize.value,
});

const guard = () => {
  if (soldOut.value) return false;
  if (needsSize.value && !size.value) {
    snackbar.add({ type: "error", text: "Choose a size first" });
    return false;
  }
  return true;
};

const addToBag = () => {
  if (!guard()) return false;
  const ok = cartStore.addToCart(cartItem());
  if (ok) snackbar.add({ type: "success", text: "Added to your bag" });
  return ok;
};

const buyNow = () => {
  if (!guard()) return;
  if (cartStore.addToCart(cartItem())) {
    cartStore.closeCart();
    router.push("/checkout");
  }
};

const toggleSave = async () => {
  const added = await wishlist.toggle(product.value.id);
  snackbar.add({ type: "success", text: added ? "Saved to your wishlist" : "Removed from your wishlist" });
};

const waLink = computed(() => {
  if (!whatsappUrl.value || !product.value) return "";
  const lines = [
    `Hi Dressnio, I would like to order:`,
    product.value.name,
    size.value ? `Size: ${size.value}` : "",
    color.value ? `Colour: ${color.value}` : "",
    `Quantity: ${quantity.value}`,
    `Price: ${formatBDT(total.value)}`,
    pageUrl.href,
  ].filter(Boolean);
  return `${whatsappUrl.value}?text=${encodeURIComponent(lines.join("\n"))}`;
});

const plain = computed(() => (product.value?.description || "").replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim());

useSeoMeta({
  title: () => (product.value ? `${product.value.name} | Dressnio` : "Product | Dressnio"),
  description: () => plain.value.slice(0, 155) || "Shop at Dressnio. Cash on delivery across Bangladesh.",
  ogImage: () => photos.value[0] || undefined,
});

useHead({
  script: computed(() =>
    product.value
      ? [
          {
            type: "application/ld+json",
            innerHTML: JSON.stringify({
              "@context": "https://schema.org",
              "@type": "Product",
              name: product.value.name,
              description: plain.value,
              image: photos.value,
              offers: {
                "@type": "Offer",
                priceCurrency: "BDT",
                price: Math.round(info.value.sale),
                availability: soldOut.value ? "https://schema.org/OutOfStock" : "https://schema.org/InStock",
              },
            }),
          },
        ]
      : []
  ),
});
</script>

<template>
  <div class="pd">
    <div class="pm-wrap">
      <nav v-if="product" class="pd-crumbs" aria-label="Breadcrumb">
        <NuxtLink to="/">Home</NuxtLink>
        <Icon name="solar:alt-arrow-right-linear" class="w-3.5 h-3.5" />
        <NuxtLink to="/shop">Shop</NuxtLink>
        <template v-if="product.categories?.[0]">
          <Icon name="solar:alt-arrow-right-linear" class="w-3.5 h-3.5" />
          <NuxtLink :to="`/category/${product.categories[0].slug}`">{{ product.categories[0].name }}</NuxtLink>
        </template>
      </nav>

      <div v-if="pending" class="pd-grid">
        <div class="skel" style="aspect-ratio: 4/5"></div>
        <div class="pd-info"><div class="skel" style="height: 3rem"></div><div class="skel" style="height: 8rem"></div></div>
      </div>

      <div v-else-if="product" class="pd-grid">
        <div class="pd-gallery">
          <div class="pd-main" :style="{ background: toneFromHex(artColor).tint }">
            <ArtImage
              :src="photos[activeImage] || null"
              :alt="product.name"
              :kind="kind"
              :color="artColor"
              :lazy="false"
            />
            <button
              type="button"
              class="pd-heart"
              :class="{ on: saved }"
              :aria-pressed="saved"
              :aria-label="saved ? 'Remove from wishlist' : 'Save to wishlist'"
              @click="toggleSave"
            >
              <Icon :name="saved ? 'solar:heart-bold' : 'solar:heart-linear'" class="w-6 h-6" />
            </button>
          </div>
          <div v-if="photos.length > 1" class="pd-thumbs">
            <button
              v-for="(p, i) in photos"
              :key="p"
              type="button"
              :class="{ on: activeImage === i }"
              :aria-label="`Show photo ${i + 1}`"
              @click="activeImage = i"
            >
              <img :src="p" alt="" loading="lazy" />
            </button>
          </div>
        </div>

        <div class="pd-info">
          <h1 class="pd-title">{{ product.name }}</h1>

          <div class="pd-price">
            <span class="pd-sale">{{ formatBDT(info.sale) }}</span>
            <span v-if="info.percent > 0" class="pd-list">{{ formatBDT(info.list) }}</span>
            <span v-if="info.percent > 0" class="pd-off">{{ Math.round(info.percent) }}% off</span>
          </div>

          <p class="pd-stock" :class="{ out: soldOut, low: lowStock }">
            <Icon :name="soldOut ? 'solar:close-circle-bold' : 'solar:check-circle-bold'" class="w-5 h-5" />
            <template v-if="soldOut">Sold out</template>
            <template v-else-if="lowStock">Only {{ stock }} left</template>
            <template v-else>In stock</template>
          </p>

          <div v-if="colors.length" class="pd-block">
            <p class="pd-label">Colour</p>
            <div class="pd-sw" role="radiogroup" aria-label="Colour">
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

          <div v-if="needsSize" class="pd-block">
            <div class="pd-label-row">
              <p class="pd-label">Size <span v-if="size">: {{ size }}</span></p>
              <button type="button" class="pd-link" @click="guideOpen = true">Size guide</button>
            </div>
            <div class="pd-sizes" role="radiogroup" aria-label="Size">
              <button v-for="s in sizes" :key="s" type="button" role="radio" :aria-checked="size === s" :class="{ on: size === s }" @click="size = s">{{ s }}</button>
            </div>
          </div>

          <div class="pd-buy">
            <div class="pd-qty" aria-label="Quantity">
              <button type="button" aria-label="Decrease quantity" @click="quantity = Math.max(1, quantity - 1)"><Icon name="solar:minus-circle-linear" class="w-5 h-5" /></button>
              <span aria-live="polite">{{ quantity }}</span>
              <button type="button" aria-label="Increase quantity" @click="quantity++"><Icon name="solar:add-circle-linear" class="w-5 h-5" /></button>
            </div>
            <button type="button" class="pm-btn pm-btn-primary pd-add" :disabled="soldOut" @click="addToBag">
              <Icon name="solar:bag-3-linear" class="w-5 h-5" />
              {{ soldOut ? "Sold out" : `Add to bag, ${formatBDT(total)}` }}
            </button>
          </div>
          <div class="pd-buy2">
            <button type="button" class="pm-btn pm-btn-ghost" :disabled="soldOut" @click="buyNow">Buy now</button>
            <a v-if="waLink" :href="waLink" target="_blank" rel="noopener" class="pm-btn pd-wa">
              <Icon name="ic:baseline-whatsapp" class="w-5 h-5" />
              Order on WhatsApp
            </a>
          </div>
          <p v-if="toFreeDelivery > 0 && !soldOut" class="pd-note">Add {{ formatBDT(toFreeDelivery) }} more to get free delivery.</p>
          <p v-else-if="!soldOut" class="pd-note ok">This order qualifies for free delivery.</p>

          <ul class="pd-promises">
            <li><Icon name="solar:wallet-money-bold-duotone" class="w-6 h-6" /><span><b>Cash on delivery</b>Pay when it arrives</span></li>
            <li><Icon name="solar:delivery-bold-duotone" class="w-6 h-6" /><span><b>{{ STORE_POLICY.deliveryInsideCity }} in your city</b>{{ STORE_POLICY.deliveryOutsideCity }} elsewhere</span></li>
            <li><Icon name="solar:refresh-circle-bold-duotone" class="w-6 h-6" /><span><b>{{ STORE_POLICY.exchangeWindow }} size exchange</b>If it does not fit</span></li>
          </ul>

          <div class="pd-tabs" role="tablist">
            <button role="tab" :aria-selected="tab === 'details'" :class="{ on: tab === 'details' }" @click="tab = 'details'">Details</button>
            <button role="tab" :aria-selected="tab === 'care'" :class="{ on: tab === 'care' }" @click="tab = 'care'">Fabric and care</button>
          </div>
          <div class="pd-tab" role="tabpanel">
            <div v-if="tab === 'details'">
              <div v-if="product.description" class="pd-rich" v-html="product.description"></div>
              <p v-else class="pd-rich">Details for this piece are being added.</p>
            </div>
            <ul v-else class="pd-care">
              <li v-if="product.fabric_type">Fabric type: {{ product.fabric_type }}</li>
              <li>Wash gently in cold water with a mild detergent.</li>
              <li>Dry in the shade and iron on the reverse side.</li>
              <li>Colours may look slightly different on screen.</li>
            </ul>
          </div>
        </div>
      </div>

      <div v-else class="pd-missing">
        <GarmentArt kind="dress" color="#8383ED" class="pd-missing-art" />
        <h1>We could not find that product</h1>
        <p>It may have been removed or sold out. Browse the shop to see what is available.</p>
        <NuxtLink to="/shop" class="pm-btn pm-btn-primary">Go to the shop</NuxtLink>
      </div>
    </div>

    <!-- Size guide -->
    <div v-if="guideOpen" class="modal" role="dialog" aria-modal="true" aria-label="Size guide" @click.self="guideOpen = false" @keydown.esc="guideOpen = false">
      <div class="modal-card">
        <div class="modal-top">
          <h2>Size guide</h2>
          <button type="button" aria-label="Close size guide" @click="guideOpen = false"><Icon name="solar:close-circle-linear" class="w-6 h-6" /></button>
        </div>
        <table>
          <thead><tr><th>Size</th><th>Bust (in)</th><th>Hip (in)</th></tr></thead>
          <tbody>
            <tr v-for="r in SIZE_CHART" :key="r.size" :class="{ hit: size === r.size }">
              <td>{{ r.size }}</td>
              <td>{{ Math.floor(r.bust[0]) }} to {{ Math.ceil(r.bust[1]) }}</td>
              <td>{{ Math.floor(r.hip[0]) }} to {{ Math.ceil(r.hip[1]) }}</td>
            </tr>
          </tbody>
        </table>
        <p class="modal-note">Measurements are body measurements. Between sizes, take the larger one.</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.pd {
  background: var(--paper);
  padding: 1.4rem 0 4rem;
}
.pd-crumbs {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.35rem;
  margin-bottom: 1.4rem;
  font-size: 0.86rem;
  color: var(--ink-soft);
}
.pd-crumbs a:hover {
  color: var(--brand);
}
.pd-grid {
  display: grid;
  gap: 2rem;
  align-items: start;
}
@media (min-width: 900px) {
  .pd-grid {
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
  }
  .pd-gallery {
    position: sticky;
    top: 6rem;
  }
}
.pd-main {
  position: relative;
  aspect-ratio: 4 / 5;
  border-radius: 24px;
  overflow: hidden;
}
.pd-main :deep(.art-drawn) {
  padding: 8% 14% 4%;
}
.pd-heart {
  position: absolute;
  top: 1rem;
  right: 1rem;
  display: grid;
  place-items: center;
  width: 3rem;
  height: 3rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.92);
  color: var(--ink-soft);
}
.pd-heart.on,
.pd-heart:hover {
  color: #e0396b;
}
.pd-thumbs {
  display: flex;
  gap: 0.6rem;
  margin-top: 0.8rem;
  overflow-x: auto;
}
.pd-thumbs button {
  flex: 0 0 4.5rem;
  height: 5.6rem;
  border-radius: 12px;
  overflow: hidden;
  border: 2px solid transparent;
}
.pd-thumbs button.on {
  border-color: var(--brand);
}
.pd-thumbs img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.pd-title {
  font-size: clamp(1.9rem, 3.6vw, 2.9rem);
  line-height: 1.1;
  font-weight: 600;
  color: var(--brand-deep);
  text-wrap: balance;
}
.pd-price {
  display: flex;
  align-items: baseline;
  flex-wrap: wrap;
  gap: 0.7rem;
  margin-top: 1rem;
}
.pd-sale {
  font-family: var(--font-display);
  font-size: 2.3rem;
  font-weight: 600;
  color: var(--brand);
}
.pd-list {
  color: #8b8ba3;
  text-decoration: line-through;
}
.pd-off {
  padding: 0.2rem 0.65rem;
  border-radius: 999px;
  background: #fde7ee;
  color: #c0285a;
  font-size: 0.82rem;
  font-weight: 600;
}
.pd-stock {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  margin-top: 0.6rem;
  font-size: 0.92rem;
  font-weight: 500;
  color: #0f8a5f;
}
.pd-stock.low {
  color: #b86e00;
}
.pd-stock.out {
  color: #c0285a;
}
.pd-block {
  margin-top: 1.5rem;
}
.pd-label-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.pd-label {
  margin-bottom: 0.6rem;
  font-size: 0.92rem;
  font-weight: 600;
}
.pd-label-row .pd-label {
  margin-bottom: 0.6rem;
}
.pd-link {
  margin-bottom: 0.6rem;
  font-size: 0.88rem;
  font-weight: 600;
  color: var(--brand);
  text-decoration: underline;
  text-underline-offset: 3px;
}
.pd-sw,
.pd-sizes {
  display: flex;
  flex-wrap: wrap;
  gap: 0.55rem;
}
.pd-sw button {
  width: 2.2rem;
  height: 2.2rem;
  border-radius: 999px;
  background: var(--sw);
  box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.12);
}
.pd-sw button.on {
  outline: 2px solid var(--brand);
  outline-offset: 3px;
}
.pd-sizes button {
  min-width: 3.1rem;
  height: 2.8rem;
  padding: 0 0.9rem;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  background: #fff;
  font-weight: 500;
}
.pd-sizes button:hover {
  border-color: var(--brand);
}
.pd-sizes button.on {
  background: var(--brand);
  border-color: var(--brand);
  color: #fff;
}
.pd-buy {
  display: flex;
  gap: 0.8rem;
  margin-top: 1.8rem;
}
.pd-qty {
  display: inline-flex;
  align-items: center;
  height: 3rem;
  padding: 0 0.4rem;
  border: 1.5px solid var(--line);
  border-radius: 999px;
  background: #fff;
}
.pd-qty button {
  display: grid;
  place-items: center;
  width: 2.3rem;
  height: 2.3rem;
  color: var(--brand);
}
.pd-qty span {
  min-width: 1.7rem;
  text-align: center;
  font-weight: 700;
}
.pd-add {
  flex: 1;
}
.pd-add:disabled,
.pd-buy2 .pm-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.pd-buy2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.8rem;
  margin-top: 0.8rem;
}
.pd-wa {
  background: #1faa59;
  color: #fff;
}
.pd-wa:hover {
  background: #168a47;
}
.pd-note {
  margin-top: 0.9rem;
  font-size: 0.9rem;
  color: var(--ink-soft);
}
.pd-note.ok {
  color: #0f8a5f;
}
.pd-promises {
  list-style: none;
  display: grid;
  gap: 0.9rem;
  margin: 1.8rem 0;
  padding: 1.2rem;
  border-radius: 18px;
  background: #fff;
  border: 1px solid var(--line);
}
.pd-promises li {
  display: flex;
  gap: 0.8rem;
  align-items: center;
  color: var(--brand);
}
.pd-promises span {
  color: var(--ink-soft);
  font-size: 0.88rem;
  line-height: 1.4;
}
.pd-promises b {
  display: block;
  color: var(--ink);
  font-size: 0.95rem;
}
.pd-tabs {
  display: flex;
  gap: 1.5rem;
  border-bottom: 1px solid var(--line);
}
.pd-tabs button {
  padding: 0.7rem 0;
  font-weight: 600;
  color: var(--ink-soft);
  border-bottom: 2px solid transparent;
  margin-bottom: -1px;
}
.pd-tabs button.on {
  color: var(--brand);
  border-color: var(--brand);
}
.pd-tab {
  padding: 1.2rem 0;
  color: var(--ink-soft);
  line-height: 1.75;
}
.pd-care {
  padding-left: 1.2rem;
}
.pd-missing {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 4rem 0;
  text-align: center;
}
.pd-missing h1 {
  font-size: 2rem;
  color: var(--brand-deep);
}
.pd-missing-art {
  width: 150px;
  height: 200px;
}
.skel {
  border-radius: 20px;
  background: linear-gradient(90deg, #eee 25%, #f6f6fb 50%, #eee 75%);
  background-size: 200% 100%;
  animation: sh 1.4s infinite;
}
@keyframes sh {
  to {
    background-position: -200% 0;
  }
}
.modal {
  position: fixed;
  inset: 0;
  z-index: 1200;
  display: grid;
  place-items: center;
  padding: 1rem;
  background: rgba(21, 21, 82, 0.55);
}
.modal-card {
  width: min(100%, 460px);
  padding: 1.5rem;
  border-radius: 22px;
  background: #fff;
}
.modal-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
}
.modal-top h2 {
  font-size: 1.6rem;
  color: var(--brand-deep);
}
.modal table {
  width: 100%;
  border-collapse: collapse;
}
.modal th,
.modal td {
  padding: 0.6rem 0.4rem;
  text-align: left;
  border-bottom: 1px solid var(--line);
}
.modal tr.hit td {
  background: var(--brand-tint);
  color: var(--brand);
  font-weight: 700;
}
.modal-note {
  margin-top: 0.9rem;
  font-size: 0.86rem;
  color: var(--ink-soft);
}
</style>
