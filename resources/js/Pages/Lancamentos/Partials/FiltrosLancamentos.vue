<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Flatpickr from 'vue-flatpickr-component'
import { useForm, router } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue';
import { ref, watch } from 'vue';

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
        onSuccess: () => {
            emit('close')
        },
        onError: (errors) => {
            form.setError(errors)
        }
    })
}

const limparFiltros = () => {
    form.data_inicio = ''
    form.data_fim = ''
    form.tipo = 'TODOS'
    router.get(route('lancamentos.index'), {}, { replace: true, preserveState: false })
    emit('close')
}

const fpInstanceInicio = ref<any>(null)
const fpInstanceFim = ref<any>(null)

const aplicarClasse = (instance: any, errorKey: 'data_inicio' | 'data_fim') => {
    if (!instance) return
    
    const input = instance.altInput as HTMLInputElement
    
    input.classList.remove(
        'border-red-300',
        'border-green-300',
        'focus:border-red-500',
        'focus:border-green-500',
        'focus:ring-red-500',
        'focus:ring-green-500'
    )
    
    input.classList.add('rounded-md', 'shadow-sm')
    
    if (form.errors[errorKey]) {
        input.classList.add(
            'border-red-300',
            'focus:border-red-500',
            'focus:ring-red-500'
        )
    } else {
        input.classList.add(
            'border-green-300',
            'focus:border-green-500',
            'focus:ring-green-500'
        )
    }
}

// Criar as configurações uma única vez
const configDataInicio = {
    dateFormat: 'Y/m/d',
    altInput: true,
    altFormat: 'd/m/Y',
    enableTime: false,
    static: true,
    allowInput: false,
    onReady: (_: any, __: any, instance: any) => {
        fpInstanceInicio.value = instance
        const input = instance.altInput as HTMLInputElement
        input.placeholder = 'Data inicial'
        aplicarClasse(instance, 'data_inicio')
    }
}

const configDataFim = {
    dateFormat: 'Y/m/d',
    altInput: true,
    altFormat: 'd/m/Y',
    enableTime: false,
    static: true,
    allowInput: false,
    onReady: (_: any, __: any, instance: any) => {
        fpInstanceFim.value = instance
        const input = instance.altInput as HTMLInputElement
        input.placeholder = 'Data final'
        aplicarClasse(instance, 'data_fim')
    }
}

watch(
    () => form.errors.data_inicio,
    () => aplicarClasse(fpInstanceInicio.value, 'data_inicio')
)

watch(
    () => form.errors.data_fim,
    () => aplicarClasse(fpInstanceFim.value, 'data_fim')
)
</script>

<template>
    <Modal :show="show" @close="emit('close')">
        <div class="px-12 py-6 space-y-6">
            <h3 class="text-lg font-bold text-gray-800">
                Filtrar lançamentos
            </h3>

            <!-- Período -->
            <div class="grid grid-cols-[1fr_auto_1fr] gap-4 items-start">
                <div class="flex flex-col">
                    <InputLabel value="Data inicial" class="mb-1" />

                    <Flatpickr 
                        v-model="form.data_inicio" 
                        :config="configDataInicio"
                        class="w-full" />

                    <div class="min-h-[24px] mt-1">
                        <InputError :message="form.errors.data_inicio" />
                    </div>
                </div>

                <div class="flex items-center pt-8 px-3 text-sm text-gray-500">
                    até
                </div>

                <div class="flex flex-col">
                    <InputLabel value="Data final" class="mb-1" />

                    <Flatpickr 
                        v-model="form.data_fim" 
                        :config="configDataFim"
                        class="w-full" />

                    <div class="min-h-[24px] mt-1">
                        <InputError :message="form.errors.data_fim" />
                    </div>
                </div>
            </div>

            <!-- Tipo -->
            <div>
                <InputLabel value="Tipo" class="mb-1" />
                <select v-model="form.tipo"
                    class="w-full max-w-[192px] rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="TODOS">Todos</option>
                    <option value="ENTRADA">Entradas</option>
                    <option value="SAIDA">Saídas</option>
                </select>

                <div class="min-h-[24px] mt-1">
                    <InputError :message="form.errors.tipo" />
                </div>
            </div>
        </div>

        <!-- Ações -->
        <div class="flex justify-between gap-3 pt-4 p-6 px-12">
            <SecondaryButton @click="limparFiltros" type="button">
                Limpar Filtros
            </SecondaryButton>

            <div class="flex gap-3">
                <SecondaryButton @click="emit('close')" type="button">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton :disabled="form.processing" @click="filtrar" type="button">
                    Aplicar filtro
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>