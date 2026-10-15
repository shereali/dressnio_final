<template>
  <div class="space-y-6 max-w-6xl mx-auto pb-12">
    <!-- Header Banner / Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm">
      <div>
        <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
          {{ isEdit ? 'Edit Product' : 'Create New Product' }}
        </h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
          Configure product details, sizes, pricing, color variations, and imagery.
        </p>
      </div>

      <div class="flex items-center gap-3">
        <NuxtLink
          to="/admin/products"
          class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 text-xs font-bold transition-all flex items-center gap-2"
        >
          <Icon name="solar:arrow-left-bold" class="w-4 h-4" />
          <span>Back to List</span>
        </NuxtLink>

        <button
          type="button"
          @click="submitForm(false)"
          :disabled="isSubmitting"
          class="px-5 py-2.5 rounded-xl bg-brand-500 hover:bg-brand-600 !text-white text-xs font-bold shadow-md shadow-brand-500/20 transition-all flex items-center gap-2 disabled:opacity-50"
        >
          <Icon v-if="isSubmitting" name="svg-spinners:90-ring-with-bg" class="w-4 h-4 text-white" />
          <Icon v-else :name="isEdit ? 'solar:diskette-bold' : 'solar:add-circle-bold'" class="w-4 h-4" />
          <span>{{ isEdit ? 'Save Changes' : 'Publish Product' }}</span>
        </button>
      </div>
    </div>

    <!-- Main Grid Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left 2-Columns: Main Product Details -->
      <div class="lg:col-span-2 space-y-6">
        <!-- 1. General Info Card -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
          <div class="flex items-center gap-2.5 pb-2 border-b border-gray-100 dark:border-gray-800">
            <Icon name="solar:document-text-bold-duotone" class="w-5 h-5 text-brand" />
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">General Information</h2>
          </div>

          <!-- Product Name -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
              Product Title <span class="text-rose-500">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              placeholder="e.g. Midnight Blue Silk Embroidery Kurti Set"
              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent outline-none transition-all"
              :class="{ 'border-rose-500 ring-1 ring-rose-500': errors.name }"
            />
            <p v-if="errors.name" class="text-xs text-rose-500 mt-1 font-semibold">{{ errors.name[0] || errors.name }}</p>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
              Description & Specifications
            </label>
            <textarea
              v-model="form.description"
              rows="4"
              placeholder="Describe garment fabric, embroidery, occasion styling, care instructions..."
              class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 focus:border-transparent outline-none transition-all resize-y"
            ></textarea>
          </div>

          <!-- Badges & Promotional Flags -->
          <div class="pt-2">
            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
              Storefront Badges & Placement
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
              <label class="flex items-center gap-2.5 p-3 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors" :class="{ 'border-brand-500 bg-brand-50/30 dark:bg-brand-950/20': form.is_featured }">
                <input v-model="form.is_featured" type="checkbox" class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand-500" />
                <span class="text-xs font-bold text-gray-800 dark:text-gray-200">Featured</span>
              </label>

              <label class="flex items-center gap-2.5 p-3 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors" :class="{ 'border-brand-500 bg-brand-50/30 dark:bg-brand-950/20': form.new_arrival }">
                <input v-model="form.new_arrival" type="checkbox" class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand-500" />
                <span class="text-xs font-bold text-gray-800 dark:text-gray-200">New Arrival</span>
              </label>

              <label class="flex items-center gap-2.5 p-3 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors" :class="{ 'border-brand-500 bg-brand-50/30 dark:bg-brand-950/20': form.best_seller }">
                <input v-model="form.best_seller" type="checkbox" class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand-500" />
                <span class="text-xs font-bold text-gray-800 dark:text-gray-200">Best Seller</span>
              </label>

              <label class="flex items-center gap-2.5 p-3 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors" :class="{ 'border-brand-500 bg-brand-50/30 dark:bg-brand-950/20': form.highlight }">
                <input v-model="form.highlight" type="checkbox" class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand-500" />
                <span class="text-xs font-bold text-gray-800 dark:text-gray-200">Spotlight Hero</span>
              </label>

              <label class="flex items-center gap-2.5 p-3 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors" :class="{ 'border-brand-500 bg-brand-50/30 dark:bg-brand-950/20': form.fashionist_choice }">
                <input v-model="form.fashionist_choice" type="checkbox" class="w-4 h-4 text-brand rounded border-gray-300 focus:ring-brand-500" />
                <span class="text-xs font-bold text-gray-800 dark:text-gray-200">Fashionist Pick</span>
              </label>
            </div>
          </div>
        </div>

        <!-- 2. Garment Classification Card -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
          <div class="flex items-center gap-2.5 pb-2 border-b border-gray-100 dark:border-gray-800">
            <Icon name="solar:t-shirt-bold-duotone" class="w-5 h-5 text-brand" />
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">Garment & Fabric Specifications</h2>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Cloth Type -->
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Garment Category</label>
              <select
                v-model.number="form.cloth_type"
                class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs font-semibold text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 outline-none"
              >
                <option :value="1">Salwar & Kameez Set</option>
                <option :value="2">Single Kameez / Kurti</option>
                <option :value="3">Salwar / Pant</option>
                <option :value="4">Saree / Lehenga / Fabric</option>
              </select>
            </div>

            <!-- Fabric Type -->
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Fabric Stitched Type</label>
              <select
                v-model.number="form.fabric_type"
                class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs font-semibold text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 outline-none"
              >
                <option :value="1">Stitched (Ready to Wear)</option>
                <option :value="2">Unstitched (Fabric Material)</option>
              </select>
            </div>

            <!-- Stock Status -->
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Inventory Status</label>
              <select
                v-model.number="form.stock_status"
                class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs font-semibold text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 outline-none"
              >
                <option :value="1">In Stock</option>
                <option :value="2">Out of Stock</option>
              </select>
            </div>
          </div>
        </div>

        <!-- 3. Size, Pricing & Inventory Matrix -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
          <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2.5">
              <Icon name="solar:ruler-angular-bold-duotone" class="w-5 h-5 text-brand" />
              <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">Sizes, Pricing & Stock Matrix</h2>
            </div>
            <div class="text-xs font-bold text-gray-500 dark:text-gray-400">
              Total Stock: <span class="text-brand font-black">{{ totalStock }} pcs</span>
            </div>
          </div>

          <!-- Variants Table / Rows -->
          <div class="space-y-3">
            <div
              v-for="(row, rIdx) in form.variants"
              :key="rIdx"
              class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/40 border border-gray-200/70 dark:border-gray-700/50 flex flex-col md:flex-row items-stretch md:items-center gap-3 transition-all"
            >
              <!-- Kameez Size (if applicable) -->
              <div v-if="form.cloth_type == 1 || form.cloth_type == 2" class="flex-1">
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-400 mb-1">Kameez/Top Size</label>
                <select
                  v-model="row.kameez_size_id"
                  class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-semibold text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 outline-none"
                >
                  <option :value="null">Select Size</option>
                  <option v-for="s in kameezSizes" :key="s.id" :value="s.id">{{ s.standard }} (Bust {{ s.bust }}")</option>
                </select>
              </div>

              <!-- Salwar Size (if applicable) -->
              <div v-if="form.cloth_type == 1 || form.cloth_type == 3" class="flex-1">
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-400 mb-1">Bottom/Pant Size</label>
                <select
                  v-model="row.salwar_size_id"
                  class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-semibold text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 outline-none"
                >
                  <option :value="null">Select Size</option>
                  <option v-for="s in salwarSizes" :key="s.id" :value="s.id">{{ s.standard }} (Length {{ s.lengths }}")</option>
                </select>
              </div>

              <!-- Price -->
              <div class="w-28">
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-400 mb-1">Price (৳)</label>
                <input
                  v-model.number="row.price"
                  type="number"
                  placeholder="0"
                  class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 outline-none"
                />
              </div>

              <!-- Discount -->
              <div class="w-24">
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-400 mb-1">Discount (৳)</label>
                <input
                  v-model.number="row.discount"
                  type="number"
                  placeholder="0"
                  class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-semibold text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 outline-none"
                />
              </div>

              <!-- Quantity -->
              <div class="w-24">
                <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-400 mb-1">Qty (Stock)</label>
                <input
                  v-model.number="row.quantity"
                  type="number"
                  placeholder="0"
                  class="w-full px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-emerald-600 dark:text-emerald-400 focus:ring-2 focus:ring-brand-500 outline-none"
                />
              </div>

              <!-- Row Action -->
              <div class="flex items-end pt-5">
                <button
                  type="button"
                  @click="removeVariantRow(rIdx)"
                  :disabled="form.variants.length <= 1"
                  class="p-2 rounded-lg text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 disabled:opacity-30 disabled:pointer-events-none transition-colors"
                  title="Remove variant row"
                >
                  <Icon name="solar:trash-bin-trash-bold" class="w-4.5 h-4.5" />
                </button>
              </div>
            </div>
          </div>

          <button
            type="button"
            @click="addVariantRow"
            class="px-4 py-2 rounded-xl border border-dashed border-gray-300 dark:border-gray-700 hover:border-brand text-gray-600 dark:text-gray-300 hover:text-brand text-xs font-bold transition-all w-full flex items-center justify-center gap-2"
          >
            <Icon name="solar:add-circle-bold" class="w-4 h-4" />
            <span>Add Another Size Variant</span>
          </button>
        </div>

        <!-- 4. Color Variation Card -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
          <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2.5">
              <Icon name="solar:pallete-2-bold-duotone" class="w-5 h-5 text-brand" />
              <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">Color Variations</h2>
            </div>
            <label class="flex items-center gap-2 text-xs font-bold text-gray-700 dark:text-gray-300 cursor-pointer">
              <input v-model="form.has_color_variation" type="checkbox" class="w-4 h-4 text-brand rounded" />
              <span>Enable Color Swatches</span>
            </label>
          </div>

          <div v-if="form.has_color_variation" class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Select Color Palette Set</label>
              <select
                v-model="form.color_set_id"
                @change="onColorSetChange"
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 text-xs font-semibold text-gray-900 dark:text-white focus:ring-2 focus:ring-brand-500 outline-none"
              >
                <option :value="null">-- Select a color set --</option>
                <option v-for="cs in colorSets" :key="cs.id" :value="cs.id">{{ cs.name }} ({{ cs.colors?.length || 0 }} shades)</option>
              </select>
            </div>

            <!-- Swatches Checkboxes -->
            <div v-if="activeColorSet?.colors?.length" class="space-y-2">
              <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider">Available Shades (Check all that apply)</label>
              <div class="flex flex-wrap gap-3">
                <label
                  v-for="c in activeColorSet.colors"
                  :key="c.id"
                  class="flex items-center gap-2.5 px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 cursor-pointer hover:border-brand transition-all"
                  :class="{ 'border-brand-500 bg-brand-50/30 dark:bg-brand-950/20 ring-1 ring-brand-500': form.color_id.includes(c.id) }"
                >
                  <input
                    type="checkbox"
                    :value="c.id"
                    v-model="form.color_id"
                    class="w-4 h-4 text-brand rounded border-gray-300"
                  />
                  <div
                    class="w-5 h-5 rounded-full border border-black/10 shadow-xs flex-shrink-0"
                    :style="{ backgroundColor: c.color_code }"
                  ></div>
                  <span class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ c.color_name || c.color_code }}</span>
                </label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right 1-Column: Categories & Media Library -->
      <div class="space-y-6">
        <!-- Status & Visibility Card -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
          <div class="flex items-center gap-2.5 pb-2 border-b border-gray-100 dark:border-gray-800">
            <Icon name="solar:eye-bold-duotone" class="w-5 h-5 text-brand" />
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">Publishing Status</h2>
          </div>

          <label class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 cursor-pointer">
            <div>
              <p class="text-xs font-bold text-gray-900 dark:text-white">Product Active</p>
              <p class="text-[10px] text-gray-500">Visible on public storefront</p>
            </div>
            <input v-model="form.is_active" type="checkbox" class="w-5 h-5 text-brand rounded" />
          </label>

          <label class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 cursor-pointer">
            <div>
              <p class="text-xs font-bold text-gray-900 dark:text-white">Show Size Guide</p>
              <p class="text-[10px] text-gray-500">Display size dropdowns</p>
            </div>
            <input v-model="form.is_size_visible" type="checkbox" class="w-5 h-5 text-brand rounded" />
          </label>

          <label class="flex items-center justify-between p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 cursor-pointer">
            <div>
              <p class="text-xs font-bold text-gray-900 dark:text-white">Show Color Swatches</p>
              <p class="text-[10px] text-gray-500">Display swatch options</p>
            </div>
            <input v-model="form.is_color_visible" type="checkbox" class="w-5 h-5 text-brand rounded" />
          </label>
        </div>

        <!-- Category Picker Card -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
          <div class="flex items-center gap-2.5 pb-2 border-b border-gray-100 dark:border-gray-800">
            <Icon name="solar:folder-with-files-bold-duotone" class="w-5 h-5 text-brand" />
            <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">Categories</h2>
          </div>

          <div class="max-h-60 overflow-y-auto space-y-1.5 custom-scrollbar pr-1">
            <label
              v-for="cat in categories"
              :key="cat.id"
              class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors border border-transparent"
              :class="{ '!border-brand-500 bg-brand-50/30 dark:bg-brand-950/20': form.category_id.includes(cat.id) }"
            >
              <input
                type="checkbox"
                :value="cat.id"
                v-model="form.category_id"
                class="w-4 h-4 text-brand rounded border-gray-300"
              />
              <span class="text-xs font-bold text-gray-800 dark:text-gray-200">{{ cat.name }}</span>
            </label>
          </div>
        </div>

        <!-- Media & Imagery Card -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
          <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2.5">
              <Icon name="solar:gallery-wide-bold-duotone" class="w-5 h-5 text-brand" />
              <h2 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-white">Media Gallery</h2>
            </div>
            <button
              type="button"
              @click="showMediaModal = true"
              class="text-xs font-bold text-brand hover:underline flex items-center gap-1"
            >
              <Icon name="solar:add-circle-bold" class="w-4 h-4" />
              <span>Browse</span>
            </button>
          </div>

          <!-- Selected Gallery Grid -->
          <div v-if="selectedMediaList.length" class="grid grid-cols-3 gap-2">
            <div
              v-for="(med, mIdx) in selectedMediaList"
              :key="med.id"
              class="relative aspect-square rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 group bg-gray-100"
            >
              <img :src="med.media_url || med.image" class="w-full h-full object-cover" />
              <button
                type="button"
                @click="removeMedia(med.id)"
                class="absolute top-1 right-1 p-1 rounded-full bg-rose-600 text-white opacity-0 group-hover:opacity-100 transition-opacity shadow-md"
                title="Remove image"
              >
                <Icon name="solar:close-circle-bold" class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>

          <div v-else class="text-center py-8 border-2 border-dashed border-gray-200 dark:border-gray-800 rounded-xl">
            <Icon name="solar:camera-bold-duotone" class="w-8 h-8 text-gray-400 mx-auto mb-2" />
            <p class="text-xs text-gray-500 font-semibold">No media attached yet</p>
            <button
              type="button"
              @click="showMediaModal = true"
              class="mt-2 text-xs font-bold text-brand hover:underline"
            >
              Attach images from Media Library
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Media Selection Modal -->
    <div
      v-if="showMediaModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
    >
      <div class="bg-white dark:bg-gray-900 w-full max-w-3xl rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 flex flex-col max-h-[85vh] overflow-hidden">
        <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
          <div class="flex items-center gap-2">
            <Icon name="solar:gallery-bold-duotone" class="w-5 h-5 text-brand" />
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Select Product Media</h3>
          </div>
          <button @click="showMediaModal = false" class="p-1 rounded-lg text-gray-400 hover:text-gray-600">
            <Icon name="solar:close-circle-bold" class="w-5 h-5" />
          </button>
        </div>

        <div class="p-5 overflow-y-auto flex-1 custom-scrollbar">
          <div v-if="allMedia.length" class="grid grid-cols-4 sm:grid-cols-5 gap-3">
            <div
              v-for="item in allMedia"
              :key="item.id"
              @click="toggleMediaSelection(item)"
              class="relative aspect-square rounded-xl overflow-hidden border-2 cursor-pointer transition-all"
              :class="form.media_id.includes(item.id) ? 'border-brand-500 ring-2 ring-brand-500/30' : 'border-transparent hover:border-gray-300'"
            >
              <img :src="item.media_url || item.image" class="w-full h-full object-cover" />
              <div
                v-if="form.media_id.includes(item.id)"
                class="absolute top-1.5 right-1.5 w-5 h-5 rounded-full bg-brand-500 text-white flex items-center justify-center shadow-md"
              >
                <Icon name="solar:check-circle-bold" class="w-3.5 h-3.5" />
              </div>
            </div>
          </div>
          <div v-else class="text-center py-12 text-gray-500 text-sm font-semibold">
            No images in library. Upload media in Media section first.
          </div>
        </div>

        <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/40 flex justify-end gap-3">
          <button
            type="button"
            @click="showMediaModal = false"
            class="px-5 py-2 rounded-xl bg-brand-500 text-white text-xs font-bold shadow-md shadow-brand-500/20"
          >
            Done ({{ form.media_id.length }} Selected)
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';

