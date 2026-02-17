<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import LimiteGastos from './Partials/LimiteGastos.vue';
import MetasFinanceiras from './Partials/MetasFinanceiras.vue';

const props = defineProps<{
    categoriasSaida: any[],
    metas: any
}>();

</script>
<template>

    <Head title="Metas" />
    <AuthenticatedLayout>

        <div class="my-6">
            <MetasFinanceiras />
        </div>

        <div class="my-6">
            <LimiteGastos :categorias-saida="props.categoriasSaida" :metas="props.metas.data" />
        </div>

    </AuthenticatedLayout>
</template>