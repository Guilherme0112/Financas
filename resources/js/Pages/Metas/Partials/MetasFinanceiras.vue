<script setup lang="ts">
import {
    Target,
} from 'lucide-vue-next';
import { router, useForm } from '@inertiajs/vue3';
import MetaForm from '../Components/MetaForm.vue';
import CalcularMeta from '../Components/CalcularMeta.vue';
import CardMeta from '../Components/CardMeta.vue';
import EditarMetaModal from '../Components/EditarMetaModal.vue';
import ConfirmDeleteModal from '@/Components/ConfirmDeleteModal.vue';
import { ref } from 'vue';
import { configInertia } from '@/inertia';
import Paginacao from '@/Components/Paginacao.vue';
import SemRegistro from '@/Pages/Dashboard/Partials/SemRegistro.vue';
import Icon from '@/Components/Icon.vue';

const props = defineProps<{
    metas: any
}>();

const mostrarModalDeletar = ref(false);
const mostrarModalEditar = ref(false);
const metaIdToDelete = ref<string | null>(null);
const metaEditando = ref<any>(null);

const deletarMeta = (id: string) =>  {
    metaIdToDelete.value = id;
    mostrarModalDeletar.value = true;
}

const editarMeta = (meta: any) => {
    metaEditando.value = meta;
    formEditar.nome = meta.nome;
    formEditar.valor_objetivo = meta.valor_objetivo;
    formEditar.ate_quando = meta.ate_quando;
    formEditar.id = meta.id;

    form.nome = meta.nome;
    form.valor_objetivo = meta.valor_objetivo;
    form.ate_quando = meta.ate_quando;
    mostrarModalEditar.value = true;
}

const confirmarDeletarMeta = () => {
    if (metaIdToDelete.value) {
        form.delete(route('metas.destroy', metaIdToDelete.value), {
            ...configInertia,
            onSuccess: () => {
                mostrarModalDeletar.value = false;
                metaIdToDelete.value = null;
            }
        });
    }
}

const form = useForm({
    nome: "",
    valor_objetivo: 0,
    ate_quando: null
});

const formEditar = useForm({
    id: null,
    nome: "",
    valor_objetivo: 0,
    ate_quando: null
});

const resetForm = () => {
    form.resetAndClearErrors();
};
</script>
<template>
    <div class="bg-white rounded-3xl shadow-lg p-6 space-y-4">
        <header class="flex items-center gap-4">
            <Icon>
                <Target :size="25" />
            </Icon>
            <div>
                <h1 class="text-2xl font-black text-gray-800">Metas Financeiras</h1>
                <p class="text-gray-500 text-sm">Planeje seus sonhos e veja quanto precisa poupar.</p>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <MetaForm :form="form" @close="resetForm" />
            <CalcularMeta :form="form" />
        </div>

        <div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" v-if="props.metas.data.length > 0">
                <CardMeta v-for="meta in props.metas.data" :key="meta.id" :meta="meta" :actions="true" @click="editarMeta(meta)" @delete="deletarMeta(meta.id)" />
            </div>
            <div v-else>
                <SemRegistro />
            </div>
        </div>

        <div class="w-full flex justify-end">
            <Paginacao :pagination="props.metas" routeName="limites.index" param="pageMeta" />
        </div>
    </div>

    <!-- MODAL PARA EDITAR META -->
    <EditarMetaModal :show="mostrarModalEditar" :form="formEditar" :meta="metaEditando" @close="mostrarModalEditar = false; resetForm();" />

    <!-- MODAL PARA DELETAR META -->
    <ConfirmDeleteModal 
        :show="mostrarModalDeletar" 
        message="Tem certeza que deseja excluir esta meta?"
        :isDisabled="form.processing"
        @confirm="confirmarDeletarMeta" 
        @close="mostrarModalDeletar = false" 
    />
</template>