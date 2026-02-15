<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import CurveChart from './Components/CurveChart.vue';
import LineChart from './Components/LineChart.vue';
import ComparisonBarChart from './Components/ComparisonBarChart.vue';
import VencimentoLancamentos from './Partials/VencimentoLancamentos.vue';
import LancamentosVencidos from './Partials/LancamentosVencidos.vue';
import TogglePieChart from './Partials/TogglePieChart.vue';
import { ArrowRight } from 'lucide-vue-next';
import NavLink from '@/Components/NavLink.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Cards from './Partials/Cards.vue';

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
    <div class="py-12 bg-gray-100 min-h-screen">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">

        <Cards :dashboard="dashboard" />
        
        <TogglePieChart :options="[
          {
            key: 'gastos',
            label: 'Gastos',
            title: 'Distribuição dos Gastos',
            rows: gastosRows,
            colors: redPalette,
            color: 'red',
            percentual: dashboard.porcentual.saidas.percentual,
            tipo: 'saida'
          },
          {
            key: 'receitas',
            label: 'Receitas',
            title: 'Distribuição das Receitas',
            rows: receitasRows,
            colors: greenPalette,
            color: 'green',
            percentual: dashboard.porcentual.entradas.percentual,
            tipo: 'entrada'
          }
        ]" />

        <div class="grid grid-cols-1 gap-6">
          <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
            <CurveChart :rows="dashboard.graficos.mensal" />
          </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
            <ComparisonBarChart :rows="dashboard.graficos.mensal" />
          </div>
          <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 w-full">
            <LineChart :rows="dashboard.graficos.mensal" />
          </div>
        </div>

        <div class="w-full">
          <div class="grid bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
            <div class="w-full flex justify-end">
              <NavLink :href="route('lancamentos.index', { tipo: 'SAIDA', foi_pago: false })"
                  :active="route().current('lancamentos.index')">
                  <DangerButton>
                    Ver Mais
                    <ArrowRight :size="12" class="ml-3 mt-[-3px]" />
                  </DangerButton>
              </NavLink>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
              <div>
                <VencimentoLancamentos :lancamentos="dashboard.lancamentos_perto_de_vencer" />
              </div>
              <div>
                <LancamentosVencidos :lancamentos="dashboard.lancamentos_vencidos" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>