const route = useRoute();
const isEdit = computed(() => !!route.params.id);
const isSubmitting = ref(false);
const errors = ref({});
const snackbar = useSnackbar();

// Lookups
const categories = ref([]);
const sizes = ref([]);
const colorSets = ref([]);
const allMedia = ref([]);
const showMediaModal = ref(false);

const form = reactive({
  name: '',
  description: '',
  cloth_type: 1,
  fabric_type: 1,
  stock_status: 1,
  is_active: true,
  is_featured: false,
  new_arrival: true,
  best_seller: false,
  highlight: false,
  fashionist_choice: false,
  has_color_variation: true,
  color_set_id: null,
  is_size_visible: true,
  is_color_visible: true,
  category_id: [],
  color_id: [],
  media_id: [],
  variants: [
    {
      kameez_size_id: null,
      salwar_size_id: null,
      price: 2500,
      discount: 0,
      quantity: 20,
    },
  ],
});

// Computed lookups
const kameezSizes = computed(() => sizes.value.filter(s => s.size_type === 1));
const salwarSizes = computed(() => sizes.value.filter(s => s.size_type === 2));

const activeColorSet = computed(() => {
  if (!form.color_set_id) return null;
  return colorSets.value.find(cs => cs.id === form.color_set_id);
});

const selectedMediaList = computed(() => {
  return allMedia.value.filter(m => form.media_id.includes(m.id));
});

