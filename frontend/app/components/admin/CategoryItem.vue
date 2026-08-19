<template>
  <li>
    <span
      @click="toggleDropdown"
      class="flex items-center group py-1.5 px-4 cursor-pointer text-md font-semibold"
    >
      <!-- <Icon
        class="shadow ring-gray-100 ring rounded-sm group-hover:bg-primary group-focus:bg-primary group-focus:text-gray-100 group-hover:text-gray-100 group-hover:ring-primary group-focus:ring-primary"
        :name="item.icon"
      /> -->
      <UCheckbox
        v-model="item.selected"
        name="category_id[]"
        :label="item.name"
        :value="item.id"
      />
      <!-- <span class="px-2 group-hover:text-gray-950">{{ item.name }}</span> -->
      <span class="ml-auto" v-if="item.children">
        <Icon
          class="group-hover:text-primary group-focus:text-primary"
          v-if="item.selected"
          name="ph:caret-down-bold"
        />
        <Icon
          class="group-hover:text-primary group-focus:text-primary"
          v-else
          name="ph:caret-right-bold"
        />
      </span>
    </span>
    <ul
      v-if="item.children && item.children.length"
      :class="{ block: item.selected }"
      class="ml-4"
    >
      <template
        v-if="item.selected"
        v-for="child in item.children"
        :key="child.id"
      >
        <CategoryItem :item="child" />
      </template>
    </ul>
  </li>
</template>

<script setup>
import { defineProps, ref, defineEmits } from "vue";
const props = defineProps(["item"]);
const emit = defineEmits(["toggleDropdown"]);
const openDropdown = ref(null);

const toggleDropdown = () => {
  props.item.selected = !props.item.selected;
  emit("toggleDropdown", props.item);
};
</script>

<style scoped>
/* Add your additional styles here */
</style>
