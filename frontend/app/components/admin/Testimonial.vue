<script setup>
const store = useUtilityStore();

store.apiEndpoint = "/testimonial";
let route = useRoute();

const galleries = ref([]);

const receiveSelectedFile = (value) => {
  galleries.value = value;
};

// GET DATA FUNCTION LOCATED IN utils DIRECCTORY

if (store.is_edit) {
  await getSingleItem
    .singleItem(store.apiEndpoint, route.params.id)
    .then((res) => {
      if (res.data.value) {
        store.formData = res.data.value?.data;
        galleries.value.push(res.data.value.data.image);
        if (galleries.value.length != 0) {
          galleries.value.map((item) =>
            item ? (item.selected = true) : false
          );
        }
      }
    });
}
</script>
<template>
  <div class="grid grid-cols-3 gap-3">
    <UFormGroup label="Name" name="name">
      <UInput v-model="store.formData.name" />
    </UFormGroup>
    <UFormGroup label="Designation(Optional)" name="designation">
      <UInput v-model="store.formData.designation" />
    </UFormGroup>
    <UFormGroup label="Post Url" name="post_url">
      <UInput v-model="store.formData.post_url" />
    </UFormGroup>

    <div class="upload-gallery-image py-3">
      <ImageUploader
        @send-selected-file="receiveSelectedFile"
        :is-multiple="true"
      />
    </div>

    <UFormGroup label="Is Active" name="is_active">
      <UToggle v-model="store.formData.is_active" />
    </UFormGroup>
  </div>
  <div class="w-5/6">
    <div class="py-3" v-if="galleries.length != 0">
      <div class="grid grid-cols-4 gap-2">
        <div class="relative" v-for="(gallery, index) in galleries">
          <NuxtImg v-if="gallery != null" :src="gallery.media_url" />
          <Icon
            class="absolute right-0 top-0 z-20 text-red-700 cursor-pointer hover:text-red-500"
            name="fluent-emoji-flat:cross-mark"
            @click="removeImage(index)"
          />
          <UCheckbox
            v-if="gallery != null"
            v-model="gallery.selected"
            :value="gallery.id"
            name="media_id"
            class="absolute right-0 bottom-0 z-20"
            label=""
          />
        </div>
      </div>
    </div>
    <client-only>
      <Tiptap v-model="store.formData.description" />
    </client-only>
    <textarea
      name="description"
      v-model="store.formData.description"
      hidden
    ></textarea>
  </div>
</template>
