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
        if (data.value && (data.value.status === "success" || data.value.status === true)) {
          const authToken = data.value.data?.token || data.value.token;
          storeToken.setToken(authToken);
          localStorage.removeItem("email");
          navigateTo("/customer");
          isLoading.value = false;
        } else if (data.value && (data.value.status === "error" || data.value.status === false)) {
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
        if (data.value && (data.value.status === "success" || data.value.status === true)) {
          const authToken = data.value.data?.token || data.value.token;
          storeToken.setToken(authToken);
          localStorage.removeItem("email");
          navigateTo("/admin/dashboard");
          utilityStore.isLoading = false;
        } else if (data.value && (data.value.status === "error" || data.value.status === false)) {
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

    async verifyOtp(payload: any, form: any, isLoading: any) {
      try {
        const storeToken = useTokenStore();
        const { data, error } = await useApiFetch("/client/verify-otp", {
          method: "POST",
          body: { ...payload },
        });
        if (data.value && (data.value.status === "success" || data.value.status === true)) {
          const authToken = data.value.data?.token || data.value.token;
          storeToken.setToken(authToken);
          localStorage.removeItem("mobile_no");
          localStorage.removeItem("email");
          navigateTo("/customer");
          isLoading.value = false;
        } else if (data.value && (data.value.status === "error" || data.value.status === false)) {
          isLoading.value = false;
          toast.error(data.value.message || "Invalid or expired OTP code");
          if (data.value.errors) {
            formError(data.value.errors, form);
          }
        }
        if (error.value) {
          isLoading.value = false;
          toast.error(error.value.data?.message || "Invalid or expired OTP code");
          formError(error.value.data?.errors, form);
        }
      } catch (error) {
        isLoading.value = false;
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

    async customerLogout(redirectPath: string = "/login") {
      const utilityStore = useUtilityStore();
      const token = useTokenStore();
      try {
        await useApiFetch("/client/logout", {
          method: "POST",
        });
      } catch (error) {
        console.warn("Client logout API call failed, clearing local session", error);
      } finally {
        token.removeToken();
        utilityStore.isLoading = false;
        if (redirectPath) {
          navigateTo(redirectPath);
        }
      }
    },

    async logout(redirectPath: string = "/login") {
      const utilityStore = useUtilityStore();
      const token = useTokenStore();
      try {
        await useApiFetch("/logout", {
          method: "POST",
        }).catch(() => useApiFetch("/client/logout", { method: "POST" }));
      } catch (error) {
        console.warn("Logout API call failed, clearing local session", error);
      } finally {
        token.removeToken();
        utilityStore.isLoading = false;
        if (redirectPath) {
          navigateTo(redirectPath);
        }
      }
    },
  },
});
