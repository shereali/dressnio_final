<script setup>
const { createItem } = useCrud();
const store = useUtilityStore();
const route = useRoute();
const sizeItems = ref([
  {
    size_type: "",
    standard: "",
    lengths: "",
    waist: "",
    hip: "",
    thighs: "",
  },
]);

const addItem = () => {
  sizeItems.value.push({
    size_type: "",
    standard: "",
    lengths: "",
    waist: "",
    hip: "",
    thighs: "",
  });
};
const removeItem = (index) => {
  sizeItems.value.splice(index, 1);
};

let form = ref();
store.apiEndpoint = "/size";
store.pageBackLink = "/products/size";
store.pageTitle = "Sizes";

// FORM SUBMISSION: createItem() FUNCTIOIN LOCATED IN useCrud
const onSubmit = async () => {
  let formId = document.querySelector(".submit-form");
  let formData = new FormData(formId);
  store.isLoading = true;
  form.value.clear();

  await createItem(store.apiEndpoint, formData, form);
  // speak("One record is added to database");
};

const sizeTypes = [
  { id: 1, size_type: "Kameez Size" },
  { id: 2, size_type: "Salwar  Size" },
];

if (store.is_edit) {
  await getSingleItem
    .singleItem(store.apiEndpoint, route.params.id)
    .then((res) => {
      if (res.data.value) {
        store.formData = res.data.value?.data;
        // sizeItems.value = [];
        // sizeItems.value.push(res.data.value.data);
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
      :state="sizeItems"
      @submit="onSubmit()"
    >
      <UFormGroup label="Size Type" name="size_type">
        <USelect
          v-model="store.formData.size_type"
          searchable
          searchable-placeholder="Search a person..."
          :search-attributes="['size_type']"
          placeholder="Select size type"
          :options="sizeTypes"
          value-attribute="id"
          option-attribute="size_type"
        />
      </UFormGroup>
      <div class="flex items-start justify-start gap-3 py-3">
        <UFormGroup label="Standard" name="standard" class="w-1/5">
          <UInput v-model="store.formData.standard" />
        </UFormGroup>

        <UFormGroup
          v-if="store.formData.size_type == 1"
          label="Bust"
          name="bust"
          class="w-1/5"
        >
          <UInput v-model="store.formData.bust" type="number" min="0" />
        </UFormGroup>

        <UFormGroup
          v-if="store.formData.size_type == 2"
          label="Length"
          name="lengths"
          class="w-1/5"
        >
          <UInput v-model="store.formData.lengths" type="number" min="0" />
        </UFormGroup>

        <UFormGroup
          v-if="store.formData.size_type == 1 || store.formData.size_type == 2"
          label="Waist"
          name="waist"
          class="w-1/5"
        >
          <UInput v-model="store.formData.waist" type="number" min="0" />
        </UFormGroup>

        <UFormGroup
          v-if="store.formData.size_type == 1 || store.formData.size_type == 2"
          label="Hip"
          name="hip"
          class="w-1/5"
        >
          <UInput v-model="store.formData.hip" type="number" min="0" />
        </UFormGroup>

        <UFormGroup
          v-if="store.formData.size_type == 2"
          label="Thighs"
          name="thighs"
          class="w-1/5"
        >
          <UInput v-model="store.formData.thighs" type="number" min="0" />
        </UFormGroup>
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
