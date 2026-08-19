<script setup>
definePageMeta({
  middleware: ["auth"],
});

onMounted(() => {
  const store = useUtilityStore();
  store.pageTitle = "Steadfast Courier";
});
</script>

<template>
  <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
    <div class="flex items-center gap-4 mb-6">
      <div class="p-4 bg-orange-100 rounded-xl text-orange-600">
        <Icon name="iconamoon:delivery-free" size="48" />
      </div>
      <div>
        <h3 class="text-2xl font-bold text-gray-900">Steadfast Courier Integration</h3>
        <p class="text-gray-500">Manage your shipping and delivery through Steadfast API.</p>
      </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
      <div class="p-6 border border-gray-100 rounded-xl bg-gray-50/50">
        <h4 class="font-bold text-lg mb-4">Credentials</h4>
        <div class="space-y-4">
          <UFormGroup label="API Key">
            <UInput placeholder="Enter Steadfast API Key" />
          </UFormGroup>
          <UFormGroup label="Secret Key">
            <UInput type="password" placeholder="Enter Steadfast Secret Key" />
          </UFormGroup>
          <UButton color="orange">Save Credentials</UButton>
        </div>
      </div>
      
      <div class="p-6 border border-gray-100 rounded-xl bg-gray-50/50">
        <h4 class="font-bold text-lg mb-4">Quick Stats</h4>
        <div class="grid grid-cols-2 gap-4 text-center">
          <div class="p-4 bg-white rounded-lg shadow-sm">
            <p class="text-xs text-gray-500 font-bold uppercase">Pending</p>
            <p class="text-2xl font-bold">12</p>
          </div>
          <div class="p-4 bg-white rounded-lg shadow-sm">
            <p class="text-xs text-gray-500 font-bold uppercase">Delivered</p>
            <p class="text-2xl font-bold">145</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

