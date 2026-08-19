<script setup>
// const { crudAction } = useApi();
// const data = crudAction("user-list");
// console.log("api", data);
// onMounted(() => {
//   const { data } = useFetch("http://localhost:8000/api/user-list");
//   console.log("user list", data);
// });

// const { data } = useFetch("http://localhost:8000/api/user-list");
// console.log("user list", data.value[0].name);

// const crud = useCrud();

let token = ref();

let form = reactive({
  name: "shereali",
  email: "sherealis@gmail.com",
  mobile_no: "01749240904",
  password: "123456",
});
import axios from "axios";

const client = useSanctumClient();

const handleCeate = async () => {
  const api_endpoint = "http://localhost:8000";

  const data = client(api_endpoint + "/api/create-user", {
    method: "POST",
    body: {
      ...form,
    },
  }).catch((error) => {
    console.log(error.statusCode, error.data);
  });
};
</script>

<template>
  <div>Page: foo</div>
  <UButton tye="submit" @click="handleCeate">create user</UButton>
</template>

<style scoped></style>

