<script setup lang="ts">
import NavLink from '@/Components/NavLink.vue';
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
    label: 'Mês',
    key: 'mes_referencia',
    align: 'center',
    format: (v: any) => formatarData(v) || '-'
  }
];

</script>
<template>
  <h3 class="font-bold pl-10 pb-3 text-red-800">Gastos perto do vencimento</h3>
  <Table :headers="headers" :rows="props.lancamentos" theme="red" />
  <div class="w-full flex justify-end mt-3">
    <NavLink :href="route('lancamentos.index')" :active="route().current('lancamentos.index')" 
      class="!text-sm !font-bold pr-2" 
    >
      Ver Mais
    </NavLink>
  </div>
</template>