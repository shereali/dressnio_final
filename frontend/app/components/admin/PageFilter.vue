<script setup>
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
const selectedItem = ref(false);

const q = ref("");
</script>

<template>
  <div>
    <div class="flex items-center space-x-4 mb-4">
      <!-- <UCheckbox
        v-model="selectedItem"
        name="notifications"
        label="Notifications"
      /> -->
      <!-- Select Dropdown -->
      <!-- <USelectMenu
        v-model="selected"
        :options="people"
        searchable
        placeholder="Select people"
        value-attribute="name"
        option-attribute="name"
      /> -->

      <!-- Search Bar -->
      <!-- <UInput
        v-model="q"
        name="q"
        placeholder="Search..."
        icon="i-heroicons-magnifying-glass-20-solid"
        autocomplete="off"
        :ui="{ icon: { trailing: { pointer: '' } } }"
      >
        <template #trailing>
          <UButton
            v-show="q !== ''"
            color="gray"
            variant="link"
            icon="i-heroicons-x-mark-20-solid"
            :padded="false"
            @click="q = ''"
          />
        </template>
      </UInput> -->

      <!-- Sorting Dropdown -->
      <!-- <USelectMenu
        v-model="selected"
        :options="people"
        searchable
        placeholder="Select people"
        value-attribute="name"
        option-attribute="name"
      /> -->
    </div>
  </div>
</template>

<style scoped></style>
