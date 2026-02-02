import { ref, computed } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { toast } from 'vue3-toastify'

export function useLancamentos() {
  const page = usePage();
  const lancamentos = computed(() => page.props.lancamentos as any);
  const deleteForm = useForm({})

  const showModal = ref(false)
  const mostrarFiltro = ref(false)
  const showDeleteModal = ref(false)
  const lancamentoParaExcluir = ref<number | null>(null)

  const lancamentosFiltrados = computed(() => lancamentos.value.data)

  const totalEntradas = computed(() =>
    lancamentos.value.data.filter((l: any) => l.tipo === 'ENTRADA')
      .reduce((t: number, l: any) => t + Number(l.valor), 0)
  )

  const totalSaidas = computed(() =>
    lancamentos.value.data.filter((l: any) => l.tipo === 'SAIDA')
      .reduce((t: number, l: any) => t + Number(l.valor), 0)
  )

  const pedirExclusao = (id: number) => {
    lancamentoParaExcluir.value = id
    showDeleteModal.value = true
  }

  const confirmarExclusao = () => {
    if (!lancamentoParaExcluir.value) return

    deleteForm.delete(route('lancamentos.destroy', lancamentoParaExcluir.value), {
      onSuccess: () => {
        toast.success('Lançamento excluído com sucesso!')
        showDeleteModal.value = false
        lancamentoParaExcluir.value = null
      }
    })
  }


  const mudarPagina = (page: number) => {
    router.get(route('lancamentos.index'), { page }, {
      preserveState: true,
      replace: true,
    })
  }

  return {
    lancamentosFiltrados,
    totalEntradas,
    totalSaidas,
    showModal,
    mostrarFiltro,
    showDeleteModal,
    pedirExclusao,
    confirmarExclusao,
    mudarPagina,
    deleteForm
  }
}
