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
    label: 'Vencimento',
    key: 'mes_referencia',
    align: 'center',
    format: (v: any) => formatarData(v) || '-'
  }
];

</script>
<template>
  <h3 class="font-bold pl-10 pb-3 text-red-800">Lançamentos Vencidos</h3>
  <Table :headers="headers" :rows="props.lancamentos" theme="red" />
  <div class="w-full flex justify-end mt-3">
    <div class="w-full flex justify-end mt-4">
      <NavLink :href="route('lancamentos.index')" :active="route().current('lancamentos.index')" class="
      text-red-700
      hover:text-red-900
      font-semibold
      text-sm
      underline
      underline-offset-4
      pr-1
    ">
        Ver mais →
      </NavLink>
    </div>
  </div>
</template>