<script setup>
const router = useRouter();
const imageUrl = ref("/images/auth/register.jpg");
const { isLoading, state } = useUtility();
const { createItem } = useApi();

const errors = ref({});

const validate = () => {
  errors.value = {};
  let isValid = true;
  
  if (!state.name) {
    errors.value.name = "Name Required";
    isValid = false;
  }
  if (!state.mobile_no) {
    errors.value.mobile_no = "Mobile Number Required";
    isValid = false;
  }
  
  return isValid;
};

const handleRegistration = async () => {
  if (!validate()) return;
  
  isLoading.value = true;
  errors.value = {};
  
  const { data, error, pending } = await createItem("/client/register", "POST", state);

  if (error.value) {
    console.log("fetch error", error.value.data?.errors);
    if (error.value.data?.errors) {
      errors.value = error.value.data.errors;
    }
    isLoading.value = false;
  }

  if (data.value) {
    localStorage.setItem("email", data.value.email);
    router.push({ path: "/otp" });
  }
};
</script>

<template>
  <div class="flex h-screen lg:flex-row flex-col relative overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
      <div class="absolute top-0 -left-4 w-72 h-72 bg-brand-200 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
      <div class="absolute top-0 -right-4 w-72 h-72 bg-brand-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
      <div class="absolute -bottom-8 left-20 w-72 h-72 bg-brand-100 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>
    </div>

    <!-- Left side with full image -->
    <div
      class="hidden lg:flex flex-none w-1/2 bg-cover bg-center relative z-10"
      :style="{ backgroundImage: 'url(' + imageUrl + ')' }"
    >
      <div class="absolute inset-0 bg-gradient-to-br from-brand-600/20 to-brand-800/20"></div>
    </div>

    <!-- Right side with registration form -->
    <div class="flex items-center justify-center w-full lg:w-1/2 p-8 relative z-10 bg-gradient-to-br from-brand-50/50 via-white to-brand-100/50">
      <div class="max-w-md w-full">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-700 rounded-2xl mb-4 shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
          </div>
          <h2 class="text-4xl font-bold bg-gradient-to-r from-brand-600 to-brand-800 bg-clip-text text-transparent mb-2">
            Create Account
          </h2>
          <p class="text-gray-600">Join us today and start your journey</p>
        </div>

        <!-- Registration Form -->
        <form @submit.prevent="handleRegistration" class="space-y-5">
          <!-- Name Input -->
          <div class="space-y-2">
            <label for="name" class="block text-sm font-semibold text-gray-700">
              Full Name
            </label>
            <div class="relative group">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-brand-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <input
                type="text"
                id="name"
                v-model="state.name"
                placeholder="John Doe"
                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white focus:shadow-lg"
                :class="{ 'border-red-500 focus:ring-red-500': errors.name }"
              />
            </div>
            <p v-if="errors.name" class="text-sm text-red-600 mt-1">{{ errors.name }}</p>
          </div>

          <!-- Mobile Number Input -->
          <div class="space-y-2">
            <label for="mobile_no" class="block text-sm font-semibold text-gray-700">
              Mobile Number
            </label>
            <div class="relative group">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-brand-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
              </div>
              <input
                type="text"
                id="mobile_no"
                v-model="state.mobile_no"
                placeholder="01860xxxxxx"
                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white focus:shadow-lg"
                :class="{ 'border-red-500 focus:ring-red-500': errors.mobile_no }"
              />
            </div>
            <p v-if="errors.mobile_no" class="text-sm text-red-600 mt-1">{{ errors.mobile_no }}</p>
          </div>

          <!-- Email Input -->
          <div class="space-y-2">
            <label for="email" class="block text-sm font-semibold text-gray-700">
              Email Address <span class="text-xs text-gray-500">(Optional)</span>
            </label>
            <div class="relative group">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-brand-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                </svg>
              </div>
              <input
                type="email"
                id="email"
                v-model="state.email"
                placeholder="john@example.com"
                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white focus:shadow-lg"
                :class="{ 'border-red-500 focus:ring-red-500': errors.email }"
              />
            </div>
            <p v-if="errors.email" class="text-sm text-red-600 mt-1">{{ errors.email }}</p>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="isLoading"
            class="w-full bg-gradient-to-r from-brand-500 to-brand-700 text-white py-3 px-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
          >
            <span class="flex items-center justify-center">
              <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span v-if="!isLoading">Create Account</span>
              <span v-else>Creating Account...</span>
              <svg v-if="!isLoading" class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
            </span>
          </button>
        </form>

        <!-- Login Link -->
        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600">
            Already have an account?
            <NuxtLink
              to="/login"
              class="font-semibold text-brand-600 hover:text-brand-700 transition-colors"
            >
              Sign In
            </NuxtLink>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}
</style>
