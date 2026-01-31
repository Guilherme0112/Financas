<script setup lang="ts">
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'

defineProps<{
  show: boolean
}>()

const emit = defineEmits<{
  (e: 'confirm'): void
  (e: 'close'): void
}>()
</script>

<template>
  <Modal :show="show" @close="emit('close')">
    <div class="p-6 space-y-4">
      <h2 class="text-lg font-semibold text-slate-800">
        Confirmar exclusão
      </h2>

      <p class="text-sm text-slate-600">
        Tem certeza que deseja excluir este lançamento?
        <br />
        <span class="text-red-600 font-semibold">Essa ação não pode ser desfeita.</span>
      </p>

      <div class="flex justify-end gap-3 pt-4">
        <SecondaryButton @click="emit('close')">
          Cancelar
        </SecondaryButton>

        <DangerButton class="bg-red-600 hover:bg-red-700" @click="emit('confirm')">
          Excluir
        </DangerButton>
      </div>
    </div>
  </Modal>
</template>
