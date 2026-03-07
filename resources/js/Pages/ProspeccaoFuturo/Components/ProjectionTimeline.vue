<script setup lang="ts">
import HelpMessage from "@/Components/HelpMessage.vue";
import Table from "@/Components/Table.vue";
import { formatarDinheiro } from "@/utils/helpers";

defineProps({
    projection: {
        type: Array,
        required: true,
    },
});

const headers = [
    { 
        label: "Mês", 
        key: "month", 
        align: "left",
        format: (v: any) => `${v}º`
    },
    {
        label: "Receita",
        key: "income",
        align: "left",
        format: (v: any) => formatarDinheiro(v),
    },
    {
        label: "Gastos",
        key: "expense",
        align: "left",
        format: (v: any) => formatarDinheiro(v),
    },
    {
        label: "Saldo Acumulado",
        key: "total",
        align: "left",
        format: (v: any) => formatarDinheiro(v),
    },
];
</script>

<template>
    <div
        class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100"
    >
        <div class="p-6 border-b border-gray-50 flex">
            <h3 class="font-bold text-gray-800">
                Cronograma de Evolução Patrimonial
            </h3>
            <HelpMessage
                message="Média baseada nos últimos 3 meses. Esta projeção é uma estimativa e não considera variações sazonais ou gastos atípicos. Recomendamos revisões periódicas para ajustes mais precisos."
                class="ml-[280px]"
            />
        </div>
        <div class="overflow-y-auto max-h-[400px]">
            <Table :headers="headers" :rows="projection">
                <template #cell-income="{ value }">
                    <span class="text-green-600">{{ formatarDinheiro(value) }}</span>
                </template>
                <template #cell-expense="{ value }">
                    <span class="text-red-400">{{ formatarDinheiro(value) }}</span>
                </template>
            </Table>
        </div>
    </div>
</template>
