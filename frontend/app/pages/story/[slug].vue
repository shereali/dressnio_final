<script lang="ts" setup>
const config = useRuntimeConfig();
const route = useRoute();
const { data, pending, error, refresh } = useFetch(
  `${config.public.apiBase}/client/story/${route.params.slug}`
);
if (data.value) {
  console.log("story details", data.value);
}
</script>

<template>
  <div>{{ data?.data?.products }}</div>
</template>

<style scoped></style>
