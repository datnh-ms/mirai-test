<template>
  <div class="container">
    <div class="flex h-screen">
      <!-- Sidebar -->
      <div class="w-1/4 bg-white p-4">
        <button class="w-full bg-blue-500 text-white py-2 rounded mb-4">
          Import documents
        </button>
        <div class="mb-4">
          <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600"> Storage </span>
            <a class="text-gray-500 text-sm" href="#"> Change plan </a>
          </div>
          <div class="relative pt-1">
            <div class="overflow-hidden h-2 mb-2 text-xs flex rounded bg-blue-200">
              <div
                class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-500"
                :style="{ width: usagePercentage + '%' }">
              </div>
            </div>
            <span class="text-gray-800 text-xl">
              <span class="text-blue-600">{{ usagePercentage.toFixed(1) }} %</span> used
              of 2GB
            </span>
          </div>
        </div>
        <div class="mb-4 relative">
          <div class="relative">
            <input
              v-model="searchQuery"
              class="w-full p-2 pl-10 border rounded"
              placeholder="e.g. image.png"
              type="text"
            />
            <i
              class="fa-solid fa-magnifying-glass absolute right-3 top-1 transform -translate-y-1/2 text-gray-400"
            ></i>
          </div>
        </div>
        <div class="mb-4">
          <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600"> Folders </span>
            <a class="text-gray-500 text-sm" href="#"> New folder </a>
          </div>
          <ul class="text-gray-600">
            <li v-for="folder in filteredFolders" :key="folder.id" class="mb-2">
              <div @click="selectFolder(folder)" class="item-container">
                <div class="flex items-center font-bold">
                  <i
                    :class="[
                      'fa-solid fa-sort-up mr-4',
                      isFolderSelected(folder)
                        ? 'text-gray-800'
                        : 'text-gray-300 rotate-right',
                    ]"
                  ></i>
                  <i
                    class="fa-solid fa-folder-open mr-2"
                    :class="[
                      'fa-solid fa-folder-open mr-4',
                      isFolderSelected(folder) ? 'text-gray-800' : 'text-gray-300',
                    ]"
                  ></i>
                  {{ folder.name }}
                </div>
                <span v-if="folder.children.length >= 0" class="badge-gray">
                  {{ folder.children.length }}
                </span>
              </div>
              <ul v-if="folder.children.length > 0" class="ml-4">
                <li v-for="subfolder in folder.children" :key="subfolder.id" class="mb-1">
                  <div
                    @click="selectSubfolder(subfolder)"
                    :class="[
                      'item-container',
                      {
                        'bg-blue-200 text-blue-500': isSubfolderSelected(subfolder),
                      },
                    ]"
                  >
                    <div class="flex items-center font-bold">
                      <i
                        :class="[
                          'fa-solid fa-sort-up rotate-right mr-4',
                          isSubfolderSelected(subfolder)
                            ? 'text-blue-500'
                            : 'text-gray-300',
                        ]"
                      ></i>
                      <i
                        :class="[
                          'fa-solid fa-folder-open mr-2',
                          isSubfolderSelected(subfolder)
                            ? 'text-blue-500'
                            : 'text-gray-300',
                        ]"
                      ></i>
                      {{ subfolder.name }}
                    </div>
                    <span
                      v-if="subfolder.children.length >= 0"
                      :class="[
                        'badge-gray',
                        { 'badge-blue': isSubfolderSelected(subfolder) },
                      ]"
                    >
                      {{ subfolder.children.length }}
                    </span>
                  </div>
                </li>
              </ul>
            </li>
          </ul>
        </div>
        <div>
          <div class="flex justify-between items-center mb-2">
            <span class="text-gray-600"> Members </span>
            <a @click="selectAllMembers" class="text-gray-500 text-sm cursor-pointer">
              Select all
            </a>
          </div>
          <ul class="font-bold">
            <li class="mb-2">
              <input
                id="checkbox-all"
                v-model="members.All"
                @change="updateMembers"
                class="mr-2"
                type="checkbox"
              />
              <label for="checkbox-all">All</label>
            </li>
            <li class="mb-2 font-bold">
              <input
                id="checkbox-admin"
                v-model="members.Admin"
                @change="updateMembers"
                class="mr-2"
                type="checkbox"
              />
              <label for="checkbox-admin">Admin</label>
            </li>
          </ul>
        </div>
      </div>
      <!-- Main Content -->
      <div class="w-3/4 bg-white p-4">
        <div class="overflow-y-auto">
          <table class="w-full">
            <thead>
              <tr class="text-left text-gray-400 font-light">
                <th class="w-1/12"><input class="mr-2" type="checkbox" disabled /></th>
                <th class="w-1/3">Select all</th>
                <th class="w-1/6 pl-4">Name <i class="fa-solid fa-sort"></i></th>
                <th class="w-1/6">Dimension <i class="fa-solid fa-sort"></i></th>
                <th class="w-1/6">Size <i class="fa-solid fa-sort"></i></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="file in displayedFiles" :key="file.id" class="border-b">
                <td>
                  <input type="checkbox" />
                </td>
                <td class="p-2 relative">
                  <img :src="file.url" :alt="file.name" class="w-60 rounded" />
                </td>
                <td class="pl-4">
                  {{ file.name }}
                </td>
                <td>{{ file.dimension }}</td>
                <td>{{ (file.size / 1024).toFixed(1) }} kB</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";

