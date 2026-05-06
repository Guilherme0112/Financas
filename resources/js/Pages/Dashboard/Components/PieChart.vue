<script setup lang="ts">
import { useGoogleCharts } from '@/hooks/useGoogleCharts'
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import SemRegistro from '../Partials/SemRegistro.vue'

const props = defineProps<{
  chartId: string
  title: string
  rows: Array<[string, number]>
  colors: string[]
  color: string
}>()

const { load } = useGoogleCharts()

const hasData = computed(() => props.rows?.length > 0)

// Referência para a div do gráfico em vez de usar getElementById
const chartContainer = ref<HTMLElement | null>(null)

// Guardamos a instância do gráfico e os dados
let chartInstance: any = null;
let chartData: any = null;
let resizeObserver: ResizeObserver | null = null;

const getOptions = () => {
  const isMobile = window.innerWidth < 768;

  return {
    title: props.title,  
    colors: props.colors,
    backgroundColor: 'transparent',
    fontName: 'Figtree, sans-serif',
    
    width: '100%',
    height: '100%',

    titleTextStyle: {
      color: props.colors[0] ?? "#059669",
      fontSize: isMobile ? 14 : 16,
      bold: true,
    },

    chartArea: {
      width: isMobile ? '100%' : '90%',
      height: isMobile ? '80%' : '75%',
      top: 40,
    },

    legend: isMobile 
      ? 'none' 
      : {
          position: 'bottom',
          alignment: 'center',
          textStyle: {
            color: props.color,
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
}

const initChart = async () => {
  if (!hasData.value || !chartContainer.value) return

  await load()

  chartData = window.google.visualization.arrayToDataTable([
    ['Categoria', 'Valor'],
    ...props.rows,
  ])

  if (!chartInstance) {
    chartInstance = new window.google.visualization.PieChart(chartContainer.value)
  }

  drawChart()
}

// Essa função só aplica os dados e opções na instância existente
const drawChart = () => {
  if (chartInstance && chartData) {
    chartInstance.draw(chartData, getOptions())
  }
}

onMounted(() => {
  initChart()

  // O ResizeObserver "espiona" a div. 
  // Qualquer mudança de tamanho (seja virar o celular, abrir um menu lateral, etc), 
  // ele manda o Google Charts se redesenhar no tamanho exato da div.
  resizeObserver = new ResizeObserver(() => {
    // Usamos requestAnimationFrame para garantir que a renderização do DOM do Vue terminou
    requestAnimationFrame(() => {
      drawChart()
    })
  })

  if (chartContainer.value) {
    resizeObserver.observe(chartContainer.value)
  }
})

onUnmounted(() => {
  // Limpa o observador para não causar vazamento de memória
  if (resizeObserver) {
    resizeObserver.disconnect()
  }
})

watch(() => props.rows, () => {
  // Se os dados mudarem, precisamos recriar o DataTable e desenhar de novo
  if (window.google && window.google.visualization) {
    chartData = window.google.visualization.arrayToDataTable([
      ['Categoria', 'Valor'],
      ...props.rows,
    ])
    drawChart()
  }
}, { deep: true })
</script>

<template>
  <div v-if="hasData" class="w-full">
     <div 
      ref="chartContainer" 
      :id="chartId" 
      class="w-full h-[280px] md:h-[350px] lg:h-[400px] overflow-hidden"
    ></div>
  </div>
  <div v-else>
    <SemRegistro />
  </div>
</template>