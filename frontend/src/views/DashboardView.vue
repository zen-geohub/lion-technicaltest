<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { toast } from "vue-sonner";
</script>

<template>
  <div>Hello from dashboard</div>
  <Button
    variant="outline"
    @click="
      () =>
        toast.info('Event has been created', {
          description: 'Sunday, December 03, 2023 at 9:00 AM',
          action: {
            label: 'Undo',
            onClick: () => console.log('Undo'),
          },
        })
    "
  >
    Show Toast
  </Button>
</template>
