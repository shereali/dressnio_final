<script setup>
definePageMeta({
  layout: "admin-custom",
  middleware: ["guest"],
});

const state = reactive({
  email: undefined,
  password: undefined,
});

const errors = ref({});

const validate = () => {
  errors.value = {};
  let isValid = true;
  
  if (!state.email) {
    errors.value.email = "Email field is required";
    isValid = false;
  }
  if (!state.password) {
    errors.value.password = "Password field is required";
    isValid = false;
  }
  
  return isValid;
};

const store = useUtilityStore();
const auth = useAuthStore();

onMounted(() => {
  store.isLoading = false;
});

async function handleLogin() {
  if (!validate()) return;
  
  store.isLoading = true;
  errors.value = {};
  
  try {
    await auth.adminLogin(state);
  } catch (error) {
    if (error.data?.errors) {
      errors.value = error.data.errors;
    }
    store.isLoading = false;
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 via-white to-gray-100 p-4">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md border border-gray-100">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-700 rounded-2xl mb-4 shadow-lg">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <h2 class="text-3xl font-bold bg-gradient-to-r from-brand-600 to-brand-800 bg-clip-text text-transparent mb-2">
          Admin Login
        </h2>
        <p class="text-gray-600 text-sm">Sign in to access admin panel</p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="space-y-5">
        <!-- Email Input -->
        <div class="space-y-2">
          <label for="email" class="block text-sm font-semibold text-gray-700">
            Email
          </label>
          <input
            type="email"
            id="email"
            v-model="state.email"
            placeholder="admin@example.com"
            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white focus:shadow-lg"
            :class="{ 'border-red-500 focus:ring-red-500': errors.email }"
          />
          <p v-if="errors.email" class="text-sm text-red-600 mt-1">{{ errors.email }}</p>
        </div>

        <!-- Password Input -->
        <div class="space-y-2">
          <label for="password" class="block text-sm font-semibold text-gray-700">
            Password
          </label>
          <input
            type="password"
            id="password"
            v-model="state.password"
            placeholder="Enter your password"
            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white focus:shadow-lg"
            :class="{ 'border-red-500 focus:ring-red-500': errors.password }"
          />
          <p v-if="errors.password" class="text-sm text-red-600 mt-1">{{ errors.password }}</p>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="store.isLoading"
          class="w-full bg-gradient-to-r from-brand-500 to-brand-700 !text-white py-3 px-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
        >
          <span class="flex items-center justify-center">
            <svg v-if="store.isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-if="!store.isLoading">Login</span>
            <span v-else>Logging in...</span>
          </span>
        </button>
      </form>
    </div>
  </div>
</template>

