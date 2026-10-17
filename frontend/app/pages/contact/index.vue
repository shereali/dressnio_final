<script setup>
// Contact form posts to /client/contact; store details come from admin settings.
const config = useRuntimeConfig();
const snackbar = useSnackbar();
const { phone, email, address, whatsappUrl } = useStoreSettings();

const form = reactive({ name: "", email: "", phone: "", subject: "", message: "" });
const errors = ref({});
const sending = ref(false);
const sent = ref(false);

const submit = async () => {
  errors.value = {};
  if (!form.name.trim()) errors.value.name = ["Enter your name."];
  if (!/^\S+@\S+\.\S+$/.test(form.email)) errors.value.email = ["Enter a valid email address."];
  if (form.message.trim().length < 5) errors.value.message = ["Write a few words about how we can help."];
  if (Object.keys(errors.value).length) return;

  sending.value = true;
  try {
    await $fetch(`${config.public.apiBase}/client/contact`, {
      method: "POST",
      body: { ...form },
      headers: { Accept: "application/json" },
    });
    sent.value = true;
  } catch (e) {
    errors.value = e?.data?.errors || {};
    snackbar.add({ type: "error", text: e?.data?.message || "We could not send your message. Try again." });
  } finally {
    sending.value = false;
  }
};

useSeoMeta({ title: "Contact us | Dressnio", description: "Questions about an order, a size or a fabric? Message the Dressnio team." });
</script>

<template>
  <div>
    <PageHero title="Contact us" subtitle="Questions about an order, a size or a fabric? Send a message and we will reply by email or phone." :crumbs="[{ label: 'Contact' }]" kind="top" />

    <section class="pm-section ct">
      <div class="pm-wrap ct-grid">
        <div v-if="sent" class="ct-done" role="status">
          <Icon name="solar:check-circle-bold" class="w-12 h-12" />
          <h2>Message sent</h2>
          <p>Thank you, {{ form.name }}. We will get back to you at {{ form.email }}.</p>
          <NuxtLink to="/shop" class="pm-btn pm-btn-ghost">Keep shopping</NuxtLink>
        </div>

        <form v-else class="ct-form" novalidate @submit.prevent="submit">
          <div class="row">
            <label>
              <span>Name</span>
              <input v-model="form.name" type="text" autocomplete="name" :aria-invalid="!!errors.name" />
              <small v-if="errors.name" class="err">{{ errors.name[0] }}</small>
            </label>
            <label>
              <span>Email</span>
              <input v-model="form.email" type="email" autocomplete="email" :aria-invalid="!!errors.email" />
              <small v-if="errors.email" class="err">{{ errors.email[0] }}</small>
            </label>
          </div>
          <div class="row">
            <label>
              <span>Phone (optional)</span>
              <input v-model="form.phone" type="tel" autocomplete="tel" />
            </label>
            <label>
              <span>Subject (optional)</span>
              <input v-model="form.subject" type="text" />
            </label>
          </div>
          <label>
            <span>Message</span>
            <textarea v-model="form.message" rows="6" :aria-invalid="!!errors.message"></textarea>
            <small v-if="errors.message" class="err">{{ errors.message[0] }}</small>
          </label>
          <button type="submit" class="pm-btn pm-btn-primary" :disabled="sending">{{ sending ? "Sending" : "Send message" }}</button>
        </form>

        <aside class="ct-info">
          <h2>Reach us directly</h2>
          <ul>
            <li v-if="phone"><Icon name="solar:phone-linear" class="w-5 h-5" /><a :href="`tel:${phone.replace(/[^\d+]/g, '')}`">{{ phone }}</a></li>
            <li v-if="whatsappUrl"><Icon name="ic:baseline-whatsapp" class="w-5 h-5" /><a :href="whatsappUrl" target="_blank" rel="noopener">Chat on WhatsApp</a></li>
            <li v-if="email"><Icon name="solar:letter-linear" class="w-5 h-5" /><a :href="`mailto:${email}`">{{ email }}</a></li>
            <li v-if="address"><Icon name="solar:map-point-linear" class="w-5 h-5" /><span>{{ address }}</span></li>
          </ul>
        </aside>
      </div>
    </section>
  </div>
</template>

<style scoped>
.ct {
  background: var(--paper);
  padding-top: 2.5rem;
}
.ct-grid {
  display: grid;
  gap: 2.5rem;
}
@media (min-width: 900px) {
  .ct-grid {
    grid-template-columns: 1.4fr 1fr;
    gap: 4rem;
  }
}
.ct-form {
  display: grid;
  gap: 1.1rem;
}
.row {
  display: grid;
  gap: 1.1rem;
}
@media (min-width: 640px) {
  .row {
    grid-template-columns: 1fr 1fr;
  }
}
label span {
  display: block;
  margin-bottom: 0.4rem;
  font-size: 0.9rem;
  font-weight: 600;
}
input,
textarea {
  width: 100%;
  padding: 0.8rem 1rem;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  background: #fff;
}
input:focus,
textarea:focus {
  border-color: var(--brand);
  box-shadow: 0 0 0 4px rgba(77, 77, 219, 0.14);
}
input[aria-invalid="true"],
textarea[aria-invalid="true"] {
  border-color: #d63a6a;
}
.err {
  display: block;
  margin-top: 0.35rem;
  font-size: 0.86rem;
  color: #c0285a;
}
.ct-form .pm-btn {
  justify-self: start;
}
.ct-info {
  align-self: start;
  padding: 1.6rem;
  border-radius: 20px;
  background: #fff;
  border: 1px solid var(--line);
}
.ct-info h2 {
  font-size: 1.5rem;
  color: var(--brand-deep);
}
.ct-info ul {
  list-style: none;
  display: grid;
  gap: 1rem;
  margin-top: 1.2rem;
}
.ct-info li {
  display: flex;
  gap: 0.8rem;
  align-items: flex-start;
  line-height: 1.5;
}
.ct-info svg {
  flex-shrink: 0;
  margin-top: 0.15rem;
  color: var(--brand);
}
.ct-info a:hover {
  color: var(--brand);
  text-decoration: underline;
}
.ct-done {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0.8rem;
  color: #0f8a5f;
}
.ct-done h2 {
  font-size: 2rem;
  color: var(--brand-deep);
}
.ct-done p {
  color: var(--ink-soft);
}
</style>
