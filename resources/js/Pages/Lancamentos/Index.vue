<script setup lang="ts">
import { onMounted, onUnmounted, ref } from "vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import LancamentoForm from "./Components/LancamentoForm.vue";
import { Lancamento } from "@/types/Lancamentos";
import { Page } from "@/types/Page";
import FiltrosLancamentos from "./Components/FiltrosLancamentos.vue";
import Paginacao from "@/Components/Paginacao.vue";
import ConfirmDeleteModal from "@/Components/ConfirmDeleteModal.vue";
import { useLancamentos } from "./Composables/useLancamentos";
import ImportarDados from "./Components/ImportarDados.vue";
import ExportarDados from "./Components/ExportarDados.vue";
import echo from "@/echo";
import { toast } from "vue3-toastify";
import TableLancamentos from "./Partials/TableLancamentos.vue";
import Acoes from "./Partials/Acoes.vue";
import { h } from "vue";
import Resumo from "./Partials/Resumo.vue";
import Load from "@/Components/Load.vue";
import { Metas } from "@/types/Metas";

const props = defineProps<{
    lancamentos: Page<Lancamento>;
    metas: Page<Metas>;
    categoriasEntrada: any[];
    categoriasSaida: any[];
    tipo: any[];
}>();

const {
    lancamentosFiltrados,
    totalEntradas,
    totalSaidas,
    showDeleteModal,
    pedirExclusao,
    confirmarExclusao,
    mudarPagina,
    deleteForm,
    headers,
} = useLancamentos();

const page = usePage();
const showModal = ref(false);
const editando = ref<Lancamento | null>(null);
const importarDados = ref(false);
const showExport = ref(false);
const mostrarFiltro = ref(false);
const loadImportacao = ref(false);
const loadExportacao = ref(false);

const form = useForm({
    id: null,
    nome: "",
    descricao: "",
    valor: "",
    tipo: "SAIDA",
    recorrente: false,
    meses_recorrentes: null,
    mes_referencia: "",
    categoria_entrada: null,
    categoria_saida: null,
    foi_pago: null,
    meta: null,
});

const actions = [
    {
        label: "Editar",
        class: "hover:bg-green-50 text-green-700",
        onClick: (row: any) => abrirEdicao(row),
    },
    {
        label: "Duplicar",
        class: "hover:bg-green-50 text-green-700",
        onClick: (row: any) => duplicar(row),
    },
    {
        label: "Excluir",
        class: "hover:bg-red-50 text-red-600",
        onClick: (row: any) => pedirExclusao(row.id),
    },
];

const abrirNovo = () => {
    editando.value = null;
    form.reset();
    showModal.value = true;
};

const abrirEdicao = (l: any) => {
    editando.value = l;
    form.id = l.id;
    form.nome = l.nome;
    form.descricao = l.descricao;
    form.valor = String(l.valor);
    form.tipo = l.tipo;
    form.recorrente = l.recorrente;
    form.categoria_saida = l.categoria_saida;
    form.categoria_entrada = l.categoria_entrada;
    form.mes_referencia = l.mes_referencia || "";
    form.foi_pago = l.foi_pago;
    form.meta = l?.meta?.id || null;
    showModal.value = true;
};

const duplicar = (l: any) => {
    editando.value = l;
    form.nome = l.nome;
    form.descricao = l.descricao;
    form.valor = String(l.valor);
    form.tipo = l.tipo;
    form.recorrente = l.recorrente;
    form.categoria_saida = l.categoria_saida;
    form.categoria_entrada = l.categoria_entrada;
    form.mes_referencia = l.mes_referencia || "";
    form.foi_pago = l.foi_pago;
    form.meta = l?.meta?.id || null;
    showModal.value = true;
};

