<script setup>
// Phone-first sign in: number -> 6-digit SMS code -> (new customers) name. No passwords.
const config = useRuntimeConfig();
const route = useRoute();
const token = useTokenStore();
const snackbar = useSnackbar();

const step = ref("phone"); // phone | code | name
const phone = ref("");
const phoneTouched = ref(false);
const sending = ref(false);
const verifying = ref(false);
const digits = ref(["", "", "", "", "", ""]);
const boxes = ref([]);
const codeError = ref("");
const expired = ref(false);
const shake = ref(false);
const notice = ref("");
const devOtp = ref("");
const resendIn = ref(0);
const name = ref("");
const savingName = ref(false);
const nameInput = ref(null);
const phoneInput = ref(null);

let timer = null;

const local = computed(() => phone.value.replace(/\D/g, ""));
const phoneValid = computed(() => /^01[3-9]\d{8}$/.test(local.value));
const phoneError = computed(() => {
  if (!phoneTouched.value || !local.value || phoneValid.value) return "";
  if (local.value.length < 11) return "Mobile numbers have 11 digits, like 01712345678.";
  return "Enter a valid Bangladeshi mobile number, like 01712345678.";
});
const pretty = computed(() => `${local.value.slice(0, 5)} ${local.value.slice(5)}`);
const code = computed(() => digits.value.join(""));

const api = (path) => `${config.public.apiBase}/client/${path}`;

const safeRedirect = () => {
  const r = String(route.query.redirect || "");
  return r.startsWith("/") && !r.startsWith("//") ? r : "/customer";
};

const startTimer = (seconds) => {
  resendIn.value = seconds;
  clearInterval(timer);
  timer = setInterval(() => {
    resendIn.value = Math.max(0, resendIn.value - 1);
    if (resendIn.value === 0) clearInterval(timer);
  }, 1000);
};
onBeforeUnmount(() => clearInterval(timer));

// Strip +88 / 88 country code when a number is pasted or autofilled
const onPhoneInput = () => {
  let d = phone.value.replace(/\D/g, "");
  if (d.startsWith("880")) d = `0${d.slice(3)}`;
  phone.value = d.slice(0, 11);
};

const focusBox = async (i) => {
  await nextTick();
  boxes.value[i]?.focus();
  boxes.value[i]?.select();
};

const sendCode = async () => {
  phoneTouched.value = true;
  if (!phoneValid.value || sending.value) return;
  sending.value = true;
  codeError.value = "";
  notice.value = "";
  try {
    const res = await $fetch(api("send-otp"), {
      method: "POST",
      body: { mobile_no: local.value },
      headers: { Accept: "application/json" },
    });
    devOtp.value = res?.data?.dev_otp || "";
    startTimer(res?.data?.resend_in || 60);
    goToCode();
  } catch (e) {
    const wait = e?.data?.errors?.retry_after;
    if (e?.status === 429 && wait) {
      // a code was sent moments ago, so let them enter it
      notice.value = "We sent a code a moment ago. Enter it below.";
      startTimer(wait);
      goToCode();
    } else {
      codeError.value = "";
      phoneTouched.value = true;
      snackbar.add({ type: "error", text: e?.data?.errors?.mobile_no?.[0] || e?.data?.message || "We could not send the code. Try again.", duration: 5000 });
    }
  } finally {
    sending.value = false;
  }
};

const goToCode = () => {
  digits.value = ["", "", "", "", "", ""];
  expired.value = false;
  codeError.value = "";
  step.value = "code";
  focusBox(0);
};

const resend = async () => {
  if (resendIn.value > 0) return;
  await sendCode();
  if (step.value === "code" && !notice.value) notice.value = "A new code is on its way.";
};

const changeNumber = () => {
  step.value = "phone";
  clearInterval(timer);
  resendIn.value = 0;
  notice.value = "";
  devOtp.value = "";
  nextTick(() => phoneInput.value?.focus());
};

const onDigit = (i, e) => {
  const v = e.target.value.replace(/\D/g, "");
  if (!v) {
    digits.value[i] = "";
    return;
  }
  // autofill and paste arrive as several digits in one box
  if (v.length > 1) {
    spread(v, i);
    return;
  }
  digits.value[i] = v;
  codeError.value = "";
  if (i < 5) focusBox(i + 1);
  else maybeSubmit();
};

