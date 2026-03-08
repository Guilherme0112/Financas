<script setup lang="ts">
import { computed } from "vue";
import FinanceCard from "@/Components/FinanceCard.vue";
import Tendencia from "../Components/Tendencia.vue";

const props = defineProps<{
    dashboard: any;
}>();

// Cálculo da performance do Saldo (Total)
const porcentualTotal = computed(() => {
    const entradas = props.dashboard.porcentual.entradas;
    const saidas = props.dashboard.porcentual.saidas;

    // Total Mês Anterior = Entradas Antigas - Saídas Antigas
    const anterior = entradas.anterior - saidas.anterior;
    // Total Atual (Saldo)
    const atual = props.dashboard.cards.total;
    const diferenca = atual - anterior;

    // Cálculo da tendência
    let tendencia: "up" | "down" | "stable" = "stable";
    if (diferenca > 0) tendencia = "up";
    if (diferenca < 0) tendencia = "down";

    return {
        anterior,
        atual,
        diferenca,
        tendencia,
    };
});
</script>

<template>
    <section class="grid grid-cols-1 gap-6 md:grid-cols-4">
        <FinanceCard
            title="Entradas"
            :value="dashboard.cards.entradas"
            type="positive"
        >
            <template #trend>
                <Tendencia
                    :value="dashboard.porcentual.entradas.diferenca"
                    :trend="dashboard.porcentual.entradas.tendencia"
                />
            </template>
        </FinanceCard>

        <FinanceCard
            title="Saídas"
            :value="dashboard.cards.saidas"
            type="negative"
        >
            <template #trend>
                <Tendencia
                    :value="dashboard.porcentual.saidas.diferenca"
                    :trend="dashboard.porcentual.saidas.tendencia"
                    invert
                />
            </template>
        </FinanceCard>

        <FinanceCard
            title="Metas"
            :value="dashboard.cards.reserva_meta"
            type="metas"
        >
            <template #trend>
                <Tendencia
                    :value="dashboard.porcentual.reserva_meta.diferenca"
                    :trend="dashboard.porcentual.reserva_meta.tendencia"
                />
            </template>
        </FinanceCard>

        <FinanceCard
            title="Saldo"
            :value="dashboard.cards.total"
            :type="dashboard.cards.total >= 0 ? 'positive' : 'negative'"
        >
            <template #trend>
                <Tendencia
                    :value="porcentualTotal.diferenca"
                    :trend="porcentualTotal.tendencia"
                />
            </template>
        </FinanceCard>
    </section>
</template>
