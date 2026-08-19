<script setup>
definePageMeta({
  middleware: ["auth"],
});

// VALIDATION: FUNCTION LOCATED IN utils directory

const store = useUtilityStore();
const validate = () => {
  let errors = [];
  // if (!store.formData.name)
  //   errors.push({ path: "name", message: "Name field is required" });
  return errors;
};

let pageInfo = ref({
  apiEndpoint: "/watch-n-buy",
  pageBackLink: "/admin/watch-n-buy",
  pageTitle: "Create Watch & Buy",
});
</script>
<template>
  <div>
    <AdminHtmlForm :pageInfo="pageInfo" :validate="validate">
      <AdminFormElementWatchAndBuy />
    </AdminHtmlForm>
  </div>
</template>

