<template>
  <CustomerLayout>
    <div class="space-y-6 max-w-2xl">
      <!-- Header -->
      <div class="pb-5 border-b border-stone-100">
        <h1 class="text-2xl font-extrabold text-stone-900">Profile & Shipping Settings</h1>
        <p class="text-xs sm:text-sm text-stone-500 mt-1">
          Keep your contact information and shipping address up to date for fast checkout.
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="pending" class="py-12 text-center">
        <Icon name="svg-spinners:180-ring-with-bg" class="w-8 h-8 text-brand-500 mx-auto mb-2" />
        <p class="text-sm text-stone-500">Loading profile data...</p>
      </div>

      <!-- Profile Form -->
      <form v-else @submit.prevent="handleUpdateProfile" class="space-y-5">
        <!-- Full Name -->
        <div>
          <label class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
            Full Name *
          </label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full px-4 py-2.5 rounded-xl border border-stone-200 text-stone-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all bg-white"
            placeholder="e.g. Ayesha Rahman"
          />
        </div>

        <!-- Mobile Phone (Identity) -->
        <div>
          <label class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
            Mobile Number (Registered)
          </label>
          <div class="relative">
            <input
              :value="customer?.mobile_no"
              type="text"
              disabled
              class="w-full px-4 py-2.5 rounded-xl border border-stone-200 text-stone-500 text-sm bg-stone-100 cursor-not-allowed"
            />
            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-emerald-600 flex items-center gap-1">
              <Icon name="solar:check-circle-bold" class="w-3.5 h-3.5" />
              Verified
            </span>
          </div>
          <p class="text-[11px] text-stone-400 mt-1">
            Mobile number is your primary login identifier and cannot be edited directly.
          </p>
        </div>

        <!-- Email Address -->
        <div>
          <label class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
            Email Address
          </label>
          <input
            v-model="form.email"
            type="email"
            class="w-full px-4 py-2.5 rounded-xl border border-stone-200 text-stone-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all bg-white"
            placeholder="ayesha@example.com"
          />
        </div>

        <!-- Delivery City / Division -->
        <div>
          <label class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
            City / Region
          </label>
          <select
            v-model="form.city"
            class="w-full px-4 py-2.5 rounded-xl border border-stone-200 text-stone-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all bg-white"
          >
            <option value="Dhaka">Dhaka (Inside Dhaka - ৳60)</option>
            <option value="Outside Dhaka">Outside Dhaka (All Bangladesh - ৳120)</option>
            <option value="Chittagong">Chittagong</option>
            <option value="Sylhet">Sylhet</option>
            <option value="Rajshahi">Rajshahi</option>
            <option value="Khulna">Khulna</option>
            <option value="Barisal">Barisal</option>
            <option value="Rangpur">Rangpur</option>
            <option value="Mymensingh">Mymensingh</option>
          </select>
        </div>

        <!-- Street / Delivery Address -->
        <div>
          <label class="block text-xs font-semibold text-stone-700 uppercase tracking-wider mb-1.5">
            Default Delivery Address
          </label>
          <textarea
            v-model="form.address"
            rows="3"
            class="w-full px-4 py-2.5 rounded-xl border border-stone-200 text-stone-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all bg-white resize-none"
            placeholder="House #, Road #, Sector / Area, Landmark (e.g. Flat 4B, House 12, Road 7, Dhanmondi, Dhaka)"
          ></textarea>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
          <button
            type="submit"
            :disabled="isSaving"
            class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-white text-sm font-semibold transition-all inline-flex items-center justify-center gap-2 shadow-sm"
          >
            <Icon v-if="isSaving" name="svg-spinners:180-ring-with-bg" class="w-4 h-4" />
            <Icon v-else name="solar:diskette-bold" class="w-4 h-4" />
            <span>{{ isSaving ? "Saving Changes..." : "Save Profile Changes" }}</span>
          </button>
        </div>
      </form>
    </div>
  </CustomerLayout>
</template>

<script setup>
const snackbar = useSnackbar();
import { ref, reactive, watch, computed } from "vue";

definePageMeta({
  middleware: "auth",
});

const isSaving = ref(false);

const { data: customerData, pending, refresh } = await useApiFetch("/client/me", {
  server: false,
  lazy: true,
});

const customer = computed(() => customerData.value?.data || null);

const form = reactive({
  name: "",
  email: "",
  city: "Dhaka",
  address: "",
});

watch(
  customer,
  (val) => {
    if (val) {
      form.name = val.name || "";
      form.email = val.email || "";
      form.city = val.city || "Dhaka";
      form.address = val.address || "";
    }
  },
  { immediate: true }
);

const handleUpdateProfile = async () => {
  isSaving.value = true;
  try {
    const { data, error } = await useApiFetch("/client/me", {
      method: "PUT",
      body: {
        name: form.name,
        email: form.email || null,
        city: form.city,
        address: form.address || null,
      },
    });

    if (error.value) {
      snackbar.add({ type: "error", text: apiErrorText(error.value, "Failed to update profile") });
    } else {
      snackbar.add({ type: "success", text: "Profile updated successfully!" });
      refresh();
    }
  } catch (err) {
    snackbar.add({ type: "error", text: "An unexpected error occurred while saving profile" });
  } finally {
    isSaving.value = false;
  }
};
</script>
