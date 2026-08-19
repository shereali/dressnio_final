<script setup>
const store = useUtilityStore();

store.apiEndpoint = "/category";
let route = useRoute();
let categories = ref([]);
store.formData.is_active = true;

// GET DATA FUNCTION LOCATED IN utils DIRECCTORY
onMounted(async () => {
  await getItems.items(store.apiEndpoint, null).then((res) => {
    categories.value = res.data.value?.data;
    store.pending = res.pending.value;
  });
});

let galleries = ref([]);

const receiveSelectedFile = (value) => {
  console.log("images", value);
  galleries.value = value;
};

const removeImage = (index) => {
  galleries.value.splice(index, 1);
};

if (store.is_edit) {
  await getSingleItem
    .singleItem(store.apiEndpoint, route.params.id)
    .then((res) => {
      if (res.data.value) {
        store.formData = res.data.value?.data;
        if (store.formData.image != null) {
          galleries.value.push(res.data.value.data.image);
          galleries.value.map((item) => (item.selected = true));
        }
      }
    });
}
</script>
<template>
  <UFormGroup label="Name" name="name">
    <UInput v-model="store.formData.name" />
  </UFormGroup>

  <div class="flex gap-x-3">
    <UToggle
      name="is_active"
      on-icon="i-heroicons-check-20-solid"
      off-icon="i-heroicons-x-mark-20-solid"
      v-model="store.formData.is_active"
    />
    <label
      class="font-medium dark:text-gray-200"
      :class="store.formData.is_active ? 'text-green-500' : 'text-red-500'"
    >
      {{ store.formData.is_active ? "Active" : "Inactive" }}
    </label>
  </div>

  <select
    class="border-2 border-gray-200 rounded-lg outline-none py-1 w-full px-3 focus:border-2 focus:border-green-400"
    name="parent_id"
    v-model="store.formData.parent_id"
  >
    <option selected>Select Category</option>
    <option v-for="(category, index) in categories" :value="category.id">
      {{ category.name }}
    </option>
  </select>
  <div class="upload-gallery-image py-3">
    <ImageUploader
      @send-selected-file="receiveSelectedFile"
      :is-multiple="true"
    />
  </div>
  <div class="py-3">
    <div class="grid grid-cols-4 gap-2">
      <div class="relative" v-for="(gallery, index) in galleries">
        <NuxtImg :src="gallery.media_url" />
        <Icon
          class="absolute right-0 top-0 z-20 text-red-700 cursor-pointer hover:text-red-500"
          name="fluent-emoji-flat:cross-mark"
          @click="removeImage(index)"
        />
        <UCheckbox
          v-model="gallery.selected"
          :value="gallery.id"
          name="media_id"
          class="absolute right-0 bottom-0 z-20"
          label=""
        />
      </div>
    </div>
  </div>
</template>
