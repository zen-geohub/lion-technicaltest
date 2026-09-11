<script setup lang="ts">
import { ref, watch, onMounted } from "vue";
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
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import type { AxiosError } from "axios";
import type { Department, Paginated, ApiErrorResponse } from "@/types";

const { folderPath, folderId } = defineProps<{
  folderPath: string;
  folderId: string | null;
}>();

const emit = defineEmits<{
  uploaded: [];
}>();

const open = ref<boolean>(false);
const title = ref<string>("");
const departmentId = ref<string>("");
const file = ref<File | null>(null);
const isDragging = ref<boolean>(false);
const saving = ref<boolean>(false);
const error = ref<string>("");
const departments = ref<Department[]>([]);

onMounted(async () => {
  const { data } = await api.get<Paginated<Department>>("/departments");
  departments.value = data.data;
});

watch(
  () => open,
  (isOpen) => {
    if (isOpen) {
      title.value = "";
      departmentId.value = "";
      file.value = null;
      error.value = "";
    }
  },
);

function handleDrop(e: DragEvent): void {
  isDragging.value = false;
  const dropped = e.dataTransfer?.files[0];
  if (dropped) file.value = dropped;
}

function handleFileInput(e: Event): void {
  const input = e.target as HTMLInputElement;
  file.value = input.files?.[0] ?? null;
}

async function handleSubmit(): Promise<void> {
  if (!file.value) {
    error.value = "Please choose a file.";
    return;
  }

  saving.value = true;
  error.value = "";

  const formData = new FormData();
  formData.append("title", title.value);
  formData.append("department_id", departmentId.value);
  formData.append("folder_id", folderId ?? "");
  formData.append("file", file.value);

  try {
    await api.post("/files", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    open.value = false;
    title.value = "";
    departmentId.value = "";

    emit("uploaded");
  } catch (e) {
    const err = e as AxiosError<ApiErrorResponse>;
    error.value = err.response?.data?.message ?? "Upload failed.";
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <Button v-if="folderPath" class="hover:cursor-pointer" @click="open = true">Upload File</Button>
  <Dialog :open="open" @update:open="(v: boolean) => (open = v)">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Upload File</DialogTitle>
      </DialogHeader>
      <form class="space-y-4" @submit.prevent="handleSubmit">
        <div class="space-y-2">
          <Label for="file-title">Title</Label>
          <Input id="file-title" v-model="title" required />
        </div>

        <div class="space-y-2">
          <Label>Department</Label>
          <Select v-model="departmentId">
            <SelectTrigger><SelectValue placeholder="Select department" /></SelectTrigger>
            <SelectContent>
              <SelectItem v-for="dept in departments" :key="dept.id" :value="String(dept.id)">
                {{ dept.name }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>

        <div
          class="border-2 border-dashed rounded-md p-6 text-center text-sm transition-colors"
          :class="isDragging ? 'border-primary bg-primary/5' : 'border-muted-foreground/30'"
          @dragover.prevent="isDragging = true"
          @dragleave.prevent="isDragging = false"
          @drop.prevent="handleDrop"
        >
          <p v-if="!file" class="text-muted-foreground">
            Drag & drop a file here, or
            <label class="text-primary underline cursor-pointer">
              browse
              <input type="file" class="hidden" @change="handleFileInput" />
            </label>
          </p>
          <p v-else class="font-medium">{{ file.name }}</p>
        </div>

        <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        <DialogFooter>
          <Button type="submit" :disabled="saving">{{ saving ? "Uploading..." : "Upload" }}</Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
