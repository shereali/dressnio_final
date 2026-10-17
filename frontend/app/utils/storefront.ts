// Shared storefront helpers: Bangladeshi money format, colour tones for artwork, price maths.

const bdFormat = new Intl.NumberFormat("en-IN", { maximumFractionDigits: 0 });

/** ৳1,50,000 style grouping used across Bangladesh. */
export function formatBDT(value: number | string | null | undefined): string {
  const n = Number(value);
  if (!Number.isFinite(n)) return "৳0";
  return `৳${bdFormat.format(Math.round(n))}`;
}

export const STORE_POLICY = {
  freeDeliveryOver: 2000,
  deliveryInsideCity: "5 days",
  deliveryOutsideCity: "10 days",
  exchangeWindow: "7 days",
};

/** Standard size chart in inches, shared by the size finder and product size guide. */
export const SIZE_CHART = [
  { size: "S", bust: [30, 33.9], hip: [34, 36.9] },
  { size: "M", bust: [34, 35.9], hip: [37, 38.9] },
  { size: "L", bust: [36, 38.4], hip: [39, 41.4] },
  { size: "XL", bust: [38.5, 40.9], hip: [41.5, 43.9] },
  { size: "XXL", bust: [41, 44.5], hip: [44, 47.5] },
];

type RGB = [number, number, number];

function hexToRgb(hex: string): RGB {
  let h = (hex || "#4D4DDB").replace("#", "").trim();
  if (h.length === 3) h = h.split("").map((c) => c + c).join("");
  if (!/^[0-9a-fA-F]{6}$/.test(h)) h = "4D4DDB";
  return [parseInt(h.slice(0, 2), 16), parseInt(h.slice(2, 4), 16), parseInt(h.slice(4, 6), 16)];
}

function rgbToHsl([r, g, b]: RGB): [number, number, number] {
  r /= 255; g /= 255; b /= 255;
  const max = Math.max(r, g, b), min = Math.min(r, g, b);
  let h = 0, s = 0;
  const l = (max + min) / 2;
  if (max !== min) {
    const d = max - min;
    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
    if (max === r) h = (g - b) / d + (g < b ? 6 : 0);
    else if (max === g) h = (b - r) / d + 2;
    else h = (r - g) / d + 4;
    h *= 60;
  }
  return [h, s * 100, l * 100];
}

function hslToHex(h: number, s: number, l: number): string {
  h = ((h % 360) + 360) % 360;
  s = Math.max(0, Math.min(100, s)) / 100;
  l = Math.max(0, Math.min(100, l)) / 100;
  const k = (n: number) => (n + h / 30) % 12;
  const a = s * Math.min(l, 1 - l);
  const f = (n: number) => l - a * Math.max(-1, Math.min(k(n) - 3, Math.min(9 - k(n), 1)));
  const to = (x: number) => Math.round(x * 255).toString(16).padStart(2, "0");
  return `#${to(f(0))}${to(f(8))}${to(f(4))}`;
}

export interface Tone {
  main: string;
  shade: string;
  dark: string;
  light: string;
  accent: string;
  trim: string;
  skin: string;
  form: string;
  tint: string;
  tintDeep: string;
}

/** Builds a full palette for one garment from a single base colour. */
export function toneFromHex(hex: string): Tone {
  const [h, s, l] = rgbToHsl(hexToRgb(hex));
  const sat = Math.max(s, 28);
  return {
    main: hslToHex(h, sat, l),
    shade: hslToHex(h, sat, Math.max(l - 11, 14)),
    dark: hslToHex(h, sat, Math.max(l - 22, 10)),
    light: hslToHex(h, sat * 0.75, Math.min(l + 24, 86)),
    accent: hslToHex(h + 26, sat * 0.7, Math.min(l + 32, 84)),
    trim: "#EBCB8B",
    skin: "#EBC6A8",
    form: "#DDD8CF",
    tint: hslToHex(h, Math.min(sat, 55), 95),
    tintDeep: hslToHex(h, Math.min(sat, 60), 88),
  };
}

/** Garment colours that sit well beside the Dressnio indigo. */
export const TONE_POOL = [
  "#4D4DDB", // brand indigo
  "#B8446B", // mulberry
  "#1F8A70", // emerald
  "#D49A1F", // marigold
  "#7A3E8E", // plum
  "#1B7F8C", // teal
  "#E07A5F", // coral
  "#26336B", // midnight
];

export function toneHexAt(index: number): string {
  return TONE_POOL[Math.abs(index) % TONE_POOL.length]!;
}

/** Local seed paths that the repo does not ship. They always render the drawn artwork instead. */
export function isDrawableOnly(src?: string | null): boolean {
  if (!src) return true;
  return /\/(img\/category-|brand\/(hero|story)-)/.test(src);
}

export interface PriceInfo {
  list: number;
  sale: number;
  percent: number;
}

/** Reads the API price shape ({ price, discount % }) and falls back to legacy fields. */
export function priceInfo(product: any, sizeName?: string): PriceInfo {
  let list = 0;
  let percent = 0;
  const prices: any[] = Array.isArray(product?.prices) ? product.prices : [];
  const bySize = sizeName ? prices.find((p) => p?.size === sizeName) : null;
  const src = bySize || product?.price || prices[0];
  if (src && typeof src === "object") {
    list = Number(src.price) || 0;
    percent = Number(src.discount) || 0;
  } else if (typeof product?.price === "number") {
    list = product.price;
  } else if (product?.base_price) {
    list = Number(product.base_price) || 0;
  }
  if (!percent && product?.discount) percent = Number(product.discount) || 0;
  const sale = percent > 0 ? list - (list * percent) / 100 : list;
  return { list, sale, percent };
}
