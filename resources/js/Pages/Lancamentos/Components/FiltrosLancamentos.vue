<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Flatpickr from 'vue-flatpickr-component'
import { useForm, router } from '@inertiajs/vue3'
import InputError from '@/Components/InputError.vue';
import { shallowRef, watch } from 'vue'; // <-- Trocado ref por shallowRef
import Checkbox from '@/Components/Checkbox.vue';
import { Filter } from 'lucide-vue-next';
import Icon from '@/Components/Icon.vue';
import { configInertia } from '@/inertia';

const props = defineProps<{
    show: boolean,
    categoriasEntrada: any[]
    categoriasSaida: any[]
}>();

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

const parseBool = (val: string | null) => {
    if (val === 'true' || val === '1') return true;
    if (val === 'false' || val === '0') return false;
    return null;
}

const urlParams = new URLSearchParams(window.location.search);

const form = useForm({
    data_inicio: urlParams.get('data_inicio') || formatar(primeiroDiaMes),
    data_fim: urlParams.get('data_fim') || formatar(ultimoDiaMes),
    tipo: urlParams.get('tipo') || 'TODOS',
    foi_pago: urlParams.get('foi_pago') || '',
    recorrentes: parseBool(urlParams.get('recorrentes')),
    categoria_entrada: urlParams.get('categoria_entrada') || null,
    categoria_saida: urlParams.get('categoria_saida') || null
});

const filtrar = () => {
    form.get(route('lancamentos.index'), {
      ...configInertia,
        onSuccess: () => {
            emit('close')
        },
        onError: (errors) => {
            form.setError(errors)
        }
    })
}

const limparFiltros = () => {
    form.reset();
    router.get(route('lancamentos.index')); 
    emit('close');
}

const fpInstanceInicio = shallowRef<any>(null) // <-- Proteção do Vue 3
const fpInstanceFim = shallowRef<any>(null) // <-- Proteção do Vue 3

const aplicarClasse = (instance: any, errorKey: 'data_inicio' | 'data_fim') => {
    if (!instance) return

    const input = instance.altInput as HTMLInputElement
    if (!input) return;

    input.classList.remove(
        'border-red-300',
        'border-green-300',
        'focus:border-red-500',
        'focus:border-green-500',
        'focus:ring-red-500',
        'focus:ring-green-500'
    )

    input.classList.add('rounded-md', 'shadow-sm', 'w-full')

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
    disableMobile: true,
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
    disableMobile: true,
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
);

watch(() => props.show, (isShown) => {
    if (isShown) {
        const params = new URLSearchParams(window.location.search);
        form.data_inicio = params.get('data_inicio') || formatar(primeiroDiaMes);
        form.data_fim = params.get('data_fim') || formatar(ultimoDiaMes);
        form.tipo = params.get('tipo') || 'TODOS';
        form.foi_pago = params.get('foi_pago') || '';
        form.recorrentes = parseBool(params.get('recorrentes'));
        form.categoria_entrada = params.get('categoria_entrada') || null;
        form.categoria_saida = params.get('categoria_saida') || null;
    }
});
</script>

<template>
    <Modal :show="show" @close="emit('close')">
        <!-- Alterado o padding para ser menor no mobile (px-6) e normal no desktop (md:px-12) -->
        <div class="px-6 md:px-12 py-6 space-y-4">
            <header class="mb-6">
                <h3 class="text-lg font-bold text-emerald-800 flex items-center gap-2">
                    <Icon>
                        <Filter :size="22" />
                    </Icon>
                    Filtrar Lançamentos
                </h3>

                <p class="text-sm text-emerald-700 mt-1 opacity-70">
                    Refine os dados por período, categoria ou tipo para visualizar exatamente o que precisa.
                </p>
            </header>

            <!-- Período: Flex em vez de Grid para poder empilhar suavemente no mobile -->
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-2 items-start">
                <div class="flex flex-col w-full">
                    <InputLabel value="Data inicial" class="mb-1" />
                    <Flatpickr v-model="form.data_inicio" :config="configDataInicio" class="w-full" />
                    <InputError :message="form.errors.data_inicio" class="mt-1" />
                </div>

                <!-- A palavra "até" some no mobile e aparece alinhada no desktop -->
                <div class="hidden sm:flex items-center pt-8 px-2 text-sm text-gray-500">
                    até
                </div>

                <div class="flex flex-col w-full">
                    <InputLabel value="Data final" class="mb-1" />
                    <Flatpickr v-model="form.data_fim" :config="configDataFim" class="w-full" />
                    <InputError :message="form.errors.data_fim" class="mt-1" />
                </div>
            </div>

            <!-- Tipo e Status: Removido o Grid complexo e o span vazio -->
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex flex-col w-full">
                    <InputLabel value="Tipo" class="mb-1" />
                    <select v-model="form.tipo"
                        class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="TODOS">Todos</option>
                        <option value="ENTRADA">Entradas</option>
                        <option value="SAIDA">Saídas</option>
                    </select>
                    <InputError :message="form.errors.tipo" class="mt-1" />
                </div>
                
                <div class="flex flex-col w-full">
                    <InputLabel value="Status de Pagamento" class="mb-1" />
                    <select v-model="form.foi_pago"
                        class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="">Todos</option>
                        <option value="true">Pagos</option>
                        <option value="false">Não Pagos</option>
                    </select>
                    <InputError :message="form.errors.foi_pago" class="mt-1" />
                </div>
            </div>

            <div class="flex flex-col w-full">
                <InputLabel value="Categoria" />
                <div>
                    <select v-if="form.tipo === 'ENTRADA'" v-model="form.categoria_entrada"
                        class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        :class="form.errors.categoria_entrada ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : null">
                        <option disabled value="">Selecione</option>
                        <option v-for="cat in categoriasEntrada" :key="cat.value" :value="cat.value">
                            {{ cat.label }}
                        </option>
                    </select>
                    <InputError :message="form.errors.categoria_entrada" class="mt-1" />
                </div>
                <div v-if="form.tipo === 'SAIDA'">
                    <select v-model="form.categoria_saida"
                        class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        :class="form.errors.categoria_saida ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : null">
                        <option disabled value="">Selecione</option>
                        <option v-for="cat in categoriasSaida" :key="cat.value" :value="cat.value">
                            {{ cat.label }}
                        </option>
                    </select>
                    <InputError :message="form.errors.categoria_saida" class="mt-1" />
                </div>
                <div v-if="form.tipo === 'TODOS'">
                    <p class="text-xs text-gray-600 mt-2">
                        Selecione um <b>Tipo</b> para conseguir escolher uma <b>Categoria.</b>
                    </p>
                </div>
            </div>

            <div class="pt-2">
                <div class="flex items-center h-[24px]">
                    <Checkbox :checked="form.recorrentes ?? false" v-model="form.recorrentes" />
                    <span class="text-sm text-gray-700 ml-2">Apenas transações recorrentes</span>
                </div>
            </div>
        </div>

        <!-- Ações: Botões empilham no mobile e ficam distribuídos no desktop -->
        <div class="flex flex-col-reverse sm:flex-row sm:justify-between gap-3 p-6 md:px-12 border-t border-gray-100">
            <SecondaryButton class="w-full sm:w-auto justify-center" @click="limparFiltros" type="button">
                Limpar Filtros
            </SecondaryButton>

            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto">
                <SecondaryButton class="w-full sm:w-auto justify-center" @click="emit('close')" type="button">
                    Cancelar
                </SecondaryButton>

                <PrimaryButton class="w-full sm:w-auto justify-center" :disabled="form.processing" @click="filtrar" type="button">
                    Aplicar filtro
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>