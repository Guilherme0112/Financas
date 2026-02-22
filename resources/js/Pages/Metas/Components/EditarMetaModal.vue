<script setup lang="ts">
import Modal from '@/Components/Modal.vue';
import MetaFormEditar from './MetaFormEditar.vue';

const props = defineProps<{
  show: boolean;
  form: any;
  meta?: any;
}>();

const emit = defineEmits(['close']);
</script>

<template>
  <Modal :show="show" @close="emit('close')">
    <div class="p-6">
      <MetaFormEditar :form="form" :meta="meta" @close="emit('close')" />
    </div>
  </Modal>
</template>
