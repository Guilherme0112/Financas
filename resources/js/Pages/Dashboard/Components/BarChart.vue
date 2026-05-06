<script setup lang="ts">
import { useGoogleCharts } from '@/hooks/useGoogleCharts'
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import SemRegistro from '../Partials/SemRegistro.vue';

const props = defineProps<{
  gastos: [string, number][],
  color: string
}>();

const { load } = useGoogleCharts();

const hasData = computed(() => props.gastos && props.gastos.length > 0);

// 1. Referência para a div do gráfico
const chartContainer = ref<HTMLElement | null>(null)

// 2. Variáveis para guardar a instância e o observador
let chartInstance: any = null;
let chartData: any = null;
let resizeObserver: ResizeObserver | null = null;

// 3. Transformado em função para calcular na hora exata do desenho
const getOptions = () => {
  const isMobile = window.innerWidth < 768;

  return {
    backgroundColor: 'transparent',
    fontName: 'Figtree, sans-serif',
    width: '100%',
    height: '100%',
    
    legend: { position: 'none' }, 
    
    chartArea: {
      width: isMobile ? '100%' : '90%',
      height: isMobile ? '80%' : '75%',
      top: 40,
    },
    
    colors: [props.color],
    animation: { startup: true, duration: 800, easing: 'out' },
    
    title: 'Gastos por categoria (Este mês)',
    titleTextStyle: { 
      color: props.color, 
      fontSize: isMobile ? 14 : 16, 
      bold: true 
    },
  }
}

const initChart = async () => {
  if (!hasData.value || !chartContainer.value) return;

  await load()

  chartData = window.google.visualization.arrayToDataTable([
    ['Categoria', 'Valor'],
    ...props.gastos,
  ])

  // Instancia apenas uma vez
  if (!chartInstance) {
    chartInstance = new window.google.visualization.BarChart(chartContainer.value)
  }

  drawChart()
}

const drawChart = () => {
  if (chartInstance && chartData) {
    chartInstance.draw(chartData, getOptions())
  }
}

onMounted(() => {
  initChart()

  // 4. Implementa o ResizeObserver igual fizemos no gráfico de pizza
  resizeObserver = new ResizeObserver(() => {
    requestAnimationFrame(() => {
      drawChart()
    })
  })

  if (chartContainer.value) {
    resizeObserver.observe(chartContainer.value)
  }
})

onUnmounted(() => {
  if (resizeObserver) {
    resizeObserver.disconnect()
  }
})

watch(() => props.gastos, () => {
  if (window.google && window.google.visualization) {
    chartData = window.google.visualization.arrayToDataTable([
      ['Categoria', 'Valor'],
      ...props.gastos,
    ])
    drawChart()
  }
}, { deep: true })
</script>

<template>
  <div v-if="hasData" class="w-full">
    <div 
      ref="chartContainer" 
      id="categoryBarChart" 
      class="w-full h-[280px] md:h-[350px] lg:h-[400px] overflow-hidden"
    ></div>
  </div>
  <div v-else>
    <SemRegistro />
  </div>
</template>