<script setup lang="ts">
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import api from "@/lib/axios";
import type { Department, FileItem, Paginated } from "@/types";
import { computed, onMounted, ref, watch } from "vue";

const { open, canManage, file } = defineProps<{
  open: boolean;
  file: FileItem | null;
  location: string;
  canManage: boolean;
}>();

const emit = defineEmits<{
  "update:open": [value: boolean];
  updated: [];
}>();

const editing = ref<boolean>(false);
const title = ref<string>("");
const departmentId = ref<string>("");
const replacement = ref<File | null>(null);
const departments = ref<Department[]>([]);
const saving = ref<boolean>(false);

onMounted(async () => {
  const { data } = await api.get<Paginated<Department>>("/departments");
  departments.value = data.data;
});

watch(
  () => open,
  (isOpen) => {
    if (isOpen && file) {
      editing.value = false;
      title.value = file.title;
      departmentId.value = String(file.department_id);
      replacement.value = null;
    }
  },
);

const previewUrl = computed<string | null>(() => {
  if (!file) return null;
  return `${import.meta.env.VITE_API_URL}/storage/${file.file_path}`;
});

const isPdf = computed<boolean>(() => file?.mime_type === "application/pdf");
const isImage = computed<boolean>(() => file?.mime_type?.startsWith("image/") ?? false);

function handleReplacementInput(e: Event): void {
  const input = e.target as HTMLInputElement;
  replacement.value = input.files?.[0] ?? null;
}

async function handleSave(): Promise<void> {
  if (!file) return;

  saving.value = true;
  const formData = new FormData();
  formData.append("title", title.value);
  formData.append("department_id", departmentId.value);
  formData.append("folder_id", String(file.folder_id));
  if (replacement.value) formData.append("file", replacement.value);

  try {
    await api.post(`/files/${file.id}`, formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    editing.value = false;
    emit("updated");
  } finally {
    saving.value = false;
  }
}
</script>

<template>
  <Dialog :open="open" @update:open="(val: boolean) => emit('update:open', val)">
    <DialogContent class="max-w-2xl">
      <DialogHeader>
        <DialogTitle>{{ file?.title }}</DialogTitle>
      </DialogHeader>

      <div v-if="file" class="space-y-4">
        <div v-if="isPdf" class="border rounded-md overflow-hidden">
          <embed :src="previewUrl ?? undefined" type="application/pdf" class="w-full h-full" />
        </div>
        <div v-else-if="isImage" class="border rounded-md overflow-hidden">
          <img :src="previewUrl ?? undefined" class="w-full max-h-96 object-contain" />
        </div>
        <p v-else class="text-sm text-muted-foreground">No preview available for this file type.</p>

        <template v-if="!editing">
          <dl class="grid grid-cols-2 gap-3 text-sm">
            <div>
              <dt class="text-muted-foreground">Folder</dt>
              <dd>{{ location }}</dd>
            </div>
            <div>
              <dt class="text-muted-foreground">Department</dt>
              <dd>{{ file.department?.name }}</dd>
            </div>
            <div>
              <dt class="text-muted-foreground">Uploaded by</dt>
              <dd>{{ file.uploader?.name }}</dd>
            </div>
            <div>
              <dt class="text-muted-foreground">Upload date</dt>
              <dd>{{ new Date(file?.created_at).toLocaleString() }}</dd>
            </div>
          </dl>
        </template>

        <form v-else class="space-y-4" @submit.prevent="handleSave">
          <div class="space-y-2">
            <Label>Title</Label>
            <Input v-model="title" required />
          </div>
          <div class="space-y-2">
            <Label>Department</Label>
            <Select v-model="departmentId">
              <SelectTrigger><SelectValue /></SelectTrigger>
              <SelectContent>
                <SelectItem v-for="dept in departments" :key="dept.id" :value="String(dept.id)">
                  {{ dept.name }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>
          <div class="space-y-2">
            <Label>Replace file (optional)</Label>
            <Input type="file" @change="handleReplacementInput" />
          </div>
        </form>
      </div>

      <DialogFooter v-if="canManage">
        <Button v-if="!editing" variant="outline" @click="editing = true">Edit</Button>
        <template v-else>
          <Button variant="outline" @click="editing = false">Cancel</Button>
          <Button :disabled="saving" @click="handleSave">{{
            saving ? "Saving..." : "Save"
          }}</Button>
        </template>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
