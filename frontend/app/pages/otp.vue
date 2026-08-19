<script setup>
definePageMeta({
  middleware: ["guest"],
});
const imageUrl = ref("/images/auth/register.jpg"); // Replace with the path to your image
const { isLoading, state, form, router, auth } = useUtility();

const validate = (state) => {
  const errors = [];
  if (!state.otp) {
    errors.push({ path: "otp", message: "OTP is required" });
  }
  return errors;
};

const handleLogin = async () => {
  isLoading.value = true;
  const credentials = {
    email: localStorage.getItem("email"),
    password: state.otp,
  };

  form.value.clear();

  try {
    const { data, error } = await auth.login(credentials, form, isLoading);
    localStorage.removeItem("email");
  } catch (error) {
    isLoading.value = false;
    throw error;
  }
};
</script>
<template>
  <div class="flex h-screen lg:flex-row flex-col">
    <!-- Left side with full image -->
    <div
      class="flex-none w-full lg:w-1/2 bg-cover bg-center"
      :style="{ backgroundImage: 'url(' + imageUrl + ')' }"
    >
      <!-- You can replace 'imageUrl' with the path to your image -->
    </div>

    <!-- Right side with registration form -->
    <div class="flex items-center justify-center w-full lg:w-1/2 p-8">
      <div class="max-w-md w-full">
        <h2 class="text-3xl font-semibold mb-4">OTP Verification</h2>

        <!-- Display registration input when step is 1 -->

        <div>
          <div class="flex items-center mb-4">
            <span class="text-indigo-800 mr-2 text-3xl">
              <Icon name="mdi:check-circle" />
            </span>
            <p>
              OTP has been sent to your mobile_no number. Please check and
              verify your 4-digit OTP to login successfully.
            </p>
          </div>
          <UForm
            ref="form"
            :validate="validate"
            :state="state"
            @submit="handleLogin"
          >
            <div class="mb-4">
              <UFormGroup
                label="OTP:"
                name="otp"
                class="block text-gray-700 text-sm font-bold mb-2"
              >
                <UInput
                  v-model="state.otp"
                  type="text"
                  id="otp"
                  name="otp"
                  inputClass="border rounded w-full py-2 px-3"
                  placeholder="xxxx"
                />
              </UFormGroup>
            </div>

            <UButton
              type="submit"
              class="bg-blue-500 text-white py-2 px-4 rounded"
              :loading="isLoading"
            >
              Verify OTP
            </UButton>
            <!-- Resend OTP button -->
            <button
              type="button"
              @click="resendOTP"
              class="bg-indigo-800 text-slate-950 py-2 px-4 rounded mx-2"
            >
              Resend OTP
            </button>
          </UForm>
        </div>
        <!-- Display OTP input when step is 2 -->

        <!-- Already have an account link -->
        <div class="mt-4">
          <p>
            Already have an account?
            <NuxtLink to="/login" class="text-indigo-600">Login here</NuxtLink>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
