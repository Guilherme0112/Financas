<script setup lang="ts">
import { computed } from 'vue'
import FinanceCard from '@/Components/FinanceCard.vue';
import { formatarDinheiro } from '@/utils/helpers';

const props = defineProps<{
    dashboard: any;
}>();

const entradas = computed(() => props.dashboard.porcentual.entradas)
const saidas = computed(() => props.dashboard.porcentual.saidas)

const entradasIsUp = computed(() => entradas.value.tendencia === 'up')
const saidasIsUp = computed(() => saidas.value.tendencia === 'up')

const entradasClass = computed(() =>
    entradasIsUp.value ? 'text-emerald-700' : 'text-red-700'
)

const saidasClass = computed(() =>
    saidasIsUp.value ? 'text-emerald-700' : 'text-red-700'
)

const entradasIcon = computed(() =>
    entradasIsUp.value ? '▲' : '▼'
)

const saidasIcon = computed(() =>
    saidasIsUp.value ? '▼' : '▲'
)

const total = computed(() => {
    const atual = entradas.value.atual - saidas.value.atual
    const anterior = entradas.value.anterior - saidas.value.anterior
    const diferenca = atual - anterior

    return {
        atual,
        anterior,
        diferenca,
        tendencia:
            diferenca > 0
                ? 'up'
                : diferenca < 0
                    ? 'down'
                    : 'stable'
    }
});

const totalClass = computed(() =>
    total.value.tendencia === 'up'
        ? 'text-emerald-700'
        : total.value.tendencia === 'down'
            ? 'text-red-700'
            : 'text-gray-500'
)

const totalIcon = computed(() =>
    total.value.tendencia === 'up'
        ? '▲'
        : total.value.tendencia === 'down'
            ? '▼'
            : '–'
)
</script>
<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <div class="flex items-center justify-end gap-1 px-3 py-1 rounded-full text-xs font-semibold mb-[-30px]"
                :class="entradasClass">
                <span>{{ entradasIcon }}</span>
                {{ formatarDinheiro(Math.abs(entradas.diferenca)) }}
                <span class="opacity-70 ml-1">vs mês anterior</span>
            </div>

            <FinanceCard title="Receitas do Mês" :value="dashboard.cards.entradas" type="positive" />
        </div>


        <div>
            <div class="flex items-center justify-end gap-1 px-3 py-1 rounded-full text-xs font-semibold mb-[-30px]"
                :class="saidasClass">
                <span>{{ saidasIcon }}</span>
                {{ formatarDinheiro(Math.abs(saidas.diferenca)) }}
                <span class="opacity-70 ml-1">vs mês anterior</span>
            </div>

            <FinanceCard title="Gastos do Mês" :value="dashboard.cards.saidas" type="negative" />
        </div>


        <div>
            <div class="flex items-center justify-end gap-1 px-3 py-1 rounded-full text-xs font-semibold mb-[-30px]"
                :class="totalClass">
                <span>{{ totalIcon }}</span>
                {{ formatarDinheiro(Math.abs(total.diferenca)) }}
                <span class="opacity-70 ml-1">vs mês anterior</span>
            </div>

            <FinanceCard title="Economia do Mês" :value="dashboard.cards.total" type="positive" />
        </div>

    </div>
</template>