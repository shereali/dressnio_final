<script setup>
// Search results from GET /client/search?q=
const route = useRoute();
const router = useRouter();
const config = useRuntimeConfig();

const term = ref(String(route.query.q || ""));
const results = ref([]);
const loading = ref(false);
const searched = ref(false);

const run = async (q) => {
  const value = (q || "").trim();
  if (value.length < 2) {
    results.value = [];
    searched.value = false;
    return;
  }
  loading.value = true;
  try {
    const res = await $fetch(`${config.public.apiBase}/client/search`, { params: { q: value } });
    results.value = res?.data || [];
  } catch (e) {
    results.value = [];
  } finally {
    loading.value = false;
    searched.value = true;
  }
};

watch(
  () => route.query.q,
  (q) => {
    term.value = String(q || "");
    run(term.value);
  },
  { immediate: true }
);

const submit = () => router.push({ path: "/search", query: { q: term.value.trim() } });

useHead({ title: computed(() => (term.value ? `Search: ${term.value} | Dressnio` : "Search | Dressnio")) });
</script>

<template>
  <div class="pm-section sp">
    <div class="pm-wrap">
      <h1 class="sp-title">Search</h1>
      <form class="sp-form" role="search" @submit.prevent="submit">
        <Icon name="solar:magnifer-linear" class="w-6 h-6 sp-icon" />
        <input v-model="term" type="search" placeholder="Search kurta, saree, lehenga..." aria-label="Search products" />
        <button type="submit" class="pm-btn pm-btn-primary">Search</button>
      </form>

      <p v-if="loading" class="sp-note" role="status">Searching...</p>
      <template v-else-if="searched">
        <p class="sp-note" role="status">
          {{ results.length }} {{ results.length === 1 ? "result" : "results" }} for "{{ route.query.q }}"
        </p>
        <Product :products="results" empty-text="No products match that search. Try another word, like kurta, saree or maxi." />
      </template>
      <p v-else class="sp-note">Type at least two letters to search the catalogue.</p>
    </div>
  </div>
</template>

<style scoped>
.sp {
  min-height: 60vh;
}
.sp-title {
  font-size: clamp(2rem, 4vw, 3rem);
  font-weight: 600;
  color: var(--brand-deep);
}
.sp-form {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  margin: 1.5rem 0 1rem;
  padding: 0.4rem 0.4rem 0.4rem 1.1rem;
  max-width: 720px;
  border: 1.5px solid var(--line);
  border-radius: 999px;
  background: #fff;
}
.sp-form:focus-within {
  border-color: var(--brand);
  box-shadow: 0 0 0 4px rgba(77, 77, 219, 0.14);
}
.sp-icon {
  color: var(--brand);
  flex-shrink: 0;
}
.sp-form input {
  flex: 1;
  min-width: 0;
  height: 2.8rem;
  font-size: 1.05rem;
  background: transparent;
}
.sp-note {
  margin: 1rem 0 1.6rem;
  color: var(--ink-soft);
}
</style>
