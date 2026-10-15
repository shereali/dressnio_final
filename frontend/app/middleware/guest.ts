export default defineNuxtRouteMiddleware((to, from) => {
  const token = useTokenStore();
  if (token.getToken) {
    if (to.path.startsWith("/admin")) {
      return navigateTo("/admin/dashboard");
    }
    return navigateTo("/customer");
  }
});
