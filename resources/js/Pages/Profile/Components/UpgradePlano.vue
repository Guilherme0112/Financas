<script setup lang="ts">
import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Check, CreditCard } from "lucide-vue-next";
import { formatarDinheiro } from "@/utils/helpers";

const props = defineProps<{
    show: boolean;
    planos: any;
}>();

const emit = defineEmits(["close"]);

const planoSelecionado = ref<number | null>(null);

const form = useForm({
    plano_id: null as number | null,
});

const opcoesDeUpgrade = computed(() => {
    return props.planos.filter((p: any) => p.plano !== 'GRATUITO');
});

const closeModal = () => {
    emit("close");
    planoSelecionado.value = null;
    form.reset();
};

const confirmarEPagar = () => {
    if (!planoSelecionado.value) return;
    form.plano_id = planoSelecionado.value;
    form.post(route('assinatura.upgrade'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <Modal :show="show" @close="closeModal" max-width="md">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900">Turbine sua conta</h2>
            <p class="mt-2 text-sm text-gray-500">
                Selecione o plano ideal para o seu momento e confirme abaixo.
            </p>

            <div class="mt-6 space-y-3">
                <div 
                    v-for="plano in opcoesDeUpgrade" 
                    :key="plano.id"
                    @click="planoSelecionado = plano.id"
                    :class="[
                        planoSelecionado === plano.id 
                            ? 'border-emerald-500 bg-emerald-50/50 ring-1 ring-emerald-500' 
                            : 'border-gray-100 bg-white hover:border-emerald-200',
                        'relative p-4 border-2 rounded-2xl transition-all cursor-pointer'
                    ]"
                >
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div :class="[
                                planoSelecionado === plano.id ? 'bg-emerald-500 border-emerald-500' : 'border-gray-300',
                                'w-5 h-5 border rounded-full flex items-center justify-center transition-colors'
                            ]">
                                <Check v-if="planoSelecionado === plano.id" :size="12" class="text-white" />
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">{{ plano.nome }}</h3>
                                <p class="text-[11px] text-gray-500">{{ plano.descricao }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block text-lg font-black text-emerald-600">
                                {{ plano.preco ? formatarDinheiro(plano.preco) : 'R$ 20,00' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3">
                <PrimaryButton
                    class="w-full py-3 flex items-center justify-center gap-2 text-sm"
                    :disabled="!planoSelecionado || form.processing"
                    @click="confirmarEPagar"
                >
                    <CreditCard v-if="!form.processing" :size="16" />
                    <span>{{ form.processing ? 'Redirecionando...' : 'Confirmar e Ir para Pagamento' }}</span>
                </PrimaryButton>

                <SecondaryButton @click="closeModal" class="justify-center border-none shadow-none text-gray-400 hover:text-gray-600" :disabled="form.processing">
                    Cancelar
                </SecondaryButton>
            </div>
        </div>
    </Modal>
</template>