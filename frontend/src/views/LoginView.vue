<script setup lang="ts">
import Loading from "@/components/Loading.vue";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { useAuthStore } from "@/stores/auth";
import axios from "axios";
import { ref, shallowRef } from "vue";
import { useRouter } from "vue-router";
import { toast } from "vue-sonner";

const email = ref<string>("");
const password = ref<string>("");
const loading = ref<boolean>(false);
const error = shallowRef<string>("");

const auth = useAuthStore();
const router = useRouter();

async function handleSubmit(): Promise<void> {
  try {
    await auth.login(email.value, password.value);
    router.push({ name: "folders" });
  } catch (err) {
    if (axios.isAxiosError(err)) {
      error.value = err.response?.data.message;
    } else {
      error.value = "Login failed.";
    }

    toast.error("Login failed", {
      description: error.value,
    });
  } finally {
    loading.value = false;
  }
}
</script>

<template>
  <div class="flex min-h-screen items-center justify-center bg-muted/40 px-4">
    <Card class="w-full max-w-sm">
      <CardHeader>
        <CardTitle class="text-2xl">File Management System</CardTitle>
        <p class="text-sm text-muted-foreground">Sign in to continue</p>
      </CardHeader>
      <CardContent>
        <form class="space-y-3" @submit.prevent="handleSubmit">
          <div class="space-y-1">
            <Label>Email</Label>
            <Input id="email" v-model="email" type="email" required autocomplete />
          </div>

          <div class="space-y-1">
            <Label>Password</Label>
            <Input id="password" v-model="password" type="password" required autocomplete />
          </div>

          <p v-if="error" class="text-sm text-destructive">{{ error }}</p>

          <Button type="submit" class="w-full" :disabled="loading">
            <Loading v-if="loading" size="sm" />
            <span v-else>Sign in</span>
          </Button>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
