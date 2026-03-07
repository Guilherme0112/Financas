<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ProjectionControls from "./Components/ProjectionControls.vue";
import ProjectedBalanceCard from "./Components/ProjectedBalanceCard.vue";
import ProjectionTimeline from "./Components/ProjectionTimeline.vue";
import InsightCards from "./Components/InsightCards.vue";
import GoalsSection from "./Components/GoalsSection.vue";
import { ref, computed } from "vue";
import { Head } from "@inertiajs/vue3";

interface MediaMes {
    entradas: string | number;
    saidas: string | number;
    mes: string;
    categoria_label?: string | null;
}

const props = defineProps({
    metas: Object,
    media: {
        type: Array as () => MediaMes[],
        default: () => []
    },
});
const months = ref(0);
const optimizationLevel = ref(0);

const mesesAtivos = computed(() => {
    if (!props.media) return [];
    return props.media.filter(item => 
        Number(item.entradas) > 0 || Number(item.saidas) > 0
    );
});

// 2. Cálculo da Média de Entrada (Baseado apenas nos meses ativos)
const mediaEntrada = computed(() => {
    const totalMeses = mesesAtivos.value.length;
    if (totalMeses === 0) return 0;
    
    const soma = mesesAtivos.value.reduce((acc, item) => acc + Number(item.entradas), 0);
    return soma / totalMeses;
});

// 3. Cálculo da Média de Saída (Baseado apenas nos meses ativos)
const mediaSaida = computed(() => {
    const totalMeses = mesesAtivos.value.length;
    if (totalMeses === 0) return 0;
    
    const soma = mesesAtivos.value.reduce((acc, item) => acc + Number(item.saidas), 0);
    return soma / totalMeses;
});

// Média de reserva (ajuste se houver lógica de metas/investimentos)
const mediaReserva = computed(() => 0);

// 4. Cálculo do Superávit Otimizado (Média Entrada - Despesa com Redução %)
const optimizedSurplus = computed(() => {
    const despesaOtimizada = mediaSaida.value * (1 - optimizationLevel.value / 100);
    return mediaEntrada.value + mediaReserva.value - despesaOtimizada;
});

// 5. Geração da Projeção Mensal Acumulada
const monthlyProjection = computed(() => {
    let projection = [];
    let cumulative = 0;

    const monthlyIncome = mediaEntrada.value;
    const monthlyOptimizedExpense = mediaSaida.value * (1 - optimizationLevel.value / 100);
    const monthlyBalance = monthlyIncome - monthlyOptimizedExpense;

    for (let i = 1; i <= months.value; i++) {
        cumulative += monthlyBalance;

        projection.push({
            month: i,
            income: monthlyIncome,
            expense: monthlyOptimizedExpense,
            total: cumulative,
        });
    }
    return projection;
});
</script>

<template>
    <Head title="Prospecção de Gastos" />
    <AuthenticatedLayout>
        <div class="p-6 bg-white rounded-2xl min-h-screen font-sans shadow-lg">
            
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-8">
                <div class="lg:col-span-3">
                    <ProjectionControls
                        :months="months"
                        :optimization-level="optimizationLevel"
                        @update:months="months = $event"
                        @update:optimization-level="optimizationLevel = $event"
                    />
                </div>
                <ProjectedBalanceCard
                    :balance="
                        monthlyProjection.length > 0
                            ? monthlyProjection[monthlyProjection.length - 1].total
                            : 0
                    "
                />
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                
                <div class="xl:col-span-2 space-y-8">
                    <ProjectionTimeline :projection="monthlyProjection" />
                    
                    <InsightCards
                        :optimized-surplus="mediaEntrada - mediaSaida"
                        :avg-income="mediaEntrada"
                        :months="months"
                    />
                </div>

                <GoalsSection
                    :metas="props?.metas?.data || []"
                    :optimized-surplus="optimizedSurplus"
                    :months="months"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>