const spread = (text, from = 0) => {
  const only = text.replace(/\D/g, "").slice(0, 6 - from);
  only.split("").forEach((d, k) => (digits.value[from + k] = d));
  codeError.value = "";
  const next = Math.min(from + only.length, 5);
  focusBox(next);
  maybeSubmit();
};

const onPaste = (e) => {
  e.preventDefault();
  spread(e.clipboardData.getData("text"), 0);
};

const onKey = (i, e) => {
  if (e.key === "Backspace" && !digits.value[i] && i > 0) {
    digits.value[i - 1] = "";
    focusBox(i - 1);
  } else if (e.key === "ArrowLeft" && i > 0) focusBox(i - 1);
  else if (e.key === "ArrowRight" && i < 5) focusBox(i + 1);
};

const maybeSubmit = () => {
  if (code.value.length === 6) nextTick(verify);
};

const verify = async () => {
  if (code.value.length < 6 || verifying.value) return;
  verifying.value = true;
  codeError.value = "";
  try {
    const res = await $fetch(api("verify-otp"), {
      method: "POST",
      body: { mobile_no: local.value, otp: code.value },
      headers: { Accept: "application/json" },
    });
    token.setToken(res?.data?.token);
    if (res?.data?.is_new) {
      step.value = "name";
      await nextTick();
      nameInput.value?.focus();
    } else {
      done();
    }
  } catch (e) {
    codeError.value = e?.data?.errors?.otp?.[0] || e?.data?.message || "That code did not work. Try again.";
    expired.value = !!e?.data?.errors?.expired;
    shake.value = true;
    setTimeout(() => (shake.value = false), 450);
    if (!expired.value) {
      digits.value = ["", "", "", "", "", ""];
      focusBox(0);
    }
  } finally {
    verifying.value = false;
  }
};

const done = () => {
  snackbar.add({ type: "success", text: "You are signed in" });
  navigateTo(safeRedirect());
};

const saveName = async () => {
  const value = name.value.trim();
  if (!value) return done();
  savingName.value = true;
  try {
    await $fetch(api("me"), {
      method: "PUT",
      body: { name: value },
      headers: { Authorization: `Bearer ${token.getToken}`, Accept: "application/json" },
    });
  } catch (e) {
    // the account works without a name, so carry on
  } finally {
    savingName.value = false;
    done();
  }
};

onMounted(() => phoneInput.value?.focus());
</script>

