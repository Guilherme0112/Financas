<script setup lang="ts">
import { onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import FinanceCard from '@/Components/FinanceCard.vue';
import { usePage } from '@inertiajs/vue3';

declare global {
  interface Window {
    google: any;
  }
}

const page = usePage();
const dashboard = page.props.dashboard as any;

const loadCharts = () => {
  const script = document.createElement('script')
  script.src = 'https://www.gstatic.com/charts/loader.js'
  script.onload = initCharts
  document.head.appendChild(script)
}

const initCharts = () => {
  window.google.charts.load('current', { packages: ['corechart'] })
  window.google.charts.setOnLoadCallback(drawCharts)
}

const drawCharts = () => {
  drawPie()
  drawLine()
  drawBar()
}

const baseOptions = {
  backgroundColor: 'transparent',
  titleTextStyle: {
    color: '#166534',
    fontSize: 16,
    bold: true,
  },
  legend: {
    textStyle: { color: '#166534' },
  },
  chartArea: {
    width: '85%',
    height: '70%',
  },
}

const drawPie = () => {
  const rows = dashboard.graficos.pizza.map((item: any) => [
    item.categoria,
    Number(item.total),
  ])

  const data = window.google.visualization.arrayToDataTable([
    ['Categoria', 'Valor'],
    ...rows,
  ])

  new window.google.visualization.PieChart(
    document.getElementById('pieChart')
  ).draw(data, {
    ...baseOptions,
    title: 'Distribuição dos Gastos',
    colors: ['#16a34a', '#4ade80', '#22c55e', '#86efac', '#15803d', '#bbf7d0'],
    pieHole: 0.55,
  })
}

const drawLine = () => {
  const rows = dashboard.graficos.linha.map((item: any) => {
    const date = new Date(item.mes);
    return [date, Number(item.total)]
  })

  const data = window.google.visualization.arrayToDataTable([
    [{ label: 'Mês', type: 'date' }, 'Gastos'],
    ...rows,
  ])

  new window.google.visualization.LineChart(
    document.getElementById('lineChart')
  ).draw(data, {
    ...baseOptions,
    title: 'Evolução Mensal de Gastos',
    colors: ['#16a34a'],
    curveType: 'function',
  })
}

const drawBar = () => {
  const data = window.google.visualization.arrayToDataTable([
    ['Tipo', 'Valor'],
    ['Fixos', Number(dashboard.graficos.barra.fixos)],
    ['Variáveis', Number(dashboard.graficos.barra.variaveis)],
  ])

  new window.google.visualization.ColumnChart(
    document.getElementById('barChart')
  ).draw(data, {
    ...baseOptions,
    title: 'Fixos vs Variáveis',
    colors: ['#16a34a', '#4ade80'],
    bar: { groupWidth: '45%' },
  })
}


onMounted(() => {
  loadCharts()
})
</script>

<template>

  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-green-800">
        Dashboard
      </h2>
    </template>

    <div class="py-12 bg-green-50 min-h-screen">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">

        <!-- Cards resumo -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <FinanceCard title="Entradas" :value="dashboard.cards.entradas"
            :type="dashboard.cards.entradas >= 0 ? 'positive' : 'negative'" />

          <FinanceCard title="Gastos do Mês" :value="dashboard.cards.saidas" type="negative" />

          <FinanceCard title="Economia" :value="dashboard.cards.total" type="positive" />

        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-green-100">
            <div id="pieChart" class="w-full h-[350px]"></div>
          </div>

          <div class="bg-white p-6 rounded-2xl shadow-sm border border-green-100">
            <div id="lineChart" class="w-full h-[350px]"></div>
          </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-green-100">
          <div id="barChart" class="w-full h-[350px]"></div>
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
