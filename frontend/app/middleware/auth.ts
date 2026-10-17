export default defineNuxtRouteMiddleware((to, from) => {
  const token = useTokenStore();
  if (!token.getToken) {
    if (to.path.startsWith("/admin")) {
      return navigateTo("/admin/login");
    }
    return navigateTo({ path: "/login", query: { redirect: to.fullPath } });
  }
});
