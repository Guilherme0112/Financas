<script setup lang="ts">
import { computed, ref } from 'vue';
import { formatarData, formatarDinheiro } from '@/utils/helpers';
import {
  Calendar,
  MoreVertical,
  TrendingUp,
  CheckCircle2
} from 'lucide-vue-next';
import Dropdown from '@/Components/Dropdown.vue';

const props = defineProps<{
  meta: {
    nome: string;
    valor_objetivo: string | number;
    ate_quando: string;
    soma_lancamentos: number | string
  },
  actions?: boolean
}>();

const emits = defineEmits(['delete', 'click']);

const objetivoNumerico = computed(() => {
  const v = props.meta?.valor_objetivo;
  return v ? parseFloat(v.toString()) : 0;
});

const valorAtualNumerico = computed(() => {
  const v = props.meta?.soma_lancamentos;
  return v ? parseFloat(v.toString()) : 0;
});

const percentual = computed(() => {
  if (objetivoNumerico.value <= 0) return 0;
  const p = (valorAtualNumerico.value / objetivoNumerico.value) * 100;
  return Math.min(Math.round(p), 100);
});

const valorFaltante = computed(() => {
  const faltante = objetivoNumerico.value - valorAtualNumerico.value;
  return faltante > 0 ? faltante : 0;
});

const mesesRestantes = computed(() => {
  if (!props.meta?.ate_quando) return 1;

  const hoje = new Date();
  const dataMeta = new Date(props.meta.ate_quando);

  if (isNaN(dataMeta.getTime())) return 1;

  const diff = (dataMeta.getFullYear() - hoje.getFullYear()) * 12 + (dataMeta.getMonth() - hoje.getMonth());
  return diff > 0 ? diff : 1;
});

const esforcoMensal = computed(() => {
  return valorFaltante.value / mesesRestantes.value;
});

const corProgresso = computed(() => {
  if (percentual.value >= 100) return 'bg-emerald-500';
  if (percentual.value >= 50) return 'bg-blue-500';
  return 'bg-amber-500';
});
</script>
<template>
  <div
    class="bg-white rounded-3xl p-6 shadow-lg border border-gray-100 hover:shadow-md transition-all relative">
    <div @click.stop class="absolute top-4 right-4 z-10" v-if="actions">
      <Dropdown align="right" width="48">
        <template #trigger>
          <button class="p-1 hover:bg-gray-50 rounded-full text-gray-400 transition-colors">
            <MoreVertical :size="18" />
          </button>
        </template>

        <template #content>
          <div>
            <button @click.stop="emits('click', props.meta)"
              class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
              Editar
            </button>
            <button @click.stop="emits('delete', props.meta)"
              class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors border-t border-gray-100">
              Deletar
            </button>
          </div>
        </template>
      </Dropdown>
    </div>

    <div class="flex items-start gap-4 mb-6">
      <div>
        <h3 class="font-bold text-gray-800 text-lg leading-tight">{{ meta.nome }}</h3>
        <p class="text-sm text-gray-400 flex items-center gap-1">
          <Calendar :size="14" /> até {{  formatarData(meta.ate_quando) }}
        </p>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-6">
      <div>
        <span class="text-[11px] uppercase tracking-wider text-gray-400 font-bold">Objetivo</span>
        <p class="text-lg font-black text-gray-700">{{ formatarDinheiro(meta.valor_objetivo) }}</p>
      </div>
      <div class="text-right">
        <span class="text-[11px] uppercase tracking-wider text-gray-400 font-bold">Já tenho</span>
        <p class="text-lg font-black text-emerald-600">{{ formatarDinheiro(meta.soma_lancamentos) }}</p>
      </div>
    </div>

    <div class="space-y-2 mb-6">
      <div class="flex justify-between items-end">
        <span class="text-sm font-bold text-gray-700">{{ percentual }}% concluído</span>
        <span class="text-xs text-gray-400">Falta {{ formatarDinheiro(valorFaltante) }}</span>
      </div>
      <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden">
        <div class="h-full transition-all duration-1000 ease-out" :class="corProgresso"
          :style="{ width: `${percentual}%` }" />
      </div>
    </div>

    <div class="bg-gray-50 rounded-2xl p-4 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <TrendingUp :size="16" class="text-blue-500" />
        <div>
          <p class="text-[10px] text-gray-500 leading-none uppercase font-bold">Esforço Mensal</p>
          <p class="text-sm font-bold text-gray-800">{{ formatarDinheiro(esforcoMensal) }}</p>
        </div>
      </div>

      <div v-if="percentual >= 100" class="flex items-center gap-1 text-emerald-600 font-bold text-xs">
        <CheckCircle2 :size="16" /> Meta Batida!
      </div>
      <div v-else class="text-right">
        <p class="text-[10px] text-gray-500 leading-none font-bold uppercase">Tempo</p>
        <p class="text-sm font-bold text-gray-800">{{ mesesRestantes }} meses</p>
      </div>
    </div>
  </div>
</template>