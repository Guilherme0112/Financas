<script setup lang="ts">
import { useGoogleCharts } from '@/hooks/useGoogleCharts'
import { computed, onMounted, watch } from 'vue'
import SemRegistro from '../Partials/SemRegistro.vue'

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
  color: string
}>()

const { load } = useGoogleCharts()

const hasData = computed(() => props.rows?.length > 0)

const baseOptions = {
  backgroundColor: 'transparent',
  fontName: 'Figtree, sans-serif',

  titleTextStyle: {
    color: props.colors[0] ?? "#059669",
    fontSize: 16,
    bold: true,
  },

  chartArea: {
    width: '90%',
    height: '80%',
  },

  legend: {
    position: 'bottom',
    alignment: 'center',
    textStyle: {
      color: '#475569',
      fontSize: 12,
    },
  },

  pieHole: 0,
  pieSliceBorderColor: 'transparent',

  tooltip: {
    text: 'value',
    textStyle: { fontSize: 13 },
    showColorCode: true,
  },

  animation: {
    startup: true,
    duration: 700,
    easing: 'out',
  },
}

const drawChart = async () => {
  if (!hasData.value) return

  await load()

  const data = window.google.visualization.arrayToDataTable([
    ['Categoria', 'Valor'],
    ...props.rows,
  ])

  const chart = new window.google.visualization.PieChart(
    document.getElementById(props.chartId)
  )

  chart.draw(data, {
    ...baseOptions,
    title: props.title,
    colors: props.colors,
  })
}

onMounted(drawChart)
watch(() => props.rows, drawChart, { deep: true })
</script>
<template>
  <div v-if="hasData">
    <div :id="chartId" class="w-full h-[350px]"></div>
  </div>
  <div v-else>
    <SemRegistro :color="color" />
  </div>
</template>
