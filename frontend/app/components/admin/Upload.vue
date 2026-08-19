<script setup>
let activeTab = ref("search");

const showTab = (tabName) => {
  activeTab.value = tabName;
};

const people = [
  {
    id: 1,
    name: "Wade Cooper",
  },
  {
    id: 2,
    name: "Arlene Mccoy",
  },
  {
    id: 3,
    name: "Devon Webb",
  },
  {
    id: 4,
    name: "Tom Cook",
  },
];

const selected = ref(people[0].name);
</script>

<template>
  <UCard>
    <template #header>
      <div class="flex items-center">
        <button
          @click="showTab('search')"
          :class="
            activeTab === 'search' ? 'bg-green-500 text-white' : 'bg-gray-200'
          "
          class="py-1 px-4 text-gray-600 focus:outline-none focus:shadow-outline"
        >
          <Icon name="gg:select-r" /> Select File
        </button>
        <button
          @click="showTab('upload')"
          :class="
            activeTab === 'upload' ? 'bg-green-500 text-white' : 'bg-gray-200'
          "
          class="py-1 px-4 text-gray-600 focus:outline-none focus:shadow-outline"
        >
          <Icon name="solar:upload-outline" /> Upload File
        </button>
      </div>
    </template>

    <div v-show="activeTab === 'search'">
      <!-- Search Tab Content -->

      <div class="flex justify-start items-center gap-5">
        <UFormGroup name="search">
          <UInput
            icon="i-heroicons-magnifying-glass-20-solid"
            size="sm"
            color="white"
            trailing
            placeholder="Search..."
          />
        </UFormGroup>
        <UFormGroup name="name">
          <USelect
            v-model="selected"
            searchable
            searchable-placeholder="Search a person..."
            :search-attributes="['name']"
            placeholder="Select Category"
            :options="people"
            value-attribute="id"
            option-attribute="name"
          />
        </UFormGroup>
      </div>
      <!-- Thumbnails go here -->

      <div class="grid grid-cols-6 h-[300px] overflow-y-auto overflow-x-hidden">
        <div class="shadow bg-green-200 w-64 h-60 m-2"></div>
        <div class="shadow bg-green-200 w-64 h-60 m-2"></div>
        <div class="shadow bg-green-200 w-64 h-60 m-2"></div>
        <div class="shadow bg-green-200 w-64 h-60 m-2"></div>
        <div class="shadow bg-green-200 w-64 h-60 m-2"></div>
        <div class="shadow bg-green-200 w-64 h-60 m-2"></div>
        <div class="shadow bg-green-200 w-64 h-60 m-2"></div>
        <div class="shadow bg-green-200 w-64 h-60 m-2"></div>
        <div class="shadow bg-green-200 w-64 h-60 m-2"></div>
        <div class="shadow bg-green-200 w-64 h-60 m-2"></div>
        <div class="shadow bg-green-200 w-64 h-60 m-2"></div>
        <div class="shadow bg-green-200 w-64 h-60 m-2"></div>
      </div>
    </div>

    <div
      v-show="activeTab === 'upload'"
      class="flex justify-start items-center gap-4"
    >
      <!-- Upload Tab Content -->
      <UButton
        class="py-1.5 px-4 bg-green-500 text-white focus:outline-none focus:shadow-outline"
      >
        <Icon name="subway:file-11" /> Brows File
      </UButton>
      <UFormGroup name="files" class="hidden">
        <UInput type="file" :multiple="true" />
      </UFormGroup>
      <UFormGroup name="name">
        <USelect
          v-model="selected"
          searchable
          searchable-placeholder="Search a person..."
          :search-attributes="['name']"
          placeholder="Select Category"
          :options="people"
          value-attribute="id"
          option-attribute="name"
          class="py-1"
        />
      </UFormGroup>
      <!-- Uploaded images preview goes here -->
    </div>

    <template #footer>
      <button
        class="bg-green-500 text-white py-1.5 px-5 justify-end rounded-md"
      >
        Upload
      </button>
    </template>
  </UCard>
</template>

<style scoped>
/* Add your Tailwind CSS styles here */
</style>
