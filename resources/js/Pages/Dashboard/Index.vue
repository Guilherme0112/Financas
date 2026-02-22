<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import CurveChart from './Components/CurveChart.vue';
import LineChart from './Components/LineChart.vue';
import ComparisonBarChart from './Components/ComparisonBarChart.vue';
import VencimentoLancamentos from './Partials/VencimentoLancamentos.vue';
import LancamentosVencidos from './Partials/LancamentosVencidos.vue';
import TogglePieChart from './Partials/TogglePieChart.vue';
import { ArrowRight, CalendarClock, ChartNoAxesCombined, ChartSpline, Goal, PieChart, Target } from 'lucide-vue-next';
import NavLink from '@/Components/NavLink.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Cards from './Partials/Cards.vue';
import CardLimite from '../Metas/Components/CardLimite.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SemRegistro from './Partials/SemRegistro.vue';
import HeaderSecao from './Components/HeaderSecao.vue';
import CardMeta from '../Metas/Components/CardMeta.vue';

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
      <div class="sm:px-6 lg:px-8 space-y-8">

        <!-- ENTRADAS, SAIDAS E TOTAL -->
        <Cards :dashboard="dashboard" />

        <!-- DISTRIBUIÇÃO DE GASTOS E RECEITAS -->
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
          <HeaderSecao :icon="PieChart" title="Distribuição de Gastos e Receitas"
            description="Visualize como seu dinheiro está distribuído entre as categorias"
            icon-color="text-emerald-800" />
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
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
          <HeaderSecao :icon="Target" title="Metas Financeiras" description="Defina objetivos e acompanhe seu progresso"
            icon-color="text-emerald-800">
            <NavLink :href="route('limites.index')" :active="route().current('limites.index')">
              <PrimaryButton class="flex items-center gap-2">
                Ver Mais
                <ArrowRight :size="14" />
              </PrimaryButton>
            </NavLink>
          </HeaderSecao>
          <template v-if="dashboard.metas.data.length > 0">
            <CardMeta v-for="meta in dashboard.metas.data" :meta="meta" :actions="false" class="max-w-[370px]" />
          </template>
          <div v-else class="col-span-full flex items-center justify-center min-h-[200px]">
            <SemRegistro />
          </div>
        </div>

        <!-- ÚLTIMOS 6 MESES -->
        <div class="bg-white shadow-lg p-6 rounded-2xl">
          <HeaderSecao :icon="ChartNoAxesCombined" title="Resumo Financeiro Semestral"
            description="Acompanhe sua situação financeira nos últimos 6 meses" icon-color="text-emerald-800" />

          <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
            <CurveChart :rows="dashboard.graficos.mensal" />
          </div>
        </div>

        <!-- GRÁFICOS QUE MOSTRAM OS ÚLTIMOS 6 MESES -->
        <div class="bg-white shadow-lg p-6 rounded-2xl">
          <HeaderSecao :icon="ChartSpline" title="Resumo de Performance"
            description="Análise detalhada de entradas e saídas semestral" icon-color="text-emerald-800" />

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
              <ComparisonBarChart :rows="dashboard.graficos.mensal" />
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-slate-100 w-full">
              <LineChart :rows="dashboard.graficos.mensal" />
            </div>
          </div>
        </div>

        <!-- TABELA DE VENCIMENTOS E PERTO DE VENCER -->
        <div class="w-full">
          <div class="grid bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
            <div class="flex items-center justify-between py-3 px-4 bg-white rounded-md">
              <div class="flex-1">
                <HeaderSecao :icon="CalendarClock" title="Contas a Pagar"
                  description="Visualize seus compromissos próximos e atrasados" icon-color="text-emerald-800">
                  <NavLink :href="route('lancamentos.index', { tipo: 'SAIDA', foi_pago: false })"
                    :active="route().current('lancamentos.index')">
                    <DangerButton>
                      Ver Mais
                      <ArrowRight :size="12" class="ml-2" />
                    </DangerButton>
                  </NavLink>
                </HeaderSecao>
              </div>
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


        <!-- CARDS DE LIMITES CONFIGURADOS -->
        <div class="w-full">
          <div class="grid bg-white p-6 rounded-2xl shadow-lg border border-slate-100">
            <div class="flex items-center justify-between py-3 bg-white">
              <div class="flex-1">
                <HeaderSecao :icon="Goal" title="Limite de Gastos"
                  description="Acompanhe seus limites de gastos por categoria" icon-color="text-emerald-800">
                  <NavLink :href="route('limites.index')" :active="route().current('limites.index')">
                    <PrimaryButton class="flex items-center gap-2">
                      Ver Mais
                      <ArrowRight :size="14" />
                    </PrimaryButton>
                  </NavLink>
                </HeaderSecao>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <template v-if="dashboard.limites.data.length > 0">
                <CardLimite v-for="categoria in dashboard.limites.data" :categoria="categoria" />
              </template>

              <div v-else class="col-span-full flex items-center justify-center min-h-[200px]">
                <SemRegistro />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>