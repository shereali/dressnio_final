<script setup>
definePageMeta({
  middleware: ["auth"],
});

const store = useUtilityStore();
const { createItem } = useCrud();
const form = ref();
const onSubmit = async () => {
  await createItem("/facebook", store.formData, form);
};

// VALIDATION: FUNCTION LOCATED IN utils directory
</script>
<template>
  <div>
    <UForm
      id="form"
      :ref="form"
      :state="store.formData"
      class="space-y-4 submit-form"
      @submit="onSubmit()"
    >
      <UFormGroup label="API Key" name="api_key">
        <UInput v-model="store.formData.api_key" />
      </UFormGroup>
      <UFormGroup label="API Secret Key" name="api_secret_key">
        <UInput v-model="store.formData.api_secret_key" />
      </UFormGroup>
      <UButton type="submit" class="my-3">Update</UButton>
    </UForm>
  </div>
</template>

