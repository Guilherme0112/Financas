<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import FinanceCard from '@/Components/FinanceCard.vue'
import Table from '@/Components/Table.vue'
import LancamentoForm from './LancamentoForm.vue'
import { Lancamento } from '@/types/Lancamentos'
import { Page } from '@/types/Page'
import { formatarDinheiro } from '@/utils/helpers'
import { toast } from 'vue3-toastify'

const props = defineProps<{
  lancamentos: Page<Lancamento>
}>()

const showModal = ref(false)
const editando = ref<Lancamento | null>(null)
const filtro = ref<'TODOS' | 'ENTRADA' | 'SAIDA'>('TODOS')

const form = useForm({
  nome: '',
  descricao: '',
  valor: '',
  tipo: 'SAIDA',
  recorrente: false,
  mes_referencia: '',
  categoria_id: null
})

/* ==========================
   DADOS FILTRADOS
========================== */
const lancamentosFiltrados = computed(() => {
  if (filtro.value === 'TODOS') return props.lancamentos.data
  return props.lancamentos.data.filter(l => l.tipo === filtro.value)
})

const totalEntradas = computed(() =>
  props.lancamentos.data
    .filter(l => l.tipo === 'ENTRADA')
    .reduce((t, l) => t + Number(l.valor), 0)
)

const totalSaidas = computed(() =>
  props.lancamentos.data
    .filter(l => l.tipo === 'SAIDA')
    .reduce((t, l) => t + Number(l.valor), 0)
)

/* ==========================
   MODAL
========================== */
const abrirNovo = () => {
  editando.value = null;
  form.reset()
  showModal.value = true;
}

const abrirEdicao = (l: Lancamento) => {
  editando.value = l
  form.nome = l.nome
  form.descricao = l.descricao
  form.valor = String(l.valor)
  form.tipo = l.tipo
  form.recorrente = l.recorrente
  form.mes_referencia = l.mes_referencia || ''
  showModal.value = true
}

/* ==========================
   CRUD
========================== */
const salvar = () => {
  if (editando.value) {
    form.put(route('gestao.update', editando.value.id), {
      onSuccess: () => {
        toast.success('Lançamento atualizado com sucesso!')
        showModal.value = false;
      }
    })
  } else {
    form.post(route('gestao.store'), {
      onSuccess: () => {
        toast.success('Lançamento criado com sucesso!')
        showModal.value = false;
      }
    })
  }
}

const excluir = (id: number) => {
  if (confirm('Excluir este lançamento?')) {
    form.delete(route('gestao.destroy', id), {
      onSuccess: () => {
        toast.success('Lançamento excluído com sucesso!')
      }
    })
  }
}

/* ==========================
   PAGINAÇÃO REAL (URL)
========================== */
const mudarPagina = (num: number) => {
  if (num >= 1 && num <= props.lancamentos.last_page) {
    router.get(route('gestao.index'), { page: num }, {
      preserveScroll: true,
      preserveState: true
    })
  }
}
</script>

<template>

  <Head title="Gestão Financeira" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-emerald-800">Gestão Financeira</h2>
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
        <div class="flex gap-3">
          <button @click="filtro = 'TODOS'" :class="[
            'px-4 py-2 rounded-lg border transition',
            filtro === 'TODOS'
              ? 'bg-emerald-600 text-white border-emerald-700'
              : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'
          ]">
            Todos
          </button>

          <button @click="filtro = 'ENTRADA'" :class="[
            'px-4 py-2 rounded-lg border transition',
            filtro === 'ENTRADA'
              ? 'bg-green-600 text-white border-green-700'
              : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'
          ]">
            Entradas
          </button>

          <button @click="filtro = 'SAIDA'" :class="[
            'px-4 py-2 rounded-lg border transition',
            filtro === 'SAIDA'
              ? 'bg-red-600 text-white border-red-700'
              : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'
          ]">
            Saídas
          </button>
        </div>


        <PrimaryButton @click="abrirNovo">Novo lançamento</PrimaryButton>
      </div>

      <!-- TABELA -->
      <Table :headers="[
        { label: 'Nome', key: 'nome' },
        {
          label: 'Valor',
          key: 'valor',
          align: 'right',
          format: (v) => formatarDinheiro(v)
        },
        { label: 'Tipo', key: 'tipo', align: 'center' },
        {
          label: 'Fixo',
          key: 'recorrente',
          align: 'center',
          format: (v) => v ? 'Sim' : 'Não'
        },
        {
          label: 'Mês',
          key: 'mes_referencia',
          align: 'center',
          format: (v) => v || '-'
        }
      ]" :rows="lancamentosFiltrados">
        <template #actions="{ row }">
          <button class="w-full text-left px-4 py-2 text-sm hover:bg-green-50 text-green-700" @click="abrirEdicao(row)">
            Editar
          </button>

          <button class="w-full text-left px-4 py-2 text-sm hover:bg-red-50 text-red-600" @click="excluir(row.id)">
            Excluir
          </button>
        </template>
      </Table>


      <!-- PAGINAÇÃO REAL -->
      <div class="flex justify-center items-center gap-2 mt-4">
        <button @click="mudarPagina(props.lancamentos.current_page - 1)" :disabled="!props.lancamentos.prev_page_url"
          class="px-3 py-1 border rounded">
          «
        </button>

        <span>
          Página {{ props.lancamentos.current_page }} de {{ props.lancamentos.last_page }}
        </span>

        <button @click="mudarPagina(props.lancamentos.current_page + 1)" :disabled="!props.lancamentos.next_page_url"
          class="px-3 py-1 border rounded">
          »
        </button>
      </div>

      <!-- MODAL -->
      <LancamentoForm :show="showModal" :form="form" :editando="!!editando" @close="showModal = false"
        @submit="salvar" />
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
