<script setup>
const emit = defineEmits(["sendSelectedFile"]);
let props = defineProps(["isMultiple"]);
const isOpen = ref(false);
const isArrayRefresh = ref(null);
const activeTab = ref("search");
const fileInput = ref();
const selectedFiles = ref([]);
const imagePreviews = ref([]);
const refreshData = ref([]);
const pending = ref("");
const form = ref();
const toast = useToast();
const is_disabled = ref(false);

const uploadedImages = ref([]);
const categories = ref([]);
const slug = ref("");

const category_id = ref(null);
const media_type = ref(null);
const search_category = ref(null);

const mediaTypes = ref([
  { id: 1, name: "Image" },
  { id: 2, name: "Video" },
]);

const handleBrowsFile = (event) => {
  console.log("category_id", category_id.value);

  toast.add({ title: "Category Or Media Type does not select" });
  if (category_id.value != null && media_type.value != null) {
    is_disabled.value = true;

    fileInput.value.click();
  } else {
    toastMessage("Select Category Or Media Type", "error");
  }
};

const handleOnChangeFile = async () => {
  let files = fileInput.value.files;

  let formData = new FormData();

  if (files) {
    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      const reader = new FileReader();

      // reader.onload = () => {
      //   imagePreviews.value.push({
      //     media_url: reader.result,
      //     progress: 0,
      //     uploaded: 0,
      //     selected: false,
      //   });

      //   // Simulate file upload
      //   simulateFileUpload(files[i], imagePreviews.value.length - 1);
      // };

      // reader.readAsDataURL(files[i]);

      formData.append("category_id", category_id.value);
      formData.append("media_type", media_type.value);
      formData.append("images[]", file);
    }
  }

  const {
    data: mediaData,
    error,
    pending: mediaPending,
    refresh,
  } = useApiFetch("/media", {
    method: "POST",
    body: formData,
  });

  if (mediaPending.value) {
    pending.value = mediaPending.value;
  }

  // if (refresh) {
  //   // refreshData.value = response.refresh();
  //   refresh();
  // }

  if (mediaData.value) {
    // form.value.clear();
    fileInput.value = null;
    imagePreviews.value = [];

    imagePreviews.value = mediaData.value;
    console.log("imagePreviews from database", mediaData.value);
  }

  if (error.value) {
    // form.value.clear();
    console.log("error", error.value.data?.errors);
    formError(error.value.data?.errors, form.value);
  }
};

// const simulateFileUpload = async (file, preview) => {
//   return new Promise((resolve) => {
//     let progress = 0;
//     const interval = setInterval(() => {
//       progress += 1;
//       preview.progress = progress;

//       if (progress === 100) {
//         clearInterval(interval);
//         preview.uploaded = 100;

//         // Perform the actual file upload here
//         const formData = new FormData();
//         formData.append("images[]", file);

//         const { data } = useAsyncData("media", () =>
//           client(config.public.sanctum.baseUrl + "/api/media", {
//             method: "POST",
//             body: formData,
//           })
//         );

//         // Set the URL of the uploaded image
//         preview.url = data.value[0].url;

//         console.log("Image uploaded:", data.value);
//         resolve();
//       }
//     }, 100);
//   });
// };

// const handleOnChangeFile = (event) => {
//   console.log("files", event.target.files);
//   selectedFiles.value = event.target.files;

//   const files = event.target.files;

//   if (files) {
//     for (let i = 0; i < files.length; i++) {
//       const reader = new FileReader();

//       reader.onload = () => {
//         imagePreviews.value.push({
//           url: reader.result,
//           progress: 0,
//           uploaded: 0,
//           selected: false,
//         });

//         // Simulate file upload
//         simulateFileUpload(files[i], imagePreviews.value.length - 1);
//       };

//       reader.readAsDataURL(files[i]);
//     }
//   }
// };

