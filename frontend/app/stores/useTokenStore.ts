export const useTokenStore = defineStore("token", {
  state: () => ({
    token: null,
    loggedIn: false,
  }),

  getters: {
    getToken: (state) => state.token,
    getStatus: (state) => state.loggedIn,
    isAuthenticated: (state) => state.loggedIn,
  },
  actions: {
    setToken(token: any) {
      this.token = token;
      this.loggedIn = true;
    },

    removeToken() {
      this.$reset();
    },
  },

  persist: true,
});
