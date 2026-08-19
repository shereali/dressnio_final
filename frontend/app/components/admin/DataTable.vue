<script lang="ts" setup>
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const props = defineProps(["apiInfo"]);
const store = useUtilityStore();
const { editItem, deleteItem } = useCrud();
const route = useRoute();

let columns = props.apiInfo.columns;
let pageTitle = props.apiInfo.pageTitle;
let pageBackLink = props.apiInfo.pageBackLink;
const apiEndpoint = props.apiInfo.apiEndpoint;
const customizedColumn = props.apiInfo.customizedColumn;

// Sync with store for layout components
onMounted(() => {
  store.pageTitle = pageTitle;
  store.pageBackLink = pageBackLink;
});

// let refreshItem = () => {};

const selectedColumns = ref(columns);
const columnsTable = computed(() =>
  columns.filter((column: any) => selectedColumns.value.includes(column))
);

// Selected Rows
const selectedRows = ref([]);

function select(row: any) {
  const index = selectedRows.value.findIndex((item) => item.id === row.id);
  if (index === -1) {
    selectedRows.value.push(row);
  } else {
    selectedRows.value.splice(index, 1);
  }
}

// Actions
const actions = [
  [
    {
      key: "completed",
      label: "Completed",
      icon: "i-heroicons-check",
    },
  ],
  [
    {
      key: "uncompleted",
      label: "In Progress",
      icon: "i-heroicons-arrow-path",
    },
  ],
];

// Filters
const todoStatus = [
  {
    key: "uncompleted",
    label: "In Progress",
    value: false,
  },
  {
    key: "completed",
    label: "Completed",
    value: true,
  },
];

const search = ref("");
const selectedStatus = ref([]);
const searchStatus = computed(() => {
  if (selectedStatus.value?.length === 0) {
    return "";
  }

  if (selectedStatus?.value?.length > 1) {
    return `?completed=${selectedStatus.value[0].value}&completed=${selectedStatus.value[1].value}`;
  }

  return `?completed=${selectedStatus.value[0].value}`;
});

const resetFilters = () => {
  search.value = "";
  selectedStatus.value = [];
};

const sort = ref({ column: "id", direction: "asc" as const });
const page = ref(1);
let pageCount = ref(5);
let pageTotal = ref(0); // This value should be dynamic coming from the API
const pageFrom = computed(() => (page.value - 1) * pageCount.value + 1);
const pageTo = computed(() =>
  Math.min(page.value * pageCount.value, pageTotal.value)
);

const config = useRuntimeConfig();
const token = useTokenStore();

const {
  data: dataList,
  error,
  pending,
  refresh,
} = await useLazyAsyncData(
  "dataList",
  () =>
    ($fetch as any)(apiEndpoint + searchStatus.value, {
      baseURL: config.public.apiBase,
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        Authorization: "Bearer " + token.getToken,
      },
      query: {
        q: search.value,
        _page: page.value,
        _limit: pageCount.value,
        _sort: sort.value.column,
        _order: sort.value.direction,
      },
    }),
  {
    default: () => [],
    watch: [page, search, searchStatus, pageCount, sort],
  }
);
// Data

pageTotal = computed(() => {
  return dataList.value?.data?.length;
});

// console.log("page count", pageCount.value);

const refreshDataTable = () => {
  refresh();
};

const q = ref("");

const filteredRows = computed(() => {
  if (dataList.value?.data?.length > 0) {
    if (!q.value) {
      return dataList.value?.data.slice(
        (page.value - 1) * pageCount.value,
        page.value * pageCount.value
      );
    }

    return dataList.value?.data.filter((item: object) => {
      return Object.values(item).some((value) => {
        return String(value).toLowerCase().includes(q.value.toLowerCase());
      });
    });
  }
});

// const showDataPerPage = async (page) => {
//   await getItems.items(apiEndpoint, page).then((res) => {
//     if (res.data.value) {
//       dataList.value.data = res.data.value?.data;
//       // console.log("datalist", res.meta);
//     }
//     store.pending = res.pending.value;
//     refreshItem = res.refresh;
//   });
// };
// showDataPerPage(page.value);

