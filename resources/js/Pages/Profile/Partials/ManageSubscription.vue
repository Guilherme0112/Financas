<script setup lang="ts">
import NavLink from "@/Components/NavLink.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import HeaderSecao from "@/Pages/Dashboard/Components/HeaderSecao.vue";
import Table from "@/Components/Table.vue";
import { ArrowRight, File } from "lucide-vue-next";
import { formatarDinheiro } from "@/utils/helpers";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import UpgradePlano from "../Components/UpgradePlano.vue";

interface TableHeader {
    label: string;
    key: string;
    align?: "left" | "center" | "right";
    format?: (value: any, row?: any) => string;
}

interface Subscription {
    status: string;
    data_proxima_cobranca?: string;
    data_fim?: string;
    payment_link?: string;
    plano?: {
        nome: string;
        plano: string;
    };
}

const props = defineProps<{
    subscription?: Subscription;
    faturas: any;
    planos: any;
}>();
const formatDate = (dateString: string) => {
    if (!dateString) return "-";
    return new Date(dateString).toLocaleDateString("pt-BR");
};

// Estado para controlar a visibilidade do modal
const confirmingUpgrade = ref(false);

// Configuração do formulário de upgrade
const form = useForm({
    plano_id: 2,
});

const openModal = () => {
    confirmingUpgrade.value = true;
};

const closeModal = () => {
    confirmingUpgrade.value = false;
};

const submitUpgrade = () => {
    form.post(route("assinatura.upgrade"), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const tableHeaders: TableHeader[] = [
    { label: "Data", key: "vencimento_em", format: formatDate },
    { label: "Valor", key: "valor", format: formatarDinheiro },
    { label: "Status", key: "status", align: "center" },
];
</script>

<template>
    <section class="space-y-4">
        <div>
            <HeaderSecao
                :icon="File"
                title="Assinatura e Faturamento"
                description="Gerencie sua conta Premium e acompanhe seus pagamentos"
                icon-color="text-emerald-800"
            >
                <NavLink
                    :href="route('faturas.index')"
                    :active="route().current('faturas.index')"
                >
                    <PrimaryButton class="flex items-center gap-2">
                        Ver Mais
                        <ArrowRight :size="14" />
                    </PrimaryButton> </NavLink
            ></HeaderSecao>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div
                class="lg:col-span-1 p-6 border border-emerald-100 rounded-2xl bg-white shadow-sm flex flex-col justify-between"
            >
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span
                            class="text-xs font-bold uppercase tracking-wider text-emerald-700"
                            >Plano Atual</span
                        >
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">
                        {{ subscription?.plano?.nome || "Free" }}
                    </h3>
                    <p
                        v-if="subscription?.data_proxima_cobranca"
                        class="mt-2 text-sm text-gray-500"
                    >
                        Próxima cobrança:
                        <span class="font-medium text-gray-900">{{
                            new Date(
                                subscription.data_proxima_cobranca,
                            ).toLocaleDateString()
                        }}</span>
                    </p>
                    <p
                        v-else-if="subscription?.data_fim"
                        class="mt-2 text-sm text-gray-500"
                    >
                        Fim do período:
                        <span class="font-medium text-gray-900">{{
                            new Date(subscription.data_fim).toLocaleDateString()
                        }}</span>
                    </p>
                </div>

                <div class="mt-8">
                    <button
                        v-if="subscription?.plano?.plano === 'GRATUITO'"
                        @click="openModal"
                        class="w-full inline-flex uppercase justify-center items-center px-4 py-3 bg-emerald-600 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-emerald-700 active:bg-emerald-800 transition duration-150"
                    >
                        Fazer Upgrade
                    </button>

                    <button
                        v-else
                        disabled
                        class="w-full inline-flex uppercase justify-center items-center px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl font-semibold text-sm text-gray-400 cursor-not-allowed"
                    >
                        {{
                            subscription?.status === "APROVADO"
                                ? "Plano Ativo"
                                : "Pendente"
                        }}
                    </button>
                </div>
            </div>

            <div
                class="lg:col-span-2 border border-gray-100 rounded-2xl overflow-hidden bg-gray-50/50"
            >
                <Table
                    v-if="props.faturas?.data?.length > 0"
                    :headers="tableHeaders"
                    :rows="props.faturas?.data"
                    theme="gray"
                >
                    <template #cell-metodo_pagamento_label="{ value }">
                        <span v-if="value" class="flex items-center gap-1">
                            <CreditCard class="w-3 h-3" />
                            {{ value }}
                        </span>
                        <span v-else>-</span>
                    </template>

                    <template #cell-status="{ row }">
                        <span
                            v-if="row.status === 'APROVADO'"
                            class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100"
                        >
                            Pago em {{ formatDate(row.pago_em) }}
                        </span>
                        <span
                            v-else
                            class="text-[10px] font-bold uppercase tracking-wider text-amber-600 bg-amber-50 px-2 py-1 rounded-md border border-amber-100"
                        >
                            Pendente
                        </span>
                    </template>
                </Table>
            </div>
        </div>
    </section>

    <UpgradePlano
        :show="confirmingUpgrade"
        :planos="props.planos"
        @close="confirmingUpgrade = false"
    />
</template>