const totalStock = computed(() => {
  return form.variants.reduce((acc, row) => acc + (Number(row.quantity) || 0), 0);
});

function addVariantRow() {
  form.variants.push({
    kameez_size_id: null,
    salwar_size_id: null,
    price: form.variants[0]?.price || 0,
    discount: form.variants[0]?.discount || 0,
    quantity: 10,
  });
}

function removeVariantRow(idx) {
  if (form.variants.length > 1) {
    form.variants.splice(idx, 1);
  }
}

function onColorSetChange() {
  if (activeColorSet.value?.colors?.length) {
    form.color_id = activeColorSet.value.colors.map(c => c.id);
  } else {
    form.color_id = [];
  }
}

function toggleMediaSelection(item) {
  const index = form.media_id.indexOf(item.id);
  if (index > -1) {
    form.media_id.splice(index, 1);
  } else {
    form.media_id.push(item.id);
  }
}

function removeMedia(id) {
  const index = form.media_id.indexOf(id);
  if (index > -1) {
    form.media_id.splice(index, 1);
  }
}

async function loadData() {
  try {
    const [catRes, sizeRes, colRes, medRes] = await Promise.all([
      useApiFetch('/category'),
      useApiFetch('/size'),
      useApiFetch('/color'),
      useApiFetch('/media'),
    ]);

    categories.value = catRes.data.value?.data || [];
    sizes.value = sizeRes.data.value?.data || [];
    colorSets.value = colRes.data.value?.data || [];
    allMedia.value = medRes.data.value?.data || [];

    // Set default color set
    if (!isEdit.value && colorSets.value.length && !form.color_set_id) {
      form.color_set_id = colorSets.value[0].id;
      onColorSetChange();
    }

    // Set default sizes for first row
    if (!isEdit.value && kameezSizes.value.length) {
      form.variants[0].kameez_size_id = kameezSizes.value[0].id;
    }
    if (!isEdit.value && salwarSizes.value.length) {
      form.variants[0].salwar_size_id = salwarSizes.value[0].id;
    }

    // If Edit mode, load product details
    if (isEdit.value) {
      const { data: prodData } = await useApiFetch(`/products/${route.params.id}`);
      if (prodData.value?.data) {
        const p = prodData.value.data;
        form.name = p.name;
        form.description = p.description || '';
        form.cloth_type = Number(p.cloth_type) || 1;
        form.fabric_type = Number(p.fabric_type) || 1;
        form.stock_status = Number(p.stock_status) || 1;
        form.is_active = !!p.is_active;
        form.is_featured = !!p.is_featured;
        form.new_arrival = !!p.new_arrival;
        form.best_seller = !!p.best_seller;
        form.highlight = !!p.highlight;
        form.fashionist_choice = !!p.fashionist_choice;
        form.has_color_variation = !!p.has_color_variation;
        form.color_set_id = p.color_set_id || (colorSets.value[0]?.id || null);
        form.is_size_visible = !!p.is_size_visible;
        form.is_color_visible = !!p.is_color_visible;

        form.category_id = p.categories?.map(c => c.id) || [];
        form.color_id = p.colors?.map(c => c.id) || [];
        form.media_id = p.media?.map(m => m.id) || [];

        // Build variants
        if (p.prices?.length) {
          form.variants = p.prices.map((pr, idx) => ({
            kameez_size_id: pr.size_id || null,
            salwar_size_id: p.sizes?.filter(s => s.size_type === 2)[idx]?.id || null,
            price: pr.price || 0,
            discount: pr.discount || 0,
            quantity: p.stocks?.[idx]?.quantity || 10,
          }));
        }
      }
    }
  } catch (err) {
    console.error('Failed loading product lookup data:', err);
  }
}

