<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import FinanceCard from '@/Components/FinanceCard.vue'
import Table from '@/Components/Table.vue'
import LancamentoForm from './Partials/LancamentoForm.vue'
import { Lancamento } from '@/types/Lancamentos'
import { Page } from '@/types/Page'
import { formatarData, formatarDinheiro } from '@/utils/helpers'
import { toast } from 'vue3-toastify';
import NavLink from '@/Components/NavLink.vue'
import FiltrosLancamentos from './Partials/FiltrosLancamentos.vue';
import { Plus, SlidersHorizontal } from 'lucide-vue-next'
import Paginacao from '@/Components/Paginacao.vue'

const props = defineProps<{
  lancamentos: Page<Lancamento>
  categoriasEntrada: any[],
  categoriasSaida: any[]
}>();


const showModal = ref(false);
const editando = ref<Lancamento | null>(null);
const filtro = ref<'TODOS' | 'ENTRADA' | 'SAIDA'>('TODOS');
const mostrarFiltro = ref(false);

const form = useForm({
  id: null,
  nome: '',
  descricao: '',
  valor: '',
  tipo: 'SAIDA',
  recorrente: false,
  mes_referencia: '',
  categoria_entrada: null,
  categoria_saida: null
});

const headers = [
  {
    label: 'Tipo',
    key: 'tipo',
    align: 'center'
  },
  { label: 'Nome', key: 'nome' },
  {
    label: 'Valor',
    key: 'valor',
    align: 'right'
  },
  {
    label: 'Fixo',
    key: 'recorrente',
    align: 'center',
    format: (v: any) => v ? 'Sim' : 'Não'
  },
  {
    label: 'Mês',
    key: 'mes_referencia',
    align: 'center',
    format: (v: any) => formatarData(v) || '-'
  }
];

const actions = [
  {
    label: 'Editar',
    class: 'hover:bg-green-50 text-green-700',
    onClick: (row: any) => abrirEdicao(row)
  },
  {
    label: 'Excluir',
    class: 'hover:bg-red-50 text-red-600',
    onClick: (row: any) => excluir(row.id)
  }
]

const lancamentosFiltrados = computed(() => {
  if (filtro.value === 'TODOS') return props.lancamentos.data
  return props.lancamentos.data.filter(l => l.tipo === filtro.value)
});

const totalEntradas = computed(() =>
  props.lancamentos.data
    .filter(l => l.tipo === 'ENTRADA')
    .reduce((t, l) => t + Number(l.valor), 0)
);

const totalSaidas = computed(() =>
  props.lancamentos.data
    .filter(l => l.tipo === 'SAIDA')
    .reduce((t, l) => t + Number(l.valor), 0)
);

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
  showModal.value = true;
}

const excluir = (id: number) => {
  if (confirm('Excluir este lançamento?')) {
    form.delete(route('lancamentos.destroy', id), {
      onSuccess: () => {
        toast.success('Lançamento excluído com sucesso!');
      }
    })
  }
}

const mudarPagina = (page: number) => {
  router.get(
    route('lancamentos.index'),
    { page },
    {
      preserveState: true,
      replace: true,
    }
  )
}

</script>
<template>

  <Head title="Gestão Financeira" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-start items-center">
        <h2 class="text-xl font-semibold text-emerald-800">Gestão Financeira</h2>
        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex h-16">
          <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
            Configurações
          </NavLink>
        </div>
      </div>
    </template>

    <div class="py-10 max-w-7xl mx-auto space-y-8 px-2">

      <!-- RESUMO -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <FinanceCard title="Entradas" :value="totalEntradas" type="positive" />
        <FinanceCard title="Saídas" :value="totalSaidas" type="negative" />
        <FinanceCard title="Saldo" :value="totalEntradas - totalSaidas" />
      </div>

      <!-- FILTROS -->
      <div class="flex justify-between">
        <div>
          <PrimaryButton @click="mostrarFiltro = true">
            <SlidersHorizontal size='16' />
          </PrimaryButton>
        </div>

        <PrimaryButton @click="abrirNovo">
          <Plus size='16' />
        </PrimaryButton>
      </div>

      <!-- TABELA -->
      <Table :headers="headers" :rows="lancamentosFiltrados" :actions="actions">
        <template #cell-tipo="{ row }">
          <span v-if="row.tipo === 'ENTRADA'" class="inline-flex items-center px-2 py-1 text-xs font-semibold
             rounded-full bg-green-100 text-green-700">
            + Entrada
          </span>

          <span v-else class="inline-flex items-center px-2 py-1 text-xs font-semibold
             rounded-full bg-red-100 text-red-700">
            − Saída
          </span>
        </template>

        <template #cell-valor="{ row }">
          <span :class="row.tipo === 'ENTRADA'
            ? 'text-green-600 font-semibold'
            : 'text-red-600 font-semibold'">
            {{ formatarDinheiro(row.valor) }}
          </span>
        </template>
      </Table>

      <!-- PAGINAÇÃO  -->
      <Paginacao :pagination="lancamentos" @change="mudarPagina" />

      <!-- FORMULÁRIO PARA CRIAR LANÇAMENTO -->
      <LancamentoForm :show="showModal" :form="form" :editando="!!editando"
        :categorias-entrada="props.categoriasEntrada" :categorias-saida="props.categoriasSaida"
        @close="showModal = false; form.resetAndClearErrors()" :id="editando?.id" />

      <!-- FILTROS DOS LANÇAMENTOS -->
      <FiltrosLancamentos :show="mostrarFiltro" @close="mostrarFiltro = false" />

    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.btn {
  padding: 6px 16px;
  border-radius: 8px;
  border: 1px solid #d1d5db;
}

.btn-green {
  background: #dcfce7;
  color: #166534;
  padding: 6px 16px;
  border-radius: 8px;
}

.btn-red {
  background: #fee2e2;
  color: #991b1b;
  padding: 6px 16px;
  border-radius: 8px;
}
</style>
