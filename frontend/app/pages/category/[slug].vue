<script setup>
const config = useRuntimeConfig();
const route = useRoute();

const { data, pending } = await useFetch(
  () => `${config.public.apiBase}/client/category-details/${route.params.slug}`,
  { key: `category-${route.params.slug}`, lazy: true }
);

const category = computed(() => data.value?.data || null);
const sortBy = ref("newest");

const products = computed(() => {
  const list = [...(category.value?.products || [])];
  const price = (p) => priceInfo(p).sale;
  if (sortBy.value === "price_low") list.sort((a, b) => price(a) - price(b));
  if (sortBy.value === "price_high") list.sort((a, b) => price(b) - price(a));
  return list;
});

const name = computed(() => category.value?.name || String(route.params.slug).replace(/-/g, " "));
const kind = computed(() => garmentKindFor(name.value, route.params.slug));

useSeoMeta({
  title: () => `${name.value} | Dressnio`,
  description: () => category.value?.description || `Shop ${name.value} from Dressnio. Cash on delivery across Bangladesh.`,
});
</script>

<template>
  <div>
    <PageHero
      :title="name"
      :subtitle="category?.description || ''"
      :crumbs="[{ label: 'Shop', to: '/shop' }, { label: name }]"
      :kind="kind"
    />

    <section class="pm-section cat-body">
      <div class="pm-wrap">
        <div v-if="pending" class="cat-skel">
          <div v-for="i in 8" :key="i" class="skel"></div>
        </div>

        <template v-else-if="products.length">
          <div class="cat-bar">
            <p>
              <strong>{{ products.length }}</strong> {{ products.length === 1 ? "product" : "products" }}
            </p>
            <label class="cat-sort">
              <span>Sort by</span>
              <select v-model="sortBy">
                <option value="newest">Newest first</option>
                <option value="price_low">Price: low to high</option>
                <option value="price_high">Price: high to low</option>
              </select>
            </label>
          </div>
          <Product :products="products" />
        </template>

        <Product v-else :products="[]" :empty-text="`There are no ${name.toLowerCase()} in stock right now. Browse the full shop to see what is available.`" />
      </div>
    </section>
  </div>
</template>

<style scoped>
.cat-body {
  background: var(--paper);
  padding-top: 2rem;
}
.cat-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.4rem;
  color: var(--ink-soft);
}
.cat-sort {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-size: 0.9rem;
}
.cat-sort select {
  height: 2.6rem;
  padding: 0 2rem 0 0.9rem;
  border: 1.5px solid var(--line);
  border-radius: 999px;
  background: #fff;
  color: var(--ink);
}
.cat-skel {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1.4rem;
}
.skel {
  height: 380px;
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
</style>
