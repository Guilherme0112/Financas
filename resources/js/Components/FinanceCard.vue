<script setup lang="ts">
import { formatarDinheiro } from '@/utils/helpers'
import { computed } from 'vue'

interface Props {
  title: string
  value: number | string
  type?: 'positive' | 'negative' | 'neutral'
}

const props = withDefaults(defineProps<Props>(), {
  type: 'neutral',
})

// Mapeamento de cores para um visual mais "Ruby"
const styles = {
  positive: 'text-emerald-500 bg-emerald-500/10 border-emerald-500/20',
  negative: 'text-rose-500 bg-rose-500/10 border-rose-500/20',
  neutral: 'text-slate-400 bg-slate-500/10 border-slate-500/20'
}

const iconColors = {
  positive: 'bg-emerald-500',
  negative: 'bg-rose-500',
  neutral: 'bg-slate-500'
}
</script>

<template>
  <div class="group relative overflow-hidden rounded-3xl border border-slate-100 bg-white p-1 shadow-lg">
    <div class="relative z-10 p-6">
      <header class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span :class="['h-2 w-2 rounded-full animate-pulse', iconColors[props.type]]"></span>
          <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400">
            {{ title }}
          </h3>
        </div>
        
        <slot name="trend" />
      </header>

      <main class="mt-5">
        <div class="flex items-baseline gap-1">
          <span class="text-sm font-medium text-slate-400">R$</span>
          <p class="text-4xl font-black tabular-nums tracking-tighter text-slate-800">
            {{ Number(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
          </p>
        </div>
      </main>
    </div>
  </div>
</template>