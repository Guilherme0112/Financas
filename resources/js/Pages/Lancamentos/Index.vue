<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LancamentoForm from './Components/LancamentoForm.vue';
import { Lancamento } from '@/types/Lancamentos';
import { Page } from '@/types/Page';
import FiltrosLancamentos from './Components/FiltrosLancamentos.vue';
import Paginacao from '@/Components/Paginacao.vue';
import DeleteLancamento from './Components/DeleteLancamento.vue';
import { useLancamentos } from './Composables/useLancamentos';
import ImportarDados from './Components/ImportarDados.vue';
import ExportarDados from './Components/ExportarDados.vue';
import echo from '@/echo';
import { toast } from 'vue3-toastify';
import TableLancamentos from './Partials/TableLancamentos.vue';
import Acoes from './Partials/Acoes.vue';
import { h } from 'vue'
import Resumo from './Partials/Resumo.vue';
import Load from '@/Components/Load.vue';

const props = defineProps<{
  lancamentos: Page<Lancamento>
  categoriasEntrada: any[],
  categoriasSaida: any[]
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
  headers
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
  nome: '',
  descricao: '',
  valor: '',
  tipo: 'SAIDA',
  recorrente: false,
  meses_recorrentes: null,
  mes_referencia: '',
  categoria_entrada: null,
  categoria_saida: null,
  foi_pago: null
});

const actions = [
  {
    label: 'Editar',
    class: 'hover:bg-green-50 text-green-700',
    onClick: (row: any) => abrirEdicao(row)
  },
  {
    label: 'Excluir',
    class: 'hover:bg-red-50 text-red-600',
    onClick: (row: any) => pedirExclusao(row.id)
  }
];

const abrirNovo = () => {
  editando.value = null;
  form.reset();
  showModal.value = true;
}

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
  form.mes_referencia = l.mes_referencia || '';
  form.foi_pago = l.foi_pago;
  showModal.value = true;
}

onMounted(() => {
  echo.private(`users.${page.props.auth.user.id}`)
    .listen('.ImportacaoFinalizada', (e: any) => {
      loadImportacao.value = false;
      if(e.error){
        toast.error(`Ocorreu um erro durante a importação: ${e.error}`);
        return;
      }
      
      mudarPagina(1);
      toast.success('Sua importação foi finalizada com sucesso!');
    });
});

onMounted(() => {
  echo.private(`users.${page.props.auth.user.id}`)
    .listen('.ExportacaoFinalizada', (e: any) => {
      showExport.value = false;
      loadExportacao.value = false;

      if(e.error){
        toast.error(`Ocorreu um erro durante a exportação: ${e.error}`);
        return;
      }

      const url = route("exportar.download", { id: e.exportacaoId });
      toast.success(
        () =>
          h('div', { class: 'flex flex-col gap-2' }, [
            h('span', 'Sua exportação está pronta!'),
            h(
              'a',
              {
                href: url,
                class: 'text-emerald-600 font-semibold underline'
              },
              'Clique aqui para baixar'
            )
          ]),
        { autoClose: false }
      )
    });
});

</script>
<template>

  <Head title="Lançamentos" />
  <AuthenticatedLayout>
    <div class="py-10 max-w-7xl mx-auto space-y-8 px-2">

      <!-- RESUMO -->
      <Resumo :total-entradas="totalEntradas" :total-saidas="totalSaidas" />

      <!-- AÇÕES -->
      <Acoes @novo="abrirNovo" @filtro="mostrarFiltro = true" @importar="importarDados = true"
        @exportar="showExport = true" />

      <!-- TABELA -->
      <TableLancamentos :headers="headers" :rows="lancamentosFiltrados" :actions="actions" theme="green" />

      <!-- PAGINAÇÃO  -->
      <Paginacao :pagination="lancamentos" @change="mudarPagina" />

      <!-- FORMULÁRIO PARA CRIAR LANÇAMENTO -->
      <LancamentoForm :show="showModal" :form="form" :editando="!!editando"
        :categorias-entrada="props.categoriasEntrada" :categorias-saida="props.categoriasSaida"
        @close="showModal = false; form.resetAndClearErrors()" :id="editando?.id" />

      <!-- IMPORTAR DADOS -->
      <ImportarDados :show="importarDados" @close="importarDados = false" @start="loadImportacao = true" />

      <!-- EXPORTAR DADOS -->
      <ExportarDados :show="showExport" @close="showExport = false" @start="loadExportacao = true" />

      <!-- FILTROS DOS LANÇAMENTOS -->
      <FiltrosLancamentos :show="mostrarFiltro" @close="mostrarFiltro = false"
        :categorias-entrada="props.categoriasEntrada" :categorias-saida="props.categoriasSaida" />

      <!-- DELETAR LANÇAMENTOS -->
      <DeleteLancamento :show="showDeleteModal" @close="showDeleteModal = false" @confirm="confirmarExclusao"
        :isDisabled="deleteForm.processing" />
    </div>

    <!-- LOAD DE IMPORTAÇÃO -->
    <Load :show="loadImportacao" :message="'Estamos importando suas finanças... Aguarde um instante'" />

    <!-- LOAD DE EXPORTAÇÃO -->
    <Load :show="loadExportacao" :message="'Estamos exportando suas finanças... Aguarde um instante'" />

  </AuthenticatedLayout>
</template>
