export default function useApi() {
  const config = useRuntimeConfig();
  const token = useTokenStore();

  const createItem = async (apiEndpoint: any, apiMethod: any, apiData: any) => {
    const response = await useLazyFetch(apiEndpoint, {
      baseURL: config.public.apiBase,
      method: apiMethod,
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(apiData),
    });

    return response;
  };
  const authApi = async (apiEndpoint: any, apiMethod: any, apiData: any) => {
    const response = await useLazyFetch(apiEndpoint, {
      baseURL: config.public.apiBase,
      method: apiMethod,
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: "Bearer " + token.getToken,
      },
      body: JSON.stringify(apiData),
    });

    return response;
  };

  return {
    createItem,
    authApi,
  };
}