<template>
  <div class="af">
    <Transition name="swap" mode="out-in">
      <!-- 1. Phone -->
      <form v-if="step === 'phone'" key="phone" class="af-card" novalidate @submit.prevent="sendCode">
        <h1 class="af-title">Sign in or create an account</h1>
        <p class="af-sub">Enter your mobile number. We text you a 6-digit code, so there is no password to remember.</p>

        <label class="af-label" for="af-phone">Mobile number</label>
        <div class="af-phone" :class="{ bad: phoneError, ok: phoneValid }">
          <span class="af-cc" aria-hidden="true">+88</span>
          <input
            id="af-phone"
            ref="phoneInput"
            v-model="phone"
            type="tel"
            inputmode="numeric"
            autocomplete="tel-national"
            placeholder="01712345678"
            maxlength="14"
            :aria-invalid="!!phoneError"
            aria-describedby="af-phone-msg"
            @input="onPhoneInput"
            @blur="phoneTouched = true"
          />
          <Icon v-if="phoneValid" name="solar:check-circle-bold" class="af-tick w-6 h-6" />
        </div>
        <p id="af-phone-msg" class="af-msg" :class="{ err: phoneError }" role="status">
          {{ phoneError || "Works with Grameenphone, Robi, Banglalink, Airtel and Teletalk." }}
        </p>

        <button type="submit" class="pm-btn pm-btn-primary af-btn" :disabled="sending">
          <span v-if="sending" class="spin" aria-hidden="true"></span>
          {{ sending ? "Sending code" : "Send code" }}
        </button>
        <p class="af-fine">
          By continuing you agree to our
          <NuxtLink to="/terms-conditions">terms and conditions</NuxtLink>.
        </p>
      </form>

      <!-- 2. Code -->
      <form v-else-if="step === 'code'" key="code" class="af-card" novalidate @submit.prevent="verify">
        <button type="button" class="af-back" @click="changeNumber">
          <Icon name="solar:arrow-left-linear" class="w-4 h-4" /> Change number
        </button>
        <h1 class="af-title">Enter your code</h1>
        <p class="af-sub">We sent a 6-digit code to <b>{{ pretty }}</b>. It can take up to a minute to arrive.</p>

        <div class="af-code" :class="{ shake }" role="group" aria-label="6-digit code" @paste="onPaste">
          <input
            v-for="(d, i) in digits"
            :key="i"
            :ref="(el) => (boxes[i] = el)"
            :value="digits[i]"
            type="text"
            inputmode="numeric"
            pattern="[0-9]*"
            maxlength="6"
            :autocomplete="i === 0 ? 'one-time-code' : 'off'"
            :aria-label="`Digit ${i + 1}`"
            :class="{ filled: digits[i], bad: codeError }"
            :disabled="expired"
            @input="onDigit(i, $event)"
            @keydown="onKey(i, $event)"
            @focus="$event.target.select()"
          />
        </div>

        <p v-if="notice && !codeError" class="af-msg" role="status">{{ notice }}</p>
        <p v-if="codeError" class="af-msg err" role="alert">{{ codeError }}</p>
        <p v-if="devOtp" class="af-dev">
          Local testing: SMS is not set up, so the code is <b>{{ devOtp }}</b>.
        </p>

        <button type="submit" class="pm-btn pm-btn-primary af-btn" :disabled="verifying || code.length < 6 || expired">
          <span v-if="verifying" class="spin" aria-hidden="true"></span>
          {{ verifying ? "Checking" : "Verify and continue" }}
        </button>

        <p class="af-resend">
          <template v-if="resendIn > 0">Send a new code in <b>{{ resendIn }}s</b></template>
          <template v-else>
            Did not get it?
            <button type="button" :class="{ strong: expired }" :disabled="sending" @click="resend">Send a new code</button>
          </template>
        </p>
      </form>

      <!-- 3. Name -->
      <form v-else key="name" class="af-card" @submit.prevent="saveName">
        <div class="af-welcome" aria-hidden="true"><Icon name="solar:hand-heart-bold" class="w-8 h-8" /></div>
        <h1 class="af-title">Welcome to Dressnio</h1>
        <p class="af-sub">Your account is ready. What should we call you?</p>
        <label class="af-label" for="af-name">Your name</label>
        <input id="af-name" ref="nameInput" v-model="name" class="af-input" type="text" autocomplete="name" placeholder="e.g. Ayesha Rahman" maxlength="80" />
        <button type="submit" class="pm-btn pm-btn-primary af-btn" :disabled="savingName">
          {{ savingName ? "Saving" : name.trim() ? "Save and continue" : "Continue" }}
        </button>
        <button type="button" class="af-skip" @click="done">Skip for now</button>
      </form>
    </Transition>
  </div>
</template>