const items = (row: any) => [
  [
    {
      label: "Edit",
      icon: "i-heroicons-pencil-square-20-solid",
      click: () => editData(row.id, route.fullPath),
    },
    {
      label: "Duplicate",
      icon: "i-heroicons-document-duplicate-20-solid",
      click: () => duplicateItem(row.id),
    },
  ],
  [
    {
      label: "Archive",
      icon: "i-heroicons-archive-box-20-solid",
    },
    {
      label: "Move",
      icon: "i-heroicons-arrow-right-circle-20-solid",
    },
  ],
  [
    {
      label: "Delete",
      icon: "i-heroicons-trash-20-solid",
      click: () => removeItem(row.id),
    },
  ],
];

// const rows = computed(() => {
//   return dataList.slice((page.value - 1) * pageCount, page.value * pageCount);
// });

const editData = (item_id: number, api: string) => {
  editItem(item_id, api);
};

let duplicateItem = async (item_id: number) => {};

const removeItem = async (item_id: number) => {
  await deleteItem(item_id, props.apiInfo.apiEndpoint);

  // showDataPerPage(page.value);
  if (store.getToastMsg) toastMessage(store.getToastMsg, "warning");

  refreshDataTable();
};

if (store.getToastMsg) toastMessage(store.getToastMsg, "success");
store.toastMsg = "";
store.is_edit = false;
</script>

