<template>
  <!-- Mobile Menu Overlay -->
  <div 
    v-if="isMobileMenuOpen" 
    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[899] lg:hidden transition-opacity"
    @click="isMobileMenuOpen = false"
  ></div>

  <header class="header bg-white/98 backdrop-blur-xl sticky top-0 z-[900] border-b border-gray-100 shadow-sm transition-all duration-300">
    <div class="container mx-auto px-3 sm:px-4 lg:px-8">
      <div class="flex items-center justify-between h-14 sm:h-16 lg:h-[48px]">
        <!-- Logo with Hamburger Menu -->
        <div class="flex-shrink-0 flex items-center gap-2">
          <!-- Hamburger Menu Button (Mobile Only) - Enhanced Design -->
          <button 
            class="lg:hidden relative w-10 h-10 flex items-center justify-center rounded-lg bg-gradient-to-br from-brand-50 to-brand-100 hover:from-brand-100 hover:to-brand-200 transition-all duration-300 shadow-sm hover:shadow-md group"
            @click="isMobileMenuOpen = !isMobileMenuOpen"
            aria-label="Toggle menu"
          >
            <!-- Animated Hamburger Icon -->
            <div class="w-5 h-4 flex flex-col justify-between relative">
              <!-- Top Bar -->
              <span 
                class="w-full h-0.5 bg-brand rounded-full transform transition-all duration-300 origin-center"
                :class="isMobileMenuOpen ? 'rotate-45 translate-y-[7px]' : 'rotate-0'"
              ></span>
              <!-- Middle Bar -->
              <span 
                class="w-full h-0.5 bg-brand rounded-full transition-all duration-300"
                :class="isMobileMenuOpen ? 'opacity-0 scale-0' : 'opacity-100 scale-100'"
              ></span>
              <!-- Bottom Bar -->
              <span 
                class="w-full h-0.5 bg-brand rounded-full transform transition-all duration-300 origin-center"
                :class="isMobileMenuOpen ? '-rotate-45 -translate-y-[7px]' : 'rotate-0'"
              ></span>
            </div>
          </button>

          <NuxtLink to="/" class="group flex items-center gap-1.5 sm:gap-2">
            <div class="w-8 h-8 sm:w-9 sm:h-9 lg:w-10 lg:h-10 bg-brand rounded-lg lg:rounded-xl flex items-center justify-center transform group-hover:rotate-12 transition-all duration-500 shadow-md shadow-brand/20">
              <span class="text-white font-black text-base sm:text-lg lg:text-xl">D</span>
            </div>
            <div class="logo text-lg sm:text-xl lg:text-2xl font-black uppercase tracking-tighter">
              <span class="text-gray-900">Dress</span><span class="text-brand">nio</span>
            </div>
          </NuxtLink>
        </div>

        <!-- Mobile Quick Menu (Visible on mobile/tablet) -->
        <nav class="flex lg:hidden flex-1 justify-center px-2">
          <ul class="flex items-center gap-1">
            <li>
              <NuxtLink to="/steal-deals" class="px-2 sm:px-3 py-1.5 text-xs sm:text-sm font-bold text-rose-500 hover:bg-rose-50 rounded-lg transition-all flex items-center gap-1">
                <Icon name="solar:flame-bold" class="w-3.5 h-3.5"/>
                <span>Deals</span>
              </NuxtLink>
            </li>
          </ul>
        </nav>

        <!-- Desktop Menu -->
        <div class="hidden lg:flex flex-1 justify-center px-6">
          <nav class="menu">
            <ul class="flex items-center space-x-8">
              <li>
                <nuxt-link to="/" class="nav-link relative group overflow-hidden">
                  <span>Home</span>
                  <div class="absolute bottom-0 left-0 w-full h-[2px] bg-brand -translate-x-full group-hover:translate-x-0 transition-transform duration-300"></div>
                </nuxt-link>
              </li>
              <li class="relative group">
                <a href="#" class="nav-link flex items-center gap-1.5 active-accent">
                  Collection <Icon name="solar:alt-arrow-down-linear" class="w-4 h-4 transition-transform group-hover:rotate-180"/>
                </a>
                
                <!-- Mega Menu -->
                <div class="absolute top-[calc(100%+5px)] left-1/2 -translate-x-1/2 w-[900px] bg-white shadow-[0_30px_60px_-15px_rgba(0,0,0,0.2)] rounded-2xl mt-0 p-8 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 grid grid-cols-4 gap-8 border border-gray-50 z-[1000]">
                   <!-- Section 1 -->
                   <div class="space-y-4">
                      <h4 class="font-black text-xs uppercase tracking-widest text-brand border-b border-gray-50 pb-3">Men's Fashion</h4>
                      <ul class="space-y-3 text-sm text-gray-500">
                          <li><NuxtLink to="/shop" class="hover:text-brand hover:translate-x-1 block transition-all">Shirts & T-Shirts</NuxtLink></li>
                          <li><NuxtLink to="/shop" class="hover:text-brand hover:translate-x-1 block transition-all">Jeans & Trousers</NuxtLink></li>
                          <li><NuxtLink to="/shop" class="hover:text-brand hover:translate-x-1 block transition-all">Winter Wear</NuxtLink></li>
                          <li><NuxtLink to="/shop" class="hover:text-brand hover:translate-x-1 block transition-all">Accessories</NuxtLink></li>
                      </ul>
                   </div>
                   
                   <!-- Section 2 -->
                   <div class="space-y-4">
                      <h4 class="font-black text-xs uppercase tracking-widest text-brand border-b border-gray-50 pb-3">Women's Fashion</h4>
                      <ul class="space-y-3 text-sm text-gray-500">
                          <li><NuxtLink to="/shop" class="hover:text-brand hover:translate-x-1 block transition-all">Dresses & Gowns</NuxtLink></li>
                          <li><NuxtLink to="/shop" class="hover:text-brand hover:translate-x-1 block transition-all">Tops & Kurtis</NuxtLink></li>
                          <li><NuxtLink to="/shop" class="hover:text-brand hover:translate-x-1 block transition-all">Sarees</NuxtLink></li>
                          <li><NuxtLink to="/shop" class="hover:text-brand hover:translate-x-1 block transition-all">Handbags</NuxtLink></li>
                      </ul>
                   </div>

                   <!-- Section 3 -->
                   <div class="space-y-4">
                      <h4 class="font-black text-xs uppercase tracking-widest text-brand border-b border-gray-50 pb-3">Trending</h4>
                      <ul class="space-y-3 text-sm text-gray-500">
                          <li><NuxtLink to="/shop" class="hover:text-brand hover:translate-x-1 block transition-all">Summer Collection</NuxtLink></li>
                          <li><NuxtLink to="/shop" class="hover:text-brand hover:translate-x-1 block transition-all">Festive Special</NuxtLink></li>
                          <li><NuxtLink to="/shop" class="hover:text-brand hover:translate-x-1 block transition-all">Office Wear</NuxtLink></li>
                      </ul>
                   </div>

                   <!-- Section 4 (Featured Image Overlay) -->
                   <div class="col-span-1 rounded-2xl overflow-hidden relative group/img aspect-square">
                      <img src="/img/shop1.jpg" alt="Collection" class="w-full h-full object-cover transition-transform duration-700 group-hover/img:scale-110"/>
                      <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                      <div class="absolute bottom-4 left-4 text-white">
                        <p class="text-[10px] uppercase tracking-widest font-bold opacity-80">Collection</p>
                        <h5 class="text-lg font-black leading-tight">New Arrivals</h5>
                      </div>
                   </div>
                </div>
              </li>

              <li><nuxt-link to="/shop" class="nav-link">Shop Collection</nuxt-link></li>
              <li><nuxt-link to="/unstitched-suits" class="nav-link">Unstitched Suits</nuxt-link></li>
              <li><nuxt-link to="/track-order" class="nav-link">Track Order</nuxt-link></li>
              <li>
                <nuxt-link to="/steal-deals" class="flex items-center gap-1.5 nav-link text-rose-500 hover:text-rose-600 font-black">
                  <Icon name="solar:flame-bold" class="w-4 h-4"/>
                  Deals
                </nuxt-link>
              </li>
              <li><nuxt-link to="/contact" class="nav-link">Contact</nuxt-link></li>
            </ul>
          </nav>
        </div>

        <!-- Right Side Icons -->
        <div class="flex items-center gap-0.5 sm:gap-2 lg:gap-3">
          <NuxtLink to="/search" class="p-1.5 sm:p-2 lg:p-2.5 text-gray-700 hover:text-brand hover:bg-brand-50 rounded-full transition-all" title="Search">
            <Icon name="solar:magnifer-linear" class="w-4 h-4 sm:w-5 sm:h-5 lg:w-5 lg:h-5"/>
          </NuxtLink>
          
          <NuxtLink to="/shop" class="hidden md:flex p-1.5 sm:p-2 lg:p-2.5 text-gray-700 hover:text-brand hover:bg-brand-50 rounded-full transition-all relative group" title="Favorites">
            <Icon name="solar:heart-linear" class="w-4 h-4 sm:w-5 sm:h-5 lg:w-5 lg:h-5"/>
          </NuxtLink>

          <button type="button" @click.prevent="openCart" class="p-1.5 sm:p-2 lg:p-2.5 text-gray-700 hover:text-brand hover:bg-brand-50 rounded-full transition-all relative group" title="Shopping Bag">
             <Icon name="solar:bag-3-linear" class="w-4 h-4 sm:w-5 sm:h-5 lg:w-5 lg:h-5"/>
             <span v-if="cartStore.items.length" class="absolute -top-0.5 -right-0.5 sm:-top-1 sm:-right-1 bg-brand text-white text-[8px] sm:text-[9px] min-w-[16px] sm:min-w-[18px] h-[16px] sm:h-[18px] flex items-center justify-center rounded-full font-bold shadow-md shadow-brand/20 px-0.5 sm:px-1">{{ cartStore.items.length }}</span>
          </button>

          <!-- Divider -->
          <div class="w-[1px] h-4 sm:h-5 bg-gray-200 mx-0.5 sm:mx-1 hidden md:block"></div>

          <!-- Auth -->
          <NuxtLink v-if="!token.isAuthenticated" to="/login" class="hidden md:flex items-center gap-1.5 lg:gap-2 bg-gradient-to-r from-brand-500 to-brand-700 !text-white px-3 sm:px-4 lg:px-5 py-1.5 sm:py-2 rounded-full hover:shadow-lg hover:from-brand-600 hover:to-brand-800 transition-all text-xs lg:text-sm font-bold shadow-md">
              <Icon name="solar:user-circle-linear" class="w-3.5 h-3.5 sm:w-4 sm:h-4 lg:w-4 lg:h-4"/>
              <span class="hidden lg:inline">Login</span>
          </NuxtLink>

          <div v-if="token.isAuthenticated" class="relative group/user items-center flex gap-2 cursor-pointer p-1 pr-2 hover:bg-gray-50 rounded-full transition-all border border-transparent hover:border-gray-100">
              <img src="/images/avatar.jpg" alt="User" class="w-9 h-9 rounded-full object-cover border-2 border-white shadow-sm ring-1 ring-gray-100"/>
              <Icon name="solar:alt-arrow-down-linear" class="w-4 h-4 text-gray-400 group-hover/user:rotate-180 transition-transform"/>
              
              <!-- Dropdown -->
              <div class="absolute right-0 top-[calc(100%+10px)] w-56 bg-white rounded-2xl shadow-2xl py-3 opacity-0 invisible group-hover/user:opacity-100 group-hover/user:visible transition-all duration-300 border border-gray-50 z-[1000] overflow-hidden">
                  <div class="px-5 py-3 border-b border-gray-50 mb-2">
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Account</p>
                    <p class="text-sm font-black text-gray-900 truncate">Customer</p>
                  </div>
                  <NuxtLink to="/account" class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-600 hover:bg-brand-50 hover:text-brand transition-all">
                      <Icon name="solar:user-id-linear" class="w-5 h-5"/>
                      My Account
                  </NuxtLink>
                   <NuxtLink to="/orders" class="flex items-center gap-3 px-5 py-2.5 text-sm text-gray-600 hover:bg-brand-50 hover:text-brand transition-all">
                      <Icon name="solar:reorder-bold" class="w-5 h-5"/>
                      My Orders
                  </NuxtLink>
                   <div class="border-t border-gray-50 my-2"></div>
                  <button @click="handleLogout" class="flex w-full items-center gap-3 px-5 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition-all font-bold">
                      <Icon name="solar:logout-2-linear" class="w-5 h-5"/>
                      Sign Out
                  </button>
              </div>
          </div>

        </div>
      </div>
    </div>
  </header>

  <!-- Mobile Menu Drawer -->
  <div 
    class="fixed top-0 left-0 h-full w-[280px] sm:w-[300px] bg-white shadow-2xl z-[1000] transform transition-transform duration-300 ease-out lg:hidden overflow-y-auto"
    :class="isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
  >
    <!-- Mobile Menu Header -->
    <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-brand-50 to-white">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 bg-brand rounded-lg flex items-center justify-center shadow-md">
          <span class="text-white font-black text-sm">D</span>
        </div>
        <span class="font-black text-lg text-gray-900">Menu</span>
      </div>
      <button @click="isMobileMenuOpen = false" class="p-2 text-gray-500 hover:text-brand hover:bg-white rounded-lg transition-all">
        <Icon name="solar:close-circle-bold" class="w-6 h-6"/>
      </button>
    </div>

    <!-- Mobile Navigation -->
    <nav class="p-3">
      <ul class="space-y-1">
        <!-- Home -->
        <li>
          <NuxtLink to="/" @click="isMobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand rounded-lg transition-all font-medium">
            <Icon name="solar:home-2-bold" class="w-5 h-5"/>
            <span>Home</span>
          </NuxtLink>
        </li>

        <!-- Collection with Submenu -->
        <li>
          <button 
            @click="isCollectionOpen = !isCollectionOpen" 
            class="flex w-full items-center justify-between gap-3 px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand rounded-lg transition-all font-medium"
          >
            <div class="flex items-center gap-3">
              <Icon name="solar:layers-bold" class="w-5 h-5"/>
              <span>Collection</span>
            </div>
            <Icon :name="isCollectionOpen ? 'solar:alt-arrow-up-bold' : 'solar:alt-arrow-down-linear'" class="w-4 h-4 transition-transform"/>
          </button>
          <!-- Submenu -->
          <div v-show="isCollectionOpen" class="ml-4 mt-1 space-y-1 border-l-2 border-brand-100 pl-3">
            <NuxtLink to="/category/women" @click="isMobileMenuOpen = false" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-brand hover:bg-brand-50 rounded-md transition-all">
              <Icon name="solar:stars-minimalistic-bold" class="w-3.5 h-3.5"/>
              Women's Collection
            </NuxtLink>
            <NuxtLink to="/category/men" @click="isMobileMenuOpen = false" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-brand hover:bg-brand-50 rounded-md transition-all">
              <Icon name="solar:stars-minimalistic-bold" class="w-3.5 h-3.5"/>
              Men's Collection
            </NuxtLink>
            <NuxtLink to="/category/kids" @click="isMobileMenuOpen = false" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 hover:text-brand hover:bg-brand-50 rounded-md transition-all">
              <Icon name="solar:stars-minimalistic-bold" class="w-3.5 h-3.5"/>
              Kids Collection
            </NuxtLink>
          </div>
        </li>

        <!-- Community -->
        <li>
          <NuxtLink to="/our-community" @click="isMobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand rounded-lg transition-all font-medium">
            <Icon name="solar:user-circle-bold" class="w-5 h-5"/>
            <span>Community</span>
          </NuxtLink>
        </li>

        <!-- Deals (Highlighted) -->
        <li>
          <NuxtLink to="/steal-deals" @click="isMobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 text-rose-500 bg-rose-50 hover:bg-rose-100 rounded-lg transition-all font-bold">
            <Icon name="solar:flame-bold" class="w-5 h-5"/>
            <span>Hot Deals</span>
            <span class="ml-auto bg-rose-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">NEW</span>
          </NuxtLink>
        </li>

        <!-- Contact -->
        <li>
          <NuxtLink to="/contact" @click="isMobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand rounded-lg transition-all font-medium">
            <Icon name="solar:phone-calling-bold" class="w-5 h-5"/>
            <span>Contact Us</span>
          </NuxtLink>
        </li>
      </ul>

      <!-- Divider -->
      <div class="my-4 border-t border-gray-100"></div>

      <!-- Quick Links -->
      <div class="space-y-1">
        <p class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Quick Links</p>
        <NuxtLink to="/search" @click="isMobileMenuOpen = false" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 hover:text-brand rounded-lg transition-all">
          <Icon name="solar:magnifer-linear" class="w-4 h-4"/>
          <span>Search Products</span>
        </NuxtLink>
        <NuxtLink to="/shop" @click="isMobileMenuOpen = false" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 hover:text-brand rounded-lg transition-all">
          <Icon name="solar:heart-linear" class="w-4 h-4"/>
          <span>Shop All</span>
        </NuxtLink>
      </div>

      <!-- Mobile Auth -->
      <div class="mt-6 pt-4 border-t border-gray-100">
        <NuxtLink v-if="!token.isAuthenticated" to="/login" @click="isMobileMenuOpen = false" class="flex items-center justify-center gap-2 bg-brand !text-white px-6 py-3 rounded-xl hover:bg-brand-700 transition-all font-bold shadow-lg">
          <Icon name="solar:user-circle-bold" class="w-5 h-5"/>
          Login / Register
        </NuxtLink>
        <div v-else class="space-y-1">
          <NuxtLink to="/account" @click="isMobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand rounded-lg transition-all font-medium">
            <Icon name="solar:user-id-linear" class="w-5 h-5"/>
            My Account
          </NuxtLink>
          <NuxtLink to="/orders" @click="isMobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-brand-50 hover:text-brand rounded-lg transition-all font-medium">
            <Icon name="solar:bag-3-linear" class="w-5 h-5"/>
            My Orders
          </NuxtLink>
          <button @click="handleLogout" class="flex w-full items-center gap-3 px-4 py-3 text-rose-600 hover:bg-rose-50 rounded-lg transition-all font-bold">
            <Icon name="solar:logout-2-bold" class="w-5 h-5"/>
            Sign Out
          </button>
        </div>
      </div>
    </nav>
  </div>
  
  <!-- Slide-over Cart Component -->
  <Cart />
</template>

<script setup>
import { useCartStore } from "~/stores/cart";
const cartStore = useCartStore();
const auth = useAuthStore();
const token = useTokenStore();
const isMobileMenuOpen = ref(false);
const isCollectionOpen = ref(false);

const openCart = () => {
  cartStore.openCart();
};

const handleLogout = async () => {
  try {
    await auth.logout();
    isMobileMenuOpen.value = false;
  } catch (error) {
    console.log("error", error);
  }
};
</script>

<style scoped>
.nav-link {
    @apply text-sm font-bold uppercase text-gray-700 hover:text-brand tracking-wide transition-colors py-2;
}
.router-link-active {
    @apply text-brand;
}
</style>
