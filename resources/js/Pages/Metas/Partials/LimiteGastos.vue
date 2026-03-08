<script setup lang="ts">
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import CardLimite from "../Components/CardLimite.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import LimitesForm from "../Components/LimitesForm.vue";
import HelpMessage from "@/Components/HelpMessage.vue";
import ConfirmDeleteModal from "@/Components/ConfirmDeleteModal.vue";
import { Receipt, Plus } from "lucide-vue-next";
import Icon from "@/Components/Icon.vue";
import { toast } from "vue3-toastify";
import Paginacao from "@/Components/Paginacao.vue";
import { Page } from "@/types/Page";
import PaginacaoPorMes from "@/Components/PaginacaoPorMes.vue";
import SemRegistro from "@/Pages/Dashboard/Partials/SemRegistro.vue";
import { configInertia } from "@/inertia";
import HeaderSecao from "@/Pages/Dashboard/Components/HeaderSecao.vue";

const props = defineProps<{
    limites: Page<any>;
    categoriasSaida: any;
}>();
const form = useForm({
    id: null,
    categoria_saida: "",
    mes_referencia: "",
    limite: "",
    notificar_ao_atingir: true,
    recorrente: false,
    meses_recorrentes: null,
});

const abrirModal = ref(false);
const mostrarModalDeletar = ref(false);
const limiteIdToDelete = ref<number | string | null>(null);

const editarMeta = (categoria: any) => {
    form.id = categoria.id;
    form.categoria_saida = categoria.categoria_saida;
    form.mes_referencia = categoria.mes_referencia;
    form.limite = categoria.limite;
    form.notificar_ao_atingir = categoria.notificar_ao_atingir;
    form.recorrente = categoria.recorrente;
    form.meses_recorrentes = form.meses_recorrentes;
    abrirModal.value = true;
};

const pedirDeletarLimite = (id: number | string) => {
    limiteIdToDelete.value = id;
    mostrarModalDeletar.value = true;
};

const confirmarDeletarLimite = () => {
    if (!limiteIdToDelete.value) return;
    form.delete(route("limites.destroy", limiteIdToDelete.value), {
        ...configInertia,
        onSuccess: () => {
            toast.success("Limite removido com sucesso!");
            mostrarModalDeletar.value = false;
            limiteIdToDelete.value = null;
        },
    });
};
</script>
<template>
    <div class="p-6 bg-white rounded-3xl shadow-lg">
        <section
            class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4"
        >
            <HeaderSecao
                :icon="Receipt"
                title="Limite Financeiro"
                description="Defina e acompanhe seus limites de gastos."
                icon-color="text-rose-800"
            />

            <PrimaryButton @click="abrirModal = true">
                <Plus :size="16" :stroke-width="3" />
            </PrimaryButton>
        </section>

        <section>
            <div
                v-if="props?.limites?.data?.length > 0"
                class="grid grid-cols-1 md:grid-cols-2 gap-4"
            >
                <CardLimite
                    v-for="categoria in props.limites.data"
                    :actions="true"
                    :categoria="categoria"
                    @click="editarMeta(categoria)"
                    @delete="pedirDeletarLimite(categoria.id!)"
                />
            </div>
            <div v-else>
                <SemRegistro />
            </div>
        </section>

        <div class="w-full flex flex-col items-end gap-3">
            <PaginacaoPorMes route-name="limites.index" />
            <Paginacao :pagination="props.limites" route-name="limites.index" />
        </div>
    </div>

    <LimitesForm
        :show="abrirModal"
        :form="form"
        :editando="!!form.id"
        @close="
            abrirModal = false;
            form.resetAndClearErrors();
        "
        :categorias-saida="props.categoriasSaida"
    />

    <ConfirmDeleteModal
        :show="mostrarModalDeletar"
        message="Tem certeza que deseja excluir este limite de gastos?"
        :isDisabled="form.processing"
        @confirm="confirmarDeletarLimite"
        @close="mostrarModalDeletar = false"
    />
</template>
