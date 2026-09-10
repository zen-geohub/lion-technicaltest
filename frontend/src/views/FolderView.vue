<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";

const auth = useAuthStore();
const router = useRouter();

async function handleLogout(): Promise<void> {
  await auth.logout();
  router.push({ name: "login" });
}
</script>

<template>
  <div>Hello from folders</div>
  <Button variant="destructive" @click="handleLogout">Logout</Button>
</template>
