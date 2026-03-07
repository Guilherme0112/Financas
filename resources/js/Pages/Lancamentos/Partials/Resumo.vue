<script setup lang="ts">
import FinanceCard from '@/Components/FinanceCard.vue';

const props = defineProps<{
    totalEntradas: number,
    totalSaidas: number,
    saldo: number,
}>()
</script>
<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <FinanceCard title="Entradas" :value="totalEntradas" type="positive" />
        <FinanceCard title="Saídas" :value="totalSaidas" type="negative" />
        <FinanceCard title="Saldo" :value="saldo" />
    </div>
</template>