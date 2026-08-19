<template>
  <div>
    <aside class="w-64 shadow-xl bg-white dark:bg-gray-950 border-r border-gray-100 dark:border-gray-800 transition-colors duration-300">
      <div class="p-6 text-2xl font-black text-brand tracking-tighter uppercase px-4">
        Dressnio
      </div>

      <nav
        class="overflow-y-auto"
        style="max-height: calc(100vh - 80px)"
      >
        <!-- Set max height for scrolling -->
        <ul>
          <template v-for="(item, index) in sidebarItems" :key="index">
            <AdminSidebarItem :item="item" />
          </template>
        </ul>
      </nav>
    </aside>
  </div>
</template>

<style scoped>
/* Add rounded corners to the top and bottom */
aside {
  /* border-radius: 8px 8px 0 0; */
}
/* Add styling for the scroll bar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f0f0f0;
}

::-webkit-scrollbar-thumb {
  background: #e4e3e3;
}
</style>

<script setup>
import { ref } from "vue";

let sidebarItems = ref([
  {
    id: 1,
    label: "Dashboard",
    link: "/admin/dashboard",
    icon: "material-symbols:dashboard-outline",
    selected: false,
  },
  {
    id: 2,
    label: "Product",
    icon: "ri:product-hunt-line",
    children: [
      {
        id: 4,
        label: "Products Color",
        icon: "ri:product-hunt-line",
        children: [
          {
            id: 5,
            label: "New Color",
            link: "/admin/products/color/create",
            icon: "ri:product-hunt-line",
            selected: false,
          },
          {
            id: 6,
            label: "All Color",
            link: "/admin/products/color",
            icon: "",
            selected: false,
          },
        ],
      },
      {
        id: 4,
        label: "Products Size",
        icon: "ri:product-hunt-line",
        children: [
          {
            id: 5,
            label: "New Size",
            link: "/admin/products/size/create",
            icon: "ri:product-hunt-line",
            selected: false,
          },
          {
            id: 6,
            label: "All Size",
            link: "/admin/products/size",
            icon: "",
            selected: false,
          },
        ],
      },
      {
        id: 3,
        label: "New Product",
        link: "/admin/products/create",
        icon: "mdi:user",
        selected: false,
      },
      {
        id: 3,
        label: "All Products",
        link: "/admin/products",
        icon: "mdi:user",
        selected: false,
      },
    ],
  },
  {
    id: 2,
    label: "Category",
    icon: "tabler:category-plus",
    children: [
      {
        id: 3,
        label: "New Category",
        link: "/admin/category/create",
        icon: "mdi:user",
        selected: false,
      },
      {
        id: 3,
        label: "All Category",
        link: "/admin/category",
        icon: "mdi:user",
        selected: false,
      },
    ],
  },
  {
    id: 3,
    label: "Media",
    icon: "tabler:category-plus",
    children: [
      {
        id: 3,
        label: "New Media",
        link: "/admin/media/create",
        icon: "mdi:user",
        selected: false,
      },
      {
        id: 3,
        label: "All Media",
        link: "/admin/media",
        icon: "mdi:user",
        selected: false,
      },
    ],
  },
  {
    id: 3,
    label: "Story",
    icon: "tabler:category-plus",
    children: [
      {
        id: 3,
        label: "New Story",
        link: "/admin/story/create",
        icon: "mdi:user",
        selected: false,
      },
      {
        id: 3,
        label: "All Story",
        link: "/admin/story",
        icon: "mdi:user",
        selected: false,
      },
    ],
  },
  {
    id: 3,
    label: "Watch & Buy",
    icon: "tabler:category-plus",
    children: [
      {
        id: 3,
        label: "New Watch & Buy",
        link: "/admin/watch-n-buy/create",
        icon: "mdi:user",
        selected: false,
      },
      {
        id: 3,
        label: "All Watch & Buy",
        link: "/admin/watch-n-buy",
        icon: "mdi:user",
        selected: false,
      },
    ],
  },
  {
    id: 4,
    label: "Order",
    link: "/admin/order",
    icon: "carbon:order-details",
    selected: false,
  },
  {
    id: 5,
    label: "Customer",
    link: "/admin/customer",
    icon: "carbon:customer",
    selected: false,
  },
  {
    id: 6,
    label: "Steadfast",
    link: "/admin/steadfast",
    icon: "iconamoon:delivery-free",
    selected: false,
  },
  {
    id: 7,
    label: "Configuration",
    icon: "ant-design:setting-outlined",
    children: [
      {
        id: 1,
        label: "Social Media",
        icon: "ri:product-hunt-line",
        children: [
          {
            id: 2,
            label: "Facebook",
            link: "/admin/configuration/facebook",
            icon: "ri:product-hunt-line",
            selected: false,
          },
          {
            id: 3,
            label: "Instagram",
            link: "/admin/configuration/instagram",
            icon: "",
            selected: false,
          },
        ],
      },
    ],
  },

  {
    id: 9,
    label: "Popup",
    link: "/admin/popup",
    icon: "material-symbols:dashboard-outline",
    selected: false,
  },
  {
    id: 10,
    label: "Slider",
    icon: "tabler:category-plus",
    children: [
      {
        id: 3,
        label: "New slider",
        link: "/admin/slider/create",
        icon: "mdi:user",
        selected: false,
      },
      {
        id: 3,
        label: "All Slider",
        link: "/admin/slider",
        icon: "mdi:user",
        selected: false,
      },
    ],
  },

  {
    id: 12,
    label: "Community",
    link: "/admin/testimonial",
    icon: "fluent-mdl2:page-add",
    selected: false,
  },

  {
    id: 14,
    label: "Blog",
    link: "/admin/post",
    icon: "fluent-mdl2:page-add",
    selected: false,
  },
  // Add more sidebar items as needed
]);
</script>

<style scoped>
/* Add your additional styles here */
</style>
