<script setup lang="ts">
import Table from "@/Components/Table.vue";
import { formatarDinheiro } from "@/utils/helpers";

const props = defineProps<{
    headers: any[];
    rows: any[];
    actions: any[];
}>();
</script>

<template>
    <Table :headers="headers" :rows="rows" :actions="actions" theme="gray">
        <template #cell-tipo="{ row }">
            <span
                v-if="row.tipo === 'ENTRADA'"
                class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700"
            >
                + Entrada
            </span>

            <span
                v-else-if="row.tipo === 'SAIDA'"
                class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700"
            >
                − Saída
            </span>
            <span
                v-else-if="row.tipo === 'RESERVA_META'"
                class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700"
            >
                Reserva para Meta
            </span>
            <span
                v-else
                class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700"
            >
                Reserva de Emergência
            </span>
        </template>

        <template #cell-valor="{ row }">
            <span
                :class="
                    row.tipo === 'ENTRADA'
                        ? 'text-green-600 font-semibold'
                        : row.tipo === 'RESERVA_META'
                          ? 'text-blue-600 font-semibold'
                          : 'text-red-600 font-semibold'
                "
            >
                {{ formatarDinheiro(row.valor) }}
            </span>
        </template>
        <template #cell-categoria_label="{ row }">
            <span>
                {{
                    row.categoria_label ? row.categoria_label : "-"
                }}
            </span>
        </template>
    </Table>
</template>
