import { ref, computed, watch } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'
import { formatarData } from '@/utils/helpers';
import { configInertia } from '@/inertia';

export function useLancamentos() {
  const page = usePage();
  
  // Pegando os dados dependendo da rota atual
  const lancamentos = computed(() => page.props.lancamentos as any);
  const kanbanData = computed(() => page.props.kanban as any); 
  
  const deleteForm = useForm({});
  const showModal = ref(false);
  const mostrarFiltro = ref(false);
  const showDeleteModal = ref(false);
  const lancamentoParaExcluir = ref<number | null>(null);
  const loadingData = ref(true);
  
  const lancamentosFiltrados = computed(() => lancamentos.value?.data || []);

  // Watchers para definir loading como false quando os dados são carregados
  watch(() => page.props.lancamentos, () => {
    if (loadingData.value) loadingData.value = false;
  }, { immediate: true });
  watch(() => page.props.kanban, () => {
    if (loadingData.value) loadingData.value = false;
  }, { immediate: true });
  watch(() => page.props.entradas, () => {
    if (loadingData.value) loadingData.value = false;
  }, { immediate: true });
  watch(() => page.props.saidas, () => {
    if (loadingData.value) loadingData.value = false;
  }, { immediate: true });
  watch(() => page.props.metas, () => {
    if (loadingData.value) loadingData.value = false;
  }, { immediate: true });

  const pedirExclusao = (id: number) => {
    lancamentoParaExcluir.value = id
    showDeleteModal.value = true
  }

  const confirmarExclusao = () => {
    if (!lancamentoParaExcluir.value) return
    loadingData.value = true;

    deleteForm.delete(route('lancamentos.destroy', lancamentoParaExcluir.value), {
      ...configInertia,
      onSuccess: () => {
        toast.success('Lançamento excluído com sucesso!')
        showDeleteModal.value = false
        lancamentoParaExcluir.value = null
        loadingData.value = false;
      },
      onError: () => {
        loadingData.value = false;
      }
    })
  }

  // Paginação da tabela normal
  const mudarPagina = (page: number) => {
    router.get(route('lancamentos.index'), { page }, configInertia)
  }

  // GERENCIA A TROCA DE TELAS (Normal = index | Agrupado = kanban)
const alterarVisualizacao = (mode: 'normal' | 'agrupado') => {
    loadingData.value = true;
    if (mode === 'agrupado') {
        router.visit(route('lancamentos.kanban'), { preserveScroll: true });
    } else {
        router.visit(route('lancamentos.index'), { preserveScroll: true });
    }
}

  // NOVO: Paginação específica para as colunas do Kanban
  const mudarPaginaKanban = (urlPaginacao: string | null) => {
    if (!urlPaginacao) return;
    loadingData.value = true;

    router.get(urlPaginacao, {}, {
      preserveState: true,  // Mantém o estado atual da tela (modais, abas, etc)
      preserveScroll: true, // Impede que a tela pule para o topo ao clicar em "Próxima"
      only: ['entradas', 'saidas', 'metas']    // Pede para o Laravel atualizar APENAS a prop 'kanban'
    });
  }

  const headers = [
    { label: 'Tipo', key: 'tipo', align: 'center' },
    { label: 'Nome', key: 'nome' },
    { label: 'Valor (R$)', key: 'valor', align: 'right' },
    { label: 'Categoria', key: 'categoria_label', align: 'center' },
    { label: 'Mês', key: 'mes_referencia', align: 'center', format: (v: any) => formatarData(v) || '-' }
  ];

  return {
    lancamentosFiltrados,
    kanbanData,
    showModal,
    mostrarFiltro,
    showDeleteModal,
    pedirExclusao,
    confirmarExclusao,
    mudarPagina,
    alterarVisualizacao,
    mudarPaginaKanban,
    deleteForm,
    headers,
    loadingData
  }
}