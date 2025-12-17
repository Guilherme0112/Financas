<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3'

const emit = defineEmits(['close', 'created'])

const props = defineProps<{
  show: boolean
}>()


const form = useForm({
  nome: '',
  tipo: 'SAIDA',
})

const salvar = () => {
  form.post('/categorias', {
    onSuccess: () => {
      emit('close')
      emit('created')
    },
  })
}

</script>

<template>
  <Modal :show="props.show" @close="emit('close')">
    <div class="p-6 space-y-4">
      <h3 class="text-lg font-bold">Nova Categoria</h3>

      <div>
        <InputLabel value="Nome" />
        <TextInput v-model="form.nome" class="w-full" />
      </div>
      <div>
        <InputLabel value="Tipo" />
        <select v-model="form.tipo"
          class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500">
          <option value="SAIDA">Saída</option>
          <option value="ENTRADA">Entrada</option>
        </select>
      </div>

      <div class="flex justify-end gap-2">
        <button @click="emit('close')" class="px-4 py-2 border rounded-md text-gray-600 hover:bg-gray-100">
          Cancelar
        </button>
        <PrimaryButton @click="salvar">Salvar</PrimaryButton>
      </div>
    </div>
  </Modal>
</template>
