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
}>();

const { load } = useGoogleCharts();

const baseOptions = {
  backgroundColor: 'transparent',
  fontName: 'Figtree, sans-serif',
  chartArea: { width: '85%', height: '70%' },
  legend: { position: 'bottom', textStyle: { color: '#64748b', fontSize: 12 } },
  animation: { startup: true, duration: 800, easing: 'out' },
  curveType: 'function',
  colors: ['#059669', '#dc2626'],
  lineWidth: 4,
}

const drawChart = async() => {
  await load();

  const data = window.google.visualization.arrayToDataTable([
    ['Mês', 'Receitas', 'Despesas'],
    ...props.rows,
  ])

  new window.google.visualization.LineChart(
    document.getElementById('curveChart')
  ).draw(data, baseOptions)
}

onMounted(drawChart)
watch(() => props.rows, drawChart)
</script>

<template>
  <div id="curveChart" class="w-full h-[350px]"></div>
</template>
