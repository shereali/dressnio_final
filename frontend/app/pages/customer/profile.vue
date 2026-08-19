<template>
  <CustomerLayout>
    <div class="container mx-auto p-4">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800">Your Profile</h1>
        <button
          @click="editProfile"
          class="text-blue-500 hover:underline focus:outline-none"
        >
          Edit Profile
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Left Column - User Information -->
        <div>
          <h2 class="text-2xl font-semibold mb-6 text-gray-800">
            User Information {{ user }}
          </h2>
          <div class="mb-4 text-gray-700">
            <strong>Email:</strong> {{ customer.email }}
          </div>
          <div class="mb-4 text-gray-700">
            <strong>Name:</strong> {{ customer.name }}
          </div>
          <!-- Add more user information fields as needed -->
        </div>

        <!-- Right Column - Address Information -->
        <div>
          <h2 class="text-2xl font-semibold mb-6 text-gray-800">
            Address Information
          </h2>
          <div class="mb-4 text-gray-700">
            <strong>Street:</strong> {{ customer.address.street }}
          </div>
          <div class="mb-4 text-gray-700">
            <strong>City:</strong> {{ customer.address.city }}
          </div>
          <!-- Add more address information fields as needed -->
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>

<script setup>
const customer = {
  email: "john.doe@example.com",
  name: "John Doe",
  address: {
    street: "123 Main St",
    city: "Cityville",
    // Add more address fields as needed
  },
};

const editProfile = () => {
  // Logic for editing the profile
};
</script>
