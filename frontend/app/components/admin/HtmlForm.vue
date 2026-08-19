<script setup>
let props = defineProps(["pageInfo", "validate"]);

const { createItem } = useCrud();
const store = useUtilityStore();
store.pageBackLink = props.pageInfo.pageBackLink;
store.isLoading = false;

let form = ref();

if (store.is_edit == false) {
  store.formData = {};
}

store.isCreateMore = false;

// FORM SUBMISSION: createItem() FUNCTIOIN LOCATED IN useCrud
const onSubmit = async () => {
  let formClass = document.querySelector(".submit-form");
  let formData = new FormData(formClass);
  store.isLoading = true;

  console.log("formData", formData);

  await createItem(props.pageInfo.apiEndpoint, formData, form);
  // store.formData = {};
  if (store.getToastMsg && store.isCreateMore)
    toastMessage(store.getToastMsg, "success");
};

function resetFormElements(formId) {
  var form = document.getElementById(formId);
  if (form) {
    var elements = form.elements;
    for (var i = 0; i < elements.length; i++) {
      if (
        elements[i].type !== "button" &&
        elements[i].type !== "submit" &&
        elements[i].type !== "reset"
      ) {
        elements[i].value = "";
      }
    }
  } else {
    console.error("Form with ID " + formId + " not found");
  }
}

if (store.getToastMsg) {
  resetFormElements("form");
}

const createMore = () => {
  store.isCreateMore = true;
};
const createOnce = () => {
  store.isCreateMore = false;
};
</script>

<template>
  <UCard>
    <template #header>
      <div class="flex justify-between">
        <h1 class="font-semibold text-xl">{{ props.pageInfo.pageTitle }}</h1>
        <ULink
          :to="props.pageInfo.pageBackLink"
          class="bg-green-500 text-white shadow-sm px-2 py-1 rounded-md"
          ><Icon name="mdi:arrow-left" />Back to List</ULink
        >
      </div>
    </template>
    <UForm
      id="form"
      ref="form"
      :validate="props.validate"
      :state="store.formData"
      class="space-y-4 submit-form"
      @submit="onSubmit()"
    >
      <slot></slot>
      <UDivider
        label="Form End"
        :ui="{ label: 'text-primary-500 dark:text-primary-400' }"
      />
      <div class="w-full flex justify-end items-end gap-x-2">
        <UButton
          type="submit"
          @click="createOnce('form')"
          class=""
          :loading="store.isLoading"
        >
          <Icon
            v-if="store.is_edit && store.isLoading == false"
            name="grommet-icons:update"
          />
          <Icon
            v-if="store.is_edit == false && store.isLoading == false"
            name="mdi:plus"
          />{{ store.is_edit === false ? "Create" : "Update" }}
        </UButton>
        <UButton
          v-if="store.is_edit === false"
          type="submit"
          @click="createMore('form')"
          class=""
          :loading="store.isLoading"
        >
          <Icon
            v-if="store.is_edit === false && store.isLoading == false"
            name="mdi:plus"
          />Create & More
        </UButton>
      </div>
    </UForm>
    <template #footer> </template>
  </UCard>
</template>
