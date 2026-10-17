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
    "~/assets/scss/dressnio-theme.scss",
    "~/assets/css/premium.css",
  ],

  runtimeConfig: {
    public: {
      // apiBase: process.env.API_BASE_URL || "https://api.dressnio.com/api",
      apiBase: process.env.API_BASE_URL || "http://localhost:8000/api",
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
        { name: "theme-color", content: "#4D4DDB" },
        { name: "format-detection", content: "telephone=no" },
      ],
      link: [
        { rel: "preconnect", href: "https://fonts.googleapis.com" },
        { rel: "preconnect", href: "https://fonts.gstatic.com", crossorigin: "" },
        {
          rel: "stylesheet",
          href: "https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,500;0,6..96,600;0,6..96,700;1,6..96,500&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,700&family=Hind+Siliguri:wght@400;500;600&display=swap",
        },
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