// Vector garment catalogue used to draw category, style and product artwork.
// Every shape is drawn in a 240 x 330 box. Fill keys map to a palette derived from one base colour.

export type GarmentKind =
  | "kurta"
  | "shrug"
  | "maxi"
  | "lehenga"
  | "suit"
  | "dress"
  | "top"
  | "saree"
  | "fabric"
  | "unstitched";

export type Fill = "main" | "shade" | "dark" | "light" | "accent" | "trim" | "skin" | "form";

export interface Layer {
  d: string;
  f: Fill;
  /** overlay the fabric pattern on this layer */
  pat?: boolean;
  /** horizontal bands are clipped to this layer */
  body?: boolean;
}

export interface Garment {
  hanger: boolean;
  layers: Layer[];
  /** soft fold lines that give the cloth depth */
  folds: string[];
  /** coloured trim strokes (neckline, plackets, hems) */
  trims: { d: string; w: number; f: Fill }[];
  /** bands clipped to the body layer */
  bands: { y: number; h: number; f: Fill }[];
  dots: [number, number, number][];
  pattern: "floral" | "dots" | "stripes" | "none";
  label: string;
}

const mirror = (d: string) =>
  d.replace(/([MLCQTHVZ])([^MLCQTHVZ]*)/g, (_m, cmd: string, args: string) => {
    if (cmd === "Z") return "Z";
    const nums = args.trim().split(/[\s,]+/).filter(Boolean).map(Number);
    if (cmd === "H") return `H${240 - nums[0]!}`;
    if (cmd === "V") return `V${nums[0]}`;
    const out: number[] = [];
    nums.forEach((n, i) => out.push(i % 2 === 0 ? 240 - n : n));
    return `${cmd}${out.join(" ")}`;
  });

