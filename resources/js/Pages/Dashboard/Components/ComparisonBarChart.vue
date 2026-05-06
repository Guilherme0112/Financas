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
}>();

const { load } = useGoogleCharts();

const hasData = computed(() => props.rows && props.rows.length > 0)

// 1. Referência para a div do gráfico no DOM
const chartContainer = ref<HTMLElement | null>(null)

// 2. Variáveis para armazenar a instância e garantir a limpeza da memória
let chartInstance: any = null;
let chartData: any = null;
let resizeObserver: ResizeObserver | null = null;

// 3. Função que calcula as opções do gráfico instantaneamente ao desenhar
const getOptions = () => {
  const isMobile = window.innerWidth < 768;

  return {
    backgroundColor: 'transparent',
    fontName: 'Figtree, sans-serif',
    width: '100%',
    height: '100%',
    
    chartArea: { 
      // Abre um pouco mais de espaço no mobile para os eixos não cortarem
      width: isMobile ? '80%' : '85%', 
      height: isMobile ? '65%' : '70%',
      top: 40 // Espaço para o título
    },
    
    legend: { 
      position: 'bottom', 
      textStyle: { 
        color: '#64748b', 
        fontSize: isMobile ? 10 : 12 
      } 
    },
    
    animation: { startup: true, duration: 800, easing: 'out' },
    title: 'Comparativo: Entradas vs Saídas',
    titleTextStyle: { 
      color: '#064e3b', 
      fontSize: isMobile ? 14 : 16, 
      bold: true 
    },
    
    colors: ['#059669', '#dc2626', '#3b82f6'],
    // Deixa as barras ligeiramente mais largas no mobile para melhor visibilidade
    bar: { groupWidth: isMobile ? '80%' : '70%' }, 
  }
}

const initChart = async () => {
  if (!hasData.value || !chartContainer.value) return;

  await load();

  chartData = window.google.visualization.arrayToDataTable([
    ['Mês', 'Receitas', 'Despesas', 'Metas'],
    ...props.rows,
  ]);

  if (!chartInstance) {
    chartInstance = new window.google.visualization.ColumnChart(chartContainer.value);
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

  // 4. Observador de redimensionamento em tempo real
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

// 5. Atualização segura caso os dados mudem ou o v-if recrie a tela
watch(() => props.rows, async () => {
  await nextTick();
  
  if (!hasData.value || !chartContainer.value) return;

  if (window.google && window.google.visualization) {
    chartData = window.google.visualization.arrayToDataTable([
      ['Mês', 'Receitas', 'Despesas', 'Metas'],
      ...props.rows,
    ]);
    
    chartInstance = new window.google.visualization.ColumnChart(chartContainer.value);
    drawChart();
  }
}, { deep: true });
</script>

<template>
  <div v-if="hasData" class="w-full">
    <!-- Tamanhos graduais via Tailwind e container ref -->
    <div 
      ref="chartContainer" 
      id="comparisonBarChart" 
      class="w-full h-[280px] md:h-[350px] lg:h-[400px] overflow-hidden"
    ></div>
  </div>
  <div v-else>
    <SemRegistro />
  </div>
</template>