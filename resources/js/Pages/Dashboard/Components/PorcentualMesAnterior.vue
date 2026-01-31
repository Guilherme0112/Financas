<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  valor: number
  tipo: 'entrada' | 'saida'
}>()

const positivo = computed(() => props.valor >= 0)

const isBom = computed(() => {
  if (props.tipo === 'entrada') {
    return positivo.value
  }

  return !positivo.value
})
</script>

<template>
  <div
    class="flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold"
    :class="isBom ? 'bg-green-200 text-green-700' : 'bg-red-200 text-red-700'"
  >
    <span>{{ positivo ? '▲' : '▼' }}</span>
    {{ Math.abs(valor).toFixed(2) }}%
    <span class="opacity-70 ml-1">vs mês anterior</span>
  </div>
</template>