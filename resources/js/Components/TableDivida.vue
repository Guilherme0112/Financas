<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, computed } from "vue";
import Checkbox from "./Checkbox.vue";

export interface TableHeader {
    label: string;
    key: string;
    align?: "left" | "center" | "right";
    format?: (value: any, row?: any) => string;
}

interface TableAction {
    label: string;
    class?: string;
    onClick: (row: any) => void;
}

const props = defineProps<{
    headers: TableHeader[];
    kanban?: any[];
    metas?: any;
    entradas?: any;
    saidas?: any;
    actions?: TableAction[] | ((row: any) => TableAction[]);
    theme?: string;
    selectable?: boolean;
}>();

const emit = defineEmits<{
    (e: "selectionChange", selectedRows: Record<string, any>[]): void;
    (e: "rowClick", row: Record<string, any>): void;
    (e: "mudarPaginaKanban", url: string): void;
}>();

const color = props.theme || "emerald";
const lastHeader = computed(() => props.headers[props.headers.length - 1]);

const groups = computed(() => {
    if (!props.kanban) return [];

    // Mapeamos o array que vem do Controller (0 => entradas, 1 => saidas, etc)
    return props.kanban.map(coluna => ({
        id: String(coluna.id),
        title: coluna.nome_coluna,
        rows: coluna.paginacao.data, // Os dados reais estão dentro de paginacao.data
        paginacao: coluna.paginacao, // O objeto completo para os botões de página
        soma: coluna.soma_valores
    }));
});

const totalRows = computed(() =>
    groups.value.reduce((acc, g) => acc + g.rows.length, 0),
);

const aberto = ref<string | null>(null);
const selectedRows = ref<Set<string>>(new Set());
const selectAll = ref(false);

const toggle = (id: string) => (aberto.value = aberto.value === id ? null : id);
const fechar = () => (aberto.value = null);

const toggleRowSelection = (groupId: string, rowIndex: number) => {
    const id = `${groupId}-${rowIndex}`;
    if (selectedRows.value.has(id)) {
        selectedRows.value.delete(id);
    } else {
        selectedRows.value.add(id);
    }
    updateSelectAll();
    emitSelection();
};

const updateSelectAll = () => {
    if (selectedRows.value.size === totalRows.value && totalRows.value > 0) {
        selectAll.value = true;
    } else {
        selectAll.value = false;
    }
};

const emitSelection = () => {
    const selected: Record<string, any>[] = [];
    groups.value.forEach((group) => {
        group.rows.forEach((row: any, index: any) => {
            if (selectedRows.value.has(`${group.id}-${index}`)) {
                selected.push(row);
            }
        });
    });
    emit("selectionChange", selected);
};

const handleClickOutside = (event: MouseEvent) => {
    const alvo = event.target as HTMLElement;
    if (!alvo.closest("[data-menu]")) fechar();
};

const resolveActions = (row: any) => {
    if (!props.actions) return [];
    if (typeof props.actions === "function") {
        const result = props.actions(row);
        return Array.isArray(result) ? result : [];
    }
    return props.actions;
};

onMounted(() => document.addEventListener("click", handleClickOutside));
onBeforeUnmount(() =>
    document.removeEventListener("click", handleClickOutside),
);
</script>

