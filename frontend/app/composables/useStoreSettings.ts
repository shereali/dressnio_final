// Public store settings (announcement, contact, free-delivery threshold) from /client/settings.
// Every field has a safe default so the page still renders when the API is unreachable.
export function useStoreSettings() {
  const { data } = useApiFetch<any>("/client/settings", {
    key: "public-settings",
    lazy: true,
    server: true,
  });

  const raw = computed<Record<string, string>>(() => data.value?.data || {});

  return {
    raw,
    announcement: computed(() => raw.value.announcement || ""),
    phone: computed(() => raw.value.contact_phone || ""),
    email: computed(() => raw.value.contact_email || ""),
    address: computed(() => raw.value.contact_address || ""),
    facebook: computed(() => raw.value.facebook_url || ""),
    instagram: computed(() => raw.value.instagram_url || ""),
    about: computed(() => raw.value.about_text || ""),
    shippingFee: computed(() => Number(raw.value.shipping_fee) || 60),
    shippingFeeOutside: computed(() => Number(raw.value.shipping_fee_outside) || 120),
    freeShippingAbove: computed(
      () => Number(raw.value.free_shipping_above) || STORE_POLICY.freeDeliveryOver
    ),
    whatsappUrl: computed(() => {
      const digits = (raw.value.contact_phone || "").replace(/\D/g, "");
      if (!digits) return "";
      const intl = digits.startsWith("880") ? digits : `880${digits.replace(/^0/, "")}`;
      return `https://wa.me/${intl}`;
    }),
  };
}
