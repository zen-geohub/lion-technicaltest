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

const { load, department } = defineProps<{
  load: () => Promise<void>;
  department: Department;
}>();

const isOpen = ref<boolean>(false);
const editedDepartment = ref<Department>({
  ...department,
});
const error = ref<string>("");

async function handleEdit() {
  error.value = "";

  try {
    await api.put(`/departments/${editedDepartment.value.id}`, {
      name: editedDepartment.value.name,
    });
    isOpen.value = false;

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
    variant="outline"
    class="hover:cursor-pointer"
    size="sm"
    @click="
      () => {
        isOpen = true;
      }
    "
    >Edit</Button
  >
  <Dialog v-model:open="isOpen">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Edit Department</DialogTitle>
      </DialogHeader>
      <form class="space-y-4" @submit.prevent="handleEdit">
        <div class="space-y-2">
          <Label for="dept-name">Name</Label>
          <Input id="dept-name" v-model="editedDepartment.name" required />
        </div>
        <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        <DialogFooter>
          <Button type="submit">Save</Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
