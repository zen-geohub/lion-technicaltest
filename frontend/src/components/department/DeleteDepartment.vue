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
import api from "@/lib/axios";
import axios from "axios";
import { ref } from "vue";
import { toast } from "vue-sonner";
import type { Department } from "@/types";
import { TrashIcon } from "@lucide/vue";

const { load, department } = defineProps<{
  load: () => Promise<void>;
  department: Department;
}>();

const isOpen = ref<boolean>(false);
const confirmDelete = ref<string>("");
const error = ref<string>("");

async function handleDelete() {
  error.value = "";

  try {
    await api.delete(`/departments/${department.id}`);
    isOpen.value = false;

    toast.success(`Successfully deleted Department ${department.name}`);
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
  <Button
    variant="ghost"
    class="text-destructive hover:text-destructive hover:cursor-pointer"
    size="sm"
    @click="
      () => {
        isOpen = true;
      }
    "
  >
    <TrashIcon />
    <span class="sr-only">Delete</span>
  </Button>
  <Dialog v-model:open="isOpen">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Delete Department</DialogTitle>
      </DialogHeader>
      <form class="space-y-4" @submit.prevent="handleDelete">
        <p>Are you sure you want to delete Department {{ department.name }}?</p>
        <div class="space-y-2">
          <Label for="dept-name"
            >Type<strong>"Delete {{ department.name }}"</strong>to proceed</Label
          >
          <Input id="dept-name" v-model="confirmDelete" required />
        </div>
        <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        <DialogFooter>
          <Button :disabled="confirmDelete !== `Delete ${department.name}`" type="submit"
            >Delete</Button
          >
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
