<!-- VideoPage.vue -->

<template>
  <div class="max-w-max mx-auto py-20 px-10">
    <Headline
      title="Dressnio On Instagram"
      subtitle="Our Instagram Shared Video"
      class="mb-10"
    />
    <div v-for="video in videos" :key="video.id">
      <video width="320" height="240" controls>
        <source :src="video.url" type="video/mp4" />
        Your browser does not support the video tag.
      </video>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const videos = ref([]);

const fetchVideos = async () => {
  try {
    const response = await fetch("https://your-api-endpoint.com/videos");
    const data = await response.json();
    videos.value = data; // Assuming the API response is an array of video objects
  } catch (error) {
    console.error("Error fetching videos:", error);
  }
};

onMounted(() => {
  fetchVideos();
});
</script>
