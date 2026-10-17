<script setup>
// SMS gateway settings used for login codes (OTP). The API key is stored encrypted and only shown masked.
definePageMeta({ middleware: ["auth"] });

const config = useRuntimeConfig();
const token = useTokenStore();
const snackbar = useSnackbar();

const headers = computed(() => ({
  Authorization: `Bearer ${token.getToken}`,
  Accept: "application/json",
}));

const form = reactive({
  sms_enabled: false,
  sms_api_key: "",
  sms_caller_id: "",
  sms_endpoint: "https://bulksmsdhaka.net/api/otpsend",
  sms_otp_template: "",
});
const keyMasked = ref("");
const showKey = ref(false);
const loading = ref(true);
const saving = ref(false);
const testNumber = ref("");
const testing = ref(false);
const testResult = ref(null);

const defaultTemplate = "Your Dressnio code is {otp}. It is valid for {minutes} minutes. Do not share it with anyone.";

const load = async () => {
  try {
    const res = await $fetch(`${config.public.apiBase}/settings`, { headers: headers.value });
    const d = res?.data || {};
    form.sms_enabled = d.sms_enabled === "1";
    form.sms_caller_id = d.sms_caller_id || "";
    form.sms_endpoint = d.sms_endpoint || form.sms_endpoint;
    form.sms_otp_template = d.sms_otp_template || defaultTemplate;
    keyMasked.value = d.sms_api_key || "";
  } catch (e) {
    snackbar.add({ type: "error", text: "Could not load the SMS settings. Refresh to try again." });
  } finally {
    loading.value = false;
  }
};
onMounted(load);

const preview = computed(() =>
  (form.sms_otp_template || defaultTemplate).replace("{otp}", "482913").replace("{minutes}", "5")
);
const parts = computed(() => Math.ceil(preview.value.length / 160));
const missing = computed(() => {
  const list = [];
  if (!form.sms_api_key && !keyMasked.value) list.push("API key");
  if (!form.sms_caller_id) list.push("caller ID");
  return list;
});

const save = async () => {
  if (!form.sms_otp_template.includes("{otp}")) {
    snackbar.add({ type: "error", text: "The message must include {otp} so customers receive their code." });
    return;
  }
  if (form.sms_enabled && missing.value.length) {
    snackbar.add({ type: "error", text: `Add the ${missing.value.join(" and ")} before turning SMS on.` });
    return;
  }
  saving.value = true;
  try {
    await $fetch(`${config.public.apiBase}/settings`, {
      method: "PUT",
      headers: headers.value,
      body: {
        sms_enabled: form.sms_enabled ? "1" : "0",
        sms_caller_id: form.sms_caller_id.trim(),
        sms_endpoint: form.sms_endpoint.trim(),
        sms_otp_template: form.sms_otp_template,
        // empty keeps the stored key
        sms_api_key: form.sms_api_key.trim(),
      },
    });
    snackbar.add({ type: "success", text: "SMS settings saved" });
    form.sms_api_key = "";
    showKey.value = false;
    await load();
  } catch (e) {
    snackbar.add({ type: "error", text: e?.data?.message || "Could not save the SMS settings." });
  } finally {
    saving.value = false;
  }
};

const sendTest = async () => {
  testResult.value = null;
  testing.value = true;
  try {
    await $fetch(`${config.public.apiBase}/settings/sms-test`, {
      method: "POST",
      headers: headers.value,
      body: { number: testNumber.value.trim() },
    });
    testResult.value = { ok: true, text: "Test SMS sent. Check that phone." };
  } catch (e) {
    const err = e?.data?.errors?.number?.[0] || e?.data?.message || "The test SMS failed.";
    testResult.value = { ok: false, text: err };
  } finally {
    testing.value = false;
  }
};
</script>

