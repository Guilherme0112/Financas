<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import FinanceCard from '@/Components/FinanceCard.vue';
import PieChart from './Components/PieChart.vue';
import PorcentualMesAnterior from './Components/PorcentualMesAnterior.vue';
import CurveChart from './Components/CurveChart.vue';
import LineChart from './Components/LineChart.vue';
import ComparisonBarChart from './Components/ComparisonBarChart.vue';
import VencimentoLancamentos from './Partials/VencimentoLancamentos.vue';
import BarChart from './Components/BarChart.vue';
import LancamentosVencidos from './Partials/LancamentosVencidos.vue';

declare global {
  interface Window {
    google: any;
  }
}

const page = usePage();
const dashboard = page.props.dashboard as any;
const redPalette = ['#991b1b', '#dc2626', '#ef4444', '#7f1d1d', '#b91c1c', '#f87171'];
const greenPalette = ['#064e3b', '#059669', '#10b981', '#065f46', '#34d399', '#022c22'];

const gastosRows = dashboard.graficos.pizza.gastos.map(
  (item: any) => [item.categoria_label, Number(item.total)]
);

const receitasRows = dashboard.graficos.pizza.receitas.map(
  (item: any) => [item.categoria_label, Number(item.total)]
);

</script>
<template>
  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <div class="py-12 bg-slate-50 min-h-screen">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <FinanceCard title="Receitas do Mês" :value="dashboard.cards.entradas" type="positive" />
          <FinanceCard title="Gastos do Mês" :value="dashboard.cards.saidas" type="negative" />
          <FinanceCard title="Economia do Mês" :value="dashboard.cards.total" type="positive" />
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-red-100 p-3 py-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex justify-end mb-[-38px] mr-2">
              <PorcentualMesAnterior :valor="dashboard.porcentual.saidas" tipo="saida" />
            </div>
            <PieChart chart-id="pieChartGastos" title="Distribuição dos Gastos" :rows="gastosRows"
              :colors="redPalette" color="red" />
          </div>
          <div class="bg-green-100 p-3 py-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex justify-end mb-[-38px] mr-2">
              <PorcentualMesAnterior :valor="dashboard.porcentual.entradas" tipo="entrada" />
            </div>
            <PieChart chart-id="pieChartReceitas" title="Distribuição das Receitas" :rows="receitasRows"
              :colors="greenPalette" color="green" />
          </div>
        </div>
        <div class="grid grid-cols-1 gap-6">
          <div class="bg-green-100 p-6 rounded-2xl shadow-sm border border-slate-100">
            <CurveChart :rows="dashboard.graficos.mensal" />
          </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-green-100 p-6 rounded-2xl shadow-sm border border-slate-100">
            <ComparisonBarChart :rows="dashboard.graficos.mensal" />
          </div>
          <div class="bg-red-100 p-6 rounded-2xl shadow-sm border border-slate-100 w-full">
            <LineChart :rows="dashboard.graficos.mensal" />
          </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-[2fr_1fr] gap-6 items-start">
          <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <div class="bg-red-100 p-6 rounded-2xl shadow-sm border border-slate-100">
              <VencimentoLancamentos :lancamentos="dashboard.lancamentos_perto_de_vencer" />
            </div>
            <div class="bg-red-100 p-6 rounded-2xl shadow-sm border border-slate-100">
              <LancamentosVencidos :lancamentos="dashboard.lancamentos_vencidos" />
            </div>
          </div>
          <div class="bg-red-100 p-6 rounded-2xl shadow-sm border border-slate-100">
            <BarChart :gastos="gastosRows" />
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>