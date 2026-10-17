<script setup>
// Which payment methods the store accepts, and the accounts customers pay into.
definePageMeta({ middleware: ["auth"] });

const { form, masked, loading, saving, save } = useSettingsForm(
  {
    payment_cod_enabled: "1",
    payment_bkash_enabled: "0",
    bkash_number: "",
    payment_nagad_enabled: "0",
    nagad_number: "",
    sslcommerz_enabled: "0",
    sslcommerz_sandbox: "1",
    sslcommerz_store_id: "",
    sslcommerz_store_password: "",
  },
  ["sslcommerz_store_password"]
);

const flag = (key) =>
  computed({
    get: () => form[key] === "1",
    set: (v) => (form[key] = v ? "1" : "0"),
  });
const cod = flag("payment_cod_enabled");
const bkash = flag("payment_bkash_enabled");
const nagad = flag("payment_nagad_enabled");
const ssl = flag("sslcommerz_enabled");
const sandbox = flag("sslcommerz_sandbox");

const config = useRuntimeConfig();
const callbackBase = computed(() => `${config.public.apiBase.replace(/\/api\/?$/, "")}/api/payments/sslcommerz`);

const enabledCount = computed(() => [cod.value, bkash.value, nagad.value, ssl.value].filter(Boolean).length);

const snackbar = useSnackbar();
const submit = async () => {
  if (enabledCount.value === 0) {
    snackbar.add({ type: "error", text: "Turn on at least one payment method, or customers cannot check out." });
    return;
  }
  if (bkash.value && !/^01[3-9]\d{8}$/.test(form.bkash_number.trim())) {
    snackbar.add({ type: "error", text: "Enter the bKash number customers pay to, like 01712345678." });
    return;
  }
  if (nagad.value && !/^01[3-9]\d{8}$/.test(form.nagad_number.trim())) {
    snackbar.add({ type: "error", text: "Enter the Nagad number customers pay to, like 01712345678." });
    return;
  }
  if (ssl.value && (!form.sslcommerz_store_id.trim() || (!form.sslcommerz_store_password && !masked.sslcommerz_store_password))) {
    snackbar.add({ type: "error", text: "Add the SSLCommerz store ID and store password before turning it on." });
    return;
  }
  await save();
};
</script>

<template>
  <div class="ad-page">
    <header class="ad-head">
      <h1>Payments</h1>
      <p>Choose how customers can pay. Orders paid by bKash or Nagad wait in the order list until you check the TrxID.</p>
    </header>

    <p v-if="loading" class="ad-muted" style="margin-top: 1.4rem">Loading settings...</p>

    <form v-else class="ad-grid two" @submit.prevent="submit">
      <section class="ad-card">
        <h2>Cash and mobile wallets</h2>

        <label class="ad-switch">
          <input v-model="cod" type="checkbox" />
          <span class="track" aria-hidden="true"></span>
          <span><b>Cash on delivery</b><small>The courier collects the money at the door.</small></span>
        </label>

        <label class="ad-switch">
          <input v-model="bkash" type="checkbox" />
          <span class="track" aria-hidden="true"></span>
          <span><b>bKash</b><small>Customers send money, then enter the TrxID at checkout.</small></span>
        </label>
        <label class="ad-field">
          <span>bKash number (Personal)</span>
          <input v-model="form.bkash_number" type="tel" inputmode="tel" placeholder="01712345678" />
        </label>

        <label class="ad-switch">
          <input v-model="nagad" type="checkbox" />
          <span class="track" aria-hidden="true"></span>
          <span><b>Nagad</b><small>Same flow as bKash.</small></span>
        </label>
        <label class="ad-field">
          <span>Nagad number (Personal)</span>
          <input v-model="form.nagad_number" type="tel" inputmode="tel" placeholder="01712345678" />
        </label>
      </section>

      <section class="ad-card">
        <h2>Cards and online banking (SSLCommerz)</h2>

        <label class="ad-switch">
          <input v-model="ssl" type="checkbox" />
          <span class="track" aria-hidden="true"></span>
          <span><b>Accept online payments</b><small>Customers pay on SSLCommerz's secure page.</small></span>
        </label>

        <label class="ad-switch">
          <input v-model="sandbox" type="checkbox" />
          <span class="track" aria-hidden="true"></span>
          <span><b>Test mode</b><small>Use sandbox credentials. Turn this off to take real payments.</small></span>
        </label>

        <label class="ad-field">
          <span>Store ID</span>
          <input v-model="form.sslcommerz_store_id" type="text" autocomplete="off" />
        </label>

        <label class="ad-field">
          <span>Store password</span>
          <input
            v-model="form.sslcommerz_store_password"
            type="password"
            autocomplete="new-password"
            :placeholder="masked.sslcommerz_store_password ? `Saved: ${masked.sslcommerz_store_password}. Type a new one to replace it.` : ''"
          />
          <small>Stored encrypted. It is never shown in full again.</small>
        </label>

        <div class="ad-field">
          <span>Addresses to enter in your SSLCommerz merchant panel</span>
          <small>IPN listener: <code>{{ callbackBase }}/ipn</code></small>
          <small>Success, fail and cancel URLs are sent with every payment, so you do not need to set them.</small>
        </div>
      </section>

      <div class="ad-row" style="grid-column: 1 / -1">
        <button type="submit" class="ad-btn primary" :disabled="saving">{{ saving ? "Saving..." : "Save settings" }}</button>
      </div>
    </form>
  </div>
</template>
