<script setup>
import { useCartStore } from "~/stores/cart";
import { useWishlistStore } from "~/stores/wishlist";

const cartStore = useCartStore();
const wishlist = useWishlistStore();
const auth = useAuthStore();
const token = useTokenStore();
const route = useRoute();
const router = useRouter();
const config = useRuntimeConfig();

const menuOpen = ref(false);
const searchOpen = ref(false);
const query = ref("");
const suggestions = ref([]);
const searching = ref(false);
const searchInput = ref(null);

// Categories come from the same cached homepage payload the page already fetched
const { data: home } = useApiFetch("/client/homepage", { key: "homepage-payload", lazy: true });
const categories = computed(() => (home.value?.data?.categories || []).slice(0, 10));
const fallbackCats = ["Kurta Sets", "Lehengas", "Sarees", "Maxis", "Suit Sets", "Dresses"].map((name) => ({
  id: name,
  name,
  slug: name.toLowerCase().replace(/[^a-z0-9]+/g, "-"),
}));
const menuCats = computed(() => (categories.value.length ? categories.value : fallbackCats));

const links = [
  { label: "Shop", to: "/shop" },
  { label: "Unstitched suits", to: "/unstitched-suits" },
  { label: "Track order", to: "/track-order" },
  { label: "Contact", to: "/contact" },
];

watch(
  () => route.fullPath,
  () => {
    menuOpen.value = false;
    searchOpen.value = false;
  }
);

watch(menuOpen, (open) => {
  if (import.meta.client) document.body.style.overflow = open ? "hidden" : "";
});

const openSearch = async () => {
  searchOpen.value = true;
  await nextTick();
  searchInput.value?.focus();
};

let timer;
watch(query, (q) => {
  clearTimeout(timer);
  if (q.trim().length < 2) {
    suggestions.value = [];
    searching.value = false;
    return;
  }
  searching.value = true;
  timer = setTimeout(async () => {
    try {
      const res = await $fetch(`${config.public.apiBase}/client/search`, { params: { q: q.trim() } });
      suggestions.value = (res?.data || []).slice(0, 5);
    } catch (e) {
      suggestions.value = [];
    } finally {
      searching.value = false;
    }
  }, 250);
});

const submitSearch = () => {
  const q = query.value.trim();
  if (!q) return;
  router.push({ path: "/search", query: { q } });
};

const handleLogout = async () => {
  try {
    await auth.logout();
    menuOpen.value = false;
  } catch (error) {
    // the session is cleared on the server even when the request fails
  }
};

onBeforeUnmount(() => {
  if (import.meta.client) document.body.style.overflow = "";
});
</script>

