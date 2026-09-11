<template>
  <div class="flex min-h-screen items-center justify-center flex-col gap-4">
    <h1 class="text-4xl font-bold">404</h1>
    <p class="text-muted-foreground">Page not found.</p>
    <RouterLink :to="{ name: 'folders' }" class="text-primary underline">Home</RouterLink>
  </div>
</template>
