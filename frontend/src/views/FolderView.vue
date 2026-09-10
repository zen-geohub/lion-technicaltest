<script setup lang="ts">
import { ExplorerRow, FileRow } from "@/components/folder";
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
  File,
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
const rootFolder = ref<Folder[]>([]);
const breadcrumb = ref<BreadcrumbEntry[]>([]);

const searchResult = ref<File[] | null>(null);
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

  const { data } = await api.get<Paginated<File>>("/files", {
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
      rootFolder.value = data;
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

const explorerItems = computed<ExplorerItem[]>(() => []);
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
        <Breadcrumb v-if="breadcrumb.length">
          <BreadcrumbList>
            <BreadcrumbItem>
              <BreadcrumbLink as-child>
                <RouterLink :to="{ name: 'folders' }">/</RouterLink>
              </BreadcrumbLink>
            </BreadcrumbItem>
            <template v-for="(crumb, i) in breadcrumb">
              <BreadcrumbSeparator />
              <BreadcrumbItem>
                <BreadcrumbLink as-child v-if="i < breadcrumb.length">
                  <RouterLink :to="crumb.path">{{ crumb.name }}</RouterLink>
                </BreadcrumbLink>
              </BreadcrumbItem>
            </template>
          </BreadcrumbList>
        </Breadcrumb>
      </div>

      <div v-if="loading" class="w-full h-full flex items-center justify-center">
        <Loading size="lg" />
      </div>

      <template v-else>
        <div class="border rounded-md overflow-hidden">
          <div
            class="grid grid-cols-4 gap-3 px-3 py-2 text-xs font-medium text-muted-foreground border-b bg-muted/30"
          >
            <span>Name</span>
            <span>Info</span>
            <span>Modified</span>
            <span></span>

            <!-- <ExplorerRow
              v-for="item in source"
            /> -->
          </div>
        </div>
        <p class="text-sm text-muted-foreground text-center">
          This folder is empty.
          {{ auth.isAdmin ? "Create a subfolder or upload a file to get started." : "" }}
        </p>
      </template>
    </template>
  </div>
</template>
