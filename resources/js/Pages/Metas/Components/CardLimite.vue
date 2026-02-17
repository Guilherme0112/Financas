<script setup lang="ts">
import { formatarDinheiro, formatarMesAno } from '@/utils/helpers'
import { TriangleAlert } from 'lucide-vue-next';
import { useMetas } from '../Composables/useMetas';
import { computed } from 'vue';

const props = defineProps<{
    categoria: any
}>();

const emits = defineEmits(['click']);
const {
    percentual,
    mostrarAlerta
} = useMetas(props.categoria);

const corBarra = computed(() => {
    const p = percentual.value;
    if (p >= 100) return 'bg-red-500';
    if (p >= 80) return 'bg-amber-500';
    return 'bg-emerald-500';
});

const statusTexto = computed(() => {
    const p = percentual.value;
    if (p >= 100) return 'Limite excedido! Ajuste sua rota.';
    if (p >= 80) return 'Ritmo acelerado. Cuidado para não estourar.';
    return '';
});

const statusCorTexto = computed(() => {
    const p = percentual.value;
    if (p >= 100) return 'text-red-600';
    if (p >= 80) return 'text-amber-600';
    return 'text-emerald-600';
});

</script>

<template>
    <div @click="emits('click', props.categoria)"
        class="bg-white p-5 shadow-lg rounded-2xl border border-gray-100 cursor-pointer transition-colors">
        <!-- Header -->
        <div class="flex justify-between items-start mb-3">
            <div class="flex items-center gap-3">
                <span class="p-2 bg-white rounded-lg text-xl">
                    {{ props.categoria.icone }}
                </span>

                <div>
                    <h3 class="font-bold text-gray-700">
                        {{ props.categoria.categoria_saida_label }}
                    </h3>

                    <p class="text-xs text-gray-400">
                        {{ formatarMesAno(props.categoria.mes_referencia) }}
                    </p>

                    <p class="text-xs text-gray-400">
                        Limite: {{ formatarDinheiro(props.categoria.limite) }}
                    </p>
                </div>
            </div>

            <span class="text-xs font-bold"
                :class="props.categoria.total_gasto > props.categoria.limite ? 'text-red-500' : 'text-gray-600'">
                {{ formatarDinheiro(props.categoria.total_gasto) }}
            </span>
        </div>

        <!-- Barra -->
        <div class="w-full bg-gray-100 h-2 rounded-full mb-2">
            <div class="h-full rounded-full transition-all" :class="corBarra"
                :style="{ width: `${Math.min(percentual, 100)}%` }" />
        </div>

        <!-- Aviso -->
        <p v-if="mostrarAlerta" class="text-[12px] flex items-center gap-1 font-medium" :class="statusCorTexto">
            <TriangleAlert :size="14" />
            {{ statusTexto }}
        </p>
    </div>
</template>
