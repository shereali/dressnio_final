<script lang="ts" setup>
const store = useUtilityStore();
const route = useRoute();
const products = ref([]);
const product_pending = ref();
store.apiEndpoint = "/watch-n-buy";
// store.formData.is_active = true;
await getItems.items("/products").then((productRes) => {
  products.value = productRes.data.value?.data;
  product_pending.value = productRes.pending.value;
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
  <div class="flex gap-5">
    <UFormGroup label="Name" name="name">
      <UInput v-model="store.formData.name" />
    </UFormGroup>
    <UFormGroup label="Select Products" name="product_id">
      <USelect
        v-model="store.formData.product_id"
        :loading="product_pending"
        searchable
        searchable-placeholder="Search a product..."
        :search-attributes="['name']"
        placeholder="Select Product"
        :options="products"
        value-attribute="id"
        option-attribute="name"
      />
    </UFormGroup>
    <div class="upload-gallery-image mt-6">
      <ImageUploader
        @send-selected-file="receiveSelectedFile"
        :is-multiple="true"
      />

      <div class="grid grid-cols-4 gap-2 mt-5">
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

    <div class="flex gap-x-3 mt-8">
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
  </div>
</template>

<style scoped></style>
