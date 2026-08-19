<!-- pages/login.vue -->
<template>
  <div class="flex justify-center items-center h-screen my-20">
    <div class="w-full max-w-md">
      <!-- Tab Switching Buttons -->
      <div class="flex justify-center">
        <button
          @click="changeTab('login')"
          :class="{
            'bg-blue-500 text-white': activeTab === 'login',
            'text-blue-500': activeTab !== 'login',
          }"
          class="px-4 py-2 rounded-l-md focus:outline-none"
        >
          Login
        </button>
        <button
          @click="changeTab('register')"
          :class="{
            'bg-blue-500 text-white': activeTab === 'register',
            'text-blue-500': activeTab !== 'register',
          }"
          class="px-4 py-2 rounded-r-md focus:outline-none"
        >
          Register
        </button>
      </div>
      <Login v-if="activeTab === 'login'" />
      <Register v-else />
    </div>
  </div>
</template>

<script setup>
const activeTab = ref("login");

const changeTab = (tab) => {
  activeTab.value = tab;
};
</script>
