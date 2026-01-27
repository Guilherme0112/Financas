<script setup lang="ts">
const props = defineProps<{
  pagination: {
    current_page: number
    last_page: number
    prev_page_url: string | null
    next_page_url: string | null
  }
}>()

const emit = defineEmits<{
  (e: 'change', page: number): void
}>()

const prev = () => {
  if (props.pagination.prev_page_url) {
    emit('change', props.pagination.current_page - 1)
  }
}

const next = () => {
  if (props.pagination.next_page_url) {
    emit('change', props.pagination.current_page + 1)
  }
}
</script>

<template>
  <div class="flex justify-center items-center gap-3 mt-4 select-none">
    <button
      @click="prev"
      :disabled="!pagination.prev_page_url"
      class="px-3 py-1 border rounded disabled:opacity-40 disabled:cursor-not-allowed"
    >
      «
    </button>

    <span class="text-sm text-gray-700">
      Página {{ pagination.current_page }} de {{ pagination.last_page }}
    </span>

    <button
      @click="next"
      :disabled="!pagination.next_page_url"
      class="px-3 py-1 border rounded disabled:opacity-40 disabled:cursor-not-allowed"
    >
      »
    </button>
  </div>
</template>