<template>
    <!-- Mudamos de flex-col para lg:flex-row para as colunas ficarem lado a lado em telas grandes -->
    <div class="w-full select-none flex flex-col xl:flex-row items-start">
        <!-- Iteração por cada grupo (Metas, Entradas, Saídas) -->
        <!-- Adicionado flex-1 w-full para dividir o espaço igualmente -->
        <div
            v-for="(group, gIndex) in groups"
            :key="group.id"
            class="flex-1 w-full flex flex-col bg-zinc-50/50 p-4 rounded-xl border border-zinc-100"
        >
            <!-- Cabeçalho do Grupo com "Selecionar Todos" integrado -->
            <div class="flex items-center justify-between mb-4 px-2">
                <h3
                    class="text-sm font-bold text-zinc-700 uppercase tracking-widest"
                >
                    {{ group.title }}
                </h3>
            </div>

            <!-- CORPO DA TABELA (Estilo Lista Compacta) -->
            <div class="flex flex-col gap-2">
                <!-- ESTADO VAZIO -->
                <div
                    v-if="group.rows.length === 0"
                    class="p-6 text-center bg-white rounded-lg border border-dashed border-zinc-200"
                >
                    <p
                        class="text-zinc-400 text-[10px] uppercase font-bold tracking-tighter"
                    >
                        Vazio
                    </p>
                </div>

                <!-- LINHAS COMPACTAS -->
                <div
                    v-else
                    v-for="(row, index) in group.rows"
                    :key="`${group.id}-${index}`"
                    class="relative group bg-white border border-zinc-100 p-3 rounded-lg transition-all duration-200 hover:border-zinc-200 hover:shadow-sm flex items-start justify-between cursor-pointer gap-3"
                    @click="emit('rowClick', row)"
                >
                    <!-- Checkbox da Linha -->
                    <div
                        v-if="selectable"
                        class="flex items-center shrink-0 mt-0.5"
                    >
                        <Checkbox
                            :checked="selectedRows.has(`${group.id}-${index}`)"
                            @change="toggleRowSelection(group.id, Number(index))"
                            @click.stop
                            class="w-4 h-4 rounded cursor-pointer bg-white border-emerald-300 border-2 checked:bg-emerald-500 checked:border-emerald-500"
                        />
                    </div>

                    <!-- Dados Essenciais e Adicionais -->
                    <div class="flex-1 flex flex-col gap-2 min-w-0">
                        <!-- LINHA SUPERIOR: Informações de Destaque -->
                        <div class="flex justify-between items-start gap-2">
                            <div class="flex flex-col min-w-0">
                                <!-- Título (1ª Coluna) -->
                                <div
                                    v-if="headers[0]"
                                    class="truncate text-sm font-bold text-zinc-800"
                                >
                                    <slot
                                        :name="`cell-${headers[0].key}`"
                                        :row="row"
                                        :value="row[headers[0].key]"
                                    >
                                        {{
                                            headers[0].format
                                                ? headers[0].format(
                                                      row[headers[0].key],
                                                      row,
                                                  )
                                                : row[headers[0].key]
                                        }}
                                    </slot>
                                </div>
                                <!-- Subtítulo (2ª Coluna) -->
                                <div
                                    v-if="headers.length > 1"
                                    class="truncate text-xs font-medium text-zinc-400 mt-0.5"
                                >
                                    <slot
                                        :name="`cell-${headers[1].key}`"
                                        :row="row"
                                        :value="row[headers[1].key]"
                                    >
                                        {{
                                            headers[1].format
                                                ? headers[1].format(
                                                      row[headers[1].key],
                                                      row,
                                                  )
                                                : row[headers[1].key]
                                        }}
                                    </slot>
                                </div>
                            </div>

                            <!-- Valor / Última Coluna (Alinhado à direita) -->
                            <div
                                v-if="headers.length > 2"
                                class="shrink-0 text-right pl-2 text-gray-600"
                            >
                                <slot
                                    :name="`cell-${lastHeader?.key}`"
                                    :row="row"
                                    :value="row[lastHeader?.key]"
                                >
                                    {{
                                        lastHeader?.format
                                            ? lastHeader.format(
                                                  row[lastHeader.key],
                                                  row,
                                              )
                                            : row[lastHeader?.key]
                                    }}
                                </slot>
                            </div>
                        </div>

                        <!-- LINHA INFERIOR: Informações Adicionais (Todas as outras colunas do meio) -->
                        <div
                            v-if="headers.length > 3"
                            class="flex flex-wrap gap-1.5 mt-1 border-t border-zinc-50 pt-2"
                        >
                            <template
                                v-for="header in headers.slice(2, -1)"
                                :key="header.key"
                            >
                                <div
                                    class="inline-flex items-center gap-1 bg-zinc-100/70 px-2 py-1 rounded text-[10px] text-zinc-500 max-w-full"
                                >
                                    <span
                                        class="font-bold uppercase tracking-wider opacity-70"
                                        >{{ header.label }}:</span
                                    >
                                    <span class="truncate">
                                        <slot
                                            :name="`cell-${header.key}`"
                                            :row="row"
                                            :value="row[header.key]"
                                        >
                                            {{
                                                header.format
                                                    ? header.format(
                                                          row[header.key],
                                                          row,
                                                      )
                                                    : row[header.key]
                                            }}
                                        </slot>
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Ações (Menu 3 Pontinhos) -->
                    <div
                        v-if="resolveActions(row).length > 0"
                        class="shrink-0 relative"
                        data-menu
                        @click.stop
                    >
                        <button
                            @click.stop="toggle(`${group.id}-${index}`)"
                            class="w-6 h-6 flex items-center justify-center rounded-md transition-all"
                            :class="
                                aberto === `${group.id}-${index}`
                                    ? `bg-${color}-100 text-${color}-600`
                                    : `text-zinc-400 hover:text-zinc-600 hover:bg-zinc-50`
                            "
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="3"
                                    d="M12 5v.01M12 12v.01M12 19v.01"
                                />
                            </svg>
                        </button>

                        <transition
                            enter-active-class="transition duration-150 ease-out"
                            enter-from-class="transform opacity-0 translate-y-1"
                            enter-to-class="transform opacity-100 translate-y-0"
                        >
                            <div
                                v-if="aberto === `${group.id}-${index}`"
                                class="absolute right-0 top-full mt-1 w-[160px] bg-white rounded-xl shadow-xl p-1 z-[100] border border-zinc-100"
                            >
                                <button
                                    v-for="(action, i) in resolveActions(row)"
                                    :key="i"
                                    @click.stop="
                                        () => {
                                            action.onClick(row);
                                            fechar();
                                        }
                                    "
                                    class="w-full text-left px-3 py-2 text-xs font-bold uppercase tracking-tight rounded-lg transition-all flex items-center justify-between group/item"
                                    :class="[
                                        `text-zinc-500 hover:bg-${color}-50 hover:text-${color}-700`,
                                        action.class,
                                    ]"
                                >
                                    {{ action.label }}
                                </button>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
            <!-- Controles de Paginação do Kanban -->

            <div
                v-if="group.paginacao && group.paginacao.last_page > 1"
                class="mt-auto pt-4 flex items-center justify-between border-t border-zinc-200"
            >
                <button
                    @click="
                        emit('mudarPaginaKanban', group.paginacao.prev_page_url)
                    "
                    :disabled="!group.paginacao?.prev_page_url"
                    class="text-[11px] px-3 py-1.5 rounded-md font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed bg-white border border-zinc-200 hover:bg-zinc-100 text-zinc-600 uppercase"
                >
                    Anterior
                </button>

                <span
                    class="text-[10px] text-zinc-400 font-bold uppercase tracking-wider"
                >
                    {{ group.paginacao?.current_page }} /
                    {{ group.paginacao?.last_page }}
                </span>

                <button
                    @click="
                        emit('mudarPaginaKanban', group.paginacao.next_page_url)
                    "
                    :disabled="!group.paginacao?.next_page_url"
                    class="text-[11px] px-3 py-1.5 rounded-md font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed bg-white border border-zinc-200 hover:bg-zinc-100 text-zinc-600 uppercase"
                >
                    Próxima
                </button>
            </div>
        </div>
    </div>
</template>