<style scoped>
.af {
  width: 100%;
  max-width: 26rem;
}
.af-card {
  display: flex;
  flex-direction: column;
}
.af-title {
  font-size: clamp(1.8rem, 3.2vw, 2.4rem);
  line-height: 1.12;
  font-weight: 600;
  color: var(--brand-deep);
  text-wrap: balance;
}
.af-sub {
  margin: 0.8rem 0 1.8rem;
  line-height: 1.65;
  color: var(--ink-soft);
}
.af-sub b {
  color: var(--ink);
  white-space: nowrap;
}
.af-label {
  margin-bottom: 0.5rem;
  font-size: 0.92rem;
  font-weight: 600;
}
.af-phone {
  display: flex;
  align-items: center;
  height: 3.5rem;
  padding: 0 0.9rem 0 0;
  border: 1.5px solid var(--line);
  border-radius: 14px;
  background: #fff;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.af-phone:focus-within {
  border-color: var(--brand);
  box-shadow: 0 0 0 4px rgba(77, 77, 219, 0.14);
}
.af-phone.bad {
  border-color: #d63a6a;
}
.af-cc {
  display: grid;
  place-items: center;
  align-self: stretch;
  padding: 0 0.9rem;
  margin-right: 0.8rem;
  border-right: 1.5px solid var(--line);
  border-radius: 12px 0 0 12px;
  background: var(--brand-tint);
  font-weight: 600;
  color: var(--brand-deep);
}
.af-phone input {
  flex: 1;
  min-width: 0;
  height: 100%;
  font-size: 1.15rem;
  font-weight: 500;
  letter-spacing: 0.04em;
  background: transparent;
}
.af-tick {
  color: #0f8a5f;
}
.af-msg {
  min-height: 1.4rem;
  margin: 0.55rem 0 1.4rem;
  font-size: 0.88rem;
  line-height: 1.45;
  color: var(--ink-soft);
}
.af-msg.err {
  color: #c0285a;
}
.af-btn {
  min-height: 3.4rem;
  width: 100%;
  font-size: 1rem;
}
.af-btn:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}
.af-fine {
  margin-top: 1.2rem;
  font-size: 0.84rem;
  color: var(--ink-soft);
}
.af-fine a {
  color: var(--brand);
  text-decoration: underline;
  text-underline-offset: 3px;
}
.af-back {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  align-self: flex-start;
  margin-bottom: 1.2rem;
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--brand);
}
.af-code {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 0.5rem;
}
.af-code input {
  width: 100%;
  aspect-ratio: 1 / 1.2;
  min-width: 0;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  background: #fff;
  text-align: center;
  font-family: var(--font-display);
  font-size: 1.7rem;
  font-weight: 600;
  color: var(--brand-deep);
  transition: border-color 0.15s, box-shadow 0.15s, background-color 0.15s;
}
.af-code input:focus {
  border-color: var(--brand);
  box-shadow: 0 0 0 4px rgba(77, 77, 219, 0.14);
}
.af-code input.filled {
  background: var(--brand-tint);
  border-color: #b9b9ee;
}
.af-code input.bad {
  border-color: #d63a6a;
  background: #fff5f8;
}
.af-code.shake {
  animation: shake 0.4s;
}
@keyframes shake {
  20%, 60% { transform: translateX(-6px); }
  40%, 80% { transform: translateX(6px); }
}
.af-code + .af-msg,
.af-code ~ .af-msg {
  margin-top: 0.9rem;
}
.af-dev {
  margin: -0.6rem 0 1.2rem;
  padding: 0.7rem 0.9rem;
  border-radius: 10px;
  background: #fff7e0;
  font-size: 0.86rem;
  color: #7a5200;
}
.af-resend {
  margin-top: 1.2rem;
  font-size: 0.92rem;
  text-align: center;
  color: var(--ink-soft);
}
.af-resend button {
  font-weight: 600;
  color: var(--brand);
  text-decoration: underline;
  text-underline-offset: 3px;
}
.af-resend button.strong {
  padding: 0.35rem 0.8rem;
  border-radius: 999px;
  background: var(--brand);
  color: #fff;
  text-decoration: none;
}
.af-welcome {
  display: grid;
  place-items: center;
  width: 3.6rem;
  height: 3.6rem;
  margin-bottom: 1.2rem;
  border-radius: 18px 18px 4px 18px;
  background: var(--brand-tint);
  color: var(--brand);
}
.af-input {
  height: 3.5rem;
  padding: 0 1rem;
  margin-bottom: 1.4rem;
  border: 1.5px solid var(--line);
  border-radius: 14px;
  background: #fff;
  font-size: 1.05rem;
}
.af-input:focus {
  border-color: var(--brand);
  box-shadow: 0 0 0 4px rgba(77, 77, 219, 0.14);
}
.af-skip {
  margin-top: 0.9rem;
  font-weight: 600;
  color: var(--ink-soft);
}
.spin {
  width: 1.1rem;
  height: 1.1rem;
  border: 2.5px solid rgba(255, 255, 255, 0.4);
  border-top-color: #fff;
  border-radius: 50%;
  animation: rot 0.7s linear infinite;
}
@keyframes rot {
  to { transform: rotate(360deg); }
}
.swap-enter-active,
.swap-leave-active {
  transition: opacity 0.18s, transform 0.18s;
}
.swap-enter-from {
  opacity: 0;
  transform: translateX(14px);
}
.swap-leave-to {
  opacity: 0;
  transform: translateX(-14px);
}
</style>
