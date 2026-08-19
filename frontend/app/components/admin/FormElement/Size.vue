<script setup>
const { createItem } = useCrud();
const store = useUtilityStore();
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
</script>

<template>
  <UCard>
   
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
      <div
        class="flex items-start justify-start gap-3 py-3"
        v-for="(item, index) in sizeItems"
        :key="index"
      >
        <UFormGroup label="Standard" name="standard[]" class="w-1/5">
          <UInput v-model="item.standard" />
        </UFormGroup>

        <UFormGroup
          v-if="store.formData.size_type == 1"
          label="Bust"
          name="bust[]"
          class="w-1/5"
        >
          <UInput v-model="item.bust" type="number" min="0" />
        </UFormGroup>

        <UFormGroup
          v-if="store.formData.size_type == 2"
          label="Length"
          name="lengths[]"
          class="w-1/5"
        >
          <UInput v-model="item.lengths" type="number" min="0" />
        </UFormGroup>

        <UFormGroup
          v-if="store.formData.size_type == 1 || store.formData.size_type == 2"
          label="Waist"
          name="waist[]"
          class="w-1/5"
        >
          <UInput v-model="item.waist" type="number" min="0" />
        </UFormGroup>

        <UFormGroup
          v-if="store.formData.size_type == 1 || store.formData.size_type == 2"
          label="Hip"
          name="hip[]"
          class="w-1/5"
        >
          <UInput v-model="item.hip" type="number" min="0" />
        </UFormGroup>

        <UFormGroup
          v-if="store.formData.size_type == 2"
          label="Thighs"
          name="thighs[]"
          class="w-1/5"
        >
          <UInput v-model="item.thighs" type="number" min="0" />
        </UFormGroup>

        <div v-if="sizeItems.length != 1">
          <UButton
            type="submit"
            class="mt-7"
            color="red"
            @click="removeItem(index)"
          >
            <Icon name="mdi:minus" />
          </UButton>
        </div>
        <div v-if="index == sizeItems.length - 1 ? true : false">
          <UButton
            type="submit"
            :class="index > 0 ? 'mt-7' : 'mt-7'"
            @click="addItem()"
          >
            <Icon name="mdi:plus" />
          </UButton>
        </div>
      </div>
      
    </UForm>
  </UCard>
</template>