const simulateFileUpload = (file, index) => {
  // Simulate file upload progress (0% to 100%)
  console.log("selected", selectedFiles.value.length);

  return new Promise((resolve) => {
    let progress = 10;
    let totalFile = selectedFiles.value.length;
    const interval = setInterval(() => {
      totalFile -= 1;
      progress += 10;
      imagePreviews.value[index].progress = progress;

      if (progress === 100) {
        clearInterval(interval);

        imagePreviews.value[index].uploaded = 100;
      }
    }, 100);
    resolve();
  });
};

const toggleSelection = (index) => {
  if (activeTab.value === "search") {
    uploadedImages.value[index].selected =
      !uploadedImages.value[index].selected;
    isArrayRefresh.value;
  } else {
    imagePreviews.value[index].selected = !imagePreviews.value[index].selected;
    isArrayRefresh.value;
  }
};

const removeImage = async (index, item) => {
  if (activeTab.value === "search") {
    uploadedImages.value.splice(index, 1);
  } else {
    imagePreviews.value.splice(index, 1);
  }

  const { data: delData, error: delError } = await useApiFetch(
    "/media/" + item.id,
    {
      method: "DELETE",
    }
  );

  if (delData.value) {
    console.log("response.data.value.message", delData.value.message);
    toastMessage(delData.value.message, "warning");
  }
};

const selectedItems = computed(() =>
  activeTab.value === "search"
    ? uploadedImages.value.filter((item) => item.selected)
    : imagePreviews.value.filter((item) => item.selected)
);
console.log("selectedFiles", selectedItems);

const showTab = (tabName) => {
  activeTab.value = tabName;
  getMediaItems();
};

// const selected = ref(categories.value[0].name);

let pending_category = ref("");

onMounted(async () => {
  await getItems.items("/category", null).then((res) => {
    categories.value = res.data.value?.data;
    pending_category.value = res.pending.value;
  });
});

const getMediaItems = async () => {
  if (activeTab.value === "search") {
    let {
      data,
      error,
      pending: ped,
      refresh,
    } = await useApiFetch(
      "/media" +
        "?category_id=" +
        search_category.value +
        "&slug=" +
        slug.value +
        "&page=" +
        null
    );

    if (data.value) {
      uploadedImages.value = [];
      imagePreviews.value;
      uploadedImages.value = data.value.data;
    }

    isArrayRefresh.value = refresh();
  }
};

const handleRefresh = () => {
  refreshData.value;
  isArrayRefresh.value;
};

// function playVideo() {
//   var video = document.getElementById("myVideo");
//   video.play();
// }

const sendSelectedFile = () => {
  emit("sendSelectedFile", selectedItems.value);
  isOpen.value = false;
};
</script>

