<script setup lang="ts">
import MetaForm from './MetaForm.vue';
import VisualizacaoMeta from '../Partials/VisualizacaoMeta.vue';

const props = defineProps<{
  form: any;
  meta?: {
    id: string;
    nome: string;
    valor_objetivo: string | number;
    valor_atual: string | number;
    ate_quando: string;
  };
}>();


const emit = defineEmits(['close']);
</script>

<template>
  <div class="grid gap-8">
    <!-- FORMULÁRIO -->
    <div>
      <MetaForm :form="form" @close="emit('close')" :editando="true" />
    </div>

    <!-- VISUALIZAÇÃO E PROGRESSO COM SCROLL -->
    <div class="min-h-0">
      <VisualizacaoMeta :form="form" :meta="meta" />
    </div>
  </div>
</template>