<template>
  <header class="nav">
    <div class="pm-wrap nav-row">
      <button type="button" class="icon-btn menu-btn" aria-label="Open menu" @click="menuOpen = true">
        <Icon name="solar:hamburger-menu-linear" class="w-6 h-6" />
      </button>

      <NuxtLink to="/" class="logo" aria-label="Dressnio home">
        <span class="logo-mark">D</span>
        <span class="logo-word">Dress<b>nio</b></span>
      </NuxtLink>

      <nav class="desk" aria-label="Main">
        <div class="has-mega">
          <button type="button" class="nav-link" aria-haspopup="true">
            Collections
            <Icon name="solar:alt-arrow-down-linear" class="w-4 h-4" />
          </button>
          <div class="mega">
            <div class="mega-cols">
              <div>
                <p class="mega-h">Shop by category</p>
                <ul class="mega-list two">
                  <li v-for="c in menuCats" :key="c.id">
                    <NuxtLink :to="`/category/${c.slug}`">{{ c.name }}</NuxtLink>
                  </li>
                </ul>
              </div>
              <div>
                <p class="mega-h">Quick picks</p>
                <ul class="mega-list">
                  <li><NuxtLink to="/shop">New arrivals</NuxtLink></li>
                  <li><NuxtLink to="/steal-deals">Best sellers and deals</NuxtLink></li>
                  <li><NuxtLink to="/unstitched-suits">Unstitched suits</NuxtLink></li>
                  <li><NuxtLink to="/our-community">Our community</NuxtLink></li>
                </ul>
              </div>
              <NuxtLink to="/unstitched-suits" class="mega-promo">
                <GarmentArt kind="unstitched" color="#EBB84C" />
                <span>Tailored to your measure</span>
              </NuxtLink>
            </div>
          </div>
        </div>

        <NuxtLink v-for="l in links" :key="l.to" :to="l.to" class="nav-link">{{ l.label }}</NuxtLink>
        <NuxtLink to="/steal-deals" class="nav-link deals">
          <Icon name="solar:fire-bold" class="w-4 h-4" />
          Deals
        </NuxtLink>
      </nav>

      <div class="tools">
        <button type="button" class="icon-btn" aria-label="Search products" @click="openSearch">
          <Icon name="solar:magnifer-linear" class="w-6 h-6" />
        </button>
        <NuxtLink :to="token.isAuthenticated ? '/customer/wishlist' : '/login'" class="icon-btn" aria-label="Wishlist">
          <Icon name="solar:heart-linear" class="w-6 h-6" />
          <span v-if="wishlist.count" class="count">{{ wishlist.count }}</span>
        </NuxtLink>
        <button type="button" class="icon-btn" aria-label="Open shopping bag" @click="cartStore.openCart()">
          <Icon name="solar:bag-3-linear" class="w-6 h-6" />
          <span v-if="cartStore.items.length" class="count">{{ cartStore.items.length }}</span>
        </button>

        <NuxtLink v-if="!token.isAuthenticated" to="/login" class="pm-btn pm-btn-primary login">Log in</NuxtLink>
        <div v-else class="acct">
          <button type="button" class="icon-btn" aria-haspopup="true" aria-label="Account menu">
            <Icon name="solar:user-circle-linear" class="w-7 h-7" />
          </button>
          <div class="acct-menu">
            <NuxtLink to="/customer"><Icon name="solar:user-id-linear" class="w-5 h-5" />My account</NuxtLink>
            <NuxtLink to="/customer/order"><Icon name="solar:box-linear" class="w-5 h-5" />My orders</NuxtLink>
            <NuxtLink to="/customer/wishlist"><Icon name="solar:heart-linear" class="w-5 h-5" />Wishlist</NuxtLink>
            <button type="button" @click="handleLogout"><Icon name="solar:logout-2-linear" class="w-5 h-5" />Log out</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Search -->
    <Transition name="fade">
      <div v-if="searchOpen" class="search" role="dialog" aria-label="Search products">
        <div class="pm-wrap search-in">
          <form class="search-form" role="search" @submit.prevent="submitSearch">
            <Icon name="solar:magnifer-linear" class="w-6 h-6 s-icon" />
            <input
              ref="searchInput"
              v-model="query"
              type="search"
              placeholder="Search kurta, saree, lehenga..."
              aria-label="Search products"
              autocomplete="off"
              @keydown.esc="searchOpen = false"
            />
            <button type="button" class="icon-btn" aria-label="Close search" @click="searchOpen = false">
              <Icon name="solar:close-circle-linear" class="w-6 h-6" />
            </button>
          </form>
          <ul v-if="suggestions.length" class="sug">
            <li v-for="p in suggestions" :key="p.id">
              <NuxtLink :to="`/product/${p.slug || p.id}`">
                <span class="sug-thumb" :style="{ background: toneFromHex(toneHexAt(p.id)).tint }">
                  <ArtImage :src="p.image" :alt="p.name" :kind="garmentKindFor(p.categories?.[0]?.name, p.name)" :color="toneHexAt(p.id)" />
                </span>
                <span class="sug-name">{{ p.name }}</span>
                <span class="sug-price">{{ formatBDT(priceInfo(p).sale) }}</span>
              </NuxtLink>
            </li>
            <li class="sug-all">
              <NuxtLink :to="{ path: '/search', query: { q: query.trim() } }">See all results for "{{ query.trim() }}"</NuxtLink>
            </li>
          </ul>
          <p v-else-if="query.trim().length >= 2 && !searching" class="sug-empty">
            Nothing matches "{{ query.trim() }}". Try a different word, like kurta or saree.
          </p>
        </div>
      </div>
    </Transition>
  </header>

  <!-- Mobile drawer -->
  <Transition name="fade">
    <div v-if="menuOpen" class="scrim" @click="menuOpen = false"></div>
  </Transition>
  <aside class="drawer" :class="{ open: menuOpen }" :aria-hidden="!menuOpen" aria-label="Menu">
    <div class="drawer-top">
      <span class="logo-word">Dress<b>nio</b></span>
      <button type="button" class="icon-btn" aria-label="Close menu" @click="menuOpen = false">
        <Icon name="solar:close-circle-linear" class="w-6 h-6" />
      </button>
    </div>
    <nav class="drawer-nav">
      <p class="mega-h">Categories</p>
      <ul>
        <li v-for="c in menuCats" :key="c.id"><NuxtLink :to="`/category/${c.slug}`">{{ c.name }}</NuxtLink></li>
      </ul>
      <p class="mega-h">More</p>
      <ul>
        <li v-for="l in links" :key="l.to"><NuxtLink :to="l.to">{{ l.label }}</NuxtLink></li>
        <li><NuxtLink to="/steal-deals" class="deals">Deals</NuxtLink></li>
      </ul>
    </nav>
    <div class="drawer-foot">
      <NuxtLink v-if="!token.isAuthenticated" to="/login" class="pm-btn pm-btn-primary">Log in or register</NuxtLink>
      <template v-else>
        <NuxtLink to="/customer" class="pm-btn pm-btn-ghost">My account</NuxtLink>
        <button type="button" class="pm-btn pm-btn-ghost" @click="handleLogout">Log out</button>
      </template>
    </div>
  </aside>

  <Cart />
