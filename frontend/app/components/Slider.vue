<script setup>
// Homepage hero. Uses the sliders managed in admin; when a slide has no photo it is drawn from the garment art.
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, Pagination, Navigation, EffectFade, A11y } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import "swiper/css/navigation";
import "swiper/css/effect-fade";

const props = defineProps({
  sliders: { type: Array, default: () => [] },
  featured: { type: Object, default: null },
});

const fallbackSlides = [
  {
    id: "f1",
    title: "Kurta sets for long, warm days",
    subtitle: "Light cotton and lawn, cut to move with you from office to dinner.",
    link: "/shop",
    cta: "Shop kurta sets",
    kinds: ["kurta", "shrug", "top"],
    color: "#4D4DDB",
  },
  {
    id: "f2",
    title: "Lehengas for the wedding season",
    subtitle: "Embroidered skirts with matching dupattas, ready to order in your size.",
    link: "/category/lehengas",
    cta: "See lehengas",
    kinds: ["lehenga", "dress", "maxi"],
    color: "#B8446B",
  },
  {
    id: "f3",
    title: "Sarees worth keeping",
    subtitle: "Soft drapes and woven borders for every celebration on your calendar.",
    link: "/category/sarees",
    cta: "Browse sarees",
    kinds: ["saree", "suit", "kurta"],
    color: "#1F8A70",
  },
  {
    id: "f4",
    title: "Unstitched suits, tailored to you",
    subtitle: "Pick the fabric here and have it cut to your own measurements.",
    link: "/unstitched-suits",
    cta: "Choose a fabric",
    kinds: ["unstitched", "fabric", "suit"],
    color: "#D49A1F",
  },
];

const rotation = [
  ["kurta", "shrug", "top"],
  ["lehenga", "dress", "maxi"],
  ["saree", "suit", "kurta"],
  ["unstitched", "fabric", "suit"],
];
const colors = ["#4D4DDB", "#B8446B", "#1F8A70", "#D49A1F"];
// light garment colours that stand out against the indigo centre arch
const pop = ["#EBB84C", "#F2A7BC", "#86D4B4", "#F3D58E"];

const slides = computed(() => {
  if (props.sliders?.length) {
    return props.sliders.map((s, i) => {
      const guess = garmentKindFor(s.title, s.subtitle, s.link);
      const base = rotation[i % rotation.length];
      const kinds = guess !== "dress" ? [guess, ...base.filter((k) => k !== guess)].slice(0, 3) : base;
      return { ...s, cta: "Shop the collection", kinds, color: colors[i % colors.length] };
    });
  }
  return fallbackSlides;
});

const tags = (slide) => {
  const m = props.featured;
  if (!m) return null;
  const info = priceInfo(m);
  return { name: m.name, price: info.sale, slug: m.slug || m.id };
};
</script>

<template>
  <section class="hero relative overflow-hidden" aria-label="Featured collections">
    <Swiper
      :modules="[Autoplay, Pagination, Navigation, EffectFade, A11y]"
      effect="fade"
      :fade-effect="{ crossFade: true }"
      :loop="slides.length > 1"
      :speed="900"
      :autoplay="{ delay: 6500, disableOnInteraction: false, pauseOnMouseEnter: true }"
      :navigation="{ nextEl: '.hero-next', prevEl: '.hero-prev' }"
      :pagination="{ el: '.hero-dots', clickable: true, bulletClass: 'hero-dot', bulletActiveClass: 'hero-dot-active' }"
      :a11y="{ enabled: true }"
    >
      <SwiperSlide v-for="(slide, i) in slides" :key="slide.id || i">
        <div class="pm-wrap hero-grid">
          <div class="hero-copy">
            <h1 class="hero-title">{{ slide.title }}</h1>
            <p class="hero-sub">{{ slide.subtitle }}</p>
            <div class="hero-actions">
              <NuxtLink :to="slide.link || '/shop'" class="pm-btn pm-btn-primary">
                {{ slide.cta }}
                <Icon name="solar:arrow-right-linear" class="w-4 h-4" />
              </NuxtLink>
              <NuxtLink to="/steal-deals" class="pm-btn pm-btn-ghost">See deals</NuxtLink>
            </div>
          </div>

          <div class="hero-stage" aria-hidden="false">
            <div class="hero-arch arch side left" :style="{ background: toneFromHex(slide.color).tint }">
              <GarmentArt :kind="slide.kinds[1]" :color="toneHexAt(i + 2)" />
            </div>
            <div class="hero-arch arch center">
              <ArtImage
                :src="slide.image"
                :alt="slide.title"
                :kind="slide.kinds[0]"
                :color="pop[i % pop.length]"
                :lazy="i > 0"
                class="center-art"
              />
              <div class="center-glow"></div>
            </div>
            <div class="hero-arch arch side right" :style="{ background: toneFromHex(slide.color).tintDeep }">
              <GarmentArt :kind="slide.kinds[2]" :color="toneHexAt(i + 5)" />
            </div>
            <div class="hero-floor"></div>

            <NuxtLink v-if="tags(slide)" :to="`/product/${tags(slide).slug}`" class="hero-tag">
              <span class="tag-name">{{ tags(slide).name }}</span>
              <span class="tag-price">{{ formatBDT(tags(slide).price) }}</span>
            </NuxtLink>
          </div>
        </div>
      </SwiperSlide>
    </Swiper>

    <div class="pm-wrap hero-controls">
      <div class="hero-dots"></div>
      <div class="hero-arrows">
        <button class="hero-prev" aria-label="Previous slide"><Icon name="solar:arrow-left-linear" class="w-5 h-5" /></button>
        <button class="hero-next" aria-label="Next slide"><Icon name="solar:arrow-right-linear" class="w-5 h-5" /></button>
      </div>
    </div>
  </section>
