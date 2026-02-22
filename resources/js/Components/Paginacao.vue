<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import PrimaryButton from './PrimaryButton.vue'
import { configInertia } from '@/inertia'

const props = defineProps<{
  pagination: {
    current_page: number
    last_page: number
    prev_page_url: string | null
    next_page_url: string | null
  },
  routeName: string
}>()

const mudarPagina = (page: number) => {
  router.get(route(props.routeName), { page }, {
    ...configInertia
  })
}

const prev = () => {
  if (props.pagination.prev_page_url) {
    mudarPagina(props.pagination.current_page - 1)
  }
}

const next = () => {
  if (props.pagination.next_page_url) {
    mudarPagina(props.pagination.current_page + 1)
  }
}
</script>
<template>
  <div class="flex justify-center items-center gap-2 select-none mt-5">
    <PrimaryButton
      @click="prev"
      :disabled="!pagination.prev_page_url"
      class="!px-2"
      aria-label="Página anterior"
    >
      <ChevronLeft :size="20" />
    </PrimaryButton>

    <div class="flex items-center bg-gray-100/50 border border-gray-200 px-4 py-2 rounded-xl shadow-inner">
      <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mr-2">Pág.</span>
      <span class="text-sm font-black text-emerald-600">{{ pagination.current_page }}</span>
      <span class="text-sm font-medium text-gray-400 mx-1.5">/</span>
      <span class="text-sm font-bold text-gray-700">{{ pagination.last_page }}</span>
    </div>

    <PrimaryButton
      @click="next"
      :disabled="!pagination.next_page_url"
      class="!px-2"
      aria-label="Próxima página"
    >
      <ChevronRight :size="20" />
    </PrimaryButton>
  </div>
</template>