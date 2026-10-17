<script setup>
// Order by chat or phone: the fastest path for customers who prefer to talk to someone.
const { whatsappUrl, phone } = useStoreSettings();
const route = useRoute();
// The product page and checkout already carry their own order buttons
const hidden = computed(() => /^\/(product|checkout)/.test(route.path));
const telHref = computed(() => (phone.value ? `tel:${phone.value.replace(/[^\d+]/g, "")}` : ""));
</script>

<template>
  <div v-if="(whatsappUrl || telHref) && !hidden" class="fc">
    <a v-if="telHref" :href="telHref" class="fc-btn fc-call" aria-label="Call to order">
      <Icon name="solar:phone-calling-bold" class="w-6 h-6" />
    </a>
    <a
      v-if="whatsappUrl"
      :href="`${whatsappUrl}?text=${encodeURIComponent('Hi Dressnio, I would like to order')}`"
      target="_blank"
      rel="noopener"
      class="fc-btn fc-wa"
      aria-label="Order on WhatsApp"
    >
      <Icon name="ic:baseline-whatsapp" class="w-7 h-7" />
      <span class="fc-label">Order on WhatsApp</span>
    </a>
  </div>
</template>

<style scoped>
.fc {
  position: fixed;
  right: 1rem;
  bottom: 1rem;
  z-index: 800;
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  align-items: flex-end;
}
.fc-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  min-height: 3.2rem;
  min-width: 3.2rem;
  justify-content: center;
  padding: 0 0.9rem;
  border-radius: 999px;
  color: #fff;
  font-weight: 600;
  font-size: 0.9rem;
  box-shadow: 0 12px 26px -10px rgba(0, 0, 0, 0.45);
  transition: transform 0.2s;
}
.fc-btn:hover {
  transform: translateY(-2px);
}
.fc-wa {
  background: #1faa59;
}
.fc-call {
  background: var(--brand-deep);
}
.fc-label {
  display: none;
}
@media (min-width: 900px) {
  .fc-label {
    display: inline;
  }
  .fc-wa {
    padding: 0 1.2rem 0 1rem;
  }
}
</style>
