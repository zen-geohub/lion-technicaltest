<script setup lang="ts">
import { ref, onMounted } from "vue";
import api from "@/lib/axios";
import { useAuthStore } from "@/stores/auth";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import CreateDepartment from "@/components/department/CreateDepartment.vue";
import EditDepartment from "@/components/department/EditDepartment.vue";
import type { Department } from "@/types";
import DeleteDepartment from "@/components/department/DeleteDepartment.vue";

const auth = useAuthStore();
const departments = ref<Department[]>([]);
const loading = ref<boolean>(true);

async function load() {
  loading.value = true;
  const { data } = await api.get("/departments");
  departments.value = data.data;
  loading.value = false;
}

onMounted(load);
</script>

<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold">Departments</h2>
      <CreateDepartment :load="load" />
    </div>

    <Table v-if="!loading">
      <TableHeader>
        <TableRow>
          <TableHead>Name</TableHead>
          <TableHead v-if="auth.isAdmin" class="w-32">Actions</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="dept in departments" :key="dept.id">
          <TableCell>{{ dept.name }}</TableCell>
          <TableCell v-if="auth.isAdmin" class="flex gap-2">
            <EditDepartment :load="load" :department="dept" />
            <DeleteDepartment :load="load" :department="dept" />
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>