<template>
  <div>
    <UCard
      class="w-full"
      :ui="{
        base: '',
        ring: '',
        divide: 'divide-y divide-gray-200 dark:divide-gray-700',
        header: { padding: 'px-4 py-5' },
        body: {
          padding: '',
          base: 'divide-y divide-gray-200 dark:divide-gray-700',
        },
        footer: { padding: 'p-4' },
      }"
    >
      <template #header>
        <div class="flex justify-between">
          <h2
            class="font-semibold text-xl text-gray-900 dark:text-white leading-tight"
          >
            {{ pageTitle }}
          </h2>
          <ULink
            :to="pageBackLink"
            class="bg-green-500 text-white shadow-sm px-2 py-1 rounded-md font-semibold"
            ><Icon name="mdi:plus" />Create New</ULink
          >
        </div>
      </template>

      <!-- Filters -->
      <div class="flex items-center justify-between gap-3 px-4 py-3">
        <UInput
          v-model="search"
          icon="i-heroicons-magnifying-glass-20-solid"
          placeholder="Search..."
        />

        <USelectMenu
          v-model="selectedStatus"
          :options="todoStatus"
          multiple
          placeholder="Status"
          class="w-40"
        />
      </div>

      <!-- Header and Action buttons -->
      <div class="flex justify-between items-center w-full px-4 py-3">
        <div class="flex items-center gap-1.5">
          <span class="text-sm leading-5">Rows per page:</span>

          <USelect
            v-model="pageCount"
            :options="[3, 5, 10, 20, 30, 40]"
            class="me-2 w-20"
            size="xs"
          />
        </div>

        <div class="flex gap-1.5 items-center">
          <UButton :loading="pending" @click="refreshDataTable"
            ><Icon v-if="pending == false" name="mdi:refresh"
          /></UButton>
          <UDropdown
            v-if="selectedRows.length > 1"
            :items="actions"
            :ui="{ width: 'w-36' }"
          >
            <UButton
              icon="i-heroicons-chevron-down"
              trailing
              color="gray"
              size="xs"
            >
              Mark as
            </UButton>
          </UDropdown>

          <USelectMenu v-model="selectedColumns" :options="columns" multiple>
            <UButton icon="i-heroicons-view-columns" color="gray" size="xs">
              Columns
            </UButton>
          </USelectMenu>

          <UButton
            icon="i-heroicons-funnel"
            color="gray"
            size="xs"
            :disabled="search === '' && selectedStatus.length === 0"
            @click="resetFilters"
          >
            Reset
          </UButton>
        </div>
      </div>

      <!-- Table -->

      <UTable
        v-model="selectedRows"
        v-model:sort="sort"
        :rows="filteredRows"
        :columns="columnsTable"
        :loading="pending"
        sort-asc-icon="i-heroicons-arrow-up-20-solid"
        sort-desc-icon="i-heroicons-arrow-down-20-solid"
        :sort-button="{
          icon: 'i-heroicons-sparkles-20-solid',
          color: 'primary',
          variant: 'outline',
          size: '2xs',
          square: false,
          ui: { rounded: 'rounded-full' },
        }"
        sort-mode="manual"
        class="w-full"
        :ui="{ td: { base: 'max-w-[0] truncate' } }"
        @select="select"
      >
        <!-- <UTable
        :loading="store.pending"
        :loading-state="{
          icon: 'i-heroicons-arrow-path-20-solid',
          label: 'Loading...',
        }"
        sort-asc-icon="i-heroicons-arrow-up-20-solid"
        sort-desc-icon="i-heroicons-arrow-down-20-solid"
        :sort-button="{
          icon: 'i-heroicons-sparkles-20-solid',
          color: 'primary',
          variant: 'outline',
          size: '2xs',
          square: false,
          ui: { rounded: 'rounded-full' },
        }"
        class="w-full"
        :columns="columns"
        v-model="selected"
        :rows="filteredRows"
      > -->

        <template #description-data="{ row }">
          <div v-html="row.description"></div>
        </template>
        <template #image-data="{ row }">
          <div class="p-1" v-if="row.image != null">
            <NuxtImg :src="row.image" class="w-24 h-24 object-cover" />
          </div>
        </template>
        <template #video-data="{ row }">
          <div class="p-1" v-if="row.video != null">
            <div class="max-w-md relative">
              <video
                id="myVideo"
                class="w-full h-[200px] object-cover"
                controls
              >
                <!-- Replace 'your-video.mp4' with the path to your video file -->
                <source :src="row.video" type="video/mp4" />
                Your browser does not support the video tag.
              </video>
            </div>
          </div>
        </template>
        <template #colors-data="{ row }">
          <div class="flex">
            <div
              class="grid grid-cols-1 gap-2"
              v-for="(color, index) in row.colors"
            >
              <div
                class="w-4 h-4 m-1 overflow-hidden shadow rounded-md"
                :style="{ background: color.color_code }"
              ></div>
            </div>
          </div>
        </template>
        <template #is_active-data="{ row }">
          <UBadge
            size="xs"
            :label="row.is_active ? 'Active' : 'Inactive'"
            :color="row.is_active ? 'emerald' : 'red'"
            variant="subtle"
          />
        </template>
        <!-- :color="row.color_code ? 'emerald' : 'orange'" -->
        <template #actions-data="{ row }">
          <UDropdown :items="items(row)">
            <UButton
              color="gray"
              variant="ghost"
              icon="i-heroicons-ellipsis-horizontal-20-solid"
            />
          </UDropdown>
        </template>
      </UTable>
      <!-- Number of rows & Pagination -->
      <template #footer>
        <div class="flex flex-wrap justify-between items-center">
          <div>
            <span class="text-sm leading-5">
              Showing
              <span class="font-medium">{{ pageFrom }}</span>
              to
              <span class="font-medium">{{ pageTo }}</span>
              of
              <span class="font-medium">{{ pageTotal }}</span>
              results
            </span>
          </div>

          <UPagination
            v-model="page"
            :page-count="pageCount"
            :total="pageTotal"
            :ui="{
              wrapper: 'flex items-center gap-1',
              rounded: '!rounded-full min-w-[32px] justify-center',
              default: {
                activeButton: {
                  variant: 'outline',
                },
              },
            }"
          />
        </div>
      </template>
    </UCard>
  </div>
</template>
