<script setup>
// SteadFast courier: API keys, a live count of parcels on the road, and a one-click status refresh.
definePageMeta({ middleware: ["auth"] });

const api = useAdminApi();
const snackbar = useSnackbar();

const { form, masked, loading, saving, save } = useSettingsForm(
  { steadfast_api_key: "", steadfast_secret_key: "" },
  ["steadfast_api_key", "steadfast_secret_key"]
);

const counts = ref({});
const syncing = ref(false);

const loadCounts = async () => {
  try {
    const res = await api("/orders", { params: { per_page: 1 } });
    counts.value = res?.meta?.counts || {};
  } catch (e) {
    counts.value = {};
  }
};
onMounted(loadCounts);

const connected = computed(() => !!(masked.steadfast_api_key && masked.steadfast_secret_key));

const submit = async () => {
  if (!form.steadfast_api_key && !masked.steadfast_api_key) {
    snackbar.add({ type: "error", text: "Enter your SteadFast API key." });
    return;
  }
  if (!form.steadfast_secret_key && !masked.steadfast_secret_key) {
    snackbar.add({ type: "error", text: "Enter your SteadFast secret key." });
    return;
  }
  await save();
};

const syncAll = async () => {
  syncing.value = true;
  try {
    const res = await api("/orders/sync-shipped", { method: "POST" });
    snackbar.add({ type: "success", text: res?.message || "Courier status refreshed" });
    await loadCounts();
  } catch (e) {
    snackbar.add({ type: "error", text: apiErrorText(e, "Could not refresh the courier status.") });
  } finally {
    syncing.value = false;
  }
};

const stats = computed(() => [
  { label: "Waiting to ship", value: (counts.value.confirmed || 0) + (counts.value.processing || 0) },
  { label: "On the road", value: counts.value.shipped || 0 },
  { label: "Delivered", value: counts.value.delivered || 0 },
  { label: "Returned", value: counts.value.returned || 0 },
]);
</script>

<template>
  <div class="ad-page">
    <header class="ad-head">
      <h1>SteadFast courier</h1>
      <p>Book parcels from an order's page and keep delivery status in sync automatically.</p>
    </header>

    <p v-if="loading" class="ad-muted" style="margin-top: 1.4rem">Loading settings...</p>

    <div v-else class="ad-grid two">
      <form class="ad-card" @submit.prevent="submit">
        <h2>
          Connection
          <span class="ad-badge" :class="connected ? 'paid' : 'pending'" style="margin-left: 0.5rem">{{ connected ? "Keys saved" : "Not connected" }}</span>
        </h2>
        <label class="ad-field">
          <span>API key</span>
          <input
            v-model="form.steadfast_api_key"
            type="password"
            autocomplete="off"
            :placeholder="masked.steadfast_api_key ? `Saved: ${masked.steadfast_api_key}. Type a new key to replace it.` : 'Paste your SteadFast API key'"
          />
        </label>
        <label class="ad-field">
          <span>Secret key</span>
          <input
            v-model="form.steadfast_secret_key"
            type="password"
            autocomplete="off"
            :placeholder="masked.steadfast_secret_key ? `Saved: ${masked.steadfast_secret_key}. Type a new key to replace it.` : 'Paste your SteadFast secret key'"
          />
          <small>Both keys are stored encrypted. Find them in your SteadFast portal under API settings.</small>
        </label>
        <div class="ad-row">
          <button type="submit" class="ad-btn primary" :disabled="saving">{{ saving ? "Saving..." : "Save keys" }}</button>
        </div>
      </form>

      <section class="ad-card">
        <h2>Parcels</h2>
        <div class="stats">
          <div v-for="s in stats" :key="s.label">
            <b>{{ s.value }}</b>
            <span>{{ s.label }}</span>
          </div>
        </div>
        <div class="ad-row">
          <button type="button" class="ad-btn" :disabled="syncing || !connected" @click="syncAll">{{ syncing ? "Refreshing..." : "Refresh courier status" }}</button>
          <NuxtLink to="/admin/order?status=confirmed" class="ad-btn">Orders ready to ship</NuxtLink>
        </div>
        <p class="ad-muted">Refreshing marks parcels SteadFast has delivered as delivered, and returned parcels as returned with their stock restored.</p>
      </section>
    </div>
  </div>
</template>

<style scoped>
.stats {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.8rem;
}
.stats div {
  padding: 1rem;
  border-radius: 12px;
  background: #f6f6ff;
}
.stats b {
  display: block;
  font-size: 1.8rem;
  color: #151552;
}
.stats span {
  font-size: 0.84rem;
  color: #55556e;
}
</style>
