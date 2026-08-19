<script setup>
const props = defineProps(["latest", "bestSeller"]);
const activeTab = ref("latest");
console.log("bestSeller", props.bestSeller);
</script>
<template>
  <div class="bg-gradient-to-b from-white to-brand-50 py-20 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <Headline
        title="New Arrivals"
        subtitle="Discover our latest collection"
        class="mb-16"
      />
      <!-- Tabs Navigation -->
      <div class="flex mb-8 max-w-md mx-auto shadow-lg rounded-full overflow-hidden" v-auto-animate>
        <button
          @click="activeTab = 'latest'"
          :class="{ 'bg-brand text-white border-brand shadow-inner': activeTab === 'latest', 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50': activeTab !== 'latest' }"
          class="flex-1 py-4 px-8 border-2 focus:outline-none uppercase text-sm font-bold tracking-wider transition-all duration-300 rounded-l-full"
        >
          Latest
        </button>
        <button
          @click="activeTab = 'bestSeller'"
          :class="{ 'bg-brand text-white border-brand shadow-inner': activeTab === 'bestSeller', 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50': activeTab !== 'bestSeller' }"
          class="flex-1 py-4 px-8 border-2 focus:outline-none uppercase text-sm font-bold tracking-wider transition-all duration-300 rounded-r-full"
        >
          Best Seller
        </button>
      </div>

    <!-- Tab Content -->
    <div v-if="activeTab === 'latest'">
      <!-- Latest Products Component -->
      <LazyLatestProducts :latestProducts="props.latest" />
    </div>
    <div v-else>
      <!-- Best Seller Products Component -->
      <LazyBestSellerProducts :bestSellerProducts="props.bestSeller" />
    </div>
    </div>
  </div>
</template>
