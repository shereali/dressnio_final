// Authenticated calls to the admin API. Resolves with the response body or throws the fetch error.
export function useAdminApi() {
  const config = useRuntimeConfig();
  const token = useTokenStore();

  return <T = any>(path: string, options: Record<string, any> = {}) =>
    $fetch<T>(`${config.public.apiBase}${path}`, {
      ...options,
      headers: {
        Accept: "application/json",
        Authorization: `Bearer ${token.getToken}`,
        ...(options.headers || {}),
      },
    });
}

/** First readable message from an API error. */
export function apiErrorText(e: any, fallback = "Something went wrong. Try again."): string {
  const errors = e?.data?.errors;
  if (errors && typeof errors === "object") {
    const first = Object.values(errors).flat()[0];
    if (typeof first === "string") return first;
  }
  return e?.data?.message || fallback;
}
