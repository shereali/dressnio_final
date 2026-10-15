import type { UseFetchOptions } from "nuxt/app";

export function useApiFetch<T>(
  path: string | (() => string),

  options: UseFetchOptions<T> = {}
) {
  const config = useRuntimeConfig();

  const token = useTokenStore(); // Retrieve CSRF token using Nuxt's cookie utility

  const csrfToken = useCookie("XSRF-TOKEN").value; // Ensure the token exists before using it

  const authorizationToken = token.getToken ? `Bearer ${token.getToken}` : null;

  return useFetch(
    `${config.public.apiBase}${typeof path === "function" ? path() : path}`,

    {
      credentials: "include", // This ensures cookies are sent with the request

      ...options,

      watch: false,

      headers: {
        ...options.headers,
        ...(csrfToken ? { "X-XSRF-TOKEN": csrfToken } : {}),
        ...(authorizationToken ? { Authorization: authorizationToken } : {}),
      },
    }
  );
}
