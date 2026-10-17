<script setup>
// Three editorial panels that route into real sections of the store.
const { freeShippingAbove } = useStoreSettings();
</script>

<template>
  <section class="pm-section bento-sec" aria-label="Featured edits">
    <div class="pm-wrap bento">
      <NuxtLink to="/unstitched-suits" class="panel panel-main">
        <div class="panel-copy">
          <h2>Unstitched suits, cut to your measure</h2>
          <p>Choose the fabric online, send your measurements, and have it tailored your way.</p>
          <span class="pm-btn pm-btn-light">Choose a fabric</span>
        </div>
        <div class="panel-art">
          <GarmentArt kind="unstitched" color="#EBB84C" class="art-a" />
          <GarmentArt kind="fabric" color="#F2A7BC" class="art-b" />
        </div>
      </NuxtLink>

      <NuxtLink to="/steal-deals" class="panel panel-deals">
        <div class="panel-copy">
          <h2>Steal deals</h2>
          <p>Marked-down pieces while stock lasts.</p>
          <span class="panel-link">Shop deals <Icon name="solar:arrow-right-linear" class="w-4 h-4" /></span>
        </div>
        <GarmentArt kind="maxi" color="#B8446B" class="panel-solo" />
      </NuxtLink>

      <NuxtLink to="/shop" class="panel panel-ship">
        <div class="panel-copy">
          <h2>Free delivery over {{ formatBDT(freeShippingAbove) }}</h2>
          <p>Fill your bag with two or three pieces and shipping is on us.</p>
          <span class="panel-link">Start shopping <Icon name="solar:arrow-right-linear" class="w-4 h-4" /></span>
        </div>
        <GarmentArt kind="saree" color="#1F8A70" class="panel-solo" />
      </NuxtLink>
    </div>
  </section>
</template>

<style scoped>
.bento-sec {
  background: #fff;
}
.bento {
  display: grid;
  gap: 1.2rem;
  grid-template-columns: 1fr;
}
.panel {
  position: relative;
  display: flex;
  overflow: hidden;
  border-radius: 26px;
  min-height: 250px;
  isolation: isolate;
}
.panel h2 {
  font-size: clamp(1.5rem, 2.4vw, 2.1rem);
  line-height: 1.15;
  font-weight: 600;
}
.panel p {
  margin-top: 0.7rem;
  max-width: 34ch;
  line-height: 1.6;
  font-size: 0.98rem;
}
.panel-copy {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;
  padding: clamp(1.4rem, 3vw, 2.4rem);
  max-width: 62%;
}
.panel-main {
  background: linear-gradient(150deg, #4d4ddb 0%, #2a2ad1 100%);
  color: #fff;
}
.panel-main p {
  color: rgba(255, 255, 255, 0.82);
}
.panel-main .pm-btn {
  margin-top: 1.6rem;
}
.panel-art {
  position: absolute;
  right: 0;
  bottom: 0;
  top: 0;
  width: 46%;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  gap: 0.5rem;
  padding: 1.2rem 1.2rem 0.6rem;
}
.art-a {
  width: 58%;
  height: auto;
  transform: rotate(-4deg);
}
.art-b {
  width: 50%;
  height: auto;
  transform: rotate(5deg) translateY(10px);
}
.panel-deals {
  background: #fbe4ea;
  color: #5a1530;
}
.panel-ship {
  background: #e3f4ee;
  color: #0f4a3a;
}
.panel-solo {
  position: absolute;
  right: 0.8rem;
  bottom: -0.3rem;
  width: auto;
  height: 92%;
  transition: transform 0.4s cubic-bezier(0.2, 0.7, 0.2, 1);
}
.panel:hover .panel-solo {
  transform: translateY(-8px) rotate(-2deg);
}
.panel-link {
  margin-top: 1.1rem;
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  font-weight: 600;
  border-bottom: 1.5px solid currentColor;
  padding-bottom: 0.2rem;
}
@media (min-width: 900px) {
  .bento {
    grid-template-columns: 1.35fr 1fr;
    grid-template-rows: 1fr 1fr;
  }
  .panel-main {
    grid-row: span 2;
    min-height: 520px;
  }
  .panel-main .panel-copy {
    max-width: 52%;
    justify-content: flex-end;
    padding-bottom: 3rem;
  }
  .panel-art {
    width: 52%;
  }
}
@media (max-width: 520px) {
  .panel-copy {
    max-width: 70%;
  }
  .panel-art {
    opacity: 0.9;
  }
}
</style>
