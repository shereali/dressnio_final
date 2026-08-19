import { defineStore } from "pinia";
import { toast } from "vue3-toastify";

export const useAuthStore = defineStore("auth", {
  state: () => ({}),
  getters: {},
  actions: {
    async login(credentials: any, form: any, isLoading: any) {
      try {
        const storeToken = useTokenStore();
        const { data, error } = await useApiFetch("/client/login", {
          method: "POST",
          body: { ...credentials },
        });
        if (data.value && data.value.status) {
          storeToken.setToken(data.value?.token);
          localStorage.removeItem("email");
          navigateTo("/customer");
          isLoading.value = false;
        } else if (data.value && !data.value.status) {
          isLoading.value = false;
          toast.error(data.value.message || "Invalid credentials");
          if (data.value.errors) {
            formError(data.value.errors, form);
          }
        }
        if (error.value) {
          isLoading.value = false;
          formError(error.value.data?.errors, form);
        }
      } catch (error) {
        throw error;
      }
    },

    async adminLogin(credentials: any, form: any) {
      console.log("credentials", credentials);
      const utilityStore = useUtilityStore();

      try {
        const storeToken = useTokenStore();
        const { data, error } = await useApiFetch("/admin-login", {
          method: "POST",
          body: { ...credentials },
        });
        if (data.value && data.value.status) {
          storeToken.setToken(data.value?.token);
          localStorage.removeItem("email");
          navigateTo("/admin");
          utilityStore.isLoading = false;
        } else if (data.value && !data.value.status) {
          utilityStore.isLoading = false;
          // Show toast notification for general error message
          toast.error(data.value.message || "Invalid credentials");
          // If there are specific field errors, show them in the form
          if (data.value.errors) {
            formError(data.value.errors, form);
          }
        }
        if (error.value) {
          utilityStore.isLoading = false;
          formError(error.value.data?.errors, form);
        }
      } catch (error) {
        utilityStore.isLoading = false;
        throw error;
      }
    },

    async sendOtp(credentials: any) {
      const response = await useApiFetch("/client/send-otp", {
        method: "POST",
        body: { ...credentials },
      });
      return response;
    },

    async sendSms() {
      const response = await fetch("http://api.quicksms.xyz/smsapi", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          api_key: "4ff8cc2cea2b5c09",
          senderid: "12345",
          type: "text",
          msg: "Thank you for using dressnio",
          contacts: "8801749240901",
        }),
      });
      return await response.json();
    },

    async logout(redirectPath: string = "/login") {
      const utilityStore = useUtilityStore();
      try {
        const token = useTokenStore();
        const config = useRuntimeConfig();
        await useLazyFetch(config.public.apiBase + "/logout", {
          method: "POST",
          headers: { Accept: "application/json" },
        });
        token.removeToken();
        utilityStore.isLoading = false;
        if (redirectPath) {
          navigateTo(redirectPath);
        }
      } catch (error) {
        utilityStore.isLoading = false;
        if (redirectPath) {
          navigateTo(redirectPath);
        }
        throw error;
      }
    },
  },
});
