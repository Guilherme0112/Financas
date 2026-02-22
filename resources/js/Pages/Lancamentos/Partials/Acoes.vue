<script setup lang="ts">
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { ArrowDownToLineIcon, ArrowUpFromLineIcon, Plus, SlidersHorizontal } from 'lucide-vue-next'

const emit = defineEmits<{
  (e: 'novo'): void
  (e: 'filtro'): void
  (e: 'importar'): void
  (e: 'exportar'): void
}>()
</script>

<template>
  <div class="flex justify-between">
    <div class="flex gap-2">
      <PrimaryButton @click="emit('filtro')">
        <SlidersHorizontal :size="16" :stroke-width="3" />
      </PrimaryButton>
    </div>

    <div class="flex gap-2">
      <PrimaryButton @click="emit('importar')">
        <ArrowUpFromLineIcon :size="16" :stroke-width="3" />
      </PrimaryButton>

      <PrimaryButton @click="emit('exportar')">
        <ArrowDownToLineIcon :size="16" :stroke-width="3" />
      </PrimaryButton>
      <PrimaryButton @click="emit('novo')">
        <Plus :size="16" :stroke-width="3" />
      </PrimaryButton>
    </div>
  </div>
</template>
