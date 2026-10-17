// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2026-01-15',
  future: {
    compatibilityVersion: 4,
  },
  devtools: { enabled: false },
  sourcemap: {
    server: true,
    client: true,
  },
  nitro: {
    preset: "node-server",
    prerender: {
      crawlLinks: false,
      failOnError: false,
    },
  },

  css: [
    "@fontsource-variable/dm-sans/index.css",
    "@fontsource/instrument-serif/400.css",
    "@fontsource/instrument-serif/400-italic.css",
    "@fontsource/hind-siliguri/400.css",
    "@fontsource/hind-siliguri/500.css",
    "@fontsource/hind-siliguri/600.css",
    "~/assets/scss/dressnio-theme.scss",
    "~/assets/css/premium.css",
    "~/assets/css/admin.css",
  ],

  runtimeConfig: {
    public: {
      // apiBase: process.env.API_BASE_URL || "https://api.dressnio.com/api",
      apiBase: process.env.NUXT_PUBLIC_API_BASE || process.env.NUXT_PUBLIC_API_BASE || process.env.API_BASE_URL || "http://localhost:8000/api",
    },
  },

  modules: [
    "nuxt-swiper",
    "@nuxt/image",
    "nuxt-payload-analyzer",
    "@nuxt/icon",
    "@nuxtjs/device",
    "@formkit/auto-animate/nuxt",
    "@pinia/nuxt",
    "@pinia-plugin-persistedstate/nuxt",
    "@nuxt/ui",
    "nuxt-tiptap-editor",
  ],

  // Icons are bundled from the installed @iconify-json sets, never fetched from a CDN at runtime
  icon: {
    provider: "server",
    serverBundle: "local",
    clientBundle: { scan: true, sizeLimitKb: 1024 },
    fallbackToApi: false,
  },

  // The storefront is designed as a light theme
  colorMode: { preference: "light", fallback: "light", storageKey: "dressnio-color-mode" },

  tiptap: {
    prefix: "Tiptap", // i.e. <TiptapEditor />
  },

  // Tailwind CSS module configuration
  // tailwindcss: {
  //   // Add the @tailwindcss/typography plugin to the Tailwind CSS configuration
  //   config: {
  //     plugins: [
  //       require("@tailwindcss/typography"),
  //       // Other Tailwind CSS plugins...
  //     ],
  //   },
  // },

  app: {
    head: {
      title: "Dressnio | Kurta sets, sarees and unstitched suits",
      htmlAttrs: {
        lang: "en",
      },
      meta: [
        { charset: "utf-8" },
        {
          name: "viewport",
          content: "width=device-width, initial-scale=1",
        },

        { hid: "description", name: "description", content: "Shop kurta sets, lehengas, sarees, maxis and unstitched suits from Dressnio. Cash on delivery across Bangladesh, bKash and Nagad accepted." },
        { name: "theme-color", content: "#5B3FD9" },
        { property: "og:image", content: "/brand/logo.png" },
        { property: "og:site_name", content: "Dressnio" },
        { name: "format-detection", content: "telephone=no" },
      ],
      link: [
        { rel: "icon", type: "image/svg+xml", href: "/favicon.svg" },
        {
          rel: "stylesheet",
          href: "/css/style.css",
        },
        {
          rel: "stylesheet",
          href: "/css/font-awesome.css",
        },
      ],
      script: [],
    },
  },

  // routeRules: {
  //   "/api/**": {
  //     cors: true,
  //   },
  // },

  vite: {
    server: {
      fs: {
        strict: false,
      },
    },
  },
});