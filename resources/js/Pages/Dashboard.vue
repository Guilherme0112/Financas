<script setup lang="ts">
import { onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import FinanceCard from '@/Components/FinanceCard.vue';

declare global {
  interface Window {
    google: any;
  }
}

const page = usePage();
const dashboard = page.props.dashboard as any;

// Paletas de Cores Profissionais
const redPalette = ['#dc2626', '#991b1b', '#ef4444', '#7f1d1d', '#b91c1c', '#f87171'];
const greenPalette = ['#059669', '#064e3b', '#10b981', '#065f46', '#34d399', '#022c22'];

const loadCharts = () => {
  const script = document.createElement('script');
  script.src = 'https://www.gstatic.com/charts/loader.js';
  script.onload = () => {
    window.google.charts.load('current', { packages: ['corechart'] });
    window.google.charts.setOnLoadCallback(drawCharts);
  };
  document.head.appendChild(script);
};

const drawCharts = () => {
  drawPie();
  drawLine();
  drawComparisonBar();
  drawCurveChart();
};

const baseOptions = {
  backgroundColor: 'transparent',
  fontName: 'Inter, sans-serif',
  chartArea: { width: '85%', height: '70%' },
  legend: { position: 'bottom', textStyle: { color: '#64748b', fontSize: 12 } },
  animation: { startup: true, duration: 800, easing: 'out' },
};

const drawPie = () => {
  const rowsGastos = dashboard.graficos.pizza.gastos.map((item: any) => [item.categoria, Number(item.total)]);
  const dataGastos = window.google.visualization.arrayToDataTable([['Categoria', 'Valor'], ...rowsGastos]);

  new window.google.visualization.PieChart(document.getElementById('pieChart')).draw(dataGastos, {
    ...baseOptions,
    title: 'Distribuição dos Gastos',
    titleTextStyle: { color: '#991b1b', fontSize: 16, bold: true },
    colors: redPalette,
    pieHole: 0,
  });

  const rowsReceitas = dashboard.graficos.pizza.receitas.map((item: any) => [item.categoria, Number(item.total)]);
  const dataReceitas = window.google.visualization.arrayToDataTable([['Categoria', 'Valor'], ...rowsReceitas]);

  new window.google.visualization.PieChart(document.getElementById('pieChart2')).draw(dataReceitas, {
    ...baseOptions,
    title: 'Distribuição das Receitas',
    titleTextStyle: { color: '#064e3b', fontSize: 16, bold: true },
    colors: greenPalette,
    pieHole: 0,
  });
};

const drawLine = () => {
  const rows = dashboard.graficos.linha.map((item: any) => [new Date(item.mes), Number(item.total)]);
  const data = window.google.visualization.arrayToDataTable([
    [{ label: 'Mês', type: 'date' }, 'Gastos'],
    ...rows,
  ]);

  new window.google.visualization.LineChart(document.getElementById('lineChart')).draw(data, {
    ...baseOptions,
    title: 'Evolução Mensal de Gastos',
    titleTextStyle: { color: '#991b1b', fontSize: 16, bold: true },
    colors: ['#dc2626'],
    curveType: 'function',
    lineWidth: 4,
    pointsVisible: true,
  });
};

const drawComparisonBar = () => {
  const rows = dashboard.graficos.mensal;
  const dataArray = [['Mês', 'Receitas', 'Despesas'], ...rows];
  const data = window.google.visualization.arrayToDataTable(dataArray);
  new window.google.visualization.ColumnChart(document.getElementById('comparisonBarChart')).draw(data, {
    ...baseOptions,
    title: 'Comparativo: Entradas vs Saídas',
    titleTextStyle: { color: '#064e3b', fontSize: 16, bold: true },
    colors: ['#059669', '#dc2626'], // Verde vs Vermelho
    bar: { groupWidth: '70%' },
  });
};

const drawCurveChart = () => {
  const rows = dashboard.graficos.mensal;
  const dataArray = [['Mês', 'Receitas', 'Despesas'], ...rows];
  const data = window.google.visualization.arrayToDataTable(dataArray);
  new window.google.visualization.LineChart(document.getElementById('curve_chart')).draw(data, {
    ...baseOptions,
    title: 'Performance Histórica (Curvado)',
    titleTextStyle: { color: '#064e3b', fontSize: 16, bold: true },
    curveType: 'function',
    colors: ['#059669', '#dc2626'],
    lineWidth: 4,
  });
};

onMounted(() => {
  loadCharts();
});
</script>

<template>

  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-bold leading-tight text-slate-800">
        Dashboard Financeiro
      </h2>
    </template>

    <div class="py-12 bg-slate-50 min-h-screen">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <FinanceCard title="Receitas do Mês" :value="dashboard.cards.entradas" type="positive" />
          <FinanceCard title="Gastos do Mês" :value="dashboard.cards.saidas" type="negative" />
          <FinanceCard title="Economia" :value="dashboard.cards.total" type="positive" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div id="pieChart" class="w-full h-[350px]"></div>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div id="pieChart2" class="w-full h-[350px]"></div>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-6">

          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div id="curve_chart" class="w-full h-[350px]"></div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div id="comparisonBarChart" class="w-full h-[350px]"></div>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 w-full">
            <div id="lineChart" class="w-full h-[350px]"></div>
          </div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>