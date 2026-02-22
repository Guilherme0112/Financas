<script setup lang="ts">
import InputDinheiro from '@/Components/InputDinheiro.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Flatpickr from 'vue-flatpickr-component';
import { Portuguese } from 'flatpickr/dist/l10n/pt';
import { ref, watch, nextTick } from 'vue';
import { Target } from 'lucide-vue-next';
import { toast } from 'vue3-toastify';
import InputError from '@/Components/InputError.vue';
import { sanitizarData } from '@/utils/helpers';
import Icon from '@/Components/Icon.vue';
import { configInertia } from '@/inertia';

const props = defineProps<{
    form: any;
    editando?: boolean;

}>();
const emit = defineEmits(["close"]);
const fpInstance = ref<any>(null);
const flatpickrKey = ref(0);

const aplicarClasse = () => {
    if (!fpInstance.value) return;

    const input = fpInstance.value.altInput as HTMLInputElement;
    if (!input) return;

    input.classList.remove(
        'border-red-300',
        'border-emerald-300',
        'focus:border-red-500',
        'focus:border-emerald-500',
        'focus:ring-red-500',
        'focus:ring-emerald-500'
    );

    input.classList.add('rounded-md', 'shadow-sm', 'w-full');

    if (props.form.errors.ate_quando) {
        input.classList.add(
            'border-red-300',
            'focus:border-red-500',
            'focus:ring-red-500'
        );
    } else {
        input.classList.add(
            'border-emerald-300',
            'focus:border-emerald-500',
            'focus:ring-emerald-500'
        );
    }
};

watch(
    () => props.form.errors.ate_quando,
    () => aplicarClasse()
);


const configFlatpickr = {
    locale: Portuguese,
    dateFormat: 'Y/m/d',
    altInput: true,
    altFormat: 'd/m/Y',
    allowInput: false,
    defaultDate: props.form.ate_quando || null,
    static: true,
    onReady: (_: any, __: any, instance: any) => {
        fpInstance.value = instance;
        nextTick(() => {
            aplicarClasse();
        });
    },
};

const salvarMeta = () => {
    const config = {
        onSuccess: () => {
            toast.success(props.editando ? 'Meta atualizada com sucesso!' : 'Meta criada com sucesso!')
            emit('close')
        }
    };

    props.form.ate_quando = sanitizarData(props.form.ate_quando);
    if (props.editando && props.form.id) {
        props.form.put(route('metas.update', props.form.id), {
            ...configInertia,
            config
        });
    } else {
        props.form.post(route('metas.store'), {
            ...configInertia,
            config
        });
    }
};
</script>
<template>
    <header class="mb-6" v-if="editando">
        <h3 class="text-lg font-bold text-emerald-800 flex items-center gap-2">
            <Icon>
                <Target :size="22" />
            </Icon>
            {{ editando ? 'Ajustar Detalhes da Meta' : 'Definir Nova Meta Financeira' }}
        </h3>

        <p class="text-sm text-emerald-700 mt-1 opacity-70">
            {{ editando
                ? 'Refine seus planos para alcançar este objetivo mais rápido.'
                : 'Dê o primeiro passo para realizar seu sonho. Planeje o quanto precisa poupar.'
            }}
        </p>
    </header>
    <section class="bg-white p-8 pt-14 rounded-3xl border border-gray-100 shadow-lg space-y-6">
        <div class="space-y-6">
            <div>
                <InputLabel value="O que você quer conquistar?" />
                <TextInput v-model="props.form.nome" class="w-full" placeholder="Ex: Viagem, Carro, Reserva..."
                    :class="props.form.errors.nome ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : null" />
                <InputError :message="props.form.errors.nome" />
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex flex-col sm:flex-grow">
                    <InputLabel value="Quanto custa?" />
                    <div class="relative w-full">
                        <InputDinheiro v-model="props.form.valor_objetivo" class="w-full"
                            :class="props.form.errors.valor_objetivo ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : null" />
                        <InputError :message="props.form.errors.valor_objetivo" />
                    </div>
                </div>

                <div class="flex flex-col sm:flex-none sm:w-40">
                    <InputLabel value="Até quando?" />
                    <div class="relative w-full">
                        <Flatpickr :key="flatpickrKey" :value="props.form.ate_quando" :config="configFlatpickr"
                            v-model="props.form.ate_quando" />
                        <InputError :message="props.form.errors.ate_quando" />
                    </div>
                </div>
            </div>
        </div>

        <div class="w-full flex justify-end">
            <PrimaryButton @click="salvarMeta" :disabled="props.form.processing">
                {{ props.editando ? 'Atualizar Meta' : 'Criar Objetivo' }}
            </PrimaryButton>
        </div>
    </section>
</template>