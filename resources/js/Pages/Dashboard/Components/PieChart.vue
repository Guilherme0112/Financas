<script setup lang="ts">
import { useGoogleCharts } from '@/hooks/useGoogleCharts';
import { onMounted, watch } from 'vue'

declare global {
  interface Window {
    google: any
  }
}

const props = defineProps<{
  chartId: string
  title: string
  rows: Array<[string, number]>
  colors: string[]
}>()

const { load } = useGoogleCharts();

const baseOptions = {
  backgroundColor: 'transparent',
  fontName: 'Figtree, sans-serif',
  titleTextStyle: { color: props.colors[0] ?? "#059669", fontSize: 16, bold: true },
  chartArea: { width: '85%', height: '70%' },
  legend: { position: 'bottom', textStyle: { color: '#64748b', fontSize: 12 } },
  animation: { startup: true, duration: 800, easing: 'out' },
  pieHole: 0,
}

const drawChart = async () => {
  await load()

  const data = window.google.visualization.arrayToDataTable([
    ['Categoria', 'Valor'],
    ...props.rows,
  ])

  new window.google.visualization.PieChart(
    document.getElementById(props.chartId)
  ).draw(data, {
    ...baseOptions,
    title: props.title,
    margin: "10px",
    colors: props.colors,
  })
}

onMounted(drawChart)
watch(() => props.rows, drawChart)
</script>

<template>
  <div :id="chartId" class="w-full h-[350px]"></div>
</template>
