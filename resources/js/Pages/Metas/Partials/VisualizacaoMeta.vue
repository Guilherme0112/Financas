<script setup lang="ts">
import { computed } from 'vue';
import { formatarDinheiro } from '@/utils/helpers';
import {
    TrendingUp,
    CheckCircle2,
    Calendar,
    Info
} from 'lucide-vue-next';

const props = defineProps<{
    form: any;
}>();

props.form.ate_quando = props.form.ate_quando.replace("-", "/");

const objetivoNumerico = computed(() => {
    const v = props.form.valor_objetivo;
    return v ? parseFloat(v.toString()) : 0;
});

const valorAtualNumerico = computed(() => {
    const v = props.form?.valor_atual || 0;
    return v ? parseFloat(v.toString()) : 0;
});

const percentual = computed(() => {
    if (objetivoNumerico.value <= 0) return 0;
    const p = (valorAtualNumerico.value / objetivoNumerico.value) * 100;
    return Math.min(Math.round(p), 100);
});

const valorFaltante = computed(() => {
    const faltante = objetivoNumerico.value - valorAtualNumerico.value;
    return faltante > 0 ? faltante : 0;
});

const mesesRestantes = computed(() => {
    const dataAlvo = props.form.ate_quando;
    if (!dataAlvo) return 0;

    const hoje = new Date();
    const [yearStr, monthStr] = dataAlvo.toString().trim().split('/');
    const year = parseInt(yearStr, 10);
    const month = parseInt(monthStr, 10) - 1;
    const meta = new Date(year, month, 1);

    if (isNaN(meta.getTime())) return 0;

    const diferenca = (meta.getFullYear() - hoje.getFullYear()) * 12 + (meta.getMonth() - hoje.getMonth());
    return diferenca > 0 ? diferenca : 1;
});

const esforcoMensal = computed(() => {
    if (mesesRestantes.value === 0) return 0;
    return valorFaltante.value / mesesRestantes.value;
});

const esforcoDiario = computed(() => {
    return esforcoMensal.value / 30;
});

const corProgresso = computed(() => {
    if (percentual.value >= 100) return 'bg-emerald-500';
    if (percentual.value >= 50) return 'bg-blue-500';
    return 'bg-amber-500';
});

const dataMeta = computed(() => {
    const data = props.form.ate_quando;
    if (!data) return '';
    const [yearStr, monthStr] = data.toString().trim().split('/');
    const year = parseInt(yearStr, 10);
    const month = parseInt(monthStr, 10) - 1;
    const date = new Date(year, month, 1);
    if (isNaN(date.getTime())) return '';
    return date.toLocaleDateString('pt-BR', { year: 'numeric', month: 'long' });
});
</script>
<template>
    <div class="space-y-6">
        <!-- CARD PRINCIPAL COM PROGRESSO -->
        <div class="bg-white rounded-3xl p-6 shadow-lg border border-gray-100">
            <!-- CABEÇALHO -->
            <div class="mb-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">{{ form.nome || 'Nova Meta' }}</h2>
                        <p class="text-sm text-gray-500 flex items-center gap-2 mt-2">
                            <Calendar :size="16" />
                            Até {{ dataMeta || 'Sem data definida' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] uppercase text-gray-400 font-bold">Objetivo</p>
                        <p class="text-2xl font-black text-gray-800">{{ formatarDinheiro(objetivoNumerico) }}</p>
                    </div>
                </div>

                <!-- PROGRESSO -->
                <div class="space-y-2">
                    <div class="flex justify-between items-end">
                        <span class="text-sm font-bold text-gray-700">{{ percentual }}% concluído</span>
                        <span class="text-xs text-gray-400">Falta {{ formatarDinheiro(valorFaltante) }}</span>
                    </div>
                    <div class="w-full bg-gray-100 h-4 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-1000 ease-out" :class="corProgresso"
                            :style="{ width: `${percentual}%` }" />
                    </div>
                </div>
            </div>

            <!-- STATUS ATUAL -->
            <div class="grid grid-cols-2 gap-4 mb-6 pb-6 border-b border-gray-100">
                <div class="text-center">
                    <p class="text-[10px] uppercase text-gray-500 font-bold mb-1">Já tenho</p>
                    <p class="text-xl font-black text-emerald-600">{{ formatarDinheiro(valorAtualNumerico) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-[10px] uppercase text-gray-500 font-bold mb-1">Falta economizar</p>
                    <p class="text-xl font-black text-amber-600">{{ formatarDinheiro(valorFaltante) }}</p>
                </div>
            </div>

            <!-- ESFORÇO MENSAL -->
            <div class="bg-blue-50 rounded-2xl p-4 mb-4 border border-blue-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 rounded-full">
                        <TrendingUp :size="18" class="text-blue-600" />
                    </div>
                    <div class="flex-1">
                        <p class="text-[10px] text-blue-600 uppercase font-bold">Esforço mensal necessário</p>
                        <p class="text-lg font-black text-blue-700">{{ formatarDinheiro(esforcoMensal) }}/mês</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-blue-600 uppercase font-bold">Por dia</p>
                        <p class="text-sm font-black text-blue-700">{{ formatarDinheiro(esforcoDiario) }}</p>
                    </div>
                </div>
            </div>

            <!-- TEMPO RESTANTE -->
            <div v-if="mesesRestantes > 0" class="grid grid-cols-3 gap-4">
                <div class="bg-purple-50 rounded-2xl p-4 border border-purple-100 text-center">
                    <p class="text-[10px] font-bold text-purple-600 uppercase mb-1">Tempo restante</p>
                    <p class="text-lg font-black text-purple-700">{{ mesesRestantes }} {{ mesesRestantes > 1 ? 'meses' :
                        'mês' }}</p>
                </div>
                <div v-if="percentual >= 100"
                    class="bg-emerald-50 rounded-2xl p-4 border border-emerald-100 flex flex-col items-center justify-center">
                    <CheckCircle2 :size="28" class="text-emerald-600 mb-1" />
                    <p class="text-xs font-bold text-emerald-600">Meta Batida!</p>
                </div>
                <div v-else class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-center col-span-2">
                    <p class="text-[10px] font-bold text-gray-600 uppercase mb-1">Progresso</p>
                    <div class="flex items-end justify-center gap-1">
                        <p class="text-2xl font-black text-gray-700">{{ percentual }}%</p>
                        <span class="text-xs text-gray-500 mb-1">do objetivo</span>
                    </div>
                </div>
            </div>

            <!-- MENSAGEM SEM DATA -->
            <div v-else class="bg-amber-50 rounded-2xl p-4 border border-amber-100 flex items-start gap-3">
                <Info :size="18" class="text-amber-600 shrink-0 mt-0.5" />
                <p class="text-xs text-amber-700">
                    <strong>Defina uma data</strong> para ver a previsão de quanto você precisa economizar por mês.
                </p>
            </div>
        </div>
    </div>
</template>
