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

// 1. Referência para a div do gráfico
const chartContainer = ref<HTMLElement | null>(null)

// 2. Guardamos a instância e o observador para limpar depois
let chartInstance: any = null;
let chartData: any = null;
let resizeObserver: ResizeObserver | null = null;

// 3. Opções dinâmicas calculadas de acordo com a tela
const getOptions = () => {
  const isMobile = window.innerWidth < 768;

  return {
    backgroundColor: 'transparent',
    fontName: 'Figtree, sans-serif',
    width: '100%',
    height: '100%',
    
    chartArea: { 
      // Em mobile deixamos 80% para garantir que os valores no eixo Y caibam
      width: isMobile ? '80%' : '85%', 
      height: isMobile ? '65%' : '75%',
      top: 20
    },
    
    legend: { 
      position: 'bottom', 
      textStyle: { 
        color: '#64748b', 
        fontSize: isMobile ? 10 : 12 // Fonte um pouco menor no celular
      } 
    },
    
    animation: { startup: true, duration: 800, easing: 'out' },
    curveType: 'function',
    colors: ['#059669', '#dc2626', '#3b82f6'],
    lineWidth: isMobile ? 3 : 4, // Linha um pouco mais fina no mobile
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

  // 4. Espiona mudanças de tamanho na div e manda redesenhar
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

// 5. Watcher blindado com nextTick para recriar se o v-if for alterado
watch(() => props.rows, async () => {
  await nextTick();
  
  if (!hasData.value || !chartContainer.value) return;

  if (window.google && window.google.visualization) {
    chartData = window.google.visualization.arrayToDataTable([
      ['Mês', 'Receitas', 'Despesas', 'Metas'],
      ...props.rows,
    ]);
    
    // Força uma nova instância caso a aba/componente pai tenha re-renderizado
    chartInstance = new window.google.visualization.LineChart(chartContainer.value);
    drawChart();
  }
}, { deep: true });
</script>

<template>
  <div v-if="hasData" class="w-full">
    <!-- Adicionado o ref e as classes de altura flexível -->
    <div 
      ref="chartContainer" 
      id="curveChart" 
      class="w-full h-[280px] md:h-[350px] lg:h-[400px] overflow-hidden"
    ></div>
  </div>
  <div v-else>
    <SemRegistro />
  </div>
</template>