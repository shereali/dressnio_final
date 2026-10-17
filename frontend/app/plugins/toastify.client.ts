import Vue3Toastify from "vue3-toastify";
import "vue3-toastify/dist/index.css";

// Registers the toast container and styles used by the admin screens
export default defineNuxtPlugin((nuxtApp) => {
  nuxtApp.vueApp.use(Vue3Toastify, { autoClose: 3500, position: "top-right", theme: "colored" });
});
