<script setup lang="ts">
import Table from '@/Components/Table.vue';
import { formatarDinheiro } from '@/utils/helpers';

const props = defineProps<{
  headers: any[],
  rows: any[],
  actions: any[]
}>();

</script>

<template>
    <Table :headers="headers" :rows="rows" :actions="actions" theme="emerald">
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
</template>