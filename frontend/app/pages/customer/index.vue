<script setup>
definePageMeta({
  middleware: "auth",
});

const token = useTokenStore();

const showUser = async () => {
  try {
    const { data, error } = await useLazyFetch(
      "http://localhost:8000/api/user",
      {
        headers: {
          Accept: "application/json",
          Authorization: "Bearer " + token.getToken,
        },
      }
    );

    if (data.value) {
      console.log(data.value);
    }
  } catch (error) {
    throw error;
  }
};

const sendSms = async () => {
  const response = await $fetch("http://api.quicksms.xyz/smsapi", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "Access-Control-Allow-Origin": "http://api.quicksms.xyz",
    },
    body: JSON.stringify({
      api_key: "4ff8cc2cea2b5c09",
      senderid: "12345",
      type: "text",
      scheduledDateTime: "",
      msg: "Thank you for using dressnio",
      contacts: "8801749240901",
    }),
  });

  // Handle the response as needed
  const data = await response.json();
  console.log(data);
};

let form = reactive({
  name: "shereali",
  email: "sherealik@gmail.com",
  mobile_no: "01749240902",
  password: "123456",
});

const handleUserCreate = () => {
  const api_endpoint = "http://localhost:8000";
  const response = useFetch(api_endpoint + "/api/create-user", {
    method: "POST",
    body: {
      ...form,
    },
  }).catch((error) => {
    console.log("error", error);
  });
};

// const { data, error, isLoading } = useFetch(sendSms);
</script>
<template>
  <CustomerLayout>
    <div>
      <h2 class="text-2xl font-semibold mb-4 p-10">
        Welcome to Your Dashboard
      </h2>
      <!-- Add content for the dashboard home page -->
      <UButton type="button" @click.prevent="showUser">Send SMS</UButton>
    </div>
  </CustomerLayout>
</template>
