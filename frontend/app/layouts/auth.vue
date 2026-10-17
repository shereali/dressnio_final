<template>
  <div class="au">
    <aside class="au-art" aria-hidden="true">
      <NuxtLink to="/" class="au-logo" tabindex="-1"><BrandLogo :size="38" dark /></NuxtLink>
      <div class="au-stage">
        <div class="au-arch arch"><GarmentArt kind="lehenga" color="#EBB84C" /></div>
        <div class="au-arch arch small"><GarmentArt kind="kurta" color="#F2A7BC" /></div>
      </div>
      <p class="au-line">Your orders, wishlist and saved details in one place.</p>
    </aside>

    <main class="au-main">
      <header class="au-top">
        <NuxtLink to="/" class="au-logo dark" aria-label="Dressnio home"><BrandLogo :size="34" /></NuxtLink>
        <NuxtLink to="/shop" class="au-skip">Continue as guest</NuxtLink>
      </header>
      <div class="au-body"><slot /></div>
      <p class="au-help">Need help signing in? <NuxtLink to="/contact">Contact us</NuxtLink></p>
    </main>
    <TheSnackbar />
  </div>
</template>

<style scoped>
.au {
  min-height: 100vh;
  display: grid;
  background: var(--paper);
}
.au-art {
  display: none;
}
.au-main {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  padding: 1.2rem clamp(1.2rem, 4vw, 3rem);
}
.au-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.au-body {
  flex: 1;
  display: grid;
  place-items: center;
  padding: 2.5rem 0;
}
.au-help {
  text-align: center;
  font-size: 0.9rem;
  color: var(--ink-soft);
}
.au-help a,
.au-skip {
  font-weight: 600;
  color: var(--brand);
}
.au-logo {
  --bl-eye: #4d4ddb;
  display: inline-flex;
  align-items: center;
  gap: 0.55rem;
  color: #fff;
}
.au-logo.dark {
  color: var(--brand-deep);
}
.au-mark {
  display: grid;
  place-items: center;
  width: 2.3rem;
  height: 2.3rem;
  border-radius: 12px 12px 4px 4px;
  background: var(--brand);
  color: #fff;
  font-family: var(--font-display);
  font-weight: 700;
  font-size: 1.25rem;
}
.au-art .au-mark {
  background: #fff;
  color: var(--brand);
}
.au-word {
  font-family: var(--font-display);
  font-size: 1.5rem;
  font-weight: 600;
}
.au-word b {
  font-weight: 600;
  color: var(--brand);
}
.au-art .au-word b {
  color: #c8c8fa;
}
@media (min-width: 960px) {
  .au {
    grid-template-columns: 5fr 6fr;
  }
  .au-art {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 2rem 3rem;
    background: linear-gradient(165deg, #5b5be3 0%, #4d4ddb 45%, #2a2ad1 100%);
  }
  .au-top .au-logo {
    display: none;
  }
  .au-top {
    justify-content: flex-end;
  }
}
.au-stage {
  display: flex;
  align-items: flex-end;
  justify-content: center;
  gap: 1rem;
  height: 26rem;
  border-bottom: 2px solid rgba(255, 255, 255, 0.4);
}
.au-arch {
  width: 15rem;
  height: 24rem;
  background: rgba(255, 255, 255, 0.14);
}
.au-arch.small {
  width: 10rem;
  height: 17rem;
  background: rgba(255, 255, 255, 0.08);
}
.au-arch :deep(svg) {
  padding: 18% 8% 0;
}
.au-line {
  max-width: 24ch;
  font-family: var(--font-display);
  font-size: 1.7rem;
  line-height: 1.25;
  color: #fff;
}
</style>
