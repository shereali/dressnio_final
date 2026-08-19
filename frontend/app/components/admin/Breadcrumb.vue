<script setup>
const route = useRoute();

const links = computed(() => {
  const pathSegments = route.path.split('/').filter(Boolean);
  const breadcrumbs = [
    {
      label: "Home",
      icon: "i-heroicons-home",
      to: "/admin/dashboard",
    }
  ];

  let currentPath = '';
  pathSegments.forEach((segment, index) => {
    if (segment === 'admin' && index === 0) return;
    currentPath += `/${segment}`;
    
    // Skip adding if it's already the dashboard link
    if (segment === 'dashboard' && index === 1) return;

    breadcrumbs.push({
      label: segment.charAt(0).toUpperCase() + segment.slice(1).replace(/-/g, ' '),
      icon: "i-heroicons-chevron-right",
      to: index === pathSegments.length - 1 ? undefined : `/admin${currentPath}`
    });
  });

  return breadcrumbs;
});
</script>

<template>
  <UBreadcrumb 
    :links="links" 
    :ui="{ 
      ol: 'gap-x-2', 
      li: 'gap-x-2',
      inactive: 'text-gray-500 hover:text-brand dark:text-gray-400 dark:hover:text-brand-400 transition-colors',
      active: 'text-gray-900 dark:text-white font-bold',
      icon: {
        base: 'w-4 h-4 flex-shrink-0'
      }
    }"
  >
    <template #divider>
      <span class="text-gray-300 dark:text-gray-700">/</span>
    </template>
  </UBreadcrumb>
</template>
