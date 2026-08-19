<script setup>
const { createItem } = useCrud();
const store = useUtilityStore();
const route = useRoute();
const incrementProducts = ref([
  {
    product_id: "",
  },
]);

const addItem = () => {
  incrementProducts.value.push({
    product_id: "",
  });
};
const removeItem = (index) => {
  incrementProducts.value.splice(index, 1);
};
store.formData = {};
let form = ref();
store.apiEndpoint = "/story";
store.pageBackLink = "/story";
store.pageTitle = "Story";
// FORM SUBMISSION: createItem() FUNCTIOIN LOCATED IN useCrud
const onSubmit = async () => {
  let formId = document.querySelector(".submit-form");
  let formData = new FormData(formId);
  store.isLoading = true;
  // form.value.clear();

  await createItem(store.apiEndpoint, formData, form);
  // speak("One record is added to database");
};

const productLists = ref([]);
onMounted(async () => {
  await getItems.items("/products", null).then((res) => {
    productLists.value = res.data.value?.data;
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
        incrementProducts.value = [];
        res.data.value?.data.products.forEach((item, index) => {
          incrementProducts.value.push({
            product_id: item.id,
          });
        });

        if (store.formData.image != null) {
          galleries.value.push(res.data.value.data.image);
          galleries.value.map((item) => (item.selected = true));
        }
      }
    });
}
</script>

<template>
  <UCard>
    <template #header>
      <div class="flex justify-between">
        <h1 class="font-semibold text-xl">
          {{ store.pageTitle }}
        </h1>
        <ULink
          :to="store.pageBackLink"
          class="bg-green-500 text-white shadow-sm px-2 py-1 rounded-md"
          ><Icon name="mdi:arrow-left" />Back to List</ULink
        >
      </div>
    </template>
    <UForm
      ref="form"
      class="submit-form"
      :state="incrementProducts"
      @submit="onSubmit()"
    >
      <div class="flex gap-3">
        <UFormGroup label="Story Name" name="name" class="">
          <UInput v-model="store.formData.name" />
        </UFormGroup>

        <ImageUploader
          class="mt-6"
          @send-selected-file="receiveSelectedFile"
          :is-multiple="false"
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
      <div
        class="flex items-start justify-start gap-3 my-3"
        v-for="(item, index) in incrementProducts"
        :key="index"
      >
        <UFormGroup label="Select Products" name="product_id[]">
          <USelect
            v-model="item.product_id"
            :loading="store.pending"
            searchable
            searchable-placeholder="Search a product..."
            :search-attributes="['name']"
            placeholder="Select Product"
            :options="productLists"
            value-attribute="id"
            option-attribute="name"
          />
        </UFormGroup>

        <div v-if="incrementProducts.length != 1">
          <UButton
            type="button"
            class="mt-7"
            color="red"
            @click="removeItem(index)"
          >
            <Icon name="mdi:minus" />
          </UButton>
        </div>

        <div v-if="index == incrementProducts.length - 1 ? true : false">
          <UButton
            type="button"
            :class="index > 0 ? 'mt-7' : 'mt-7'"
            @click="addItem()"
          >
            <Icon name="mdi:plus" />
          </UButton>
        </div>
      </div>
      <UDivider
        label="Form End"
        class="py-5"
        :ui="{ label: 'text-primary-500 dark:text-primary-400' }"
      />
      <div class="flex justify-end items-end">
        <UButton type="submit" :loading="store.isLoading">Save Changes</UButton>
      </div>
    </UForm>
  </UCard>
</template>
