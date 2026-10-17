<script setup>
// Newsletter form wired to POST /client/newsletter.
const props = defineProps({ tone: { type: String, default: "light" } });

const config = useRuntimeConfig();
const email = ref("");
const state = ref("idle"); // idle | loading | done | error
const message = ref("");

const submit = async () => {
  if (state.value === "loading") return;
  if (!/^\S+@\S+\.\S+$/.test(email.value)) {
    state.value = "error";
    message.value = "Enter a valid email address.";
    return;
  }
  state.value = "loading";
  try {
    const res = await $fetch(`${config.public.apiBase}/client/newsletter`, {
      method: "POST",
      body: { email: email.value },
      headers: { Accept: "application/json" },
    });
    state.value = "done";
    message.value = res?.message || "You are on the list. Watch your inbox.";
    email.value = "";
  } catch (e) {
    state.value = "error";
    message.value =
      e?.data?.errors?.email?.[0] || e?.data?.message || "We could not subscribe you right now. Please try again.";
  }
};
</script>

<template>
  <form class="nl" :class="`nl-${props.tone}`" novalidate @submit.prevent="submit">
    <label class="sr-only" for="nl-email">Email address</label>
    <div class="nl-row">
      <input
        id="nl-email"
        v-model="email"
        type="email"
        autocomplete="email"
        placeholder="Your email address"
        :aria-invalid="state === 'error'"
        aria-describedby="nl-msg"
      />
      <button type="submit" class="pm-btn pm-btn-primary" :disabled="state === 'loading'">
        {{ state === "loading" ? "Subscribing" : "Subscribe" }}
      </button>
    </div>
    <p id="nl-msg" class="nl-msg" :class="state" role="status" aria-live="polite">{{ message }}</p>
  </form>
</template>

<style scoped>
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  overflow: hidden;
  clip: rect(0 0 0 0);
}
.nl-row {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}
.nl input {
  flex: 1 1 14rem;
  min-width: 0;
  height: 3rem;
  padding: 0 1.1rem;
  border-radius: 999px;
  border: 1.5px solid var(--line);
  background: #fff;
  color: var(--ink);
  font-size: 0.98rem;
}
.nl input:focus {
  border-color: var(--brand);
  box-shadow: 0 0 0 4px rgba(77, 77, 219, 0.16);
}
.nl-dark input {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(255, 255, 255, 0.22);
  color: #fff;
}
.nl-dark input::placeholder {
  color: rgba(255, 255, 255, 0.55);
}
.nl-msg {
  min-height: 1.3rem;
  margin-top: 0.6rem;
  font-size: 0.88rem;
}
.nl-msg.done {
  color: #0f8a5f;
}
.nl-msg.error {
  color: #d63a6a;
}
.nl-dark .nl-msg.done {
  color: #7fe0b8;
}
.nl-dark .nl-msg.error {
  color: #ff9db8;
}
.pm-btn:disabled {
  opacity: 0.7;
}
</style>
