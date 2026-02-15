<script setup lang="ts">
import { useGoogleCharts } from '@/hooks/useGoogleCharts'
import { computed, onMounted, watch } from 'vue'
import SemRegistro from '../Partials/SemRegistro.vue';

const props = defineProps<{
  gastos: [string, number][],
  color: string
}>();

const { load } = useGoogleCharts();

const options = computed(() => ({
  backgroundColor: 'transparent',
  fontName: 'Figtree, sans-serif',
  legend: { position: 'none' },
  chartArea: { width: '80%', height: '70%' },
  colors: [props.color],
  animation: { startup: true, duration: 800, easing: 'out' },
  title: 'Gastos por categoria (Este mês)',
  titleTextStyle: { color: props.color, fontSize: 16, bold: true },
}));

const hasData = computed(() => props.gastos && props.gastos.length > 0);

const drawChart = async () => {
  if (!hasData.value) return;

  await load()

  const data = window.google.visualization.arrayToDataTable([
    ['Categoria', 'Valor'],
    ...props.gastos,
  ])

  new window.google.visualization.BarChart(
    document.getElementById('categoryBarChart')
  ).draw(data, options.value);
}

onMounted(drawChart)
watch(() => props.gastos, drawChart, { deep: true })
</script>
<template>
  <div v-if="hasData">
    <div id="categoryBarChart" class="w-full h-[350px]"></div>
  </div>
  <div v-else>
    <SemRegistro />
  </div>
</template>