async function submitForm(createMore = false) {
  errors.value = {};
  if (!form.name || !form.name.trim()) {
    errors.value.name = ['Please enter a product title.'];
    return;
  }

  isSubmitting.value = true;

  // Flatten structured payload matching backend ProductController
  const payload = {
    name: form.name,
    description: form.description,
    cloth_type: form.cloth_type,
    fabric_type: form.fabric_type,
    stock_status: form.stock_status,
    is_active: form.is_active,
    is_featured: form.is_featured,
    new_arrival: form.new_arrival,
    best_seller: form.best_seller,
    highlight: form.highlight,
    fashionist_choice: form.fashionist_choice,
    has_color_variation: form.has_color_variation,
    color_set_id: form.color_set_id,
    is_size_visible: form.is_size_visible,
    is_color_visible: form.is_color_visible,
    category_id: form.category_id,
    color_id: form.color_id,
    media_id: form.media_id,
    kameez_size_id: form.variants.map(v => v.kameez_size_id),
    salwar_size_id: form.variants.map(v => v.salwar_size_id),
    price: form.variants.map(v => v.price),
    discount: form.variants.map(v => v.discount),
    quantity: form.variants.map(v => v.quantity),
  };

  try {
    const endpoint = isEdit.value ? `/products/${route.params.id}` : '/products';
    const method = isEdit.value ? 'PUT' : 'POST';

    const { data, error } = await useApiFetch(endpoint, {
      method,
      body: payload,
    });

    if (error.value) {
      if (error.value.data?.errors) {
        errors.value = error.value.data.errors;
      }
      snackbar.add({ type: 'error', text: error.value.data?.message || 'Failed to save product.' });
      isSubmitting.value = false;
      return;
    }

    snackbar.add({
      type: 'success',
      text: isEdit.value ? 'Product updated successfully!' : 'Product created successfully!',
    });

    isSubmitting.value = false;

    if (createMore) {
      form.name = '';
      form.description = '';
    } else {
      navigateTo('/admin/products');
    }
  } catch (err) {
    isSubmitting.value = false;
    snackbar.add({ type: 'error', text: 'An unexpected error occurred.' });
  }
}

onMounted(() => {
  loadData();
});
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 5px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.3);
  border-radius: 9999px;
}
</style>
