<template>
  <div class="shell">
    <a href="#main" class="skip-link">Skip to content</a>
    <AnnouncementBar />
    <Navbar />
    <main id="main">
      <slot></slot>
    </main>
    <DressnioFooter />
    <FloatingContact />
  </div>
</template>

<style scoped>
.shell {
  background: var(--paper);
}
.skip-link {
  position: absolute;
  left: 0.5rem;
  top: -4rem;
  z-index: 2000;
  padding: 0.6rem 1rem;
  background: var(--brand-deep);
  color: #fff;
  border-radius: 8px;
}
.skip-link:focus {
  top: 0.5rem;
}
</style>
