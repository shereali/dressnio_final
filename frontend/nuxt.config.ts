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
      crawlLinks: true,
      failOnError: false,
    },
  },

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
      title: "Dressnio",
      htmlAttrs: {
        lang: "en",
      },
      meta: [
        { charset: "utf-8" },
        {
          name: "viewport",
          content: "width=device-width, initial-scale=1",
        },

        { hid: "description", name: "description", content: "" },
        { name: "format-detection", content: "telephone=no" },
      ],
      link: [
        {
          rel: "stylesheet",
          href: "/css/style.css",
        },
        {
          rel: "stylesheet",
          href: "/css/font-awesome.css",
        },
      ],
      script: [
        {
          type: "text/javascript",
          src: "/js/script.js",
        },
      ],
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