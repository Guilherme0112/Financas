<script setup lang="ts">
import { Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import HeaderSecao from "@/Pages/Dashboard/Components/HeaderSecao.vue";
import { File, FileText, CreditCard } from "lucide-vue-next";
import SemRegistro from "@/Pages/Dashboard/Partials/SemRegistro.vue";
import Table from "@/Components/Table.vue"; // Ajuste o caminho conforme seu projeto
import { formatarDinheiro } from "@/utils/helpers";
import Paginacao from "@/Components/Paginacao.vue";
import { Page } from "@/types/Page";
import HelpMessage from "@/Components/HelpMessage.vue";

const props = defineProps<{
    faturas: Page<any>;
}>();

const formatDate = (dateString: string) => {
    if (!dateString) return "-";
    return new Date(dateString).toLocaleDateString("pt-BR");
};

// Configuração das Colunas da Tabela
const headers = [
    { label: "Vencimento", key: "vencimento_em", format: formatDate },
    { label: "Valor", key: "valor", format: formatarDinheiro },
    { label: "Tipo", key: "tipo_cobranca_label" },
    { label: "Método", key: "metodo_pagamento_label" },
    { label: "Status", key: "status", align: "center" as const },
];

// Configuração das Ações Dinâmicas
const getActions = (invoice: any) => {
    const actions = [];

    if (invoice.status === "PENDENTE" && invoice.url_pagamento) {
        actions.push({
            label: "Pagar Agora",
            class: "text-amber-600 hover:bg-amber-50",
            onClick: (row: any) => window.open(row.url_pagamento, "_blank"),
        });
        return actions;
    }

    return [];
};
</script>

<template>
    <Head title="Faturas" />
    <AuthenticatedLayout>
        <div class="py-6 space-y-4">
            <div class="p-6 bg-white rounded-2xl font-sans shadow-lg">
                <div class="flex">
                    <HeaderSecao
                        :icon="File"
                        title="Minhas Faturas"
                        description="Visualize e baixe o histórico completo das suas faturas"
                        icon-color="text-emerald-800"
                    />
                    <HelpMessage
                        message="As faturas são geradas automaticamente 7 dias antes do vencimento da sua assinatura."
                        class="ml-[190px]"
                    />
                </div>

                <div class="mt-6">
                    <Table
                        v-if="faturas?.data?.length > 0"
                        :headers="headers"
                        :rows="faturas.data"
                        :actions="getActions"
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

                    <div
                        v-else
                        class="flex flex-col items-center justify-center min-h-[250px] p-6 bg-white border border-dashed border-zinc-200 rounded-2xl"
                    >
                        <SemRegistro
                            message="Você ainda não possui faturas geradas no SaldoUp."
                        />
                    </div>
                </div>
            </div>
            <div class="w-full flex justify-end">
                <Paginacao
                    :pagination="faturas"
                    route-name="lancamentos.index"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
