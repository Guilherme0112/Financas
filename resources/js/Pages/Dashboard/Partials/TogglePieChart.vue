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
  <div class="bg-white p-4 py-6 rounded-2xl shadow-sm border border-slate-100">

    <!-- Header -->
    <div class="flex justify-between items-center mb-4">

      <!-- Toggle -->
      <div class="flex bg-slate-100 rounded-xl p-1 text-sm font-medium">
        <button v-for="option in options" :key="option.key" @click="selected = option.key" :class="[
          'px-4 py-1 rounded-lg transition',
          selected === option.key
            ? option.color === 'red'
              ? 'bg-red-500 text-white'
              : 'bg-green-700 text-white'
            : 'text-slate-600'
        ]">
          {{ option.label }}
        </button>
      </div>

      <!-- Percentual -->
      <PorcentualMesAnterior :valor="activeOption.percentual" :tipo="activeOption.tipo" />
    </div>

    <!-- Chart -->
    <div class="w-full flex justify-around">
      <PieChart chart-id="pieChartToggle" :title="activeOption.title" :rows="activeOption.rows"
        :colors="activeOption.colors" :color="activeOption.colors[0]" />

      <BarChart :gastos="activeOption.rows" :color="activeOption.colors[0]" />
    </div>
  </div>
</template>
