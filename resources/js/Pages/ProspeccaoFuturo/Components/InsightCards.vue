<script setup>
import { computed } from 'vue';

const props = defineProps({
    optimizedSurplus: {
        type: Number,
        required: true
    },
    avgIncome: {
        type: Number,
        required: true
    },
    months: {
        type: Number,
        required: true
    }
});

// Formatação de moeda
const formatBRL = (val) => {
    return val.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

// Cálculo da porcentagem de economia (com proteção contra divisão por zero)
const savingsPercentage = computed(() => {
    if (props.avgIncome <= 0) return "0.0";
    const percent = (props.optimizedSurplus / props.avgIncome) * 100;
    return percent.toFixed(1);
});

const isPositive = computed(() => props.optimizedSurplus >= 0);
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div 
            :class="isPositive ? 'bg-green-50 border-green-100' : 'bg-rose-50 border-rose-100'"
            class="p-6 rounded-2xl border shadow-sm transition-colors duration-500"
        >
            <h4 :class="isPositive ? 'text-green-800' : 'text-rose-800'" class="font-bold text-sm mb-2 uppercase tracking-tight">
                {{ isPositive ? 'Capacidade de Poupança' : 'Déficit Mensal' }}
            </h4>
            <p :class="isPositive ? 'text-green-700' : 'text-rose-700'" class="text-xs leading-relaxed">
                <template v-if="isPositive">
                    Você está poupando <strong>{{ formatBRL(optimizedSurplus) }}</strong> por mês. 
                    Isso representa <strong>{{ savingsPercentage }}%</strong> da sua renda média disponível.
                </template>
                <template v-else>
                    Atenção: Seus gastos estão <strong>{{ formatBRL(Math.abs(optimizedSurplus)) }}</strong> acima da sua renda. 
                    Você está operando com um déficit de <strong>{{ savingsPercentage }}%</strong>.
                </template>
            </p>
        </div>

        <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100 shadow-sm">
            <h4 class="text-blue-800 font-bold text-sm mb-2 uppercase tracking-tight">Dica de Prospecção</h4>
            <p class="text-xs text-blue-700 leading-relaxed">
                {{ months <= 0 
                    ? 'Selecione um período acima para ver como a inflação e os juros podem impactar seu patrimônio a longo prazo.' 
                    : `Em ${months} meses, o valor de ${formatBRL(optimizedSurplus * months)} pode sofrer variações de poder de compra. Considere investir o excedente.` 
                }}
            </p>
        </div>
    </div>
</template>