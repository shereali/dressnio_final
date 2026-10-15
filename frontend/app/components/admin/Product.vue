<script setup>
const store = useUtilityStore();
store.apiEndpoint = "/products";
let pending_color = ref("");
let pending_size = ref("");
let colors = ref([]);
let sizes = ref([]);
let galleries = ref([]);
let route = useRoute();
let stockStatus = [
  {
    value: 1,
    label: "In Stock",
  },
  {
    value: 2,
    label: "Out of Stock",
  },
];

store.formData.stock_status = 1;

let fabrics = [
  {
    value: 1,
    label: "Stitched",
  },
  {
    value: 2,
    label: "Unstiched",
  },
];

store.formData.fabric_type = 1;

let cloths = [
  {
    value: 1,
    label: "Salwar & Kameez",
  },
  {
    value: 2,
    label: "Kameez",
  },
  {
    value: 3,
    label: "Salwar",
  },
  {
    value: 4,
    label: "Average",
  },
];

store.formData.cloth_type = 1;

await getItems.items("/size", null).then((res) => {
  sizes = res.data.value?.data;
  pending_size = res.pending.value;
});

await getItems.items("/color", null).then((res) => {
  colors = res.data.value?.data;
  pending_color = res.pending.value;
});

async function search(q) {
  store.isLoading = true;
  try {
    const { data } = await useApiFetch(`/category`, {
      params: { q },
    });
    store.isLoading = false;
    return data.value?.data || [];
  } catch (err) {
    store.isLoading = false;
    return [];
  }
}

let kameez_sizes = computed(() => {
  return sizes.filter((sizeItem) => sizeItem.size_type === 1);
});

let salwar_sizes = computed(() => {
  return sizes.filter((sizeItem) => sizeItem.size_type === 2);
});

let receiveSelectedFile = (value) => {
  console.log("images", value);
  galleries = value;
};

let removeImage = (index) => {
  galleries.splice(index, 1);
};

let colorSetId = ref(null);

let getColor = (event) => {
  colorSetId.value = event.target.value;
};

// Create a reactive reference for store.formData.color
let watchedColor = ref(store.formData.color);

watch(colorSetId, (newValue) => {
  if (newValue !== null) {
    let singleColor = colors.find((item) => item.id == newValue);
    store.formData.color = singleColor;

    // Update watchedColor whenever store.formData.color changes
    watchedColor.value = store.formData.color;
  }
});

let itemAttrs = ref([
  {
    quantity: 0,
    kameez_size: 0,
    salwar_size: 0,
    price: 0,
    discount: 0,
  },
]);

let addItem = () => {
  itemAttrs.value.push({
    quantity: 0,
    kameez_size: 0,
    salwar_size: 0,
    price: 0,
    discount: 0,
  });
};
let removeItem = (index) => {
  itemAttrs.value.splice(index, 1);
};

let editedCategories = ref([]);

