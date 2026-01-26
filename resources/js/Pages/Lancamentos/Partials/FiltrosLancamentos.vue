<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Flatpickr from 'vue-flatpickr-component'
import { useForm, router } from '@inertiajs/vue3'

defineProps<{
    show: boolean
}>()

const emit = defineEmits(['close'])

const hoje = new Date()
const primeiroDiaMes = new Date(hoje.getFullYear(), hoje.getMonth(), 1)
const ultimoDiaMes = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 0)

const formatar = (date: Date) => {
    const y = date.getFullYear()
    const m = String(date.getMonth() + 1).padStart(2, '0')
    const d = String(date.getDate()).padStart(2, '0')
    return `${y}/${m}/${d}`
}

const form = useForm({
    data_inicio: formatar(primeiroDiaMes),
    data_fim: formatar(ultimoDiaMes),
    tipo: 'TODOS',
})

const filtrar = () => {
    router.get(route('lancamentos.index'), {
        data_inicio: form.data_inicio,
        data_fim: form.data_fim,
        tipo: form.tipo,
    }, {
        preserveState: true,
        replace: true,
    })

    emit('close')
}

const limparFiltros = () => {
    form.data_inicio = ''
    form.data_fim = ''
    form.tipo = 'TODOS'
    router.get(route('lancamentos.index'), {}, { replace: true, preserveState: false })
    emit('close')
}


</script>

<template>
    <Modal :show="show" @close="emit('close')">
        <div class="px-12 py-6 space-y-6">

            <h3 class="text-lg font-bold text-gray-800">
                Filtrar lançamentos
            </h3>

            <!-- Período -->
            <div class="grid grid-cols-3 items-end">
                <div>
                    <InputLabel value="Data inicial" />
                    <Flatpickr v-model="form.data_inicio" :config="{
                        dateFormat: 'Y/m/d',
                        altInput: true,
                        altFormat: 'd/m/Y',
                        enableTime: false,
                        static: true,
                        allowInput: false,
                        onReady: (_, __, instance) => {
                            (instance.altInput as HTMLInputElement).placeholder = 'Data inicial'
                        }
                    }"
                        class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
                </div>

                <div class="flex justify-center pb-2 text-sm text-gray-500">
                    até
                </div>

                <div>
                    <InputLabel value="Data final" />
                    <Flatpickr v-model="form.data_fim" :config="{
                        dateFormat: 'Y/m/d',
                        altInput: true,
                        altFormat: 'd/m/Y',
                        enableTime: false,
                        static: true,
                        allowInput: false,
                        onReady: (_, __, instance) => {
                            (instance.altInput as HTMLInputElement).placeholder = 'Data final'
                        }
                    }"
                        class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500" />
                </div>
            </div>

            <!-- Tipo -->
            <div>
                <InputLabel value="Tipo" />
                <select v-model="form.tipo"
                    class="w-full max-w-[192px] rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="TODOS">Todos</option>
                    <option value="ENTRADA">Entradas</option>
                    <option value="SAIDA">Saídas</option>
                </select>
            </div>
        </div>
        <!-- Ações -->
        <div class="flex justify-between gap-3 pt-4 p-6 px-12">
            <div class="flex">
                <SecondaryButton @click="limparFiltros" type="button">
                    Limpar Filtros
                </SecondaryButton>
            </div>
            <div class="flex gap-3">
                <SecondaryButton @click="emit('close')" type="button">
                    Cancelar
                </SecondaryButton>
                <PrimaryButton @click="filtrar" type="button">
                    Aplicar filtro
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
