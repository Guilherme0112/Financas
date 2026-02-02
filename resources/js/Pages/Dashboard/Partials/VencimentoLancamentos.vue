<script setup lang="ts">
import NavLink from '@/Components/NavLink.vue';
import Table from '@/Components/Table.vue';
import { formatarData, formatarDinheiro } from '@/utils/helpers';
import { ArrowRight } from 'lucide-vue-next';

const props = defineProps<{
  lancamentos: any[]
}>();

const headers = [
  { label: 'Nome', key: 'nome' },
  {
    label: 'Valor',
    key: 'valor',
    align: 'right',
    format: (v: any) => formatarDinheiro(v) || '-'
  },
  {
    label: 'Categoria',
    key: 'categoria_label',
    align: 'right',
  },
  {
    label: 'Vencimento',
    key: 'mes_referencia',
    align: 'center',
    format: (v: any) => formatarData(v) || '-'
  }
];

</script>
<template>
  <h3 class="font-bold pl-10 pb-3 text-red-800">Gastos perto do vencimento</h3>
  <Table :headers="headers" :rows="props.lancamentos" theme="red" />
  <div class="w-full flex justify-end">
    <div class="w-full flex justify-end">
      <NavLink :href="route('lancamentos.index', { tipo: 'SAIDA', foi_pago: false })" :active="route().current('lancamentos.index')" class="
      text-red-700
      hover:text-red-900
      font-semibold
      text-sm
      underline
      underline-offset-4
      pr-1
      mt-2
    ">
        Ver Mais
        <ArrowRight :size="14" class="ml-1" />
      </NavLink>
    </div>
  </div>
</template>