</template>

<style scoped>
.hero {
  background:
    radial-gradient(60% 80% at 85% 20%, #e4e4fd 0%, transparent 70%),
    linear-gradient(180deg, #f0f0fe 0%, #faf9f6 100%);
}
.hero-grid {
  display: grid;
  grid-template-columns: 1fr;
  align-items: center;
  gap: 2rem;
  padding-top: clamp(2rem, 5vw, 4rem);
  padding-bottom: 5.5rem;
  min-height: 620px;
}
.hero-title {
  font-size: clamp(2.9rem, 6.2vw, 5.6rem);
  line-height: 1.04;
  font-weight: 600;
  color: var(--brand-deep);
  max-width: 14ch;
  text-wrap: balance;
}
.hero-sub {
  margin-top: 1.4rem;
  max-width: 44ch;
  font-size: 1.1rem;
  line-height: 1.65;
  color: var(--ink-soft);
}
.hero-actions {
  margin-top: 2.2rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.8rem;
}

.hero-stage {
  position: relative;
  display: grid;
  grid-template-columns: 0.78fr 1.1fr 0.78fr;
  align-items: end;
  gap: clamp(0.5rem, 1.4vw, 1.2rem);
  height: clamp(340px, 52vw, 520px);
  padding-bottom: 14px;
}
.side {
  height: 76%;
  position: relative;
}
.hero-arch.center {
  height: 100%;
  position: relative;
  background: linear-gradient(170deg, #6b6be6 0%, #4d4ddb 45%, #2a2ad1 100%);
}
.center-art {
  position: relative;
  z-index: 1;
}
.center-art :deep(svg) {
  padding-top: 14%;
}
.center-glow {
  position: absolute;
  inset: 0;
  background: radial-gradient(70% 45% at 50% 30%, rgba(255, 255, 255, 0.35), transparent 70%);
  pointer-events: none;
}
.side :deep(svg) {
  padding: 22% 8% 0;
}
.hero-floor {
  position: absolute;
  left: -2%;
  right: -2%;
  bottom: 0;
  height: 2px;
  background: linear-gradient(90deg, transparent, #151552 20%, #151552 80%, transparent);
  opacity: 0.35;
}
.hero-tag {
  position: absolute;
  left: 4%;
  bottom: 12%;
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
  max-width: 190px;
  padding: 0.8rem 1rem;
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 18px 40px -14px rgba(21, 21, 82, 0.45);
  z-index: 3;
  transition: transform 0.25s ease;
}
.hero-tag:hover {
  transform: translateY(-4px);
}
.tag-name {
  font-size: 0.82rem;
  line-height: 1.3;
  color: var(--ink-soft);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.tag-price {
  font-family: var(--font-display);
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--brand);
}

.hero-controls {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 1.4rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  z-index: 5;
  pointer-events: none;
}
.hero-controls > * {
  pointer-events: auto;
}
.hero-dots {
  display: flex;
  gap: 0.5rem;
}
.hero-arrows {
  display: flex;
  gap: 0.5rem;
}
.hero-arrows button {
  width: 2.75rem;
  height: 2.75rem;
  border-radius: 999px;
  border: 1.5px solid var(--brand-deep);
  color: var(--brand-deep);
  display: grid;
  place-items: center;
  transition: background-color 0.2s, color 0.2s;
}
.hero-arrows button:hover {
  background: var(--brand-deep);
  color: #fff;
}
:global(.hero-dot) {
  width: 0.55rem;
  height: 0.55rem;
  border-radius: 999px;
  background: #b9b9ee;
  cursor: pointer;
  transition: width 0.3s ease, background-color 0.3s;
}
:global(.hero-dot-active) {
  width: 2rem;
  background: var(--brand);
}

@media (min-width: 1024px) {
  .hero-grid {
    grid-template-columns: 5fr 7fr;
    gap: 3rem;
  }
}
@media (max-width: 1023px) {
  .hero-grid {
    padding-bottom: 6rem;
  }
  .hero-stage {
    order: -1;
    height: clamp(280px, 70vw, 420px);
  }
  .hero-copy {
    text-align: left;
  }
}
@media (max-width: 520px) {
  .hero-tag {
    left: 2%;
    bottom: 6%;
    max-width: 150px;
  }
  .hero-arrows {
    display: none;
  }
}
</style>