<template>
  <div class="sms">
    <header class="sms-head">
      <h1>SMS and login codes</h1>
      <p>Customers sign in with a one-time code sent by SMS. Connect your bulksmsdhaka.net account here.</p>
    </header>

    <p v-if="loading" class="muted">Loading settings...</p>

    <form v-else class="sms-grid" @submit.prevent="save">
      <section class="card">
        <label class="switch">
          <input v-model="form.sms_enabled" type="checkbox" />
          <span class="track" aria-hidden="true"></span>
          <span>
            <b>Send login codes by SMS</b>
            <small>When off, customers cannot sign in.</small>
          </span>
        </label>

        <label class="field">
          <span>API key</span>
          <div class="with-btn">
            <input
              v-model="form.sms_api_key"
              :type="showKey ? 'text' : 'password'"
              autocomplete="off"
              :placeholder="keyMasked ? `Saved: ${keyMasked}. Type a new key to replace it.` : 'Paste your bulksmsdhaka.net API key'"
            />
            <button type="button" @click="showKey = !showKey">{{ showKey ? "Hide" : "Show" }}</button>
          </div>
          <small>Stored encrypted. It is never shown in full again.</small>
        </label>

        <label class="field">
          <span>Caller ID</span>
          <input v-model="form.sms_caller_id" type="text" inputmode="numeric" placeholder="e.g. 1234" />
          <small>The sender ID approved on your SMS account.</small>
        </label>

        <label class="field">
          <span>Gateway URL</span>
          <input v-model="form.sms_endpoint" type="url" />
          <small>Change this only if your provider gives you a different OTP endpoint.</small>
        </label>

        <label class="field">
          <span>Message</span>
          <textarea v-model="form.sms_otp_template" rows="3"></textarea>
          <small>Use <code>{otp}</code> for the code and <code>{minutes}</code> for how long it lasts.</small>
        </label>

        <button type="submit" class="primary" :disabled="saving">{{ saving ? "Saving..." : "Save settings" }}</button>
      </section>

      <aside class="side">
        <section class="card">
          <h2>What customers receive</h2>
          <div class="phone">
            <p>{{ preview }}</p>
          </div>
          <small>{{ preview.length }} characters, {{ parts }} SMS {{ parts === 1 ? "part" : "parts" }}.</small>
        </section>

        <section class="card">
          <h2>Send a test</h2>
          <p class="muted">Save your settings first, then send a message to your own phone.</p>
          <div class="with-btn">
            <input v-model="testNumber" type="tel" inputmode="tel" placeholder="01712345678" aria-label="Phone number for the test message" />
            <button type="button" :disabled="testing || !testNumber" @click="sendTest">{{ testing ? "Sending..." : "Send test" }}</button>
          </div>
          <p v-if="testResult" class="result" :class="{ ok: testResult.ok }" role="status">{{ testResult.text }}</p>
        </section>
      </aside>
    </form>
  </div>
</template>

<style scoped>
.sms {
  max-width: 1040px;
  padding: 1.5rem;
}
.sms-head h1 {
  font-size: 1.6rem;
  font-weight: 700;
  color: #151552;
}
.sms-head p,
.muted {
  margin-top: 0.4rem;
  color: #55556e;
}
.sms-grid {
  display: grid;
  gap: 1.2rem;
  margin-top: 1.4rem;
}
@media (min-width: 900px) {
  .sms-grid {
    grid-template-columns: 1.3fr 1fr;
    align-items: start;
  }
}
.card {
  display: grid;
  gap: 1.1rem;
  padding: 1.4rem;
  background: #fff;
  border: 1px solid #e4e4f2;
  border-radius: 16px;
}
.side {
  display: grid;
  gap: 1.2rem;
}
.card h2 {
  font-weight: 700;
  color: #151552;
}
.field {
  display: grid;
  gap: 0.35rem;
}
.field > span {
  font-weight: 600;
  font-size: 0.92rem;
}
small {
  color: #6b6b80;
  font-size: 0.82rem;
}
input[type="text"],
input[type="password"],
input[type="url"],
input[type="tel"],
textarea {
  width: 100%;
  min-height: 2.8rem;
  padding: 0.6rem 0.85rem;
  border: 1.5px solid #e4e4f2;
  border-radius: 10px;
  background: #fff;
}
input:focus,
textarea:focus {
  border-color: #4d4ddb;
  box-shadow: 0 0 0 3px rgba(77, 77, 219, 0.15);
}
.with-btn {
  display: flex;
  gap: 0.5rem;
}
.with-btn button {
  flex-shrink: 0;
  padding: 0 1rem;
  border: 1.5px solid #4d4ddb;
  border-radius: 10px;
  color: #4d4ddb;
  font-weight: 600;
}
.with-btn button:disabled {
  opacity: 0.5;
}
.primary {
  min-height: 2.9rem;
  border-radius: 10px;
  background: #4d4ddb;
  color: #fff;
  font-weight: 600;
}
.primary:hover {
  background: #3e3ebe;
}
.primary:disabled {
  opacity: 0.6;
}
.switch {
  display: flex;
  gap: 0.9rem;
  align-items: center;
  cursor: pointer;
}
.switch input {
  position: absolute;
  opacity: 0;
}
.track {
  position: relative;
  flex-shrink: 0;
  width: 2.8rem;
  height: 1.6rem;
  border-radius: 999px;
  background: #c9c9dc;
  transition: background 0.2s;
}
.track::after {
  content: "";
  position: absolute;
  top: 0.2rem;
  left: 0.2rem;
  width: 1.2rem;
  height: 1.2rem;
  border-radius: 50%;
  background: #fff;
  transition: transform 0.2s;
}
.switch input:checked + .track {
  background: #4d4ddb;
}
.switch input:checked + .track::after {
  transform: translateX(1.2rem);
}
.switch input:focus-visible + .track {
  outline: 2px solid #4d4ddb;
  outline-offset: 2px;
}
.switch b {
  display: block;
}
.phone {
  padding: 1rem;
  border-radius: 18px 18px 18px 4px;
  background: #f0f0fe;
  line-height: 1.55;
}
.result {
  font-size: 0.9rem;
  color: #c0285a;
}
.result.ok {
  color: #0f8a5f;
}
code {
  padding: 0 0.3rem;
  background: #f0f0fe;
  border-radius: 4px;
}
</style>
