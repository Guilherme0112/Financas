<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    metas: {
        type: Array,
        required: true
    },
    optimizedSurplus: {
        type: Number,
        default: 0
    },
    months: {
        type: Number,
        default: 0 // Começa em 0 para mostrar o saldo real por padrão
    }
});

const getProjectedTotal = (atual, months, surplus) => {
    const valorAtual = Number(atual) || 0;
    const sobraMensal = Number(surplus) || 0;
    const meses = Number(months) || 0;

    // Se meses for 0, retorna apenas o que já tem.
    // Se for 1 ou mais, já aplica a projeção (sobra * meses).
    if (meses <= 0) return valorAtual;
    
    return valorAtual + (sobraMensal * meses);
};

const calculateProgress = (valor_objetivo, lancamentos_sum_valor) => {
    const objetivo = Number(valor_objetivo);
    const totalComProjecao = getProjectedTotal(lancamentos_sum_valor, props.months, props.optimizedSurplus);
    
    const progress = (totalComProjecao / objetivo) * 100;
    return Math.min(progress, 100).toFixed(0);
};

const getMissingValue = (valor_objetivo, lancamentos_sum_valor) => {
    const objetivo = Number(valor_objetivo);
    const totalComProjecao = getProjectedTotal(lancamentos_sum_valor, props.months, props.optimizedSurplus);
    
    const missing = objetivo - totalComProjecao;
    return missing > 0 ? missing : 0;
};

const checkGoalStatus = (valor_objetivo, lancamentos_sum_valor) => {
    const missing = getMissingValue(valor_objetivo, lancamentos_sum_valor);
    
    if (missing <= 0) {
        return {
            text: "Alcançável",
            class: "bg-green-100 text-green-700",
        };
    }

    return {
        text: "Pendente",
        class: "bg-yellow-50 text-yellow-700",
    };
};

const formatCurrency = (value) => {
    return Number(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

const goToMetas = () => {
    router.visit(route('limites.index'));
};
</script>

<template>
    <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
        <h3 class="font-bold text-gray-800 mb-6 flex items-center justify-between">
            Metas Ativas
            <span class="text-[10px] bg-gray-100 px-2 py-1 rounded text-gray-500 uppercase transition-all">
                {{ props.months === 0 ? 'Saldo Atual' : `Projeção (${props.months} ${props.months === 1 ? 'mês' : 'meses'})` }}
            </span>
        </h3>

        <div class="space-y-6">
            <div v-for="meta in metas" :key="meta.id" class="group">
                <div class="flex justify-between items-end mb-2">
                    <span class="text-sm font-bold text-gray-700">{{ meta.nome }}</span>
                    <span class="text-xs font-bold text-gray-400">
                        {{ formatCurrency(meta.valor_objetivo) }}
                    </span>
                </div>
                
                <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                    <div
                        class="bg-emerald-600 h-2 rounded-full transition-all duration-1000"
                        :style="{ width: calculateProgress(meta.valor_objetivo, meta.lancamentos_sum_valor) + '%' }"
                    ></div>
                </div>

                <div class="mb-3">
                    <template v-if="getMissingValue(meta.valor_objetivo, meta.lancamentos_sum_valor) > 0">
                        <p class="text-[11px] text-gray-500 italic">
                            {{ props.months === 0 ? 'Falta' : 'Faltariam' }} 
                            <span class="font-bold text-rose-500">{{ formatCurrency(getMissingValue(meta.valor_objetivo, meta.lancamentos_sum_valor)) }}</span>
                        </p>
                    </template>
                    <template v-else>
                        <p class="text-[11px] text-emerald-600 font-bold">
                            ✨ {{ props.months === 0 ? 'Objetivo atingido!' : 'Alcançado nesta projeção!' }}
                        </p>
                    </template>
                </div>

                <div class="flex justify-between items-center">
                    <span 
                        class="text-[9px] font-bold uppercase p-1 rounded" 
                        :class="checkGoalStatus(meta.valor_objetivo, meta.lancamentos_sum_valor).class"
                    >
                        {{ checkGoalStatus(meta.valor_objetivo, meta.lancamentos_sum_valor).text }}
                    </span>
                    <span class="text-[10px] text-gray-400 font-medium">
                        {{ calculateProgress(meta.valor_objetivo, meta.lancamentos_sum_valor) }}% 
                        <span class="text-[9px] opacity-70 italic">
                            ({{ props.months === 0 ? 'atual' : 'projetado' }})
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <PrimaryButton class="mt-8 w-full justify-center" @click="goToMetas">
            + Adicionar Nova Meta
        </PrimaryButton>
    </div>
</template>