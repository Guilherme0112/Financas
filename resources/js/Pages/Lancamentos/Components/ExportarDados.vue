<script setup lang="ts">
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { ref } from 'vue';
import { router } from '@inertiajs/vue3'

const props = defineProps<{
  show: boolean
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'start'): void
}>()

const format = ref<'pdf' | 'xlsx' | null>(null);
const processing = ref(false);

const submit = () => {
  if (!format.value || processing.value) return;

  processing.value = true;

  const query = Object.fromEntries(
    new URLSearchParams(window.location.search)
  );

  router.post(route('exportar.' + format.value), {
    ...query,
  }, {
    preserveState: true,
    preserveScroll: true,
    onFinish: () => {
      processing.value = false
    },
    onSuccess: () => {
      emit('close')
      emit('start')
    }
  }); 
}

</script>

<template>
  <Modal :show="props.show" @close="emit('close')">
    <div class="px-8 py-6 space-y-6">
      <h3 class="text-lg font-bold text-gray-800">
        Exportar Dados
      </h3>

      <p class="text-sm text-gray-600">
        Escolha o formato do arquivo para exportação.
      </p>

      <div class="space-y-3">
        <label class="flex items-center gap-2 cursor-pointer">
          <input
            type="radio"
            value="xlsx"
            v-model="format"
            class="text-emerald-600 focus:ring-emerald-500"
          />
          <span class="text-sm text-gray-700">Excel (.xlsx)</span>
        </label>

        <label class="flex items-center gap-2 cursor-pointer">
          <input
            type="radio"
            value="pdf"
            v-model="format"
            class="text-emerald-600 focus:ring-emerald-500"
          />
          <span class="text-sm text-gray-700">PDF</span>
        </label>
      </div>

      <div class="flex justify-end gap-3 pt-4">
        <SecondaryButton
          type="button"
          @click="emit('close')"
        >
          Cancelar
        </SecondaryButton>

        <PrimaryButton
          type="button"
          :disabled="!format || processing"
          @click="submit"
        >
          Exportar
        </PrimaryButton>
      </div>
    </div>
  </Modal>
</template>