onMounted(() => {
    const canal = echo.private(`users.${page.props.auth.user.id}`);
    canal.listen(".ImportacaoFinalizada", (e: any) => {
        loadImportacao.value = false;
        if (e.error) {
            toast.error(`Ocorreu um erro durante a importação: ${e.error}`);
            return;
        }
        mudarPagina(1);
        toast.success("Sua importação foi finalizada com sucesso!");
    });

    // Timeout pois algumas exportações são tão rápidas que faz a var que
    // controla o load mudar para false antes mesmo de inicar, logo ela inicia
    // em true e fica carregando infinitamente
    canal.listen(".ExportacaoFinalizada", async (e: any) => {
        const start = performance.now();
        showExport.value = false;

        if (e.error) {
            loadExportacao.value = false;
            toast.error(`Ocorreu um erro durante a exportação: ${e.error}`);
            return;
        }

        const elapsed = performance.now() - start;
        const delay = Math.max(1000 - elapsed, 0);
        setTimeout(() => {
            loadExportacao.value = false;

            const url = route("exportar.download", { id: e.exportacaoId });
            toast.success(
                () =>
                    h("div", { class: "flex flex-col gap-2" }, [
                        h("span", "Sua exportação está pronta!"),
                        h(
                            "a",
                            {
                                href: url,
                                class: "text-emerald-600 font-semibold underline",
                            },
                            "Clique aqui para baixar",
                        ),
                    ]),
                { autoClose: false, toastId: "export-success" },
            );
        }, delay);
    });
});

onUnmounted(() => {
    echo.leave(`users.${page.props.auth.user.id}`);
});
</script>
<template>
    <Head title="Lançamentos" />
    <AuthenticatedLayout>
        <div class="py-10 space-y-5 px-2">
            <!-- RESUMO -->
            <Resumo
                :total-entradas="totalEntradas"
                :total-saidas="totalSaidas"
            />

            <!-- AÇÕES -->
            <Acoes
                @novo="abrirNovo"
                @filtro="mostrarFiltro = true"
                @importar="importarDados = true"
                @exportar="showExport = true"
            />

            <!-- TABELA -->
            <TableLancamentos
                :headers="headers"
                :rows="lancamentosFiltrados"
                :actions="actions"
                theme="green"
            />

            <!-- PAGINAÇÃO  -->
            <div class="w-full flex justify-end">
                <Paginacao
                    :pagination="lancamentos"
                    route-name="lancamentos.index"
                />
            </div>

            <!-- FORMULÁRIO PARA CRIAR LANÇAMENTO -->
            <LancamentoForm
                :show="showModal"
                :form="form"
                :editando="!!editando"
                :categorias-entrada="props.categoriasEntrada"
                :categorias-saida="props.categoriasSaida"
                @close="
                    showModal = false;
                    form.resetAndClearErrors();
                "
                :id="editando?.id"
                :tipo="props.tipo"
                :metas="props.metas.data"
            />

            <!-- IMPORTAR DADOS -->
            <ImportarDados
                :show="importarDados"
                @close="importarDados = false"
                @start="loadImportacao = true"
            />

            <!-- EXPORTAR DADOS -->
            <ExportarDados
                :show="showExport"
                @close="showExport = false"
                @start="loadExportacao = true"
            />

            <!-- FILTROS DOS LANÇAMENTOS -->
            <FiltrosLancamentos
                :show="mostrarFiltro"
                @close="mostrarFiltro = false"
                :categorias-entrada="props.categoriasEntrada"
                :categorias-saida="props.categoriasSaida"
            />

            <!-- DELETAR LANÇAMENTOS -->
            <ConfirmDeleteModal
                :show="showDeleteModal"
                message="Tem certeza que deseja excluir este lançamento?"
                @close="showDeleteModal = false"
                @confirm="confirmarExclusao"
                :isDisabled="deleteForm.processing"
            />
        </div>

        <!-- LOAD DE IMPORTAÇÃO -->
        <Load
            :show="loadImportacao"
            :message="'Estamos importando suas finanças... Aguarde um instante'"
        />

        <!-- LOAD DE EXPORTAÇÃO -->
        <Load
            :show="loadExportacao"
            :message="'Estamos exportando suas finanças... Aguarde um instante'"
        />
    </AuthenticatedLayout>
</template>
