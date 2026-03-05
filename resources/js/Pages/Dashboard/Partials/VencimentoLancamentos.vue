<script setup lang="ts">
import Table from '@/Components/Table.vue';
import { formatarData, formatarDinheiro } from '@/utils/helpers';

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
] as any;

</script>
<template>
  <h3 class="font-bold pl-2 text-red-800 py-2">Gastos perto do vencimento</h3>
  <Table :headers="headers" :rows="props.lancamentos" theme="gray" />
</template>