const data = ref([
  {
    id: 1,
    name: "Uploads",
    children: [
      {
        id: 10,
        name: "Images",
        children: [
          {
            id: 101,
            name: "Seasandiego.jpg",
            url:
              "https://assets.site-static.com/userFiles/2597/image/2023/CARDIFF_BY_THE_SEA/5-23-2023_1__reasons-why-cardiff-by-the-sea-san-diego-great-place-to-live/9_Reasons_Why_Cardiff-by-the-Sea_San_Diego_.jpg",
            type: "image/jpg",
            dimension: "2000x2000",
            size: 763300, // byte
            photo_by: "Admin",
          },
          {
            id: 102,
            name: "iMactwin.png",
            url:
              "https://i.insider.com/60e4cb0d22d19400191c957c?width=1000&format=jpeg&auto=webp",
            type: "image/png",
            dimension: "2000x2000",
            size: 640200, // byte
            photo_by: "Apple",
          },
          {
            id: 103,
            name: "hustlecup.jpg",
            url: "https://images.deliveryhero.io/image/fd-ph/LH/kmph-hero.jpg",
            type: "image/jpg",
            dimension: "2000x2000",
            size: 100400, // byte
            photo_by: "Admin",
          },
          {
            id: 104,
            name: "MS-Surface.jpg",
            url:
              "https://img-prod-cms-rt-microsoft-com.akamaized.net/cms/api/am/imageFileData/RE4OXzi?ver=3a58",
            type: "image/jpg",
            dimension: "2000x2000",
            size: 113200, // byte
            photo_by: "Admin",
          },
          {
            id: 105,
            name: "Famoustower.jpg",
            url:
              "https://www.worldfamousthings.com/wp-content/uploads/2018/01/Leaning-Tower-of-Pisa-Italy.jpg",
            type: "image/jpg",
            dimension: "2000x2000",
            size: 763300, // byte
            photo_by: "Admin",
          },
        ],
      },
      {
        id: 20,
        name: "Document",
        children: [],
      },
      {
        id: 30,
        name: "Videos",
        children: [
          {
            id: 301,
            name: "GODZILLA MINUS ONE Official Trailer",
            url:
              "https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png?20200912122019",
            type: "video/mp4",
            dimension: "2000x2000",
            size: 763300, // byte
            photo_by: "Admin",
          },
          {
            id: 302,
            name: "Marvel Studios’ Loki Season 2 | October 6 on Disney+",
            url:
              "https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png?20200912122019",
            type: "video/mp4",
            dimension: "2000x2000",
            size: 763300, // byte
            photo_by: "Admin",
          },
          {
            id: 303,
            name: "THE BOY AND THE HERON | Official Teaser Trailer",
            url:
              "https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png?20200912122019",
            type: "video/mp4",
            dimension: "2000x2000",
            size: 763300, // byte
            photo_by: "Admin",
          },
        ],
      },
    ],
  },
  {
    id: 2,
    name: "Sources",
    children: [],
  },
  {
    id: 3,
    name: "Shared",
    children: [],
  },
]);

