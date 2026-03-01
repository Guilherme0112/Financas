<script setup lang="ts">
import { formatarDinheiro, formatarMesAno, toNumber } from '@/utils/helpers'
import { TriangleAlert, CheckCircle2, Wallet, MoreVertical } from 'lucide-vue-next';
import { computed } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';

const props = defineProps<{
    categoria: any,
    actions?: boolean
}>();

const emits = defineEmits(['click', 'delete']);

const percentualReativo = computed(() => {
    if (!props.categoria.limite || props.categoria.limite <= 0) return 0;
    return (props.categoria.soma_categoria / props.categoria.limite) * 100;
});

const valorEconomizado = computed(() => {
    const sobra = props.categoria.limite - props.categoria.soma_categoria;
    return sobra;
});

const corBarra = computed(() => {
    const p = percentualReativo.value;
    if (p >= 100) return 'bg-red-500';
    if (p >= 80) return 'bg-amber-500';
    return 'bg-emerald-500';
});

const statusTexto = computed(() => {
    const p = percentualReativo.value;
    if (p >= 100) return 'Limite atingido.';
    if (p >= 80) return 'Atenção ao saldo restante.';
    return 'Dentro do planejado!';
});

const statusCorTexto = computed(() => {
    const p = percentualReativo.value;
    if (p >= 100) return 'text-red-600';
    if (p >= 80) return 'text-amber-600';
    return 'text-emerald-600';
});


</script>

<template>
    <div 
        class="bg-white p-5 shadow-lg rounded-2xl border border-gray-100 hover:shadow-xl transition-all relative"
    >
        <div class="flex justify-between items-start mb-4">
            <div class="flex-1">
                <h3 class="font-bold text-gray-800 leading-none mb-1">
                    {{ props.categoria.categoria_saida_label }}
                </h3>
                <p class="text-[12px] tracking-wider text-gray-400">
                    {{ formatarMesAno(props.categoria.mes_referencia) }}
                </p>
            </div>

            <div @click.stop v-if="actions">
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button class="p-1 hover:bg-gray-50 rounded-full text-gray-400 transition-colors">
                            <MoreVertical :size="18" />
                        </button>
                    </template>

                    <template #content>
                        <div>
                            <button 
                                @click="emits('click', props.categoria)" 
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                            >
                                Editar
                            </button>
                            <button 
                                @click="emits('delete', props.categoria)" 
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors border-t border-gray-100"
                            >
                                Deletar
                            </button>
                        </div>
                    </template>
                </Dropdown>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <span class="block text-[12px] text-gray-400">Economizado</span>
                <span class="text-lg font-black" :class="valorEconomizado > 0 ? 'text-emerald-600' : 'text-red-500'">
                    {{ formatarDinheiro(valorEconomizado) }}
                </span>
            </div>

            <div class="relative w-full bg-gray-100 h-3 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-700 ease-out" 
                    :class="corBarra"
                    :style="{ width: `${Math.min(percentualReativo, 100)}%` }" />
            </div>

            <div class="flex justify-between items-end">
                <div>
                    <p class="text-[11px] text-gray-400 font-medium">Gasto atual</p>
                    <p class="text-sm font-bold text-gray-700">{{ formatarDinheiro(props.categoria.soma_categoria) }}</p>
                </div>
                
                <div class="text-right">
                    <p class="text-[12px] flex items-center justify-end gap-1 font-bold" :class="statusCorTexto">
                        <component :is="percentualReativo >= 100 ? TriangleAlert : (percentualReativo >= 80 ? TriangleAlert : CheckCircle2)" :size="14" />
                        {{ statusTexto }}
                    </p>
                    <p class="text-[10px] text-gray-400">de {{ formatarDinheiro(props.categoria.limite) }}</p>
                </div>
            </div>

            <div v-if="valorEconomizado > 0" class="mt-3 pt-3 border-t border-gray-50 flex items-center gap-2">
                <div class="bg-emerald-100 p-1 rounded">
                    <Wallet :size="12" class="text-emerald-600" />
                </div>
                <span class="text-[11px] text-emerald-700 font-medium">
                    Você ainda tem <strong>{{ formatarDinheiro(valorEconomizado) }}</strong> de margem.
                </span>
            </div>
        </div>
    </div>
</template>