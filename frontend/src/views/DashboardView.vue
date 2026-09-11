<script setup lang="ts">
import { ref, onMounted } from "vue";
import api from "@/lib/axios";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { FileRow } from "@/components/folder";
import type { DashboardStats, FileItem } from "@/types";
import Loading from "@/components/Loading.vue";

const stats = ref<DashboardStats | null>(null);
const loading = ref(true);

onMounted(async () => {
  const { data } = await api.get<DashboardStats>("/dashboard/stats");
  stats.value = data;
  loading.value = false;
});

async function downloadFile(file: FileItem) {
  const response = await api.get(`/files/${file.id}/download`, {
    responseType: "blob",
  });

  const url = URL.createObjectURL(response.data);

  const link = document.createElement("a");
  link.href = url;
  link.download = file.original_name;
  document.body.appendChild(link);
  link.click();
  link.remove();

  URL.revokeObjectURL(url);
}
</script>

<template>
  <div class="space-y-6">
    <h2 class="text-xl font-semibold">Dashboard</h2>

    <div v-if="loading" class="w-full h-full flex items-center justify-center">
      <Loading size="lg" />
    </div>

    <template v-else>
      <div v-if="stats" class="grid grid-cols-3 gap-4">
        <Card>
          <CardHeader
            ><CardTitle class="text-sm text-muted-foreground">Total Folders</CardTitle></CardHeader
          >
          <CardContent
            ><p class="text-3xl font-bold">{{ stats.total_folders }}</p></CardContent
          >
        </Card>
        <Card>
          <CardHeader
            ><CardTitle class="text-sm text-muted-foreground">Total Files</CardTitle></CardHeader
          >
          <CardContent
            ><p class="text-3xl font-bold">{{ stats.total_files }}</p></CardContent
          >
        </Card>
        <Card>
          <CardHeader
            ><CardTitle class="text-sm text-muted-foreground"
              >Total Departments</CardTitle
            ></CardHeader
          >
          <CardContent
            ><p class="text-3xl font-bold">{{ stats.total_departments }}</p></CardContent
          >
        </Card>
      </div>

      <div v-if="stats" class="space-y-2">
        <h3 class="text-sm font-medium text-muted-foreground">10 Latest Files</h3>
        <FileRow
          v-for="(file, i) in stats.latest_files"
          :key="i"
          :file="file"
          :can-manage="false"
          @view="() => {}"
          @download="downloadFile(file)"
        />
      </div>
    </template>
  </div>
</template>