export const GARMENTS: Record<GarmentKind, Garment> = {
  kurta: {
    hanger: true,
    label: "Kurta set",
    pattern: "floral",
    layers: [
      { d: "M88 236 L152 236 L168 322 L124 322 L120 262 L116 322 L72 322 Z", f: "dark" },
      { d: "M92 76 L56 136 Q64 144 74 138 L98 106 Z", f: "shade" },
      { d: mirror("M92 76 L56 136 Q64 144 74 138 L98 106 Z"), f: "shade" },
      { d: "M92 76 L106 70 Q120 90 134 70 L148 76 L154 140 L160 246 L126 252 L120 240 L114 252 L80 246 L86 140 Z", f: "main", pat: true, body: true },
    ],
    bands: [
      { y: 222, h: 14, f: "trim" },
      { y: 240, h: 3, f: "accent" },
    ],
    trims: [
      { d: "M106 70 Q120 90 134 70", w: 3, f: "trim" },
      { d: "M120 88 V150", w: 2, f: "trim" },
      { d: "M56 136 Q64 144 74 138", w: 3, f: "trim" },
      { d: mirror("M56 136 Q64 144 74 138"), w: 3, f: "trim" },
    ],
    folds: ["M104 150 Q100 200 96 244", "M136 150 Q140 200 144 244"],
    dots: [[120, 102, 2.2], [120, 118, 2.2], [120, 134, 2.2]],
  },
  shrug: {
    hanger: true,
    label: "Shrug set",
    pattern: "dots",
    layers: [
      { d: "M92 236 L148 236 L186 322 L54 322 Z", f: "dark" },
      { d: "M104 78 Q120 98 136 78 L142 98 L144 232 L96 232 L98 98 Z", f: "light" },
      { d: "M92 76 L52 154 L46 208 L64 212 L78 156 L100 110 Z", f: "shade", pat: true },
      { d: mirror("M92 76 L52 154 L46 208 L64 212 L78 156 L100 110 Z"), f: "shade", pat: true },
      { d: "M92 76 L106 72 Q112 140 108 236 L80 236 L86 140 Z", f: "main", pat: true, body: true },
      { d: mirror("M92 76 L106 72 Q112 140 108 236 L80 236 L86 140 Z"), f: "main", pat: true },
    ],
    bands: [{ y: 222, h: 14, f: "trim" }],
    trims: [
      { d: "M106 72 Q112 140 108 236", w: 2.5, f: "trim" },
      { d: mirror("M106 72 Q112 140 108 236"), w: 2.5, f: "trim" },
      { d: "M46 208 L64 212", w: 4, f: "trim" },
      { d: mirror("M46 208 L64 212"), w: 4, f: "trim" },
    ],
    folds: ["M130 250 L140 318", "M110 250 L100 318"],
    dots: [],
  },
  maxi: {
    hanger: true,
    label: "Maxi",
    pattern: "floral",
    layers: [
      {
        d: "M100 74 L106 72 Q120 92 134 72 L140 74 L144 84 L138 128 C152 170 176 250 198 322 Q120 336 42 322 C64 250 88 170 102 128 L96 84 Z",
        f: "main",
        pat: true,
        body: true,
      },
      { d: "M101 124 Q120 134 139 124 L141 138 Q120 148 99 138 Z", f: "accent" },
    ],
    bands: [{ y: 296, h: 16, f: "trim" }],
    trims: [
      { d: "M106 72 Q120 92 134 72", w: 3, f: "accent" },
      { d: "M62 252 Q91 264 120 252 T178 252", w: 3, f: "trim" },
      { d: "M52 288 Q86 300 120 288 T188 288", w: 3, f: "trim" },
    ],
    folds: ["M112 150 Q96 230 78 316", "M128 150 Q144 230 162 316", "M120 150 V322"],
    dots: [],
  },
  lehenga: {
    hanger: true,
    label: "Lehenga",
    pattern: "floral",
    layers: [
      { d: "M100 72 C56 110 44 220 58 316 L84 310 C72 220 82 128 116 86 Z", f: "light", pat: true },
      { d: "M90 138 L150 138 L216 322 Q120 340 24 322 Z", f: "main", pat: true, body: true },
      { d: "M92 112 H148 V142 H92 Z", f: "skin" },
      { d: "M94 76 L106 72 Q120 88 134 72 L146 76 L158 102 L146 108 L148 120 L92 120 L94 108 L82 102 Z", f: "shade", pat: true },
      { d: "M90 134 H150 V146 H90 Z", f: "trim" },
    ],
    bands: [
      { y: 252, h: 10, f: "trim" },
      { y: 268, h: 4, f: "accent" },
      { y: 292, h: 22, f: "trim" },
    ],
    trims: [{ d: "M106 72 Q120 88 134 72", w: 2.5, f: "trim" }],
    folds: ["M116 150 L92 320", "M124 150 L148 320", "M120 150 V326"],
    dots: [],
  },
  suit: {
    hanger: true,
    label: "Suit set",
    pattern: "stripes",
    layers: [
      { d: "M92 244 L148 244 L152 322 L126 322 L120 272 L114 322 L88 322 Z", f: "dark" },
      { d: "M92 76 L56 136 Q64 144 74 138 L98 106 Z", f: "shade" },
      { d: mirror("M92 76 L56 136 Q64 144 74 138 L98 106 Z"), f: "shade" },
      { d: "M92 76 L106 70 Q120 86 134 70 L148 76 L152 140 L156 242 L84 242 L88 140 Z", f: "main", pat: true, body: true },
      { d: "M138 72 L152 78 C132 140 96 200 74 304 L100 308 C114 222 152 152 166 94 Z", f: "accent", pat: true },
    ],
    bands: [{ y: 226, h: 14, f: "trim" }],
    trims: [
      { d: "M106 70 Q120 86 134 70", w: 3, f: "trim" },
      { d: "M152 78 C132 140 96 200 74 304", w: 2.5, f: "trim" },
    ],
    folds: ["M104 150 Q100 200 96 240"],
    dots: [],
  },
  dress: {
    hanger: true,
    label: "Dress",
    pattern: "dots",
    layers: [
      { d: "M92 76 L66 110 L82 120 L98 98 Z", f: "shade" },
      { d: mirror("M92 76 L66 110 L82 120 L98 98 Z"), f: "shade" },
      { d: "M92 76 L106 70 L120 102 L134 70 L148 76 L144 134 L198 282 Q120 300 42 282 L96 134 Z", f: "main", pat: true, body: true },
      { d: "M95 128 H145 V143 H95 Z", f: "accent" },
    ],
    bands: [{ y: 268, h: 14, f: "trim" }],
    trims: [{ d: "M106 70 L120 102 L134 70", w: 3, f: "accent" }],
    folds: ["M110 150 Q92 220 70 280", "M130 150 Q148 220 170 280"],
    dots: [[120, 135, 5]],
  },
  top: {
    hanger: true,
    label: "Top & co-ord",
    pattern: "stripes",
    layers: [
      { d: "M92 160 L148 160 L192 322 L48 322 Z", f: "dark", pat: true },
      { d: "M94 76 L106 70 Q120 86 134 70 L146 76 L172 110 L152 120 L148 108 L148 152 L92 152 L92 108 L88 120 L68 110 Z", f: "main", pat: true, body: true },
      { d: "M92 152 H148 V164 H92 Z", f: "trim" },
    ],
    bands: [{ y: 140, h: 12, f: "accent" }],
    trims: [{ d: "M106 70 Q120 86 134 70", w: 3, f: "trim" }],
    folds: ["M112 170 L90 318", "M128 170 L150 318", "M120 170 V322"],
    dots: [],
  },
  saree: {
    hanger: false,
    label: "Saree",
    pattern: "floral",
    layers: [
      { d: "M118 246 H122 V312 H118 Z", f: "form" },
      { d: "M96 78 Q120 66 144 78 L150 122 Q138 162 142 218 L98 218 Q102 162 90 122 Z", f: "form" },
      { d: "M112 74 Q120 50 128 74 Z", f: "form" },
      { d: "M96 78 Q120 66 144 78 L148 110 H92 Z", f: "accent" },
      { d: "M90 172 L150 172 L186 316 L54 316 Z", f: "main", pat: true, body: true },
      { d: "M90 82 L110 74 C132 102 154 142 162 192 L166 270 L136 272 L138 202 C128 154 110 120 90 100 Z", f: "shade", pat: true },
    ],
    bands: [{ y: 296, h: 20, f: "trim" }],
    trims: [{ d: "M110 74 C132 102 154 142 162 192 L166 270", w: 2.5, f: "trim" }],
    folds: ["M110 180 L100 312", "M120 180 V314", "M130 180 L140 312"],
    dots: [],
  },
  fabric: {
    hanger: false,
    label: "Raw fabric",
    pattern: "stripes",
    layers: [
      { d: "M62 112 H188 V166 H62 Z", f: "main", pat: true, body: true },
      { d: "M48 168 H176 V222 H48 Z", f: "accent", pat: true },
      { d: "M64 224 H192 V278 H64 Z", f: "light", pat: true },
      { d: "M62 112 a14 27 0 1 0 0 54 a14 27 0 1 0 0 -54 Z", f: "dark" },
      { d: "M48 168 a14 27 0 1 0 0 54 a14 27 0 1 0 0 -54 Z", f: "shade" },
      { d: "M64 224 a14 27 0 1 0 0 54 a14 27 0 1 0 0 -54 Z", f: "dark" },
      { d: "M30 292 H212 V306 H30 Z", f: "trim" },
    ],
    bands: [],
    trims: [
      { d: "M62 120 a8 19 0 1 0 0 38", w: 2, f: "trim" },
      { d: "M48 176 a8 19 0 1 0 0 38", w: 2, f: "trim" },
      { d: "M64 232 a8 19 0 1 0 0 38", w: 2, f: "trim" },
    ],
    folds: [],
    dots: [],
  },
  unstitched: {
    hanger: false,
    label: "Unstitched suit",
    pattern: "floral",
    layers: [
      { d: "M44 236 Q44 226 54 226 H186 Q196 226 196 236 V282 Q196 292 186 292 H54 Q44 292 44 282 Z", f: "dark" },
      { d: "M52 178 Q52 168 62 168 H178 Q188 168 188 178 V228 H52 Z", f: "accent", pat: true },
      { d: "M44 120 Q44 110 54 110 H186 Q196 110 196 120 V176 H44 Z", f: "main", pat: true, body: true },
      { d: "M108 100 H132 V300 H108 Z", f: "trim" },
    ],
    bands: [
      { y: 156, h: 14, f: "trim" },
      { y: 150, h: 3, f: "accent" },
    ],
    trims: [{ d: "M44 176 H196", w: 2, f: "shade" }],
    folds: ["M44 228 H196"],
    dots: [],
  },
};

export const GARMENT_KINDS = Object.keys(GARMENTS) as GarmentKind[];

/** Maps free text (category name, slug, fabric, cloth type) to the closest drawn garment. */
const KEYWORDS: [RegExp, GarmentKind][] = [
  [/unstitch|three.?piece|3.?piece|fabric set/i, "unstitched"],
  [/raw|fabric|cloth|thaan|roll/i, "fabric"],
  [/saree|sari|shari/i, "saree"],
  [/lehenga|lehnga|ghagra|choli|bridal/i, "lehenga"],
  [/shrug|jacket|cape|overlay/i, "shrug"],
  [/maxi|gown|long dress|flowy/i, "maxi"],
  [/suit|salwar|shalwar|kameez|churidar|patiala/i, "suit"],
  [/kurta|kurti|panjabi|punjabi|tunic|kaftan/i, "kurta"],
  [/co.?ord|top|blouse|shirt|tee/i, "top"],
  [/dress|frock|midi|skirt/i, "dress"],
];

export function garmentKindFor(...texts: (string | null | undefined)[]): GarmentKind {
  const hay = texts.filter(Boolean).join(" ");
  for (const [re, kind] of KEYWORDS) if (re.test(hay)) return kind;
  return "dress";
}
