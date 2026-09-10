<script setup lang="ts">
import { RouterLink, RouterView, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { Button } from "@/components/ui/button";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { FolderIcon, LayoutDashboardIcon, LogOutIcon, UserRoundGroupIcon } from "@lucide/vue";
import Theme from "@/components/Theme.vue";

const auth = useAuthStore();
const router = useRouter();

async function handleLogout() {
  await auth.logout();
  router.push({ name: "login" });
}

const initials = () => auth.user?.name?.charAt(0)?.toUpperCase() || "?";
</script>

<template>
  <div class="flex min-h-screen">
    <aside class="w-60 border-r bg-muted/30 p-4 flex flex-col justify-between">
      <div class="flex flex-col gap-1">
        <h1 class="text-lg font-bold mb-4 px-2">File Management System</h1>
        <RouterLink
          v-if="auth.isAdmin"
          :to="{ name: 'dashboard' }"
          class="flex gap-2 px-3 py-2 rounded-md text-sm hover:bg-muted hover:text-foreground"
          exact-active-class="bg-primary text-primary-foreground"
        >
          <LayoutDashboardIcon class="size-5" />
          Dashboard
        </RouterLink>
        <RouterLink
          :to="{ name: 'folders' }"
          class="flex gap-2 px-3 py-2 rounded-md text-sm hover:bg-muted hover:text-foreground"
          active-class="bg-primary text-primary-foreground"
        >
          <FolderIcon class="size-5" />
          Files
        </RouterLink>
        <RouterLink
          v-if="auth.isAdmin"
          :to="{ name: 'departments' }"
          class="flex gap-2 px-3 py-2 rounded-md text-sm hover:bg-muted hover:text-foreground"
          active-class="bg-primary text-primary-foreground"
        >
          <UserRoundGroupIcon class="size-5" />
          Departments
        </RouterLink>
      </div>

      <div>
        <div class="flex items-center gap-3 justify-between">
          <div class="flex items-center gap-1">
            <Avatar>
              <AvatarFallback>{{ initials() }}</AvatarFallback>
            </Avatar>
            <h1>{{ auth.user?.name }}</h1>
          </div>
          <div class="flex items-center gap-1">
            <Theme />
            <Button
              variant="outline"
              class="text-destructive hover:text-destructive"
              size="icon"
              @click="handleLogout"
              ><LogOutIcon
            /></Button>
          </div>
        </div>
      </div>
    </aside>

    <main class="flex-1 p-6">
      <RouterView />
    </main>
  </div>
</template>
