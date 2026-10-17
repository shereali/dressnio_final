<script setup>
const { phone, email, address, facebook, instagram, about, whatsappUrl } = useStoreSettings();

const shop = [
  { label: "All products", to: "/shop" },
  { label: "Unstitched suits", to: "/unstitched-suits" },
  { label: "Steal deals", to: "/steal-deals" },
  { label: "Our community", to: "/our-community" },
];
const help = [
  { label: "Track your order", to: "/track-order" },
  { label: "Contact us", to: "/contact" },
  { label: "About Dressnio", to: "/about-us" },
  { label: "Terms and conditions", to: "/terms-conditions" },
];

const socials = computed(() =>
  [
    { label: "Facebook", icon: "ic:baseline-facebook", href: facebook.value },
    { label: "Instagram", icon: "mdi:instagram", href: instagram.value },
    { label: "WhatsApp", icon: "ic:baseline-whatsapp", href: whatsappUrl.value },
  ].filter((s) => s.href)
);

const year = new Date().getFullYear();
</script>

<template>
  <footer class="ft">
    <div class="pm-wrap">
      <div class="ft-news">
        <div>
          <h2 class="ft-news-title">New arrivals and offers, in your inbox</h2>
          <p>One email a week. Unsubscribe whenever you like.</p>
        </div>
        <NewsletterSignup tone="dark" />
      </div>

      <div class="ft-grid">
        <div class="ft-brand">
          <NuxtLink to="/" class="ft-logo" aria-label="Dressnio home">
            <BrandLogo :size="38" dark />
          </NuxtLink>
          <p class="ft-about">
            {{ about || "Stitched and unstitched wear for women: kurta sets, lehengas, sarees and more." }}
          </p>
          <ul v-if="socials.length" class="ft-social">
            <li v-for="s in socials" :key="s.label">
              <a :href="s.href" target="_blank" rel="noopener" :aria-label="s.label">
                <Icon :name="s.icon" class="w-5 h-5" />
              </a>
            </li>
          </ul>
        </div>

        <nav aria-label="Shop">
          <h3>Shop</h3>
          <ul>
            <li v-for="l in shop" :key="l.to"><NuxtLink :to="l.to">{{ l.label }}</NuxtLink></li>
          </ul>
        </nav>

        <nav aria-label="Help">
          <h3>Help</h3>
          <ul>
            <li v-for="l in help" :key="l.to"><NuxtLink :to="l.to">{{ l.label }}</NuxtLink></li>
          </ul>
        </nav>

        <div>
          <h3>Visit or call</h3>
          <ul class="ft-contact">
            <li v-if="address"><Icon name="solar:map-point-linear" class="w-5 h-5" />{{ address }}</li>
            <li v-if="phone"><Icon name="solar:phone-linear" class="w-5 h-5" /><a :href="`tel:${phone.replace(/[^\d+]/g, '')}`">{{ phone }}</a></li>
            <li v-if="email"><Icon name="solar:letter-linear" class="w-5 h-5" /><a :href="`mailto:${email}`">{{ email }}</a></li>
          </ul>
          <p class="ft-pay">We accept cash on delivery, bKash, Nagad and cards.</p>
        </div>
      </div>

      <div class="ft-bottom">
        <p>&copy; {{ year }} Dressnio. All rights reserved.</p>
        <p>Prices are in Bangladeshi taka (৳).</p>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.ft {
  background: var(--brand-deep);
  color: rgba(255, 255, 255, 0.78);
  padding-top: clamp(2.5rem, 5vw, 4rem);
}
.ft-news {
  display: grid;
  gap: 1.4rem;
  align-items: center;
  padding-bottom: clamp(2rem, 4vw, 3rem);
  border-bottom: 1px solid rgba(255, 255, 255, 0.14);
}
@media (min-width: 900px) {
  .ft-news {
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
  }
}
.ft-news-title {
  font-size: clamp(1.5rem, 2.6vw, 2.2rem);
  line-height: 1.15;
  font-weight: 600;
  color: #fff;
}
.ft-news p {
  margin-top: 0.5rem;
  font-size: 0.95rem;
}
.ft-grid {
  display: grid;
  gap: 2.4rem 2rem;
  padding: clamp(2rem, 4vw, 3.2rem) 0;
  grid-template-columns: 1fr 1fr;
}
@media (min-width: 900px) {
  .ft-grid {
    grid-template-columns: 1.5fr 1fr 1fr 1.4fr;
  }
}
.ft-brand {
  grid-column: 1 / -1;
}
@media (min-width: 900px) {
  .ft-brand {
    grid-column: auto;
  }
}
.ft-logo {
  --bl-eye: var(--brand-deep);
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  color: #fff;
}
.ft-mark {
  display: grid;
  place-items: center;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 12px;
  background: var(--brand);
  font-family: var(--font-display);
  font-weight: 700;
  font-size: 1.3rem;
}
.ft-word {
  font-family: var(--font-display);
  font-size: 1.6rem;
  font-weight: 600;
}
.ft-word b {
  font-weight: 600;
  color: #aaaaf2;
}
.ft-about {
  margin-top: 1rem;
  max-width: 40ch;
  font-size: 0.95rem;
  line-height: 1.7;
}
.ft-grid .ft-social {
  list-style: none;
  display: flex;
  gap: 0.6rem;
  margin-top: 1.2rem;
}
.ft-social a {
  display: grid;
  place-items: center;
  width: 2.7rem;
  height: 2.7rem;
  border-radius: 999px;
  border: 1px solid rgba(255, 255, 255, 0.25);
  color: #fff;
  transition: background-color 0.2s, border-color 0.2s;
}
.ft-social a:hover {
  background: var(--brand);
  border-color: var(--brand);
}
h3 {
  font-family: var(--font-body);
  font-size: 0.95rem;
  font-weight: 700;
  letter-spacing: 0;
  color: #fff;
  margin-bottom: 1rem;
}
.ft-grid ul {
  list-style: none;
  display: grid;
  gap: 0.7rem;
  font-size: 0.95rem;
}
.ft-grid a:hover {
  color: #fff;
  text-decoration: underline;
  text-underline-offset: 4px;
}
.ft-contact li {
  display: flex;
  gap: 0.6rem;
  align-items: flex-start;
  line-height: 1.5;
}
.ft-contact svg {
  flex-shrink: 0;
  margin-top: 0.15rem;
  color: #aaaaf2;
}
.ft-pay {
  margin-top: 1.1rem;
  font-size: 0.88rem;
  line-height: 1.6;
}
.ft-bottom {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 0.5rem 1.5rem;
  padding: 1.4rem 0 1.8rem;
  border-top: 1px solid rgba(255, 255, 255, 0.14);
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.6);
}
</style>
