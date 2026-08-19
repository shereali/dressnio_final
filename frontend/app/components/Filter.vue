<template>
  <div
    class="border-b-2 border-t-2 border-[#BC96CA] my-5 px-10 flex justify-between flex-col lg:flex-row"
  >
    <div class="item-view flex flex-col lg:flex-row gap-4 lg:text-xl py-3">
      <div class="flex gap-4">
        <Icon
          name="carbon:thumbnail-2"
          class="text-3xl cursor-pointer"
          :class="{ 'text-[#2A2AD1]': thumbnail_view === true }"
          @click="productView('thumbnail')"
        />
        <Icon
          name="cil:list"
          class="text-3xl cursor-pointer"
          :class="{ 'text-[#2A2AD1]': list_view === true }"
          @click="productView('list')"
        />
        <p>Showing 1–20 of 87 results</p>
      </div>
      <select class="focus:outline-none px-5 bg-[#BC96CA]">
        <option value="">All Category</option>
        <option value="">Best Sellers(5)</option>
        <option value="">Celebs(0)</option>
        <option value="">Designer's Pick(28)</option>
        <option value="">Dresses(66)</option>
        <option value="">Dupattas(4)</option>
        <option value="">Kurta Sets(10)</option>
        <option value="">Lehengas(21)</option>
        <option value="">Maxis(30)</option>
        <option value="">News(101)</option>
        <option value="">Raw Fabrics(1)</option>
        <option value="">Shrug Sets(1)</option>
        <option value="">Shoes(1)</option>
        <option value="">Suit Sets(1)</option>
        <option value="">Tops & Co-ord's(1)</option>
      </select>
    </div>
    <div class="sorting py-3 lg:text-xl">
      <select class="focus:outline-none w-full px-5 bg-[#BC96CA]">
        <option value="">Select Sorting</option>
        <option value="">Default Sorting</option>
        <option value="">Sort by popularity</option>
        <option value="">Sort by average rating</option>
        <option value="">Sort by latest</option>
        <option value="">Sort by price low to high</option>
        <option value="">Sort by price high to low</option>
      </select>
    </div>
  </div>
</template>

<script setup>
let thumbnail_view = ref(true);
let list_view = ref(false);
let productView = (listPreview) => {
  if (listPreview == "thumbnail") {
    thumbnail_view.value = true;
    list_view.value = false;
  }
  if (listPreview == "list") {
    list_view.value = true;
    thumbnail_view.value = false;
  }
};
</script>
