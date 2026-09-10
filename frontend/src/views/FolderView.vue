<script setup lang="ts">
import { ExplorerRow, FileDetail, FileRow, FolderForm, UploadFile } from "@/components/folder";
import Loading from "@/components/Loading.vue";
import {
  Breadcrumb,
  BreadcrumbItem,
  BreadcrumbLink,
  BreadcrumbList,
  BreadcrumbSeparator,
} from "@/components/ui/breadcrumb";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import api from "@/lib/axios";
import { useAuthStore } from "@/stores/auth";
import type {
  BreadcrumbEntry,
  ExplorerItem,
  FileItem,
  Folder,
  FolderResponse,
  Paginated,
} from "@/types";
import { useDebounceFn } from "@vueuse/core";
import { computed, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const currentFolder = ref<Folder | null>(null);
const rootFolders = ref<Folder[]>([]);
const breadcrumb = ref<BreadcrumbEntry[]>([]);

const detailOpen = ref<boolean>(false);
const selectedFile = ref<FileItem | null>(null);

const folderDialogOpen = ref<boolean>(false);
const editingFolder = ref<Folder | null>(null);

const searchResult = ref<FileItem[] | null>(null);
const loading = ref<boolean>(false);
const folderPath = computed<string>(() => {
  const path = route.params.pathMatch;

  if (Array.isArray(path)) {
    return path.join("/");
  }

  return path as string;
});

const handleSearch = useDebounceFn(async (value: string) => {
  if (!value) {
    searchResult.value = null;
    return;
  }

  const { data } = await api.get<Paginated<FileItem>>("/files", {
    params: { search: value },
  });

  searchResult.value = data.data;
}, 500);

async function load(): Promise<void> {
  loading.value = true;

  try {
    if (folderPath.value) {
      const { data } = await api.get<FolderResponse>(`/folders/${folderPath.value}`);

      currentFolder.value = data.folder;
      breadcrumb.value = data.breadcrumb;
    } else {
      currentFolder.value = null;
      breadcrumb.value = [];

      const { data } = await api.get<Folder[]>("/folders");
      rootFolders.value = data;
    }
  } finally {
    loading.value = false;
  }
}

watch(
  () => route.params.pathMatch,
  () => load(),
  { immediate: true },
);

const childFolders = computed<Folder[]>(() =>
  folderPath.value ? (currentFolder.value?.children ?? []) : rootFolders.value,
);
const files = computed<FileItem[]>(() =>
  folderPath.value ? (currentFolder.value?.files ?? []) : [],
);

const explorerItems = computed<ExplorerItem[]>(() => [
  ...childFolders.value.map((folder): ExplorerItem => ({ kind: "folder", data: folder })),
  ...files.value.map((file): ExplorerItem => ({ kind: "file", data: file })),
]);

function openFolder(item: ExplorerItem): void {
  if (item.kind === "folder") {
    const current = route.params.pathMatch;

    const segments = Array.isArray(current) ? current : current ? [current] : [];

    router.push({
      name: "folders",
      params: {
        pathMatch: [...segments, item.data.slug],
      },
    });
  }
}

function openFile(item: ExplorerItem): void {
  if (item.kind === "file") {
    selectedFile.value = item.data;
    detailOpen.value = true;
  }
}

async function downloadFile(item: ExplorerItem): Promise<void> {
  if (item.kind === "file") {
    const response = await api.get(`/files/${item.data.id}/download`, {
      responseType: "blob",
    });

    const url = URL.createObjectURL(response.data);

    const link = document.createElement("a");
    link.href = url;
    link.download = item.data.original_name;
    document.body.appendChild(link);
    link.click();
    link.remove();

    URL.revokeObjectURL(url);
  }
}

async function handleFolderSaved(): Promise<void> {
  folderDialogOpen.value = false;
  await load();
}
</script>

<template>
  <div class="space-y-6">
    <Input
      placeholder="Search files..."
      @input="handleSearch(($event.target as HTMLInputElement).value)"
    />

    <template v-if="searchResult">
      <h3 class="text-base font-medium text-muted-foreground">Search results</h3>
      <p v-if="!searchResult.length" class="text-sm text-muted-foreground">
        No files match your search.
      </p>

      <FileRow
        v-for="file in searchResult"
        :key="file.id"
        :file="file"
        :can-manage="auth.isAdmin"
      />
    </template>

    <template v-else>
      <div class="flex items-center justify-between">
        <Breadcrumb>
          <BreadcrumbList>
            <BreadcrumbItem>
              <BreadcrumbLink as-child>
                <RouterLink :to="{ name: 'folders' }">/</RouterLink>
              </BreadcrumbLink>
            </BreadcrumbItem>
            <template v-if="breadcrumb.length" v-for="(crumb, i) in breadcrumb">
              <BreadcrumbSeparator />
              <BreadcrumbItem>
                <BreadcrumbLink as-child v-if="i < breadcrumb.length">
                  <RouterLink :to="crumb.path">{{ crumb.name }}</RouterLink>
                </BreadcrumbLink>
              </BreadcrumbItem>
            </template>
          </BreadcrumbList>
        </Breadcrumb>

        <div v-if="auth.isAdmin" class="flex gap-2">
          <Button
            variant="outline"
            @click="
              () => {
                editingFolder = null;
                folderDialogOpen = true;
              }
            "
            >New Folder</Button
          >
          <UploadFile
            :folder-path="folderPath"
            :folder-id="
              String(
                breadcrumb.filter((crumb) => crumb.slug === folderPath.split('/').pop())[0]?.id,
              ) ?? ''
            "
            @uploaded="load"
          />
        </div>
      </div>

      <div v-if="loading" class="w-full h-full flex items-center justify-center">
        <Loading size="lg" />
      </div>

      <template v-else>
        <div class="border rounded-md overflow-hidden">
          <div
            class="grid grid-cols-[1fr_180px_140px_20px] gap-3 px-3 py-2 text-xs font-medium text-muted-foreground border-b bg-muted/30"
          >
            <span>Name</span>
            <span>Info</span>
            <span>Modified</span>
            <span></span>
          </div>
          <ExplorerRow
            v-for="item in explorerItems"
            :item="item"
            :can-manage="auth.isAdmin"
            :load="load"
            @open="openFolder(item)"
            @view="openFile(item)"
            @rename="
              () => {
                if (item.kind === 'folder') {
                  editingFolder = item.data;
                  folderDialogOpen = true;
                }
              }
            "
            @download="downloadFile(item)"
          />
        </div>
        <p class="text-sm text-muted-foreground text-center">
          This folder is empty.
          {{ auth.isAdmin ? "Create a subfolder or upload a file to get started." : "" }}
        </p>
      </template>
    </template>
    <FolderForm
      v-model:open="folderDialogOpen"
      :folder="editingFolder"
      :folder-id="
        String(breadcrumb.filter((crumb) => crumb.slug === folderPath.split('/').pop())[0]?.id) ??
        ''
      "
      @saved="handleFolderSaved"
    />
    <FileDetail
      v-model:open="detailOpen"
      :file="selectedFile"
      :location="breadcrumb.filter((crumb) => crumb.id === selectedFile?.folder_id)[0]?.name ?? ''"
      :can-manage="auth.isAdmin"
      @updated="load"
    />
  </div>
</template>