if (store.is_edit) {
  await getSingleItem
    .singleItem(store.apiEndpoint, route.params.id)
    .then((res) => {
      if (res.data.value) {
        editedCategories = [];
        editedCategories = res.data.value.data.categories;

        // if (res.data.value?.data.sizes.length > 0) {
        const filerkameezSizes = res.data.value?.data.sizes.filter(
          (item) => item.size_type == 1
        );

        const filerSalwarSizes = res.data.value?.data.sizes.filter(
          (item) => item.size_type == 2
        );

        const prices = res.data.value?.data.prices;
        const stocks = res.data.value?.data.stocks;

        if (filerkameezSizes.length > 0) {
          itemAttrs.value = [];

          filerkameezSizes.forEach((item, index) => {
            itemAttrs.value.push({
              quantity: stocks[index]?.quantity,
              kameez_size: filerkameezSizes[index]?.id,
              salwar_size: filerSalwarSizes[index]?.id,
              price: prices[index]?.price,
              discount: prices[index]?.discount,
            });
          });
        }

        if (filerkameezSizes.length == 0 && filerSalwarSizes.length > 0) {
          itemAttrs.value = [];

          filerSalwarSizes.forEach((item, index) => {
            itemAttrs.value.push({
              quantity: stocks[index]?.quantity,
              // kameez_size: item.id,
              salwar_size: filerSalwarSizes[index]?.id,
              price: prices[index]?.price,
              discount: prices[index]?.discount,
            });
          });
        }

        if (
          filerkameezSizes.length == 0 &&
          filerSalwarSizes.length == 0 &&
          res.data.value?.data.price != null
        ) {
          itemAttrs.value = [];
          // prices.forEach((item, index) => {
          itemAttrs.value.push({
            quantity: stocks[0]?.quantity,
            // kameez_size: item.id,
            // salwar_size: filerSalwarSizes[index]?.id,
            price: res.data.value?.data.price.price,
            discount: res.data.value?.data.price.discount,
          });
          // });

          console.log("prices", prices);
        }

        // } else {
        //   itemAttrs.value = [];
        // }

        watchedColor.value = res.data.value?.data.colorSet;
        res.data.value?.data.media.forEach((obj) => {
          if (obj.selected === false) {
            obj.selected = true;
          }
        });
        galleries.value = res.data.value?.data.media;

        store.formData = res.data.value?.data;
      }
    });
}
</script>
<template>
  <div class="flex justify-center mt-8">
    <!-- Left Part - General Information -->
    <div class="w-2/3 px-4">
      <h2 class="text-xl font-bold mb-4">General Information</h2>

      <UFormGroup label="Name" name="name">
        <UInput v-model="store.formData.name" placeholder="Enter name" />
      </UFormGroup>
      <client-only>
        <Tiptap v-model="store.formData.description" />
      </client-only>
      <textarea
        name="description"
        v-model="store.formData.description"
        hidden
      ></textarea>

      <div class="flex space-x-4 mb-2">
        <div class="grid grid-cols-4 gap-2">
          <UCheckbox
            v-model="store.formData.is_featured"
            class="py-1"
            name="is_featured"
            label="Featured?"
          />
          <UCheckbox
            v-model="store.formData.new_arrival"
            class="py-1"
            name="new_arrival"
            label="New Arrival?"
          />
          <UCheckbox
            v-model="store.formData.best_seller"
            class="py-1"
            name="best_seller"
            label="Best Seller?"
          />

          <UCheckbox
            v-model="store.formData.highlight"
            class="py-1"
            name="highlight"
            label="Highlight?"
          />
          <UCheckbox
            v-model="store.formData.fashionist_choice"
            class="py-1"
            name="fashionist_choice"
            label="Fashionist Choice?"
          />
        </div>
      </div>
      <URadioGroup
        v-model="store.formData.stock_status"
        name="stock_status"
        legend="Choose Stock"
        :options="stockStatus"
      />
      <label class="block font-medium text-gray-700 dark:text-gray-200 mt-3">
        Select Sizes For Salwar & Kameez
      </label>
      <div class="flex space-x-4">
        <div class="grid grid-cols-3 gap-5 pt-2 pb-4">
          <!-- <UCheckbox
            v-model="store.formData.is_active"
            class="py-3"
            name="is_active"
            label="Is Active?"
          />
          <UToggle
            name="stitched"
            label="Stitched"
            v-model="store.formData.unstitched"
          /> -->
          <URadioGroup
            v-model="store.formData.fabric_type"
            name="fabric_type"
            legend="Choose Fabric"
            :options="fabrics"
          />
          <URadioGroup
            v-model="store.formData.cloth_type"
            name="cloth_type"
            legend="Choose Cloth"
            :options="cloths"
          />

          <div class="flex gap-x-3">
            <UToggle
              name="is_size_visible"
              on-icon="i-heroicons-check-20-solid"
              off-icon="i-heroicons-x-mark-20-solid"
              v-model="store.formData.is_size_visible"
            />
            <label
              class="font-medium dark:text-gray-200"
              :class="
                store.formData.is_size_visible
                  ? 'text-brand'
                  : 'text-rose-500'
              "
            >
              {{
                store.formData.is_size_visible
                  ? "Yes, Size Visible"
                  : "Is Size Visible?"
              }}
            </label>
          </div>
        </div>
      </div>

      <UDivider />
      <div class="flex space-x-2 mb-5 mt-2" v-for="(item, index) in itemAttrs">
        <UFormGroup
          v-if="
            store.formData.cloth_type == 1 || store.formData.cloth_type == 2
          "
          label="Kameez/Shirt"
          name="kameez_size_id[]"
        >
          <USelect
            v-model="item.kameez_size"
            :loading="pending_size"
            searchable
            searchable-placeholder="Search a kameez size..."
            :search-attributes="['standard']"
            placeholder="Select kameez Size"
            :options="kameez_sizes"
            value-attribute="id"
            option-attribute="standard"
          />
        </UFormGroup>
        <UFormGroup
          v-if="
            store.formData.cloth_type == 1 || store.formData.cloth_type == 3
          "
          label="Salwar/Pant"
          name="salwar_size_id[]"
        >
          <USelect
            v-model="item.salwar_size"
            :loading="pending_size"
            searchable
            searchable-placeholder="Search a pant size..."
            :search-attributes="['standard']"
            placeholder="Select Pant Size"
            :options="salwar_sizes"
            value-attribute="id"
            option-attribute="standard"
          />
        </UFormGroup>
        <UFormGroup label="Price" name="price[]">
          <UInput v-model="item.price" placeholder="Enter price" type="text" />
        </UFormGroup>
        <UFormGroup label="Discount" name="discount[]">
          <UInput
            v-model="item.discount"
            placeholder="Enter discount"
            type="text"
          />
        </UFormGroup>

        <UFormGroup label="Quantity" name="quantity[]">
          <UInput
            v-model="item.quantity"
            placeholder="Enter quantity"
            type="number"
          />
        </UFormGroup>
        <div v-if="itemAttrs.length != 1">
          <UButton
            type="button"
            class="mt-7"
            color="red"
            @click="removeItem(index)"
          >
            <Icon name="mdi:minus" />
          </UButton>
        </div>

        <div v-if="index == itemAttrs.length - 1 ? true : false">
          <UButton
            v-if="store.formData.cloth_type != 4"
            type="button"
            class="mt-7"
            @click="addItem()"
          >
            <Icon name="mdi:plus" />
          </UButton>
        </div>
      </div>
      <div class="flex space-x-4">
        <div class="grid grid-cols-3 gap-3">
          <div class="flex gap-x-3">
            <UToggle
              name="has_color_variation"
              on-icon="i-heroicons-check-20-solid"
              off-icon="i-heroicons-x-mark-20-solid"
              v-model="store.formData.has_color_variation"
            />
            <label
              class="font-medium dark:text-gray-200"
              :class="
                store.formData.has_color_variation
                  ? 'text-brand'
                  : 'text-rose-500'
              "
            >
              {{
                store.formData.has_color_variation
                  ? "Yes, Color has variation"
                  : "Has Color Variation?"
              }}
            </label>
          </div>

          <div class="flex gap-x-3">
            <UToggle
              name="is_color_visible"
              on-icon="i-heroicons-check-20-solid"
              off-icon="i-heroicons-x-mark-20-solid"
              v-model="store.formData.is_color_visible"
            />
            <label
              class="font-medium dark:text-gray-200"
              :class="
                store.formData.is_color_visible
                  ? 'text-brand'
                  : 'text-rose-500'
              "
            >
              {{
                store.formData.is_color_visible
                  ? "Yes, Color Visible"
                  : "Is Color Visible?"
              }}
            </label>
          </div>
        </div>
      </div>

      <UFormGroup
        v-if="store.formData.has_color_variation"
        label="Color"
        name="color_set_id"
        class="mb-5"
      >
        <USelect
          v-model="store.formData.color_set_id"
          :loading="pending_color"
          searchable
          searchable-placeholder="Search a color..."
          :search-attributes="['name']"
          placeholder="Select Color"
          :options="colors"
          value-attribute="id"
          option-attribute="name"
          @input="getColor"
        />
      </UFormGroup>

      <div class="flex" v-if="store.formData.has_color_variation">
        <div
          class="grid grid-cols-1"
          v-for="(colorItem, index) in watchedColor?.colors"
        >
          <div
            class="w-16 h-16 m-2 relative"
            :style="{ background: colorItem.color_code }"
          >
            <input
              v-model="colorItem.selected"
              :value="colorItem.id"
              name="color_id[]"
              type="checkbox"
              class="absolute bottom-0 left-0 p-2"
            />
          </div>
        </div>
      </div>
      <div class="upload-gallery-image py-3">
        <ImageUploader
          @send-selected-file="receiveSelectedFile"
          :is-multiple="true"
        />
      </div>
      <div class="py-3">
        <div class="grid grid-cols-4 gap-2">
          <div class="relative" v-for="(gallery, index) in galleries">
            <NuxtImg :src="gallery.media_url" />
            <Icon
              class="absolute right-0 top-0 z-20 text-red-700 cursor-pointer hover:text-red-500"
              name="fluent-emoji-flat:cross-mark"
              @click="removeImage(index)"
            />
            <UCheckbox
              v-model="gallery.selected"
              :value="gallery.id"
              name="media_id[]"
              class="absolute right-0 bottom-0 z-20"
              label=""
            />
          </div>
        </div>
      </div>
      <div class="flex gap-x-3">
        <UToggle
          name="is_active"
          on-icon="i-heroicons-check-20-solid"
          off-icon="i-heroicons-x-mark-20-solid"
          v-model="store.formData.is_active"
        />
        <label
          class="font-medium dark:text-gray-200"
          :class="store.formData.is_active ? 'text-brand' : 'text-rose-500'"
        >
          {{ store.formData.is_active ? "Active" : "Inactive" }}
        </label>
      </div>
    </div>

    <!-- Right Side - Additional Information -->
    <div class="w-1/3 px-4 bg-gray-50">
      <h2 class="text-xl font-bold mb-2">Additional Information</h2>
      <h3
        class="font-semibold mb-4 px-3 py-2 shadow-sm text-lg text-white bg-brand rounded-md"
      >
        Category
      </h3>

      <CategoryMain :category-value="editedCategories" />
    </div>
  </div>
</template>
