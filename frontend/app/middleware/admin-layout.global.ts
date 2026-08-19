export default defineNuxtRouteMiddleware((to, from) => {
  if (to.path.startsWith('/admin')) {
    if (to.path !== '/admin/login' && to.path !== '/admin/register') {
      setPageLayout('admin')
    }
  }
})
