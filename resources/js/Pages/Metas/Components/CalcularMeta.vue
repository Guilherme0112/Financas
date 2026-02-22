<script setup lang="ts">
import { formatarDinheiro } from '@/utils/helpers';
import { computed } from 'vue';

const props = defineProps<{
    form: any
}>();

const esforcoMensal = computed(() => {
    if (mesesRestantes.value === 0) return 0;
    return objetivoNumerico.value / mesesRestantes.value;
});

const mesesRestantes = computed(() => {
    if (!props.form.ate_quando) return 0;
    const hoje = new Date();
    const targetDate = new Date(props.form.ate_quando);
    const year = targetDate.getFullYear();
    const month = targetDate.getMonth();
    const meta = new Date(year, month, 1);
    const diferenca = (meta.getFullYear() - hoje.getFullYear()) * 12 + (meta.getMonth() - hoje.getMonth());
    return diferenca > 0 ? diferenca : 1;
});

const objetivoNumerico = computed(() => {
    const v = props.form.valor_objetivo;
    return v ? parseFloat(v.toString().replace(',', '.')) : 0;
});

const valorFaltante = computed(() => {
    const faltante = objetivoNumerico.value;
    return faltante > 0 ? faltante : 0;
});
</script>
<template>
    <section class="space-y-6">
        <div class="bg-emerald-600 p-8 rounded-3xl text-white shadow-lg relative overflow-hidden">

            <TrendingUp class="absolute -right-4 -bottom-4 text-emerald-500 opacity-20" :size="120" />

            <p class="text-emerald-100 font-medium mb-1">Para atingir em {{ mesesRestantes }} meses:</p>
            <div class="text-4xl font-black mb-4">
                {{ formatarDinheiro(esforcoMensal) }} <span class="text-lg font-normal">/mês</span>
            </div>

            <div class="bg-emerald-700/50 p-4 rounded-2xl flex items-start gap-3">
                <Info :size="18" class="shrink-0 mt-1" />
                <p class="text-xs leading-relaxed">
                    Se você começar hoje, precisará de aproximadamente <strong>{{ formatarDinheiro(esforcoMensal
                        / 30) }}</strong> por dia para bater sua meta.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-amber-50 p-4 rounded-2xl border border-amber-100 shadow-lg">
                <p class="text-[10px] font-bold text-amber-600 uppercase mb-1 text-center">Falta economizar</p>
                <p class="text-lg font-black text-amber-700 text-center">{{ formatarDinheiro(valorFaltante) }}
                </p>
            </div>
            <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100 text-center shadow-lg">
                <p class="text-[10px] font-bold text-blue-600 uppercase mb-1">Meses totais</p>
                <p class="text-lg font-black text-blue-700">{{ mesesRestantes }}</p>
            </div>
        </div>
    </section>
</template>