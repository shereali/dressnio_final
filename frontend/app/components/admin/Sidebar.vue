<template>
  <div>
    <!-- Mobile Backdrop -->
    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs z-40 lg:hidden transition-opacity"
    ></div>

    <!-- Sidebar Container -->
    <aside
      class="fixed lg:static top-0 left-0 bottom-0 z-50 bg-white dark:bg-gray-950 border-r border-gray-100 dark:border-gray-800 flex flex-col transition-all duration-300 transform"
      :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        isCollapsed ? 'w-20' : 'w-64'
      ]"
    >
      <!-- Brand Logo Header -->
      <div
        class="h-16 px-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-800"
        :class="{ 'justify-center': isCollapsed }"
      >
        <NuxtLink to="/admin/dashboard" class="flex items-center gap-3 group">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white shadow-md shadow-brand-500/20 group-hover:scale-105 transition-transform flex-shrink-0">
            <span class="font-black text-base tracking-tight">D</span>
          </div>
          <div v-if="!isCollapsed" class="overflow-hidden transition-all">
            <span class="text-base font-black tracking-tight text-gray-900 dark:text-white group-hover:text-brand transition-colors block leading-tight">DRESSNIO</span>
            <span class="text-[9px] font-extrabold tracking-widest text-brand-600 uppercase">Studio Admin</span>
          </div>
        </NuxtLink>

        <!-- Toggle Collapse Button (Desktop) -->
        <button
          v-if="!isCollapsed"
          @click="isCollapsed = !isCollapsed"
          class="hidden lg:flex p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors"
          title="Collapse sidebar"
        >
          <Icon name="solar:arrow-left-bold" class="w-4 h-4" />
        </button>

        <!-- Close button on mobile -->
        <button
          @click="sidebarOpen = false"
          class="lg:hidden p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
        >
          <Icon name="solar:close-circle-bold" class="w-5 h-5" />
        </button>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-5 custom-scrollbar">
        <div v-for="(group, gIdx) in menuGroups" :key="gIdx">
          <!-- Group Title -->
          <div
            v-if="group.title && !isCollapsed"
            class="px-3 mb-2 text-[10px] font-extrabold tracking-wider text-gray-400 dark:text-gray-500 uppercase"
          >
            {{ group.title }}
          </div>

          <!-- Group Divider when collapsed -->
          <div
            v-else-if="group.title && isCollapsed && gIdx > 0"
            class="my-2 border-t border-gray-100 dark:border-gray-800"
          ></div>

          <ul class="space-y-1">
            <li v-for="item in group.items" :key="item.id">
              <!-- Single Route Link (No Children) -->
              <NuxtLink
                v-if="!item.children || item.children.length === 0"
                :to="item.link"
                @click="handleLinkClick"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-400 hover:text-brand dark:hover:text-brand-400 hover:bg-brand-50/60 dark:hover:bg-brand-950/30 transition-all duration-200 group relative"
                :class="[
                  isCollapsed ? 'justify-center px-0' : '',
                  isRouteActive(item.link) ? '!bg-brand-500 !text-white shadow-md shadow-brand-500/20 dark:!bg-brand-600 font-bold' : ''
                ]"
                :title="isCollapsed ? item.label : undefined"
              >
                <Icon
                  :name="item.icon"
                  class="w-5 h-5 flex-shrink-0 transition-transform group-hover:scale-110"
                />
                <span v-if="!isCollapsed" class="truncate">{{ item.label }}</span>

                <!-- Tooltip for collapsed mode -->
                <div
                  v-if="isCollapsed"
                  class="absolute left-full ml-2 px-2.5 py-1 bg-gray-900 text-white text-[11px] font-semibold rounded-md opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap shadow-lg"
                >
                  {{ item.label }}
                </div>
              </NuxtLink>

              <!-- Parent Accordion Dropdown (Has Children) -->
              <div v-else>
                <button
                  type="button"
                  @click="toggleParent(item)"
                  class="w-full flex items-center justify-between gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-gray-600 dark:text-gray-400 hover:text-brand dark:hover:text-brand-400 hover:bg-brand-50/40 dark:hover:bg-brand-950/20 transition-all duration-200 group relative"
                  :class="[
                    isCollapsed ? 'justify-center px-0' : '',
                    isParentActive(item) ? 'text-brand dark:text-brand-400 font-bold' : ''
                  ]"
                  :title="isCollapsed ? item.label : undefined"
                >
                  <div class="flex items-center gap-3 truncate" :class="{ 'justify-center': isCollapsed }">
                    <Icon
                      :name="item.icon"
                      class="w-5 h-5 flex-shrink-0 transition-transform group-hover:scale-110"
                      :class="{ 'text-brand dark:text-brand-400': isParentActive(item) }"
                    />
                    <span v-if="!isCollapsed" class="truncate">{{ item.label }}</span>
                  </div>

                  <Icon
                    v-if="!isCollapsed"
                    name="solar:alt-arrow-down-bold"
                    class="w-3.5 h-3.5 flex-shrink-0 text-gray-400 transition-transform duration-200"
                    :class="{ 'rotate-180 text-brand': openParents[item.id] }"
                  />

                  <!-- Tooltip for collapsed mode -->
                  <div
                    v-if="isCollapsed"
                    class="absolute left-full ml-2 px-2.5 py-1 bg-gray-900 text-white text-[11px] font-semibold rounded-md opacity-0 pointer-events-none group-hover:opacity-100 transition-opacity z-50 whitespace-nowrap shadow-lg"
                  >
                    {{ item.label }}
                  </div>
                </button>

                <!-- Sub-menu list (Expanded mode) -->
                <ul
                  v-if="!isCollapsed && openParents[item.id]"
                  class="mt-1 ml-4 pl-3 border-l-2 border-gray-100 dark:border-gray-800 space-y-0.5"
                >
                  <li v-for="sub in item.children" :key="sub.id">
                    <NuxtLink
                      :to="sub.link"
                      @click="handleLinkClick"
                      class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[11px] font-semibold text-gray-600 dark:text-gray-400 hover:text-brand dark:hover:text-brand-400 hover:bg-brand-50/50 dark:hover:bg-brand-950/20 transition-all duration-200 group"
                      :class="[
                        isRouteActive(sub.link) ? '!text-brand dark:!text-brand-400 font-bold bg-brand-50/80 dark:bg-brand-950/40' : ''
                      ]"
                    >
                      <Icon
                        :name="sub.icon"
                        class="w-4 h-4 flex-shrink-0"
                        :class="[isRouteActive(sub.link) ? 'text-brand' : 'text-gray-400']"
                      />
                      <span class="truncate">{{ sub.label }}</span>
                    </NuxtLink>
                  </li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Sidebar Footer Status -->
      <div
        class="p-3 border-t border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/30"
        :class="{ 'flex justify-center': isCollapsed }"
      >
        <div class="flex items-center gap-2.5 truncate" :class="{ 'justify-center': isCollapsed }">
          <div class="w-8 h-8 rounded-full bg-brand-50 dark:bg-brand-900/30 flex items-center justify-center flex-shrink-0 text-brand">
            <Icon name="solar:shield-user-bold-duotone" class="w-4.5 h-4.5" />
          </div>
          <div v-if="!isCollapsed" class="truncate">
            <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">Store Manager</p>
            <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
              MySQL Live
            </p>
          </div>
        </div>
      </div>
    </aside>
  </div>
