/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/**/*.{vue,js,ts,jsx,tsx}",
    "./components/**/*.{vue,js,ts,jsx,tsx}",
    "./layouts/**/*.vue",
    "./pages/**/*.vue",
    "./plugins/**/*.{js,ts}",
    "./app.vue",
    "./error.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        display: ['"Instrument Serif"', '"Hind Siliguri"', "Georgia", "serif"],
        sans: ['"DM Sans Variable"', '"Hind Siliguri"', "system-ui", "sans-serif"],
      },
      colors: {
        brand: {
          50: '#f0f0fe',
          100: '#e4e4fd',
          200: '#cdcdfa',
          300: '#ababf5',
          400: '#8383ed',
          500: '#4D4DDB', // Base Color
          600: '#3e3ebe',
          700: '#2A2AD1', // Hover Color
          800: '#2626b0',
          900: '#23238c',
          950: '#151552',
          DEFAULT: '#4D4DDB',
        },
        secondary: '#7373E3',
        tertiary: '#2A2AD1',
      }
    },
  },
  plugins: [
    require("@tailwindcss/typography"),
  ],

  image: {
    // The screen sizes predefined by `@nuxt/image`:
    screens: {
      xs: 320,
      sm: 640,
      md: 768,
      lg: 1024,
      xl: 1280,
      xxl: 1536,
      "2xl": 1536,
    },
    domains: ["images.unsplash.com"],
    alias: {
      unsplash: "https://images.unsplash.com",
    },
  },
};