</template>

<style scoped>
.nav {
  position: sticky;
  top: 0;
  z-index: 900;
  background: rgba(255, 255, 255, 0.94);
  backdrop-filter: blur(14px);
  border-bottom: 1px solid var(--line);
}
.nav-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  height: 4.4rem;
}
.logo {
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
  margin-right: auto;
  color: var(--brand-deep);
}
.logo-mark {
  display: grid;
  place-items: center;
  width: 2.3rem;
  height: 2.3rem;
  border-radius: 12px 12px 4px 4px;
  background: var(--brand);
  color: #fff;
  font-family: var(--font-display);
  font-weight: 700;
  font-size: 1.25rem;
}
.logo-word {
  font-family: var(--font-display);
  font-size: 1.55rem;
  font-weight: 600;
  letter-spacing: -0.01em;
}
.logo-word b {
  font-weight: 600;
  color: var(--brand);
}
.desk {
  display: none;
  align-items: center;
  gap: 1.7rem;
  margin: 0 auto;
}
.nav-link {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  height: 4.4rem;
  font-size: 0.95rem;
  font-weight: 500;
  white-space: nowrap;
  color: var(--ink);
  border-bottom: 2px solid transparent;
  transition: color 0.2s, border-color 0.2s;
}
.nav-link:hover,
.nav-link.router-link-active {
  color: var(--brand);
  border-bottom-color: var(--brand);
}
.nav-link.deals {
  color: #d1285b;
}
.has-mega {
  position: static;
}
.mega {
  position: absolute;
  left: 0;
  right: 0;
  top: 100%;
  background: #fff;
  border-bottom: 1px solid var(--line);
  box-shadow: 0 30px 50px -30px rgba(21, 21, 82, 0.35);
  opacity: 0;
  visibility: hidden;
  transform: translateY(-6px);
  transition: opacity 0.2s, transform 0.2s, visibility 0.2s;
}
.has-mega:hover .mega,
.has-mega:focus-within .mega {
  opacity: 1;
  visibility: visible;
  transform: none;
}
.mega-cols {
  max-width: 1100px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1.4fr 1fr 0.9fr;
  gap: 2.5rem;
  padding: 2rem 2.5rem 2.2rem;
}
.mega-h {
  margin-bottom: 0.9rem;
  font-size: 0.85rem;
  font-weight: 700;
  color: var(--brand);
}
.mega-list {
  list-style: none;
  display: grid;
  gap: 0.6rem;
}
.mega-list.two {
  grid-template-columns: 1fr 1fr;
}
.mega-list a {
  font-size: 0.97rem;
  color: var(--ink);
}
.mega-list a:hover {
  color: var(--brand);
  text-decoration: underline;
  text-underline-offset: 4px;
}
.mega-promo {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem;
  border-radius: 18px 18px 0 0;
  background: var(--brand-tint);
  font-family: var(--font-display);
  text-align: center;
  color: var(--brand-deep);
}
.mega-promo svg {
  width: 110px;
  height: 140px;
}
.tools {
  display: flex;
  align-items: center;
  gap: 0.2rem;
}
.icon-btn {
  position: relative;
  display: grid;
  place-items: center;
  width: 2.8rem;
  height: 2.8rem;
  border-radius: 999px;
  color: var(--ink);
  transition: background-color 0.2s, color 0.2s;
}
.icon-btn:hover {
  background: var(--brand-tint);
  color: var(--brand);
}
.count {
  position: absolute;
  top: 0.15rem;
  right: 0.1rem;
  min-width: 1.15rem;
  height: 1.15rem;
  padding: 0 0.25rem;
  border-radius: 999px;
  background: var(--brand);
  color: #fff;
  font-size: 0.68rem;
  font-weight: 700;
  display: grid;
  place-items: center;
}
.login {
  display: none;
  min-height: 2.5rem;
  margin-left: 0.5rem;
  padding: 0 1.2rem;
  font-size: 0.9rem;
}
.acct {
  position: relative;
}
.acct-menu {
  position: absolute;
  right: 0;
  top: 100%;
  width: 14rem;
  padding: 0.5rem;
  background: #fff;
  border: 1px solid var(--line);
  border-radius: 16px;
  box-shadow: 0 24px 44px -20px rgba(21, 21, 82, 0.4);
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.2s, visibility 0.2s;
}
.acct:hover .acct-menu,
.acct:focus-within .acct-menu {
  opacity: 1;
  visibility: visible;
}
.acct-menu a,
.acct-menu button {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  width: 100%;
  padding: 0.7rem 0.8rem;
  border-radius: 10px;
  font-size: 0.93rem;
  text-align: left;
}
.acct-menu a:hover,
.acct-menu button:hover {
  background: var(--brand-tint);
  color: var(--brand);
}