<template>
  <div>
    <UButton label="Upload Files" @click="(isOpen = true), getMediaItems()" />
    <UModal v-model="isOpen" fullscreen>
      <UCard
        :ui="{
          base: 'h-full flex flex-col',
          rounded: '',
          divide: 'divide-y divide-gray-100 dark:divide-gray-800',
          body: {
            base: 'grow',
          },
        }"
      >
        <template #header>
          <div class="flex items-center justify-between">
            <div
              class="text-base font-semibold leading-6 text-gray-900 dark:text-white"
            >
              <div class="flex items-center">
                <button
                  @click="showTab('search')"
                  :class="
                    activeTab === 'search'
                      ? 'bg-brand text-white'
                      : 'bg-gray-200'
                  "
                  class="py-1 px-4 text-gray-600 focus:outline-none focus:shadow-outline"
                >
                  <Icon name="gg:select-r" /> Select File
                </button>
                <button
                  @click="showTab('upload')"
                  :class="
                    activeTab === 'upload'
                      ? 'bg-brand text-white'
                      : 'bg-gray-200'
                  "
                  class="py-1 px-4 text-gray-600 focus:outline-none focus:shadow-outline"
                >
                  <Icon name="solar:upload-outline" /> Upload File
                </button>

                <!-- Search Tab Content -->

                <div
                  v-show="activeTab === 'search'"
                  class="flex items-center gap-5 mx-5"
                >
                  <UFormGroup name="search">
                    <UInput
                      icon="i-heroicons-magnifying-glass-20-solid"
                      size="sm"
                      color="white"
                      trailing
                      placeholder="Search..."
                    />
                  </UFormGroup>
                  <!-- <UFormGroup name="search_category">
                    <USelect
                      :loading="pending_category"
                      v-model="search_category"
                      searchable
                      searchable-placeholder="Search a person..."
                      :search-attributes="['name']"
                      placeholder="Select Category"
                      :options="categories"
                      value-attribute="id"
                      option-attribute="name"
                    />
                  </UFormGroup> -->
                  <select
                    class="border-2 border-gray-200 rounded-lg outline-none py-1 px-3 focus:border-2 focus:border-brand"
                    name="parent_id"
                    v-model="search_category"
                  >
                    <option selected>Select Category</option>
                    <option
                      v-for="(category, index) in categories"
                      :value="category.id"
                      class="text-gray-900"
                    >
                      {{ category.name }}
                    </option>
                  </select>
                </div>
                <div
                  v-show="activeTab === 'upload'"
                  class="flex items-center gap-4 mx-5"
                >
                  <!-- Upload Tab Content -->
                  <input
                    @change="handleOnChangeFile($event)"
                    type="file"
                    class="hidden"
                    ref="fileInput"
                    :multiple="props.isMultiple"
                  />
                  <!-- <UFormGroup name="category_id">
                    <USelect
                      v-model="category_id"
                      searchable
                      searchable-placeholder="Search a category..."
                      :search-attributes="['name']"
                      placeholder="Select Category"
                      :options="categories"
                      value-attribute="id"
                      option-attribute="name"
                      class="py-1"
                    />
                  </UFormGroup> -->
                  <select
                    class="border-2 border-gray-200 rounded-lg outline-none py-1 px-3 focus:border-2 focus:border-brand"
                    name="parent_id"
                    v-model="category_id"
                  >
                    <option selected>Select Category</option>
                    <option
                      v-for="(category, index) in categories"
                      :value="category.id"
                      class="text-gray-900"
                    >
                      {{ category.name }}
                    </option>
                  </select>
                  <UFormGroup name="media_type">
                    <USelect
                      v-model="media_type"
                      searchable
                      searchable-placeholder="Search a category..."
                      :search-attributes="['name']"
                      placeholder="Select Media Type"
                      :options="mediaTypes"
                      value-attribute="id"
                      option-attribute="name"
                      class="py-1"
                    />
                  </UFormGroup>

                  <UButton
                    @click="handleBrowsFile"
                    class="py-1.5 px-4 bg-brand text-white focus:outline-none focus:shadow-outline hover:bg-brand-700"
                  >
                    <Icon name="subway:file-11" /> Brows File
                  </UButton>
                  <UButton
                    @click="handleRefresh"
                    class="py-1.5 px-4 bg-brand text-white focus:outline-none focus:shadow-outline hover:bg-brand-700"
                  >
                    <Icon name="iconoir:refresh" />
                  </UButton>
                  <!-- Uploaded images preview goes here -->
                </div>
              </div>
            </div>
            <UButton
              color="gray"
              variant="ghost"
              icon="i-heroicons-x-mark-20-solid"
              class="-my-1"
              @click="isOpen = false"
            />
          </div>
        </template>
        <!-- MAIN CONTENT HERE  -->
        <div :loading="pending" class="overflow-y-auto h-[450px]">
          <div v-show="activeTab === 'search'" class="grid grid-cols-8 gap-2">
            <div
              v-for="(preview, index) in uploadedImages"
              :key="index"
              class="relative"
            >
              <NuxtImg
                v-if="preview.media_type == 1"
                :src="preview.media_url"
                alt="Preview"
                class="w-full h-[200px] object-cover"
              />
              <div class="max-w-md relative">
                <video
                  id="myVideo"
                  v-if="preview.media_type == 2"
                  class="w-full h-[200px] object-cover"
                  controls
                >
                  <!-- Replace 'your-video.mp4' with the path to your video file -->
                  <source :src="preview.media_url" type="video/mp4" />
                  Your browser does not support the video tag.
                </video>
              </div>
              <div
                v-if="preview.uploaded === 100"
                @click="toggleSelection(index)"
                class="absolute inset-0 flex items-center justify-center bg-gray-900 bg-opacity-75"
              >
                <!-- <span class="text-white">Uploaded</span> -->
              </div>
              <!-- <div
                v-else
                class="absolute inset-0 flex items-center justify-center bg-gray-500 bg-opacity-25"
              >
                <UProgress :value="preview.progress">
                  <template #indicator="{ percent }">
                    <div class="text-right" :style="{ width: `${percent}%` }">
                      <span class="text-primary font-bold"
                        >{{ preview.progress }}%</span
                      >
                    </div>
                  </template>
                </UProgress>
              </div> -->
              <button
                v-if="preview.uploaded === 100"
                @click="removeImage(index, preview)"
                class="absolute top-0 right-0 text-red-500"
              >
                <Icon name="typcn:delete" class="text-2xl" />
              </button>
              <input
                v-if="preview.selected"
                type="checkbox"
                class="absolute bottom-0 left-0 p-2"
                v-model="preview.selected"
              />
            </div>
          </div>
          <div
            v-if="imagePreviews.length === 0 && activeTab === 'upload'"
            class="flex items-center justify-center h-screen"
          >
            <div class="text-center">
              <h1 class="text-xl text-gray-500 font-semibold mb-4">
                Select Image or Video
              </h1>
              <UButton
                @click="handleBrowsFile"
                class="bg-brand text-white px-4 py-1.5 rounded hover:bg-brand-700"
              >
                Brows File
              </UButton>
            </div>
          </div>

          <div v-if="imagePreviews.length > 0" class="grid grid-cols-8 gap-2">
            <div
              v-for="(preview, index) in imagePreviews"
              :key="index"
              class="relative"
            >
              <div v-if="index < imagePreviews.length">
                <NuxtImg
                  v-if="preview.media_type == 1"
                  :src="preview.media_url"
                  alt="Preview"
                  class="w-full h-[200px] object-cover"
                />
                <div class="max-w-md relative">
                  <video
                    id="myVideo"
                    v-if="preview.media_type == 2"
                    class="w-full h-[200px] object-cover"
                    controls
                  >
                    <!-- Replace 'your-video.mp4' with the path to your video file -->
                    <source :src="preview.media_url" type="video/mp4" />
                    Your browser does not support the video tag.
                  </video>
                  <!-- <button
                    class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white p-2 rounded-full"
                    @click="playVideo"
                  >
                    Play
                  </button> -->
                </div>
                <div
                  v-if="preview.uploaded === 100"
                  @click="toggleSelection(index)"
                  class="absolute inset-0 flex items-center justify-center bg-gray-900 bg-opacity-75"
                >
                  <span class="text-white">Uploaded</span>
                </div>
                <div
                  v-else
                  class="absolute inset-0 flex items-center justify-center bg-gray-500 bg-opacity-25"
                >
                  <UProgress :value="preview.progress">
                    <template #indicator="{ percent }">
                      <div class="text-right" :style="{ width: `${percent}%` }">
                        <span class="text-primary font-bold"
                          >{{ preview.progress }}%</span
                        >
                      </div>
                    </template>
                  </UProgress>
                </div>
                <button
                  v-if="preview.uploaded === 100"
                  @click="removeImage(index, preview)"
                  class="absolute top-0 right-0 text-red-500"
                >
                  <Icon name="typcn:delete" class="text-2xl" />
                </button>
                <input
                  v-if="preview.selected"
                  type="checkbox"
                  class="absolute bottom-0 left-0 p-2"
                  v-model="preview.selected"
                />
              </div>
            </div>
          </div>
        </div>

        <template #footer>
          <div class="flex items-end justify-end">
            <button
              @click="sendSelectedFile"
              class="bg-brand text-white py-1.5 px-5 rounded-md hover:bg-brand-700"
            >
              Select
            </button>
          </div>
        </template>
      </UCard>
    </UModal>
  </div>
</template>

<style scoped></style>
