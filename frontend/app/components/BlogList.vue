<script setup>
const props = defineProps({
  posts: {
    type: Object,
    required: true,
  },
});

const { posts } = toRefs(props);

watch(posts, (newPost, oldPost) => {
  return newPost;
});
</script>

<template>
  <Headline
    title="Read Our Article"
    subtitle="Hear what our Dear Clients say about us!"
    class="mb-10"
  />

  <div class="flex flex-wrap px-5">
    <!-- Blog Item 1 -->
    <div v-for="blog in posts" class="w-full md:w-1/2 lg:w-1/3 xl:w-1/4 p-4">
      <div class="bg-white rounded-lg overflow-hidden shadow-md">
        <NuxtImg
          v-if="blog.image != null"
          :src="blog.image"
          alt="Blog Image"
          class="w-full h-40 object-cover"
        />
        <div class="p-4">
          <h3 class="text-xl font-bold mb-2">{{ blog.title }}</h3>
          <div
            class="text-gray-700 leading-relaxed"
            v-html="blog.description"
          ></div>
          <NuxtLink
            :to="`/blog/${blog.slug}`"
            class="mt-2 inline-block text-blue-500 hover:underline"
            >Read More</NuxtLink
          >
        </div>
      </div>
    </div>

    <!-- Add more blog items as needed -->
  </div>
</template>

<style>
/* Add your custom styles here */
</style>