</template>

<script setup>
import { reactive, watch, onMounted } from 'vue';

const sidebarOpen = useState('admin_sidebar_open', () => false);
const isCollapsed = useState('admin_sidebar_collapsed', () => false);
const route = useRoute();

const openParents = reactive({
  products: true,
  categories: false,
  media: false,
  sliders: false,
  stories: false,
  'watch-n-buy': false,
  configuration: false,
});

const menuGroups = [
  {
    title: 'Overview',
    items: [
      {
        id: 'dashboard',
        label: 'Dashboard',
        link: '/admin/dashboard',
        icon: 'solar:widget-2-bold-duotone',
      },
    ],
  },
  {
    title: 'Catalog & Store',
    items: [
      {
        id: 'products',
        label: 'Products',
        icon: 'solar:t-shirt-bold-duotone',
        children: [
          {
            id: 'products-all',
            label: 'All Products',
            link: '/admin/products',
            icon: 'solar:list-bold',
          },
          {
            id: 'products-create',
            label: 'Add Product',
            link: '/admin/products/create',
            icon: 'solar:add-circle-bold',
          },
          {
            id: 'products-colors',
            label: 'Color Sets',
            link: '/admin/products/color',
            icon: 'solar:pallete-2-bold-duotone',
          },
          {
            id: 'products-sizes',
            label: 'Garment Sizes',
            link: '/admin/products/size',
            icon: 'solar:ruler-angular-bold-duotone',
          },
        ],
      },
      {
        id: 'categories',
        label: 'Categories',
        icon: 'solar:folder-with-files-bold-duotone',
        children: [
          {
            id: 'categories-all',
            label: 'All Categories',
            link: '/admin/category',
            icon: 'solar:list-bold',
          },
          {
            id: 'categories-create',
            label: 'Add Category',
            link: '/admin/category/create',
            icon: 'solar:add-circle-bold',
          },
        ],
      },
      {
        id: 'orders',
        label: 'Orders',
        link: '/admin/order',
        icon: 'solar:bag-check-bold-duotone',
      },
      {
        id: 'customers',
        label: 'Customers',
        link: '/admin/customer',
        icon: 'solar:users-group-rounded-bold-duotone',
      },
      {
        id: 'steadfast',
        label: 'Courier & Shipping',
        link: '/admin/steadfast',
        icon: 'solar:delivery-bold-duotone',
      },
    ],
  },
  {
    title: 'Content & Engagement',
    items: [
      {
        id: 'media',
        label: 'Media Library',
        icon: 'solar:gallery-wide-bold-duotone',
        children: [
          {
            id: 'media-all',
            label: 'All Media',
            link: '/admin/media',
            icon: 'solar:gallery-bold',
          },
          {
            id: 'media-create',
            label: 'Upload Media',
            link: '/admin/media/create',
            icon: 'solar:upload-track-bold',
          },
        ],
      },
      {
        id: 'sliders',
        label: 'Hero Sliders',
        icon: 'solar:slider-vertical-bold-duotone',
        children: [
          {
            id: 'sliders-all',
            label: 'All Sliders',
            link: '/admin/slider',
            icon: 'solar:list-bold',
          },
          {
            id: 'sliders-create',
            label: 'Add Slider',
            link: '/admin/slider/create',
            icon: 'solar:add-circle-bold',
          },
        ],
      },
      {
        id: 'stories',
        label: 'Stories & Reels',
        icon: 'solar:play-circle-bold-duotone',
        children: [
          {
            id: 'stories-all',
            label: 'All Stories',
            link: '/admin/story',
            icon: 'solar:list-bold',
          },
          {
            id: 'stories-create',
            label: 'Add Story',
            link: '/admin/story/create',
            icon: 'solar:add-circle-bold',
          },
        ],
      },
      {
        id: 'watch-n-buy',
        label: 'Watch & Buy (Live)',
        icon: 'solar:videocamera-record-bold-duotone',
        children: [
          {
            id: 'watch-all',
            label: 'All Videos',
            link: '/admin/watch-n-buy',
            icon: 'solar:list-bold',
          },
          {
            id: 'watch-create',
            label: 'Add Video',
            link: '/admin/watch-n-buy/create',
            icon: 'solar:add-circle-bold',
          },
        ],
      },
      {
        id: 'popups',
        label: 'Promo Popups',
        link: '/admin/popup',
        icon: 'solar:chat-round-dots-bold-duotone',
      },
      {
        id: 'testimonials',
        label: 'Reviews & Community',
        link: '/admin/testimonial',
        icon: 'solar:star-fall-bold-duotone',
      },
      {
        id: 'blog',
        label: 'Blog & Editorial',
        link: '/admin/post',
        icon: 'solar:document-text-bold-duotone',
      },
    ],
  },
  {
    title: 'System Settings',
    items: [
      {
        id: 'configuration',
        label: 'Settings & Integrations',
        icon: 'solar:settings-bold-duotone',
        children: [
          {
            id: 'config-fb',
            label: 'Facebook Pixel / API',
            link: '/admin/configuration/facebook',
            icon: 'solar:link-circle-bold',
          },
          {
            id: 'config-sms',
            label: 'SMS & Login Codes',
            link: '/admin/configuration/sms',
            icon: 'solar:chat-round-dots-bold',
          },
          {
            id: 'config-insta',
            label: 'Instagram Feed',
            link: '/admin/configuration/instagram',
            icon: 'solar:camera-bold',
          },
        ],
      },
    ],
  },
];

function toggleParent(item) {
  if (isCollapsed.value) {
    isCollapsed.value = false;
  }
  openParents[item.id] = !openParents[item.id];
}

function isRouteActive(link) {
  if (!link) return false;
  if (link === '/admin/dashboard') {
    return route.path === '/admin/dashboard' || route.path === '/admin';
  }
  return route.path === link || route.path.startsWith(link + '/');
}

function isParentActive(item) {
  if (!item.children) return false;
  return item.children.some(child => isRouteActive(child.link));
}

function autoOpenActiveGroup() {
  menuGroups.forEach(group => {
    group.items.forEach(item => {
      if (item.children && isParentActive(item)) {
        openParents[item.id] = true;
      }
    });
  });
}

watch(() => route.path, () => {
  autoOpenActiveGroup();
});

onMounted(() => {
  autoOpenActiveGroup();
});

function handleLinkClick() {
  if (process.client && window.innerWidth < 1024) {
    sidebarOpen.value = false;
  }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.25);
  border-radius: 9999px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(156, 163, 175, 0.45);
}
</style>