/* Search sheet */
.search {
  position: absolute;
  left: 0;
  right: 0;
  top: 100%;
  background: #fff;
  border-bottom: 1px solid var(--line);
  box-shadow: 0 30px 50px -30px rgba(21, 21, 82, 0.4);
  padding: 1rem 0 1.4rem;
}
.search-in {
  max-width: 760px;
}
.search-form {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  border-bottom: 2px solid var(--brand);
  padding-bottom: 0.4rem;
}
.s-icon {
  color: var(--brand);
  flex-shrink: 0;
}
.search-form input {
  flex: 1;
  min-width: 0;
  height: 2.8rem;
  font-size: 1.15rem;
  background: transparent;
}
.sug {
  list-style: none;
  margin-top: 0.8rem;
}
.sug a {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  padding: 0.55rem 0.4rem;
  border-radius: 12px;
}
.sug a:hover {
  background: var(--brand-tint);
}
.sug-thumb {
  width: 3rem;
  height: 3.75rem;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}
.sug-name {
  flex: 1;
  font-weight: 500;
}
.sug-price {
  font-weight: 700;
  color: var(--brand);
}
.sug-all a {
  justify-content: center;
  color: var(--brand);
  font-weight: 600;
}
.sug-empty {
  margin-top: 1rem;
  color: var(--ink-soft);
}

/* Mobile drawer */
.scrim {
  position: fixed;
  inset: 0;
  z-index: 1099;
  background: rgba(21, 21, 82, 0.5);
}
.drawer {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  z-index: 1100;
  width: min(88vw, 340px);
  display: flex;
  flex-direction: column;
  background: #fff;
  transform: translateX(-100%);
  visibility: hidden;
  transition: transform 0.3s ease, visibility 0.3s;
}
.drawer.open {
  transform: none;
  visibility: visible;
}
.drawer-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1rem 1rem 1.4rem;
  border-bottom: 1px solid var(--line);
}
.drawer-nav {
  flex: 1;
  overflow-y: auto;
  padding: 1.2rem 1.4rem;
}
.drawer-nav ul {
  list-style: none;
  margin-bottom: 1.6rem;
}
.drawer-nav li a {
  display: block;
  padding: 0.75rem 0;
  border-bottom: 1px solid var(--line);
  font-size: 1.05rem;
}
.drawer-nav li a.deals {
  color: #d1285b;
  font-weight: 600;
}
.drawer-foot {
  display: grid;
  gap: 0.6rem;
  padding: 1rem 1.4rem 1.4rem;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@media (min-width: 1024px) {
  .desk {
    display: flex;
  }
  .menu-btn {
    display: none;
  }
  .logo {
    margin-right: 0;
  }
  .login {
    display: inline-flex;
  }
}
@media (max-width: 1023px) {
  .logo {
    margin-right: auto;
  }
  .logo-mark {
    display: none;
  }
}
</style>
