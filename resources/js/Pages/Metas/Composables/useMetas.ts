import { computed, ref } from "vue"

export function useMetas(categoria: any) {

  const metaGlobal = ref({
    objetivo: 5000,
    atual: 3200
  });

  const porcentagemEconomizada = computed(() => {
    return Math.round((metaGlobal.value.atual / metaGlobal.value.objetivo) * 100);
  });

  const statusMensagem = computed(() => {
    if (porcentagemEconomizada.value >= 100) return 'Meta Batida! 🎉';
    if (porcentagemEconomizada.value >= 70) return 'Quase lá!';
    return 'Em andamento';
  });

  const statusColorClass = computed(() => {
    if (porcentagemEconomizada.value >= 100) return 'bg-green-100 text-green-700';
    if (porcentagemEconomizada.value >= 70) return 'bg-emerald-100 text-green-700';
    return 'bg-gray-100 text-gray-600';
  });

  const barraGlobalColor = computed(() => {
    if (porcentagemEconomizada.value >= 100) return 'bg-green-600';
    return 'bg-emerald-600';
  });

  return {
    porcentagemEconomizada,
    statusMensagem,
    statusColorClass,
    barraGlobalColor,
    metaGlobal
  }
}
