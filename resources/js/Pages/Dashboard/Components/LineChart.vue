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
  title: 'Evolução Mensal de Gastos',
  titleTextStyle: { color: '#991b1b', fontSize: 16, bold: true },
  colors: ['#dc2626'],
  curveType: 'function',
  lineWidth: 4,
  pointsVisible: true,
}

const drawChart = async() => {
  await load();

  const data = window.google.visualization.arrayToDataTable([
    [{ label: 'Mês', type: 'date' }, 'Gastos'],
    ...props.rows.map((item: any) => [new Date(item[0]), Number(item[2])]),
  ])

  new window.google.visualization.LineChart(
    document.getElementById('lineChart')
  ).draw(data, baseOptions)
}

onMounted(drawChart)
watch(() => props.rows, drawChart)
</script>

<template>
  <div id="lineChart" class="w-full h-[350px]"></div>
</template>
