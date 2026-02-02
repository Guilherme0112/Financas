<script setup lang="ts">
defineProps<{
  type?: 'button' | 'submit' | 'reset'
  loading?: boolean
  disabled?: boolean
}>()
</script>

<template>
  <button
    :type="type ?? 'button'"
    :disabled="disabled || loading"
    :class="[
      'inline-flex items-center rounded-md border border-transparent px-4 py-2 text-xs font-semibold uppercase tracking-widest transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2',
      (disabled || loading)
        ? 'bg-red-300 text-white cursor-not-allowed opacity-70'
        : 'bg-red-600 text-white hover:bg-red-500 active:bg-red-700 focus:ring-red-500'
    ]"
  >
    <slot />
  </button>
</template>
