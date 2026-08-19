<script setup>
const store = useUtilityStore();

store.apiEndpoint = "/slider";
let route = useRoute();
const galleries = ref([]);

const receiveSelectedFile = (value) => {
  console.log("images", value);
  galleries.value = value;
};
// GET DATA FUNCTION LOCATED IN utils DIRECCTORY

if (store.is_edit) {
  await getSingleItem
    .singleItem(store.apiEndpoint, route.params.id)
    .then((res) => {
      if (res.data.value) {
        store.formData = res.data.value?.data;
        if (res.data.value.data.image != null) {
          galleries.value.push({
            media_url: res.data.value.data.image,
            id: res.data.value.data.media_id,
            selected: true
          });
        }
      }
    });
}
</script>
<template>
  <div class="w-3/4">
    <div class="grid grid-cols-1 place-content-center">
      <UFormGroup label="Title" name="title">
        <UInput v-model="store.formData.title" />
      </UFormGroup>
      <client-only>
        <Tiptap v-model="store.formData.description" />
      </client-only>
      <textarea
        name="description"
        v-model="store.formData.description"
        hidden
      ></textarea>
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

      <UFormGroup label="Is Active" name="is_active">
        <UToggle v-model="store.formData.is_active" />
      </UFormGroup>
    </div>
  </div>
</template>
