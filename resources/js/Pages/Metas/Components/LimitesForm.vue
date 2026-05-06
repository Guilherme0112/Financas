<script setup lang="ts">
import Modal from '@/Components/Modal.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import InputDinheiro from '@/Components/InputDinheiro.vue'
import InputError from '@/Components/InputError.vue'
import Flatpickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css';
import "flatpickr/dist/plugins/monthSelect/style.css";
import { toast } from 'vue3-toastify'
import { Portuguese } from 'flatpickr/dist/l10n/pt.js'
import Checkbox from '@/Components/Checkbox.vue';
import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect";
import { Goal } from 'lucide-vue-next'
import TextInput from '@/Components/TextInput.vue'
import HelpMessage from '@/Components/HelpMessage.vue'
import { shallowRef, watch } from 'vue'
import Icon from '@/Components/Icon.vue'
import { configInertia } from '@/inertia'

const MonthSelectPlugin = monthSelectPlugin as any

const props = defineProps<{
    show: boolean
    form: any
    editando?: boolean
    id?: number
    categoriasSaida: { label: string, value: string }[]
}>();

const emit = defineEmits(['close', 'saved'])

const salvar = () => {
    if (props.form.mes_referencia.length === 7) {
        props.form.mes_referencia = props.form.mes_referencia + "-01";
    }

    if (props.editando && props.form.id) {
        props.form.put(route('limites.update', props.form.id), {
            ...configInertia,
            onSuccess: () => {
                toast.success('Limite atualizado com sucesso!')
                emit('saved')
                emit('close')
            }
        })
        return;
    }

    props.form.post(route('limites.store'), {
        ...configInertia,
        onSuccess: () => {
            toast.success('Limite configurado com sucesso!')
            emit('saved')
            emit('close')
        }
    })
}


const fpInstance = shallowRef<any>(null)
const aplicarClasse = () => {
    if (!fpInstance.value) return;

    // altInput é o input que o usuário vê
    const input = fpInstance.value.altInput as HTMLInputElement;

    // remove classes antigas
    input.classList.remove(
        'border-red-300',
        'border-green-300',
        'focus:border-red-500',
        'focus:border-green-500',
        'focus:ring-red-500',
        'focus:ring-green-500'
    );

    // classes padrão
    input.classList.add('rounded-md', 'shadow-sm');

    // aplica borda vermelha se houver erro, verde se não
    if (props.form.errors.mes_referencia) {
        input.classList.add(
            'border-red-300',
            'focus:border-red-500',
            'focus:ring-red-500'
        );
    } else {
        input.classList.add(
            'border-green-300',
            'focus:border-green-500',
            'focus:ring-green-500'
        );
    }
};

// atualiza a borda quando o erro mudar
watch(
    () => props.form.errors.mes_referencia,
    () => aplicarClasse()
);

const configFlatpickr = {
    locale: Portuguese,
    disableMobile: true,
    plugins: [
        new MonthSelectPlugin({
            shorthand: false,
            dateFormat: "Y-m",
            altFormat: "F Y"
        })
    ],
    altInput: true,
    allowInput: false,
    static: true,
    onReady: (_: any, __: any, instance: any) => {
        fpInstance.value = instance;
        aplicarClasse();
    }
};

</script>

<template>
    <Modal :show="show" @close="emit('close')">
        <div class="p-6 space-y-6">
            <header class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-bold text-emerald-800 flex items-center gap-2">
                        <Icon>
                            <Goal :size="23" />
                        </Icon>
                        {{ editando ? 'Editar Limite de Gasto' : 'Configurar Novo Limite' }}
                    </h3>
                    <p class="text-sm text-emerald-700 mt-1 opacity-70">
                        Defina um teto para não estourar seu orçamento nesta categoria.
                    </p>
                </div>
            </header>


            <div>
                <InputLabel value="Categoria de Saída" />
                <select v-model="form.categoria_saida"
                    class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500 mt-1"
                    :class="form.errors.categoria_saida ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : null">
                    <option disabled value="">Selecione uma categoria</option>
                    <option v-for="cat in categoriasSaida" :key="cat.value" :value="cat.value">
                        {{ cat.label }}
                    </option>
                </select>
                <InputError :message="form.errors.categoria_saida" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                    <InputLabel value="Teto Máximo (R$)" />
                    <InputDinheiro v-model="form.limite" class="mt-1"
                        :class="form.errors.limite ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : null" />
                    <InputError :message="form.errors.limite" />
                </div>

                <div class="grid mb-[-3px]">
                    <InputLabel value="Mês de Referência" />
                    <Flatpickr v-model="form.mes_referencia" :config="configFlatpickr"
                        class="mt-1 w-full rounded-md border-emerald-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    <InputError :message="form.errors.mes_referencia" />
                </div>
            </div>

            <div v-if="!form.id">
                <div>
                    <div class="flex">
                        <Checkbox :checked="form.recorrente" v-model="form.recorrente" />
                        <span class="text-sm text-gray-700 ml-2">Limite recorrente</span>
                        <HelpMessage
                            message="Esta opção sinaliza se você deseja criar o mesmo limite para os próximos meses."
                            class="ml-40" />
                    </div>
                </div>
                <div v-if="form.recorrente" class="mt-4">
                    <InputLabel value="Meses Recorrentes" />
                    <TextInput v-model="form.meses_recorrentes" class="w-[150px]" type="number" min="1"
                        :class="form.errors.meses_recorrentes ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : null" />
                    <InputError :message="form.errors.meses_recorrentes" />
                </div>
            </div>

            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <Checkbox :checked="form.notificar_ao_atingir" id="notificar"
                            v-model="form.notificar_ao_atingir" />
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="notificar" class="font-medium text-emerald-800">Notificar proximidade do
                            limite</label>
                        <p class="text-emerald-600/70 text-xs">Avisaremos você quando os gastos atingirem 80% desta
                            categoria.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <SecondaryButton @click="emit('close')">Cancelar</SecondaryButton>
                <PrimaryButton @click="salvar" :disabled="form.processing">
                    {{ editando ? 'Atualizar Limite' : 'Ativar Limite' }}
                </PrimaryButton>
            </div>

        </div>
    </Modal>
</template>

<style>
/* Garante que o seletor de data apareça sobre o modal */
.flatpickr-calendar {
    z-index: 10000 !important;
}
</style>