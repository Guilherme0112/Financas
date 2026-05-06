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
    class="flex items-center rounded-full font-semibold transition-all"
    :class="[
      isBom ? 'bg-green-200 text-green-700' : 'bg-red-200 text-red-700',
      // Mobile primeiro (Padrão)
      'text-[10px] px-2 py-0.5 gap-0.5', 
      // Telas médias em diante (Tablets e Desktop)
      'md:text-xs md:px-3 md:py-1 md:gap-1'
    ]"
  >
    <span>{{ positivo ? '▲' : '▼' }}</span>
    <span>{{ Math.abs(valor).toFixed(2) }}%</span>
    
    <!-- Esconde o texto longo em telas MUITO pequenas (opcional, ajuda muito no layout) -->
    <span class="opacity-70 ml-0.5 md:ml-1 hidden sm:inline">
      vs mês anterior
    </span>
  </div>
</template>