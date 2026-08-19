<script setup>
const crudStore = useCrudStore();
const categoryApiEndpoint = "/category";
let state = crudStore.formData;
let mediaTypes = [
  { name: "Image", media_type: 1 },
  { name: "video", media_type: 2 },
];
let categories = ref([]);
onMounted(async () => {
  await getItems.items(categoryApiEndpoint, null).then((res) => {
    categories.value = res.data.value?.data;
    crudStore.pending = res.pending.value;
  });
});
</script>
<template>
  <UFormGroup label="Name" name="name">
    <UInput type="file" />
  </UFormGroup>

  <UFormGroup label="Media Type" name="media_type">
    <USelect
      v-model="crudStore.formData.media_type"
      searchable
      searchable-placeholder="Search a person..."
      :search-attributes="['name']"
      placeholder="Select Media Type"
      :options="mediaTypes"
      value-attribute="media_type"
      option-attribute="name"
    />
  </UFormGroup>
  <UFormGroup label="Category" name="category_id">
    <USelect
      v-model="crudStore.formData.category_id"
      :loading="crudStore.pending"
      searchable
      searchable-placeholder="Search a person..."
      :search-attributes="['name']"
      placeholder="Select Category"
      :options="categories"
      value-attribute="id"
      option-attribute="name"
    />
  </UFormGroup>
</template>
