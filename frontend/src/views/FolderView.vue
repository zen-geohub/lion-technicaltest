<script setup lang="ts">
import { FileRow } from "@/components/folder";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import api from "@/lib/axios";
import { useAuthStore } from "@/stores/auth";
import type { File, Paginated } from "@/types";
import { useDebounceFn } from "@vueuse/core";
import { ref } from "vue";
import { useRouter } from "vue-router";

const auth = useAuthStore();
const router = useRouter();

const searchResult = ref<File[] | null>(null);

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
  </div>
</template>
