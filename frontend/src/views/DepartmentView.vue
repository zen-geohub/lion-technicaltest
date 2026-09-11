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
import type { Department } from "@/types";
import { CreateDepartment, DeleteDepartment, EditDepartment } from "@/components/department";
import PaginationControl from "@/components/PaginationControl.vue";

const auth = useAuthStore();
const departments = ref<Department[]>([]);
const loading = ref<boolean>(true);

const currentPage = ref<number>(1);
const lastPage = ref<number>(1);
const total = ref<number>(0);

async function load(page: number = 1) {
  loading.value = true;
  const { data } = await api.get("/departments", {
    params: { page },
  });
  departments.value = data.data;
  currentPage.value = data.current_page;
  lastPage.value = data.last_page;
  total.value = data.total;
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

    <PaginationControl
      :current-page="currentPage"
      :last-page="lastPage"
      :total="total"
      @change="load"
    />
  </div>
</template>
