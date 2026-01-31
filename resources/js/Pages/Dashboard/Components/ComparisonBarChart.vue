<script setup lang="ts">
import { useGoogleCharts } from '@/hooks/useGoogleCharts';
import { onMounted, watch } from 'vue'

declare global {
  interface Window {
    google: any
  }
}

const props = defineProps<{
  rows: any[]
}>()

const { load } = useGoogleCharts();

const baseOptions = {
  backgroundColor: 'transparent',
  fontName: 'Figtree, sans-serif',
  chartArea: { width: '85%', height: '70%' },
  legend: { position: 'bottom', textStyle: { color: '#64748b', fontSize: 12 } },
  animation: { startup: true, duration: 800, easing: 'out' },
  title: 'Comparativo: Entradas vs Saídas',
  titleTextStyle: { color: '#064e3b', fontSize: 16, bold: true },
  colors: ['#059669', '#dc2626'],
  bar: { groupWidth: '70%' },
}

const drawChart = async() => {
  await load();

  const data = window.google.visualization.arrayToDataTable([
    ['Mês', 'Receitas', 'Despesas'],
    ...props.rows,
  ])

  new window.google.visualization.ColumnChart(
    document.getElementById('comparisonBarChart')
  ).draw(data, baseOptions)
}

onMounted(drawChart)
watch(() => props.rows, drawChart)
</script>

<template>
  <div id="comparisonBarChart" class="w-full h-[350px]"></div>
</template>
