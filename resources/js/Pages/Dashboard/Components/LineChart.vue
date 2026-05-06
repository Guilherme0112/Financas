<script setup lang="ts">
import { useGoogleCharts } from '@/hooks/useGoogleCharts';
import { computed, onMounted, onUnmounted, ref, watch, nextTick } from 'vue'
import SemRegistro from '../Partials/SemRegistro.vue';

declare global {
  interface Window {
    google: any
  }
}

const props = defineProps<{
  rows: any[]
}>()

const { load } = useGoogleCharts();

const hasData = computed(() => props.rows && props.rows.length > 0)

// 1. Referência do DOM e variáveis de controle
const chartContainer = ref<HTMLElement | null>(null)
let chartInstance: any = null;
let chartData: any = null;
let resizeObserver: ResizeObserver | null = null;

// 2. Opções responsivas
const getOptions = () => {
  const isMobile = window.innerWidth < 768;

  return {
    backgroundColor: 'transparent',
    fontName: 'Figtree, sans-serif',
    width: '100%',
    height: '100%',
    
    chartArea: { 
      width: isMobile ? '85%' : '90%', 
      height: isMobile ? '65%' : '70%',
      top: 40 // Garante espaço para o título respirar
    },
    
    legend: { 
      position: 'bottom', 
      textStyle: { 
        color: '#64748b', 
        fontSize: isMobile ? 10 : 12 
      } 
    },
    
    animation: { startup: true, duration: 800, easing: 'out' },
    
    title: 'Evolução Mensal de Gastos',
    titleTextStyle: { 
      color: '#991b1b', 
      fontSize: isMobile ? 14 : 16, 
      bold: true 
    },
    
    colors: ['#dc2626'],
    curveType: 'function',
    lineWidth: isMobile ? 3 : 4, // Linha um pouco mais delicada no celular
    pointsVisible: true, // Mantido do seu original!
  }
}

const initChart = async () => {
  if (!hasData.value || !chartContainer.value) return;

  await load();

  // Mantive a sua lógica original de mapeamento de Datas e Valores
  chartData = window.google.visualization.arrayToDataTable([
    [{ label: 'Mês', type: 'date' }, 'Gastos'],
    ...props.rows.map((item: any) => [new Date(item[0]), Number(item[2])]),
  ]);

  if (!chartInstance) {
    chartInstance = new window.google.visualization.LineChart(chartContainer.value);
  }

  drawChart();
}

const drawChart = () => {
  if (chartInstance && chartData) {
    chartInstance.draw(chartData, getOptions());
  }
}

onMounted(() => {
  initChart();

  // 3. Monitoramento de tela para redesenhar o SVG sem perder a animação
  resizeObserver = new ResizeObserver(() => {
    requestAnimationFrame(() => {
      drawChart();
    });
  });

  if (chartContainer.value) {
    resizeObserver.observe(chartContainer.value);
  }
});

onUnmounted(() => {
  if (resizeObserver) {
    resizeObserver.disconnect();
  }
});

// 4. Watcher seguro para não tentar desenhar em divs destruídas
watch(() => props.rows, async () => {
  await nextTick();
  
  if (!hasData.value || !chartContainer.value) return;

  if (window.google && window.google.visualization) {
    chartData = window.google.visualization.arrayToDataTable([
      [{ label: 'Mês', type: 'date' }, 'Gastos'],
      ...props.rows.map((item: any) => [new Date(item[0]), Number(item[2])]),
    ]);
    
    chartInstance = new window.google.visualization.LineChart(chartContainer.value);
    drawChart();
  }
}, { deep: true });
</script>

<template>
  <div v-if="hasData" class="w-full">
    <!-- Adicionado ref e classes fluídas do Tailwind -->
    <div 
      ref="chartContainer" 
      id="lineChart" 
      class="w-full h-[280px] md:h-[350px] lg:h-[400px] overflow-hidden"
    ></div>
  </div>
  <div v-else>
    <SemRegistro />
  </div>
</template>