<script setup lang="ts">
import { ref, computed } from 'vue';
import PorcentualMesAnterior from '../Components/PorcentualMesAnterior.vue';
import PieChart from '../Components/PieChart.vue';
import BarChart from '../Components/BarChart.vue';

interface ChartOption {
  key: string
  label: string
  title: string
  rows: any[]
  colors: string[]
  color: string
  percentual: number
  tipo: 'entrada' | 'saida'
}

const props = defineProps<{
  options: ChartOption[]
  defaultKey?: string
}>()

const selected = ref(props.defaultKey ?? props.options[0].key)

const activeOption = computed(() =>
  props.options.find(o => o.key === selected.value)!
)
</script>

<template>
  <div class="bg-white p-4 py-6 rounded-2xl shadow-lg border border-slate-100">

    <!-- Header Responsivo -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-0 mb-6">

      <!-- Toggle -->
      <div class="flex bg-slate-100 rounded-xl p-1 text-sm font-medium w-full sm:w-auto">
        <button v-for="option in options" :key="option.key" @click="selected = option.key" :class="[
          'px-4 py-1.5 sm:py-1 rounded-lg transition flex-1 sm:flex-none text-center',
          selected === option.key
            ? option.color === 'red'
              ? 'bg-red-500 text-white'
              : 'bg-emerald-700 text-white'
            : 'text-slate-600'
        ]">
          {{ option.label }}
        </button>
      </div>

      <!-- Percentual -->
      <PorcentualMesAnterior :valor="activeOption.percentual" :tipo="activeOption.tipo" />
    </div>

    <!-- Charts -->
    <!-- Mudamos de flex para grid. 1 coluna no mobile, 2 colunas a partir de telas lg (1024px) -->
    <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
      <PieChart 
        :key="'pie-' + activeOption.key"
        chart-id="pieChartToggle" 
        :title="activeOption.title" 
        :rows="activeOption.rows"
        :colors="activeOption.colors" 
        :color="activeOption.colors[0]" 
      />

      <BarChart 
        :key="'bar-' + activeOption.key"
        :gastos="activeOption.rows" 
        :color="activeOption.colors[0]" 
      />
    </div>
    
  </div>
</template>