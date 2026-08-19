<script setup>
const store = useUtilityStore();
const validate = () => {
  let errors = [];
  if (!store.formData.name)
    errors.push({ path: "name", message: "Name field is required" });
  return errors;
};
</script>
<template>
  <div>
    <AdminFormElementColor />
  </div>
</template>
