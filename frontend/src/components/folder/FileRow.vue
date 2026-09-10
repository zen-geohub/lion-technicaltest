<script setup lang="ts">
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import type { File } from "@/types";
import { DownloadIcon, FileIcon, TrashIcon } from "@lucide/vue";

defineProps<{
  file: File;
  canManage: boolean;
}>();

defineEmits<{
  view: [];
  download: [];
  delete: [];
}>();
</script>

<template>
  <div class="flex items-center justify-between border rounded-md p-3 hover:bg-muted/40">
    <button class="flex items-center gap-3" @click="$emit('view')">
      <span><FileIcon class="size-6" /></span>
      <div class="flex flex-col items-baseline">
        <p class="font-medium">{{ file.title }}</p>
        <p class="text-xs text-muted-foreground">{{ file.original_name }}</p>
      </div>
    </button>

    <div class="flex items-center gap-2">
      <Badge>{{ file.department?.name }}</Badge>
      <Button size="icon" variant="outline" @click="$emit('download')">
        <DownloadIcon />
        <span class="sr-only">Download</span>
      </Button>

      <Button v-if="canManage" size="icon" variant="destructive" @click="$emit('delete')">
        <TrashIcon />
        <span class="sr-only">Delete</span>
      </Button>
    </div>
  </div>
</template>
