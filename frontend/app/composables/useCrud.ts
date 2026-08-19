export default function useCrud() {
  let route = useRoute();
  let router = useRouter();
  const store = useUtilityStore();

  const config = useRuntimeConfig();
  const token = useTokenStore();

  if (route.params.id === undefined) store.is_edit = false;
  else store.is_edit = true;

  const createItem = async (apiEndpoint: string, formData: any, form: any) => {
    console.log("formData from create method", formData);

    const route = useRoute();
    form.value.clear();
    const apiMethod = store.is_edit ? "PUT" : "POST";

    const api = store.is_edit
      ? apiEndpoint + "/" + route.params.id
      : apiEndpoint;

    if (store.is_edit) {
      formData.append("_method", "PUT");
    } else {
      formData;
    }

    const { data, error, pending, refresh } = await useApiFetch(api, {
      method: "POST",

      // body: JSON.stringify(formData),
      body: formData,
    });

    if (data.value) {
      store.isLoading = false;
      store.toastMsg = data.value?.message;
      store.formData = {};
      speak(data.value?.message);
    }

    if (error.value) {
      store.isLoading = false;
      if (error.value.statusCode == 422)
        formError(error.value?.data?.errors, form);
    }

    getItems.items(apiEndpoint, 1);

    if (store.toastMsg !== null) {
      store.formData = {};
      store.isCreateMore
        ? navigateTo(store.pageBackLink + "/create")
        : navigateTo(store.pageBackLink);
    }
  };

  const getItem = async (apiEndpoint: any) => {
    const response = await useLazyFetch(apiEndpoint, {
      baseURL: config.public.apiBase,
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: "Bearer " + token.getToken,
      },
    });
    return response;
  };

  const editItem = (item_id: number, apiEndpoint: string) => {
    store.is_edit = true;
    navigateTo(apiEndpoint + "/" + item_id);
  };
  const deleteItem = async (item_id: number, apiEndpoint: string) => {
    let { data, error, pending } = await useLazyFetch(
      apiEndpoint + "/" + item_id,
      {
        baseURL: config.public.apiBase,
        method: "DELETE",
        headers: {
          Authorization: "Bearer " + token.getToken,
        },
      }
    );

    // await client(base_url + apiEndpoint + "/" + item_id, {
    //   method: "DELETE",
    // });

    if (data.value?.status === "danger") {
      console.log("response delete", data.value?.message);
      getItem(apiEndpoint);
      store.toastMsg = data.value?.message;
      speak(data.value?.message);
    }
  };
  const updateItem = async () => {};
  const duplicateItem = async () => {};
  return {
    createItem,
    editItem,
    deleteItem,
    updateItem,
    duplicateItem,
  };
}
