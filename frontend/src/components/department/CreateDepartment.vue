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
import { useAuthStore } from "@/stores/auth";
import axios from "axios";
import { ref } from "vue";
import { toast } from "vue-sonner";

const { load } = defineProps<{
  load: () => Promise<void>;
}>();

const auth = useAuthStore();

const isOpen = ref<boolean>(false);
const name = ref<string>("");
const error = ref<string>("");

async function handleSubmit() {
  error.value = "";

  try {
    await api.post("/departments", { name: name.value });
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
    v-if="auth.isAdmin"
    class="hover:cursor-pointer"
    size="sm"
    @click="
      () => {
        isOpen = true;
      }
    "
    >New Department</Button
  >
  <Dialog v-model:open="isOpen">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>New Department</DialogTitle>
      </DialogHeader>
      <form class="space-y-4" @submit.prevent="handleSubmit">
        <div class="space-y-2">
          <Label for="dept-name">Name</Label>
          <Input id="dept-name" v-model="name" required />
        </div>
        <p v-if="error" class="text-sm text-destructive">{{ error }}</p>
        <DialogFooter>
          <Button type="submit">Submit</Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
