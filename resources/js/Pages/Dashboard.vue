<script setup lang="ts">
import { onMounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import FinanceCard from '@/Components/FinanceCard.vue'

declare global {
  interface Window {
    google: any
  }
}

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
  const data = window.google.visualization.arrayToDataTable([
    ['Categoria', 'Valor'],
    ['Aluguel', 1200],
    ['Mercado', 850],
    ['Lazer', 420],
    ['Transporte', 300],
  ])

  const chart = new window.google.visualization.PieChart(
    document.getElementById('pieChart')
  )

  chart.draw(data, {
    ...baseOptions,
    title: 'Distribuição dos Gastos',
    pieHole: 0.55,
    colors: ['#16a34a', '#22c55e', '#4ade80', '#86efac'],
  })
}

const drawLine = () => {
  const data = window.google.visualization.arrayToDataTable([
    ['Mês', 'Gastos'],
    ['Jan', 1200],
    ['Fev', 1500],
    ['Mar', 1100],
    ['Abr', 1800],
    ['Mai', 1700],
  ])

  const chart = new window.google.visualization.LineChart(
    document.getElementById('lineChart')
  )

  chart.draw(data, {
    ...baseOptions,
    title: 'Evolução Mensal de Gastos',
    curveType: 'function',
    colors: ['#16a34a'],
    lineWidth: 3,
    pointSize: 6,
  })
}

const drawBar = () => {
  const data = window.google.visualization.arrayToDataTable([
    ['Tipo', 'Valor'],
    ['Fixos', 2100],
    ['Variáveis', 900],
  ])

  const chart = new window.google.visualization.ColumnChart(
    document.getElementById('barChart')
  )

  chart.draw(data, {
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
          <FinanceCard title="Saldo Atual" :value="2350.00" type="positive" />
          <FinanceCard title="Gastos do Mês" :value="1780.00" type="negative" />
          <FinanceCard title="Economia" :value="570.00" type="positive" />
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
