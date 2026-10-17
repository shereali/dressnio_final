<script setup>
// Size finder: maps bust and hip measurements to a standard size. Works fully in the browser.
const chart = SIZE_CHART;

const bust = ref("");
const hip = ref("");
const unit = ref("in");

const toInches = (v) => (unit.value === "cm" ? Number(v) / 2.54 : Number(v));

const result = computed(() => {
  const b = toInches(bust.value);
  const h = toInches(hip.value);
  if (!bust.value || !(b >= 26 && b <= 50)) return null;
  const idx = (value, key) => {
    const i = chart.findIndex((r) => value <= r[key][1]);
    return i === -1 ? chart.length - 1 : i;
  };
  let i = idx(b, "bust");
  if (hip.value && h >= 30 && h <= 52) i = Math.max(i, idx(h, "hip"));
  return chart[i];
});

const invalid = computed(() => !!bust.value && !result.value);
</script>

<template>
  <section class="pm-section fit" aria-labelledby="fit-title">
    <div class="pm-wrap fit-grid">
      <div class="fit-copy">
        <h2 id="fit-title" class="fit-title">Find your size before you order</h2>
        <p>
          Enter your measurements and we suggest a size. Cuts vary a little by style, so each
          product page lists its own size chart too.
        </p>
        <NuxtLink to="/unstitched-suits" class="fit-link">
          Prefer a custom fit? Browse unstitched suits
          <Icon name="solar:arrow-right-linear" class="w-4 h-4" />
        </NuxtLink>
      </div>

      <form class="fit-card" novalidate @submit.prevent>
        <div class="unit" role="radiogroup" aria-label="Measurement unit">
          <button type="button" role="radio" :aria-checked="unit === 'in'" :class="{ on: unit === 'in' }" @click="unit = 'in'">Inches</button>
          <button type="button" role="radio" :aria-checked="unit === 'cm'" :class="{ on: unit === 'cm' }" @click="unit = 'cm'">Centimetres</button>
        </div>

        <div class="fields">
          <label>
            <span>Bust ({{ unit }})</span>
            <input v-model="bust" type="number" inputmode="decimal" min="0" step="0.5" :placeholder="unit === 'in' ? 'e.g. 36' : 'e.g. 91'" />
          </label>
          <label>
            <span>Hip ({{ unit }}), optional</span>
            <input v-model="hip" type="number" inputmode="decimal" min="0" step="0.5" :placeholder="unit === 'in' ? 'e.g. 39' : 'e.g. 99'" />
          </label>
        </div>

        <div class="out" aria-live="polite">
          <template v-if="result">
            <span class="out-size">{{ result.size }}</span>
            <span class="out-text">Your suggested size. Between sizes? Take the larger one for a relaxed fit.</span>
          </template>
          <p v-else-if="invalid" class="out-err">Enter a bust measurement between 26 and 50 inches (66 to 127 cm).</p>
          <p v-else class="out-hint">Measure around the fullest part of your bust.</p>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr><th>Size</th><th>Bust (in)</th><th>Hip (in)</th></tr>
            </thead>
            <tbody>
              <tr v-for="r in chart" :key="r.size" :class="{ hit: result?.size === r.size }">
                <td>{{ r.size }}</td>
                <td>{{ Math.floor(r.bust[0]) }} to {{ Math.ceil(r.bust[1]) }}</td>
                <td>{{ Math.floor(r.hip[0]) }} to {{ Math.ceil(r.hip[1]) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </form>
    </div>
  </section>
</template>

<style scoped>
.fit {
  background: var(--brand-tint);
}
.fit-grid {
  display: grid;
  gap: 2.5rem;
  align-items: center;
}
@media (min-width: 900px) {
  .fit-grid {
    grid-template-columns: 1fr 1.1fr;
    gap: 4rem;
  }
}
.fit-title {
  font-size: clamp(1.9rem, 3.4vw, 2.9rem);
  line-height: 1.12;
  font-weight: 600;
  color: var(--brand-deep);
  text-wrap: balance;
}
.fit-copy p {
  margin-top: 1rem;
  max-width: 46ch;
  line-height: 1.7;
  color: var(--ink-soft);
}
.fit-link {
  margin-top: 1.5rem;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: var(--brand);
  border-bottom: 1.5px solid currentColor;
  padding-bottom: 0.2rem;
}
.fit-card {
  background: #fff;
  border-radius: 24px;
  padding: clamp(1.2rem, 3vw, 2rem);
  box-shadow: 0 30px 60px -36px rgba(21, 21, 82, 0.45);
}
.unit {
  display: inline-flex;
  padding: 0.25rem;
  border-radius: 999px;
  background: var(--brand-tint);
}
.unit button {
  min-height: 2.3rem;
  padding: 0 1rem;
  border-radius: 999px;
  font-size: 0.86rem;
  font-weight: 600;
  color: var(--brand-deep);
}
.unit button.on {
  background: var(--brand);
  color: #fff;
}
.fields {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.9rem;
  margin-top: 1.2rem;
}
.fields label span {
  display: block;
  margin-bottom: 0.4rem;
  font-size: 0.84rem;
  font-weight: 500;
  color: var(--ink-soft);
}
.fields input {
  width: 100%;
  height: 3rem;
  padding: 0 0.9rem;
  border: 1.5px solid var(--line);
  border-radius: 12px;
  font-size: 1rem;
  background: #fff;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.fields input:focus {
  border-color: var(--brand);
  box-shadow: 0 0 0 4px rgba(77, 77, 219, 0.14);
}
.out {
  margin-top: 1.2rem;
  min-height: 4.2rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.9rem 1rem;
  border-radius: 14px;
  background: var(--paper);
}
.out-size {
  display: grid;
  place-items: center;
  flex-shrink: 0;
  width: 3.4rem;
  height: 3.4rem;
  border-radius: 999px;
  background: var(--brand);
  color: #fff;
  font-family: var(--font-display);
  font-size: 1.3rem;
  font-weight: 600;
}
.out-text,
.out-hint {
  font-size: 0.92rem;
  line-height: 1.5;
  color: var(--ink-soft);
}
.out-err {
  font-size: 0.92rem;
  color: #c0285a;
}
.table-wrap {
  margin-top: 1.2rem;
  overflow-x: auto;
}
table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.88rem;
}
th,
td {
  padding: 0.55rem 0.5rem;
  text-align: left;
  border-bottom: 1px solid var(--line);
}
th {
  font-weight: 600;
  color: var(--ink-soft);
}
tr.hit td {
  background: var(--brand-tint);
  font-weight: 700;
  color: var(--brand);
}
</style>
