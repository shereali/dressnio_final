<template>
  <div>
    <aside>
      <!-- Set max height for scrolling -->
      <nav class="overflow-y-auto" style="max-height: calc(100vh - 400px)">
        <ul>
          <template v-for="(item, index) in categories" :key="index">
            <CategoryItem :item="item" @toggleDropdown="getSelectedItem" />
          </template>
        </ul>
      </nav>
    </aside>
  </div>
</template>

<script setup>
const props = defineProps({
  categoryValue: {
    type: String,
    default: "",
  },
});
const categories = ref([]);
const pending = ref("");

const selectedCategories = ref([]);

watch(
  () => props.categoryValue,
  (value) => {
    selectedCategories.value = value;
  }
);

const getSelectedItem = (value) => {
  selectedCategories.value.push(value);
};

const selectedItems = computed(() => {
  return selectedCategories.value.filter((item) => item.selected == true);
});

onMounted(async () => {
  const res = await getItems.items("/category");
  if (res.data.value) {
    categories.value = res.data.value?.data;
  }
  pending.value = res.pending.value;
  categories.value.forEach((obj) => {
    props.categoryValue.forEach((editedObj) => {
      if (obj.id == editedObj.id) {
        obj.selected = true;
      }
    });
  });
  console.log("categoryValue", categories.value);
});
</script>

<style scoped>
/* Add rounded corners to the top and bottom */
aside {
  /* border-radius: 8px 8px 0 0; */
}
/* Add styling for the scroll bar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f0f0f0;
}

::-webkit-scrollbar-thumb {
  background: #e4e3e3;
}
</style>
