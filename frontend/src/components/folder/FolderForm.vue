<script setup lang="ts">
import { ref, watch } from "vue";
import api from "@/lib/axios";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import type { AxiosError } from "axios";
import type { Folder, ApiErrorResponse } from "@/types";

const props = defineProps<{
  open: boolean;
  folder: Folder | null;
  folderId: string | null;
}>();

const emit = defineEmits<{
  "update:open": [value: boolean];
  saved: [];
}>();

const name = ref<string>("");
const saving = ref<boolean>(false);
const error = ref<string>("");

watch(
  () => props.open,
  (isOpen) => {
    if (isOpen) {
      name.value = props.folder?.name ?? "";
      error.value = "";
    }
  },
);

async function handleSubmit(): Promise<void> {
  saving.value = true;
  error.value = "";
  try {
    if (props.folder) {
      await api.put(`/folders/${props.folder.id}`, { name: name.value });
    } else {
      await api.post("/folders", { name: name.value, parent_id: props.folderId });
    }
    emit("saved");
  } catch (e) {
    const err = e as AxiosError<ApiErrorResponse>;
    error.value = err.response?.data?.message ?? "Something went wrong.";
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <Dialog :open="open" @update:open="(v: boolean) => emit('update:open', v)">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ folder ? "Rename Folder" : "New Folder" }}</DialogTitle>
      </DialogHeader>
      <form class="space-y-4" @submit.prevent="handleSubmit">
        <div class="space-y-2">
          <Label for="folder-name">Name</Label>
          <Input id="folder-name" v-model="name" required autofocus />
        </div>
        <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        <DialogFooter>
          <Button type="submit" :disabled="saving">{{ saving ? "Saving..." : "Save" }}</Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
