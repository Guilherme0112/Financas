<script setup lang="ts">
defineProps<{
  type?: 'button' | 'submit' | 'reset'
  loading?: boolean
  disabled?: boolean
}>()
</script>

<template>
  <button
    :type="type || 'button'"
    :disabled="disabled || loading"
   class="inline-flex items-center justify-center rounded-md px-4 py-2 
           text-xs font-semibold uppercase text-white transition duration-150 tracking-widest
           bg-gradient-to-r from-emerald-600 to-green-500
           hover:from-emerald-700 hover:to-green-600 hover:shadow-lg
           focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2
           disabled:cursor-not-allowed disabled:opacity-60 disabled:shadow-none"
  >
    <svg
      v-if="loading"
      class="mr-2 h-4 w-4 animate-spin"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      />
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
      />
    </svg>

    <slot />
  </button>
</template>
