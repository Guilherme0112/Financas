<script setup lang="ts">
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import InputDinheiro from '@/Components/InputDinheiro.vue'
import Flatpickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import { ref, watch } from 'vue'
import { toast } from 'vue3-toastify'
import InputError from '@/Components/InputError.vue'

const props = defineProps<{
    show: boolean
    form: any
    editando: boolean
    id?: number
    categoriasEntrada: string[]
    categoriasSaida: string[]
}>()

const emit = defineEmits(['close', 'saved'])

const salvar = () => {
    if (props.form.mes_referencia) {
        if (props.form.mes_referencia.toString().includes("T")) {
            props.form.mes_referencia = props.form.mes_referencia
                .toString()
                .split("T")[0]
                .replaceAll("-", "/");
        } else {
            props.form.mes_referencia = props.form.mes_referencia
                .toString()
                .replaceAll("-", "/");
        }
    }
    if (props.editando && props.id) {
        props.form.put(route('lancamentos.update', props.id), {
            onSuccess: () => {
                toast.success('Lançamento atualizado com sucesso!')
                emit('saved')
                emit('close')
            }
        })
        return;
    }

    props.form.post(route('lancamentos.store'), {
        onSuccess: () => {
            toast.success('Lançamento criado com sucesso!')
            emit('saved')
            emit('close')
        }
    })
}

const fpInstance = ref<any>(null)
const aplicarClasse = () => {
    if (!fpInstance.value) return
    const input = fpInstance.value.altInput as HTMLInputElement
    input.classList.remove(
        'border-red-300',
        'border-green-300',
        'focus:border-red-500',
        'focus:border-green-500',
        'focus:ring-red-500',
        'focus:ring-green-500'
    )
    input.classList.add(
        'rounded-md',
        'shadow-sm'
    )
    if (props.form.errors.mes_referencia) {
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
watch(
    () => props.form.errors.mes_referencia,
    () => aplicarClasse()
)

watch(
    () => props.form.tipo,
    (tipo) => {
        if (tipo === 'ENTRADA') {
            props.form.categoria_saida = null;
            props.form.errors.categoria_saida = null;
        } else {
            props.form.categoria_entrada = null;
            props.form.errors.categoria_entrada = null;
        }
    }
)

watch(
    () => props.show,
    (abriu) => {
        if (abriu && props.editando && props.form.mes_referencia) {
            const [y, m, d] = props.form.mes_referencia.split('-')
            props.form.mes_referencia = new Date(+y, +m - 1, +d)
        }
    }
)

</script>
<template>
    <Modal :show="show" @close="emit('close')">
        <div class="p-6 space-y-6">

            <h3 class="text-lg font-bold text-gray-800">
                {{ editando ? 'Editar lançamento' : 'Novo lançamento' }}
            </h3>

            <div>
                <InputLabel value="Nome" />
                <TextInput v-model="form.nome" class="w-full"
                    :class="form.errors.nome ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : null" />
                <InputError :message="form.errors.nome" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel value="Valor" />
                    <InputDinheiro v-model="form.valor"
                        :class="form.errors.valor ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : null" />
                    <InputError :message="form.errors.valor" />
                </div>

                <div>
                    <InputLabel value="Tipo" />
                    <select v-model="form.tipo"
                        class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        :class="form.errors.tipo ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : null">
                        <option value="SAIDA">Saída</option>
                        <option value="ENTRADA">Entrada</option>
                    </select>
                    <InputError :message="form.errors.tipo" />
                </div>
            </div>

            <div class="grid" v-if="!form.id">
                <div>
                    <input type="checkbox" v-model="form.recorrente" />
                    <span class="text-sm text-gray-700 ml-2">Lançamento fixo mensal</span>
                </div>
                <div v-if="form.recorrente" class="mt-4">
                    <InputLabel value="Meses Recorrentes" />
                    <TextInput v-model="form.meses_recorrentes" class="w-[150px]"
                        :class="form.errors.meses_recorrentes ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : null" />
                    <InputError :message="form.errors.meses_recorrentes" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel value="Data de Vencimento" />
                    <Flatpickr v-model="form.mes_referencia" :config="{
                        dateFormat: 'Y-m-d',
                        altInput: true,
                        altFormat: 'd/m/Y',
                        static: true,
                        enableTime: false,
                        allowInput: false,
                        onReady: (_, __, instance) => {
                            fpInstance = instance
                            const input = instance.altInput as HTMLInputElement
                            input.placeholder = 'dd/MM/yyyy'
                            aplicarClasse()
                        }
                    }" />
                    <InputError :message="form.errors.mes_referencia" />
                </div>
                <div>
                    <InputLabel value="Categoria" />

                    <!-- ENTRADA -->
                    <div>
                        <select v-if="form.tipo === 'ENTRADA'" v-model="form.categoria_entrada"
                            class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            :class="form.errors.categoria_entrada ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : null">
                            <option disabled value="">Selecione</option>
                            <option v-for="cat in categoriasEntrada" :key="cat.value" :value="cat.value">
                                {{ cat.label }}
                            </option>
                        </select>
                        <InputError :message="form.errors.categoria_entrada" />
                    </div>

                    <!-- SAÍDA -->
                    <div v-if="form.tipo === 'SAIDA'">
                        <select v-model="form.categoria_saida"
                            class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                            :class="form.errors.categoria_saida ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : null">
                            <option disabled value="">Selecione</option>
                            <option v-for="cat in categoriasSaida" :key="cat.value" :value="cat.value">
                                {{ cat.label }}
                            </option>
                        </select>
                        <InputError :message="form.errors.categoria_saida" />
                    </div>

                </div>

            </div>

            <div>
                <InputLabel value="Descrição" />
                <textarea v-model="form.descricao"
                    class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    :class="form.errors.descricao ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : null" />
                <InputError :message="form.errors.descricao" />
            </div>


            <div class="flex justify-end gap-3 pt-6">
                <SecondaryButton @click="emit('close')">Cancelar</SecondaryButton>
                <PrimaryButton @click="salvar">Salvar</PrimaryButton>
            </div>

        </div>
    </Modal>
</template>
<style>
.flatpickr-calendar {
    z-index: 9999 !important;
}
</style>