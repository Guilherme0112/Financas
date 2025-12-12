<script setup lang="ts">
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import InputDinheiro from '@/Components/InputDinheiro.vue'
import Flatpickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'

defineProps<{
    show: boolean
    form: any
    editando: boolean
}>()

const emit = defineEmits(['close', 'submit']);

</script>

<template>
    <Modal :show="show" @close="emit('close')">
        <div class="p-6 space-y-6">

            <h3 class="text-lg font-bold text-gray-800">
                {{ editando ? 'Editar lançamento' : 'Novo lançamento' }}
            </h3>

            <div>
                <InputLabel value="Nome" />
                <TextInput v-model="form.nome" class="w-full" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel value="Valor" />
                    <InputDinheiro v-model="form.valor" />
                </div>

                <div>
                    <InputLabel value="Tipo" />
                    <select v-model="form.tipo"
                        class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="SAIDA">Saída</option>
                        <option value="ENTRADA">Entrada</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" v-model="form.recorrente" />
                <span class="text-sm text-gray-700">Lançamento fixo mensal</span>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel value="Data de Vencimento" />
                    <Flatpickr v-model="form.mes_referencia" :config="{
                        dateFormat: 'd/m/Y',
                        enableTime: false,
                        static: true,
                    }" class="rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500" />

                </div>
                <div>
                    <InputLabel value="Categorias" />
                    <select v-model="form.categoria_id"
                        class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="1">Moradia (Aluguel)</option>
                        <option value="2">Transporte (Uber, Gasolina e etc)</option>
                    </select>
                </div>
            </div>

            <div>
                <InputLabel value="Descrição" />
                <textarea v-model="form.descricao"
                    class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
            </div>

            <div class="flex justify-end gap-3 pt-6">
                <button @click="emit('close')" class="px-4 py-2 border rounded-md text-gray-600 hover:bg-gray-100">
                    Cancelar
                </button>

                <PrimaryButton @click="emit('submit')">
                    Salvar
                </PrimaryButton>
            </div>

        </div>
    </Modal>
</template>
<style>
.flatpickr-calendar {
    z-index: 9999 !important;
}
</style>