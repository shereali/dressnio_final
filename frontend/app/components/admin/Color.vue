<script setup>
const { createItem } = useCrud();
const store = useUtilityStore();
const route = useRoute();
const colors = ref([
  {
    color_code: "",
  },
]);

const addItem = () => {
  colors.value.push({
    color_code: "",
  });
};
const removeItem = (index) => {
  colors.value.splice(index, 1);
};

let form = ref();
store.apiEndpoint = "/color";
store.pageBackLink = "/admin/products/color";
store.pageTitle = "Color";
// FORM SUBMISSION: createItem() FUNCTIOIN LOCATED IN useCrud
const onSubmit = async () => {
  let formId = document.querySelector(".submit-form");
  let formData = new FormData(formId);
  store.isLoading = true;
  // form.value.clear();

  await createItem(store.apiEndpoint, formData, form);
  // speak("One record is added to database");
};

if (store.is_edit) {
  await getSingleItem
    .singleItem(store.apiEndpoint, route.params.id)
    .then((res) => {
      if (res.data.value) {
        store.formData = res.data.value?.data;
        colors.value = res.data.value?.data?.colors;
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
    <UForm ref="form" class="submit-form" :state="colors" @submit="onSubmit()">
      <UFormGroup label="Color Set Name" name="name" class="w-1/5 mb-5">
        <UInput v-model="store.formData.name" />
      </UFormGroup>
      <div
        class="flex items-start justify-start gap-3"
        v-for="(item, index) in colors"
        :key="index"
      >
        <UFormGroup label="Color Code" name="color_code[]" class="w-1/12" hex>
          <UInput type="color" inputClass="h-20" v-model="item.color_code" />
        </UFormGroup>

        <UFormGroup label="Color Name" name="color_name[]">
          <UInput type="text" />
        </UFormGroup>

        <div v-if="colors.length != 1">
          <UButton
            type="button"
            class="mt-6"
            color="red"
            @click="removeItem(index)"
          >
            <Icon name="mdi:minus" />
          </UButton>
        </div>

        <div v-if="index == colors.length - 1 ? true : false">
          <UButton
            type="button"
            :class="index > 0 ? 'mt-6' : 'mt-7'"
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
