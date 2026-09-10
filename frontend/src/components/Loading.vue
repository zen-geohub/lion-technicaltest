<script setup lang="ts">
interface LoadingProps {
  size: "sm" | "md" | "lg";
}

const loadingSize = {
  sm: "h-4 w-4",
  md: "h-6 w-6",
  lg: "h-8 w-8",
};

const props = withDefaults(defineProps<LoadingProps>(), {
  size: "md",
});
</script>

<template>
  <div
    :class="[
      loadingSize[props.size],
      'animate-spin rounded-full border-2 border-primary border-t-transparent',
    ]"
  ></div>
</template>
