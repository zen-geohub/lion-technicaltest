<script setup lang="ts">
import { DeleteItem } from "@/components/folder";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import type { ExplorerItem } from "@/types";
import { EllipsisVerticalIcon, FileIcon, FolderIcon } from "@lucide/vue";

const { item } = defineProps<{
  item: ExplorerItem;
  canManage: boolean;
  load: () => Promise<void>;
}>();

const emit = defineEmits<{
  open: [];
  view: [];
  download: [];
  rename: [];
}>();

function handleRowClick(): void {
  if (item.kind === "folder") {
    emit("open");
  } else {
    emit("view");
  }
}
</script>

<template>
  <div
    class="grid grid-cols-[1fr_180px_140px_20px] items-center gap-3 border-b px-3 py-2 text-sm hover:bg-muted/40 cursor-pointer"
    @click="handleRowClick"
  >
    <div class="flex items-center gap-3 min-w-0">
      <span>
        <FileIcon v-if="item.kind === 'file'" />
        <FolderIcon v-if="item.kind === 'folder'" />
      </span>
      <template v-if="item.kind === 'folder'">
        <span class="truncate font-medium">{{ item.data.name }}</span>
      </template>
      <template v-if="item.kind === 'file'">
        <span class="truncate font-medium">{{ item.data.title }}</span>
      </template>
    </div>

    <div class="truncate text-muted-foreground">
      <template v-if="item.kind === 'folder'">
        {{ item.data.children_count ?? 0 }} folders, {{ item.data.children_count }} files
      </template>
      <Badge v-else>{{ item.data.department?.name }}</Badge>
    </div>

    <div class="text-muted-foreground truncate">
      {{ new Date(item.data.updated_at).toLocaleDateString() }}
    </div>

    <div class="flex items-center gap-1 justify-end" @click.stop>
      <DropdownMenu v-if="canManage">
        <DropdownMenuTrigger>
          <Button variant="ghost" size="icon-xs">
            <EllipsisVerticalIcon />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent>
          <DropdownMenuItem v-if="item.kind === 'folder'" @click="emit('rename')">
            Rename
          </DropdownMenuItem>
          <DropdownMenuItem v-if="item.kind === 'file'" @click="emit('download')">
            Download
          </DropdownMenuItem>
          <template v-if="item.kind === 'folder'">
            <DeleteItem :load="load" :data="item.data" kind="folder" />
          </template>

          <template v-else>
            <DeleteItem :load="load" :data="item.data" kind="file" />
          </template>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  </div>
</template>
