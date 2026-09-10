<script setup lang="ts">
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { DropdownMenuItem } from "@/components/ui/dropdown-menu";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import api from "@/lib/axios";
import type { FileItem, Folder } from "@/types";
import axios from "axios";
import { computed, ref, watch } from "vue";
import { toast } from "vue-sonner";

type Props =
  | {
      load: () => Promise<void>;
      data: Folder;
      kind: "folder";
    }
  | {
      load: () => Promise<void>;
      data: FileItem;
      kind: "file";
    };

const { load, data, kind } = defineProps<Props>();

const name = computed<string>(() => (kind === "folder" ? data.name : data.title));
const isOpen = ref<boolean>(false);
const confirmDelete = ref<string>("");
const error = ref<string>("");

async function handleDelete() {
  error.value = "";

  try {
    await api.delete(`/${kind}s/${data.id}`);
    isOpen.value = false;
    confirmDelete.value = "";

    toast.success(`Successfully deleted ${kind} ${name}`);
    await load();
  } catch (err) {
    if (axios.isAxiosError(err)) {
      error.value = err.response?.data.message;
    } else {
      error.value = "Something went wrong.";
    }

    toast.error("Something went wrong.", {
      description: error.value,
    });
  }
}
</script>

<template>
  <DropdownMenuItem class="text-destructive hover:text-destructive" @select.prevent="isOpen = true">
    Delete
  </DropdownMenuItem>
  <Dialog v-model:open="isOpen">
    <DialogContent>
      <DialogHeader>
        <DialogTitle class="capitalize">Delete {{ kind }}</DialogTitle>
      </DialogHeader>
      <form class="space-y-4" @submit.prevent="handleDelete">
        <p>Are you sure you want to delete {{ kind }} {{ name }}?</p>
        <div class="space-y-2">
          <Label for="confirm-delete"
            >Type<strong>"Delete {{ name }}"</strong>to proceed</Label
          >
          <Input id="confirm-delete" v-model="confirmDelete" required />
        </div>
        <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        <DialogFooter>
          <Button variant="destructive" :disabled="confirmDelete !== `Delete ${name}`" type="submit"
            >Delete</Button
          >
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
