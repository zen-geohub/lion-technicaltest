<script setup lang="ts">
import { Button } from "@/components/ui/button";

const props = defineProps<{
  currentPage: number;
  lastPage: number;
  total: number;
}>();

const emit = defineEmits<{
  change: [page: number];
}>();
</script>

<template>
  <div v-if="lastPage > 1" class="flex items-center justify-between text-sm pt-2">
    <span class="text-muted-foreground">
      Page {{ currentPage }} of {{ lastPage }} · {{ total }} total
    </span>
    <div class="flex gap-2">
      <Button
        variant="outline"
        size="sm"
        :disabled="currentPage <= 1"
        @click="emit('change', currentPage - 1)"
      >
        Previous
      </Button>
      <Button
        variant="outline"
        size="sm"
        :disabled="currentPage >= lastPage"
        @click="emit('change', currentPage + 1)"
      >
        Next
      </Button>
    </div>
  </div>
</template>
