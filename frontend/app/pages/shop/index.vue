<template>
  <div class="dn-container" style="padding-top: 2rem; padding-bottom: 5rem;">
    <!-- Catalog Header Banner -->
    <div
      class="dn-card"
      style="
        background: linear-gradient(135deg, var(--dn-tertiary), var(--dn-tertiary-subtle));
        color: #ffffff;
        padding: 3rem 2rem;
        border-radius: var(--dn-radius-2xl);
        margin-bottom: 2.5rem;
        text-align: center;
        position: relative;
        overflow: hidden;
      "
    >
      <div style="position: absolute; right: -50px; top: -50px; width: 200px; height: 200px; background: var(--dn-primary); filter: blur(90px); opacity: 0.35; border-radius: 50%;"></div>
      <span style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--dn-secondary-light); margin-bottom: 0.5rem; display: block;">
        Curated Fashion Collection
      </span>
      <h1 style="font-size: 2.5rem; font-weight: 900; letter-spacing: -0.02em; margin-bottom: 0.75rem;">
        Discover Luxury & Daily Elegance
      </h1>
      <p style="color: var(--dn-text-subtle); max-width: 600px; margin: 0 auto; font-size: 1rem;">
        Explore handcrafted kurtis, salwar kameez sets, festive lehengas, and authentic raw fabrics tailored for perfection.
      </p>
    </div>

    <!-- Main Layout (Sidebar Filters + Products Grid) -->
    <div class="catalog-layout">
      <!-- Sidebar Filters -->
      <aside class="catalog-sidebar">
        <div class="dn-card" style="padding: 1.5rem; position: sticky; top: 90px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--dn-border-light); padding-bottom: 0.75rem;">
            <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--dn-tertiary);">Filters</h3>
            <button
              v-if="hasActiveFilters"
              type="button"
              style="font-size: 0.8rem; color: var(--dn-accent-rose); font-weight: 600;"
              @click="resetFilters"
            >
              Reset All
            </button>
          </div>

          <!-- Cloth Type Filter -->
          <div style="margin-bottom: 1.5rem;">
            <label class="dn-label">Cloth Type</label>
            <div style="display: flex; flex-direction: column; gap: 0.4rem; font-size: 0.875rem;">
              <label
                v-for="type in clothTypes"
                :key="type.id"
                style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;"
              >
                <input
                  type="radio"
                  :value="type.id"
                  v-model="selectedClothType"
                  name="cloth_type"
                  style="accent-color: var(--dn-primary);"
                />
                <span>{{ type.label }}</span>
              </label>
            </div>
          </div>

          <!-- Category Filter -->
          <div v-if="categories.length" style="margin-bottom: 1.5rem;">
            <label class="dn-label">Categories</label>
            <div style="display: flex; flex-direction: column; gap: 0.4rem; font-size: 0.875rem;">
              <label
                v-for="cat in categories"
                :key="cat.id"
                style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;"
              >
                <input
                  type="radio"
                  :value="cat.slug"
                  v-model="selectedCategory"
                  name="category"
                  style="accent-color: var(--dn-primary);"
                />
                <span>{{ cat.name }}</span>
              </label>
            </div>
          </div>

          <!-- Price Range Filter -->
          <div style="margin-bottom: 1.5rem;">
            <label class="dn-label">Max Price (৳{{ maxPrice }})</label>
            <input
              type="range"
              min="500"
              max="20000"
              step="500"
              v-model="maxPrice"
              style="width: 100%; accent-color: var(--dn-primary);"
            />
            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--dn-text-muted); margin-top: 0.25rem;">
              <span>৳500</span>
              <span>৳20,000+</span>
            </div>
          </div>
        </div>
      </aside>

      <!-- Products Grid Section -->
      <div>
        <!-- Top Toolbar (Results count + Sort dropdown) -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
          <span style="font-size: 0.95rem; color: var(--dn-text-muted);">
            Showing <strong style="color: var(--dn-tertiary);">{{ products.length }}</strong> products
          </span>

          <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span style="font-size: 0.85rem; color: var(--dn-text-muted);">Sort by:</span>
            <select v-model="sortBy" class="dn-select" style="width: auto; padding: 0.45rem 1.75rem 0.45rem 0.75rem; font-size: 0.85rem;">
              <option value="newest">Newest Arrivals</option>
              <option value="popular">Most Popular</option>
              <option value="price_low">Price: Low to High</option>
              <option value="price_high">Price: High to Low</option>
            </select>
          </div>
        </div>

        <!-- Products Grid -->
        <div v-if="pending" class="dn-grid dn-grid-3">
          <div v-for="n in 6" :key="n" class="dn-skeleton" style="height: 380px; border-radius: var(--dn-radius-lg);"></div>
        </div>

        <div v-else-if="products.length === 0" class="dn-card" style="padding: 3rem; text-align: center;">
          <Icon name="solar:magnifer-outline" style="font-size: 2.5rem; color: var(--dn-text-muted); margin-bottom: 0.75rem;" />
          <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">No garments match your filters</h3>
          <p style="color: var(--dn-text-muted); font-size: 0.9rem; margin-bottom: 1.25rem;">Try adjusting or resetting your filter criteria.</p>
          <button type="button" class="dn-btn dn-btn-outline" @click="resetFilters">Reset Filters</button>
        </div>

        <div v-else class="dn-grid dn-grid-3">
          <ProductCard
            v-for="product in products"
            :key="product.id"
            :product="product"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";

const config = useRuntimeConfig();

const selectedClothType = ref("");
const selectedCategory = ref("");
const maxPrice = ref(20000);
const sortBy = ref("newest");

const clothTypes = [
  { id: "", label: "All Garments" },
  { id: "1", label: "Kurti + Pant Sets" },
  { id: "2", label: "Single Kameez" },
  { id: "3", label: "Salwar / Pants" },
  { id: "4", label: "Sarees, Lehengas & Fabrics" },
];

const { data: homeData } = await useFetch(`${config.public.apiBase}/client/homepage`);
const categories = computed(() => homeData.value?.data?.categories || []);

const queryParams = computed(() => {
  const params = {
    sort: sortBy.value,
  };
  if (selectedClothType.value) params.cloth_type = selectedClothType.value;
  if (selectedCategory.value) params.category = selectedCategory.value;
  if (maxPrice.value < 20000) params.max_price = maxPrice.value;
  return params;
});

const { data: catalogData, pending, refresh } = await useFetch(`${config.public.apiBase}/client/products`, {
  params: queryParams,
  watch: [queryParams],
});

const products = computed(() => catalogData.value?.data || []);

const hasActiveFilters = computed(() => {
  return selectedClothType.value !== "" || selectedCategory.value !== "" || maxPrice.value < 20000;
});

const resetFilters = () => {
  selectedClothType.value = "";
  selectedCategory.value = "";
  maxPrice.value = 20000;
  sortBy.value = "newest";
};
</script>

<style scoped>
.catalog-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 2.5rem;
}

@media (max-width: 991px) {
  .catalog-layout {
    grid-template-columns: 1fr;
  }
}
</style>
