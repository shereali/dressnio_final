export const useUtility = () => {
  const isLoading = ref(false);
  const state = reactive({});
  const form = ref({});
  const getData = ref([]);
  const route = useRoute();
  const router = useRouter();
  const auth = useAuthStore();

  return {
    isLoading,
    state,
    form,
    getData,
    route,
    router,
    auth,
  };
};
