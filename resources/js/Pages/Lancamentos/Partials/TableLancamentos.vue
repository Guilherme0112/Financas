<script setup lang="ts">
import { ref, watch } from "vue";
import Table from "@/Components/Table.vue";
import TableDivida, { type TableHeader } from "@/Components/TableDivida.vue";
import { formatarDinheiro, formatarData } from "@/utils/helpers";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Kanban, List } from "lucide-vue-next";

const props = defineProps<{
    headers: any[];
    kanban?: any[];
    rows?: any[];
    metas?: any[];
    entradas?: any[];
    saidas?: any[];
    currentView?: "normal" | "agrupado";
    actions: any[] | ((row: any) => any[]);
    loading?: boolean;
}>();

const emit = defineEmits<{
    (e: "rowClick", row: any): void;
    (e: "selectionChange", selectedRows: any[]): void;
    (e: "changeView", view: "normal" | "agrupado"): void;
}>();

// Estado do toggle (inicia com base na prop passada pela página)
const viewMode = ref<"normal" | "agrupado">(props.currentView || "normal");
watch(
    () => props.currentView,
    (value) => {
        if (value && viewMode.value !== value) {
            viewMode.value = value;
        }
    },
);

const dividaHeaders: TableHeader[] = [
    { label: "Nome", key: "nome" },
    { label: "Mês", key: "mes_referencia" },
    { label: "Categoria", key: "categoria_label" },
    { label: "Valor", key: "valor", align: "right" },
];

const setViewMode = (mode: "normal" | "agrupado") => {
    if (viewMode.value !== mode) {
        viewMode.value = mode;
        emit("changeView", mode);
    }
};
</script>

<template>
    <div class="flex flex-col gap-4 w-full">
        <!-- Toggle de Visualização -->
        <div class="flex justify-end w-full">
            <div class="inline-flex bg-zinc-100 p-1 gap-2 rounded-lg">
                <PrimaryButton
                    @click="setViewMode('normal')"
                    :class="
                        viewMode === 'normal'
                            ? 'outline-none ring-2 ring-emerald-500 ring-offset-2'
                            : null
                    "
                    title="Visualização Normal"
                >
                    <List :size="16" :stroke-width="3" />
                </PrimaryButton>

                <PrimaryButton
                    @click="setViewMode('agrupado')"
                    :class="
                        viewMode === 'agrupado'
                            ? 'outline-none ring-2 ring-emerald-500 ring-offset-2'
                            : null
                    "
                    title="Visualização Agrupada (Kanban)"
                >
                    <Kanban :size="16" :stroke-width="3" />
                </PrimaryButton>
            </div>
        </div>

        <!-- TABELA NORMAL -->
        <div v-if="viewMode === 'normal' && rows">
            <div v-if="props.loading" class="flex justify-center items-center py-8">
                <div class="flex items-center gap-2 text-gray-500">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-emerald-500"></div>
                    Carregando...
                </div>
            </div>
            <Table
                v-else
                :headers="headers"
                :rows="rows"
                :actions="actions"
                :selectable="true"
                theme="gray"
                @rowClick="emit('rowClick', $event)"
                @selectionChange="emit('selectionChange', $event)"
            >
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
                    <span>{{
                        row.categoria_label ? row.categoria_label : "-"
                    }}</span>
                </template>
            </Table>
        </div>

        <!-- NOVO: TABELA AGRUPADA (KANBAN) -->
        <div v-else-if="viewMode === 'agrupado'">
            <div v-if="props.loading" class="flex justify-center items-center py-8">
                <div class="flex items-center gap-2 text-gray-500">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-emerald-500"></div>
                    Carregando...
                </div>
            </div>
            <TableDivida
                v-else
                :kanban="props.kanban"
                :headers="dividaHeaders"
                :metas="props.metas"
                :entradas="props.entradas"
                :saidas="props.saidas"
                :actions="actions"
                :selectable="true"
                theme="green"
                @rowClick="emit('rowClick', $event)"
                @selectionChange="emit('selectionChange', $event)"
            >
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

                <template #cell-mes_referencia="{ row }">
                    {{ formatarData(row.mes_referencia) }}
                </template>

                <template #cell-categoria_label="{ row }">
                    <span>{{
                        row.categoria_label ? row.categoria_label : "-"
                    }}</span>
                </template>

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
            </TableDivida>
        </div>
    </div>
</template>