const searchQuery = ref("");
const members = ref({
  All: true,
  Admin: true,
});

// Tính tổng kích thước sử dụng (tính bằng byte)
const totalSizeUsed = computed(() => {
  return data.value
    .flatMap((folder) => folder.children)
    .flatMap((subfolder) => subfolder.children)
    .reduce((sum, file) => sum + file.size, 0);
});

// Tính phần trăm sử dụng (so với 2GB = 2,147,483,648 byte)
const usagePercentage = computed(() => {
  const totalCapacityBytes = 2 * 1024 * 1024 * 1024; // 2GB = 2,147,483,648 byte
  const usedBytes = totalSizeUsed.value;
  return (usedBytes / totalCapacityBytes) * 100;
});

// Hiển thị tất cả folder và lọc theo search
const filteredFolders = computed(() => {
  if (!searchQuery.value) {
    return data.value;
  }
  return data.value.filter(
    (folder) =>
      folder.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      folder.children.some(
        (subfolder) =>
          subfolder.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          subfolder.children.some((file) =>
            file.name.toLowerCase().includes(searchQuery.value.toLowerCase())
          )
      )
  );
});

// Theo dõi folder/subfolder được chọn
const selectedFolder = ref(null);
const selectedSubfolder = ref(null);

// Danh sách tệp được hiển thị
const displayedFiles = computed(() => {
  let files = [];

  // Lấy tất cả file dựa trên folder/subfolder được chọn
  if (selectedSubfolder.value) {
    files = selectedSubfolder.value.children || [];
  } else if (selectedFolder.value) {
    files =
      selectedFolder.value.children.flatMap((subfolder) => subfolder.children) || [];
  }

  // Lọc theo member (photo_by)
  if (!members.value.All) {
    if (members.value.Admin) {
      files = files.filter((file) => file.photo_by === "Admin");
    } else {
      files = []; // Nếu không chọn "All" và "Admin", không hiển thị file nào
    }
  }

  // Lọc theo searchQuery
  if (searchQuery.value) {
    files = files.filter((file) =>
      file.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  }

  return files;
});

// Chọn folder
const selectFolder = (folder) => {
  selectedFolder.value = folder;
  selectedSubfolder.value = null; // Reset subfolder khi chọn folder
};

// Chọn subfolder và tự động chọn folder cha
const selectSubfolder = (subfolder) => {
  selectedSubfolder.value = subfolder;

  // Tìm folder cha chứa subfolder này
  const parentFolder = data.value.find((folder) =>
    folder.children.some((sf) => sf.id === subfolder.id)
  );
  selectedFolder.value = parentFolder || null; // Gán folder cha, hoặc null nếu không tìm thấy
};

// Xử lý khi thay đổi member
const updateMembers = () => {
  if (!members.value.All && !members.value.Admin) {
    members.value.All = true;
  }
};

// Chọn tất cả member
const selectAllMembers = () => {
  members.value.All = true;
  members.value.Admin = true;
};

const selectedFolderId = computed(() => selectedFolder.value?.id);
const isFolderSelected = (folder) => selectedFolderId.value === folder.id;
const selectedSubfolderId = computed(() => selectedSubfolder.value?.id);
const isSubfolderSelected = (subfolder) => selectedSubfolderId.value === subfolder.id;

// Mặc định chọn folder "Uploads"
onMounted(() => {
  const uploadsFolder = data.value.find((folder) => folder.name === "Uploads");
  if (uploadsFolder) {
    selectFolder(uploadsFolder);
  }
});
</script>

<style scoped>
.rotate-right {
  transform: rotate(90deg);
  display: inline-block;
}

.relative {
  position: relative;
}

.absolute {
  position: absolute;
}

.right-3 {
  right: 0.75rem;
}

.top-1 {
  top: 50%;
}

.transform {
  transform: translateY(-50%);
}

.item-container {
  @apply cursor-pointer p-1 rounded flex justify-between items-center;
}

.rotate-right {
  transform: rotate(90deg);
  display: inline-block;
}

.badge-gray {
  @apply bg-gray-200 text-gray-800 rounded-full px-2 py-1 text-xs font-bold;
}

.badge-blue {
  @apply bg-blue-500 text-white font-bold;
}
</style>
