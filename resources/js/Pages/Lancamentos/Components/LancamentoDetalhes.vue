<script setup lang="ts">
import { computed, ref } from "vue";
import Modal from "@/Components/Modal.vue";
import {
    Calendar,
    Target,
    CheckCircle2,
    XCircle,
    X,
    Repeat,
    ArrowUpCircle,
    ArrowDownCircle,
    CalendarCheck,
    FileText,
    Layers
} from "lucide-vue-next";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { formatarData, formatarDinheiro } from "@/utils/helpers";
import { toast } from "vue3-toastify";
import { router } from "@inertiajs/vue3";

const props = defineProps<{
    show: boolean;
    lancamento: any; 
}>();

const emit = defineEmits(["close"]);
const loadingMarkAsPaid = ref(false);


const isEntrada = computed(() => {
    if (!props.lancamento) return false;
    const tipo = props.lancamento.tipo?.toLowerCase();
    return tipo === "entrada" || tipo === "receita";
});

const isMeta = computed(() => {
    if (!props.lancamento) return false;
    return props.lancamento.tipo === 'RESERVA_META';
});

const statusColors = computed(() => {
    if (!props.lancamento) return {};
    if (isMeta.value) {
        return {
            text: "text-blue-600",
            bg: "bg-blue-50/50",
            border: "border-blue-100",
            icon: "text-blue-500",
        };
    }
    return {
        text: isEntrada.value ? "text-emerald-600" : "text-rose-600",
        bg: isEntrada.value ? "bg-emerald-50/50" : "bg-rose-50/50",
        border: isEntrada.value ? "border-emerald-100" : "border-rose-100",
        icon: isEntrada.value ? "text-emerald-500" : "text-rose-500",
    };
});

const confirmarMarcarComoPaga = () => {
    if (!props.lancamento) return;

    loadingMarkAsPaid.value = true;
    router.put(route('lancamentos.marcar-como-paga', props.lancamento.id), {}, {
        onSuccess: () => {
            toast.success('Lançamento marcado como pago!');
            router.reload();
        },
        onError: () => {
            toast.error('Erro ao marcar como pago.');
        },
        onFinish: () => {
            loadingMarkAsPaid.value = false;
            emit('close');
        }
    });
};
</script>

<template>
    <Modal :show="props.show" @close="() => emit('close')" maxWidth="2xl">
        <div class="overflow-hidden bg-white rounded-xl shadow-2xl">
            <div :class="['px-6 py-5 border-b flex justify-between items-start', statusColors.bg]">
                <div class="flex items-center gap-3">
                    <component 
                        :is="isMeta ? Target : (isEntrada ? ArrowUpCircle : ArrowDownCircle)" 
                        :class="['w-8 h-8', statusColors.icon]"
                    />
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 leading-none">
                            Detalhes do Lançamento
                        </h2>
                        <span :class="['text-[10px] uppercase tracking-widest font-bold mt-1 inline-block', statusColors.text]">
                            {{ props.lancamento?.tipo.replace("RESERVA_META", "Reserva de Meta") }}
                        </span>
                    </div>
                </div>
                <button @click="emit('close')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <X class="w-5 h-5" />
                </button>
            </div>

            <div v-if="props.lancamento" class="p-6">
                <div :class="['text-center mb-8 py-6 rounded-2xl border-2 border-dashed transition-colors', statusColors.border, statusColors.bg]">
                    <span class="text-xs text-gray-500 block mb-1 font-semibold uppercase tracking-wider">Valor total</span>
                    <h3 :class="['text-4xl font-black tracking-tight', statusColors.text]">
                        {{ formatarDinheiro(props.lancamento.valor) }}
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <div class="space-y-6">
                        <div>
                            <label class="text-[11px] font-bold text-gray-400 uppercase flex items-center gap-1.5 mb-2">
                                <FileText class="w-3.5 h-3.5" /> Identificação
                            </label>
                            <p class="text-gray-900 font-bold text-lg leading-tight">{{ props.lancamento.nome }}</p>
                            <p class="text-sm text-gray-500 mt-2 p-3 bg-gray-50 rounded-lg border border-gray-100 italic">
                                {{ props.lancamento.descricao || "Sem descrição informada." }}
                            </p>
                        </div>

                        <div class="flex flex-col gap-4">
                            <div>
                                <label class="text-[11px] font-bold text-gray-400 uppercase mb-2 block">Categoria</label>
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-sm font-bold bg-slate-100 text-slate-700">
                                    <Layers class="w-4 h-4 text-slate-500" />
                                    {{ props.lancamento.categoria_label || "Geral" }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <div>
                            <label class="text-[11px] font-bold text-gray-400 uppercase mb-2 block">Situação Atual</label>
                            <div v-if="props.lancamento.foi_pago || props.lancamento.tipo === 'RESERVA_META'" class="space-y-2">
                                <div class="flex items-center gap-1.5 text-emerald-600 text-sm font-bold">
                                    <CheckCircle2 class="w-5 h-5" /> Confirmado / Pago
                                </div>
                                <div v-if="props.lancamento.data_quitacao" class="flex items-center gap-2 text-[11px] text-gray-500">
                                    <CalendarCheck class="w-3.5 h-3.5" />
                                    Pago em: {{ formatarData(props.lancamento.data_quitacao) }}
                                </div>
                            </div>
                            <div v-else class="flex items-center gap-1.5 text-amber-500 text-sm font-bold">
                                <XCircle class="w-5 h-5" /> Aguardando Pagamento
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 pt-2">
                            <div>
                                <label class="text-[11px] font-bold text-gray-400 uppercase mb-1 block tracking-tighter">Mês de Referência</label>
                                <div class="flex items-center gap-2 text-sm font-bold text-gray-700 font-mono">
                                    <Calendar class="w-4 h-4 text-indigo-500" />
                                    {{ formatarData(props.lancamento.mes_referencia) }}
                                </div>
                            </div>

                            <div>
                                <label class="text-[11px] font-bold text-gray-400 uppercase mb-1 block tracking-tighter">Periodicidade</label>
                                <div class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                    <Repeat class="w-4 h-4" :class="props.lancamento.recorrente ? 'text-blue-500' : 'text-gray-300'" />
                                    {{ props.lancamento.recorrente ? 'Mensal / Recorrente' : 'Lançamento Único' }}
                                </div>
                            </div>
                        </div>

                        <div v-if="props.lancamento.meta" class="pt-2 border-t border-gray-200">
                             <label class="text-[11px] font-bold text-gray-400 uppercase mb-1 block">Meta Associada</label>
                             <div class="flex items-center gap-2 text-sm font-bold text-indigo-600">
                                <Target class="w-4 h-4" />
                                {{ props.lancamento.meta.nome }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 flex gap-3 justify-end">
                <SecondaryButton @click="emit('close')">
                    Fechar
                </SecondaryButton>
                <PrimaryButton v-if="!props.lancamento?.foi_pago && props.lancamento.tipo !== 'RESERVA_META'" @click="confirmarMarcarComoPaga" :disabled="loadingMarkAsPaid">
                    Marcar como Pago
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>