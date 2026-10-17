// Loads and saves a group of admin settings. Credentials come back masked from the API:
// leave them blank to keep the saved value, type a new one to replace it.
export function useSettingsForm(fields: Record<string, string>, secrets: string[] = []) {
  const api = useAdminApi();
  const snackbar = useSnackbar();

  const form = reactive<Record<string, string>>({ ...fields });
  const masked = reactive<Record<string, string>>({});
  const loading = ref(true);
  const saving = ref(false);

  const load = async () => {
    try {
      const res = await api("/settings");
      const data = res?.data || {};
      for (const key of Object.keys(fields)) {
        if (secrets.includes(key)) {
          masked[key] = data[key] || "";
          form[key] = "";
        } else {
          form[key] = data[key] ?? fields[key];
        }
      }
    } catch (e) {
      snackbar.add({ type: "error", text: "Could not load the settings. Refresh to try again." });
    } finally {
      loading.value = false;
    }
  };

  const save = async (extra: Record<string, string> = {}) => {
    saving.value = true;
    try {
      await api("/settings", { method: "PUT", body: { ...form, ...extra } });
      snackbar.add({ type: "success", text: "Settings saved" });
      await load();
      return true;
    } catch (e) {
      snackbar.add({ type: "error", text: apiErrorText(e, "Could not save the settings.") });
      return false;
    } finally {
      saving.value = false;
    }
  };

  onMounted(load);

  return { form, masked, loading, saving, load, save };
}
