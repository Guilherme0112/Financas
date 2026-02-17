<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import CardLimite from '../Components/CardLimite.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import LimitesForm from '../Components/LimitesForm.vue';
import HelpMessage from '@/Components/HelpMessage.vue';
import { Receipt } from 'lucide-vue-next';
import Icon from '@/Components/Icon.vue';

const props = defineProps<{
    metas: any
    categoriasSaida: any
}>();

const form = useForm({
    id: null,
    categoria_saida: '',
    mes_referencia: '',
    limite: '',
    notificar_ao_atingir: true,
    recorrente: false,
    meses_recorrentes: null
});

const abrirModal = ref(false);

const editarMeta = (categoria: any) => {
    form.id = categoria.id;
    form.categoria_saida = categoria.categoria_saida;
    form.mes_referencia = categoria.mes_referencia;
    form.limite = categoria.limite;
    form.notificar_ao_atingir = categoria.notificar_ao_atingir;
    form.recorrente = categoria.recorrente;
    form.meses_recorrentes = form.meses_recorrentes;
    abrirModal.value = true;
}

</script>
<template>
    <div class="max-w-4xl mx-auto p-6 bg-gray-50 rounded-3xl shadow-lg m-6">
            <section
                class="flex flex-col md:flex-row items-start md:items-center justify-start mb-8 pb-4 border-b border-gray-100 gap-4">
                <div class="flex items-center gap-3">
                    <Icon>
                        <Receipt :size="25" />
                    </Icon>
                    <div>
                        <h1 class="flex text-2xl font-extrabold text-gray-900 tracking-tight">
                            Limite Financeiro
                            <HelpMessage class="ml-[200px] mx-3 text-amber-800"
                                message="Apenas lançamentos marcados como pagos são considerados no cálculo das metas. Esse critério pode ser alterado nas configurações." />
                        </h1>
                        <p class="text-sm text-gray-500">Acompanhe seu progresso em tempo real</p>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <CardLimite v-for="categoria in props.metas" :categoria="categoria" @click="editarMeta(categoria)" />
            </section>

            <footer class="mt-8 p-4 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center justify-between">
                <p class="text-sm text-emerald-700 font-medium">
                    Mantenha o controle dos seus <strong>gastos</strong> e aproveite cada centavo do seu <strong>Lazer</strong> sem estourar o limite!
                </p>
                <PrimaryButton @click="abrirModal = true">
                    Criar Limite
                </PrimaryButton>
            </footer>
        </div>

        <LimitesForm :show="abrirModal" :form="form" :editando="!!form.id" @close="abrirModal = false; form.resetAndClearErrors()"
            :categorias-saida="props.categoriasSaida" />
</template>