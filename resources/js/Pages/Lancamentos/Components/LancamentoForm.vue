<script setup lang="ts">
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputDinheiro from "@/Components/InputDinheiro.vue";
import Flatpickr from "vue-flatpickr-component";
import "flatpickr/dist/flatpickr.css";
import { computed, ref, watch } from "vue";
import { toast } from "vue3-toastify";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";
import TextArea from "@/Components/TextArea.vue";
import { Wallet } from "lucide-vue-next";
import Icon from "@/Components/Icon.vue";
import AutoSelect from "@/Components/AutoSelect.vue";
import { Metas } from "@/types/Metas";
import debounce from "debounce";
import { router } from "@inertiajs/vue3";
import { configInertia } from "@/inertia";

const props = defineProps<{
    show: boolean;
    form: any;
    metas: Metas[];
    editando: boolean;
    id?: number;
    categoriasEntrada: Array<{ label: string; value: string }>;
    categoriasSaida: Array<{ label: string; value: string }>;
    tipo: Array<{ label: string; value: string }>;
}>();

const emit = defineEmits(["close", "saved"]);

const metasOptions = computed(() =>
    props.metas.map((meta) => ({
        label: meta.nome,
        value: meta.id,
    })),
);

const salvar = () => {
    if (props.form.mes_referencia instanceof Date) {
        const d = props.form.mes_referencia;
        props.form.mes_referencia = `${d.getFullYear()}/${String(d.getMonth() + 1).padStart(2, "0")}/${String(d.getDate()).padStart(2, "0")}`;
    }

    if (props.editando && props.id) {
        props.form.put(route("lancamentos.update", props.id), {
            ...configInertia,
            onSuccess: () => {
                toast.success("Lançamento atualizado com sucesso!");
                emit("saved");
                emit("close");
            },
        });
        return;
    }

    props.form.post(route("lancamentos.store"), {
        ...configInertia,
        onSuccess: () => {
            toast.success("Lançamento criado com sucesso!");
            emit("saved");
            emit("close");
        },
    });
};

const fpInstance = ref<any>(null);
const aplicarClasse = () => {
    if (!fpInstance.value) return;
    const input = fpInstance.value.altInput as HTMLInputElement;
    input.classList.remove(
        "border-red-300",
        "border-green-300",
        "focus:border-red-500",
        "focus:border-green-500",
        "focus:ring-red-500",
        "focus:ring-green-500",
    );
    input.classList.add("rounded-md", "shadow-sm");
    if (props.form.errors.mes_referencia) {
        input.classList.add(
            "border-red-300",
            "focus:border-red-500",
            "focus:ring-red-500",
        );
    } else {
        input.classList.add(
            "border-green-300",
            "focus:border-green-500",
            "focus:ring-green-500",
        );
    }
};
watch(
    () => props.form.errors.mes_referencia,
    () => aplicarClasse(),
);

watch(
    () => props.form.tipo,
    (tipo) => {
        if (tipo === "ENTRADA") {
            props.form.categoria_saida = null;
            props.form.errors.categoria_saida = null;
        } else {
            props.form.categoria_entrada = null;
            props.form.errors.categoria_entrada = null;
        }
    },
);

watch(
    () => props.show,
    (abriu) => {
        if (abriu && props.editando) {
            if (props.form.meta_id && typeof props.form.meta_id === "object") {
                props.form.meta_id = props.form.meta.id;
            }

            if (props.form.mes_referencia) {
                const [y, m, d] = props.form.mes_referencia.split("-");
                props.form.mes_referencia = new Date(+y, +m - 1, +d);
            }
        }
    },
);

const _buscarMetasDebounced = debounce((value: string) => {
    router.reload({
        data: {
            search_metas: value,
        },
        only: ["metas"],
        replace: true,
    });
}, 300);

const buscarMetasComDebounce = (value: string) => {
    _buscarMetasDebounced(value);
};

const labelParaDataReferencia = (tipo: string) => {
    if (tipo === "ENTRADA") return "Data de Pagamento";
    if (tipo === "SAIDA") return "Data de Vencimento";
    if (tipo === "RESERVA_META") return "Data de Quitação";
    if (tipo === "RESERVA_EMERGENCIA") return "Data de Quitação";
};

const mostrarCampoEntradaOuSaida = (tipo: string) => {
    return tipo === "ENTRADA" || tipo === "SAIDA";
};

const mostrarCampoMetas = (tipo: string) => {
    return tipo === "RESERVA_META";
};

const mostrarCampoEmergencia = (tipo: string) => {
    return tipo === "RESERVA_EMERGENCIA";
};
</script>
<template>
    <Modal :show="show" @close="emit('close')">
        <div class="p-6 space-y-4">
            <header class="mb-6">
                <h3
                    class="text-lg font-bold text-emerald-800 flex items-center gap-2"
                >
                    <Icon>
                        <Wallet :size="22" />
                    </Icon>
                    {{
                        editando && form.id
                            ? "Editar Lançamento Financeiro"
                            : "Registrar Lançamento Financeiro"
                    }}
                </h3>

                <p class="text-sm text-emerald-700 mt-1 opacity-70">
                    Registre uma entrada ou saída e mantenha seu controle
                    financeiro atualizado.
                </p>
            </header>

            <div>
                <InputLabel value="Nome *" />
                <TextInput
                    :limit="50"
                    v-model="form.nome"
                    class="w-full"
                    required
                    :class="
                        form.errors.nome
                            ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
                            : null
                    "
                />
                <InputError :message="form.errors.nome" class="mt-[-20px]" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel value="Valor (R$) *" />
                    <InputDinheiro
                        v-model="form.valor"
                        :class="
                            form.errors.valor
                                ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
                                : null
                        "
                    />
                    <InputError :message="form.errors.valor" />
                </div>

                <div>
                    <InputLabel value="Tipo *" />
                    <select
                        v-model="form.tipo"
                        class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                        :class="
                            form.errors.tipo
                                ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
                                : null
                        "
                        required
                    >
                        <option
                            v-for="cat in tipo"
                            :key="cat.value"
                            :value="cat.value"
                        >
                            {{ cat.label }}
                        </option>
                    </select>
                    <InputError :message="form.errors.tipo" />
                </div>
            </div>

            <div class="mt-3 space-y-4">
                <div class="flex justify-between">
                    <div class="w-[50%] grid">
                        <div
                            v-if="
                                !form.id ||
                                mostrarCampoEntradaOuSaida(form.tipo)
                            "
                        >
                            <div>
                                <Checkbox
                                    :checked="form.recorrente"
                                    v-model="form.recorrente"
                                />
                                <span class="text-sm text-gray-700 ml-2"
                                    >Lançamento recorrente</span
                                >
                            </div>
                            <div
                                v-if="form.recorrente && !form.id"
                                class="mt-4"
                            >
                                <InputLabel value="Meses Recorrentes" />
                                <TextInput
                                    v-model="form.meses_recorrentes"
                                    class="w-[150px]"
                                    type="number"
                                    min="1"
                                    :class="
                                        form.errors.meses_recorrentes
                                            ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
                                            : null
                                    "
                                />
                                <InputError
                                    :message="form.errors.meses_recorrentes"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="w-[50%] flex justify-start">
                        <div v-if="form.tipo === 'SAIDA'" class="ml-2">
                            <Checkbox
                                :checked="form.foi_pago"
                                v-model="form.foi_pago"
                            />
                            <span class="text-sm text-gray-700 ml-2"
                                >Marcar como paga</span
                            >
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel
                            :value="labelParaDataReferencia(form.tipo)"
                        />
                        <Flatpickr
                            v-model="form.mes_referencia"
                            :config="{
                                dateFormat: 'Y/m/d',
                                altInput: true,
                                altFormat: 'd/m/Y',
                                static: true,
                                enableTime: false,
                                allowInput: false,
                                onReady: (_, __, instance) => {
                                    fpInstance = instance;
                                    aplicarClasse();
                                },
                            }"
                        />
                        <InputError :message="form.errors.mes_referencia" />
                    </div>
                    <div v-if="mostrarCampoEntradaOuSaida(form.tipo)">
                        <InputLabel value="Categoria *" />
                        <div>
                            <select
                                v-if="form.tipo === 'ENTRADA'"
                                v-model="form.categoria_entrada"
                                class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                :class="
                                    form.errors.categoria_entrada
                                        ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
                                        : null
                                "
                            >
                                <option disabled value="">Selecione</option>
                                <option
                                    v-for="cat in categoriasEntrada"
                                    :key="cat.value"
                                    :value="cat.value"
                                >
                                    {{ cat.label }}
                                </option>
                            </select>
                            <InputError
                                :message="form.errors.categoria_entrada"
                            />
                        </div>

                        <div v-if="form.tipo === 'SAIDA'">
                            <select
                                v-model="form.categoria_saida"
                                class="w-full rounded-md border-green-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                :class="
                                    form.errors.categoria_saida
                                        ? 'border-red-300 focus:border-red-500 focus:ring-red-500'
                                        : null
                                "
                            >
                                <option disabled value="">Selecione</option>
                                <option
                                    v-for="cat in categoriasSaida"
                                    :key="cat.value"
                                    :value="cat.value"
                                >
                                    {{ cat.label }}
                                </option>
                            </select>
                            <InputError
                                :message="form.errors.categoria_saida"
                            />
                        </div>
                    </div>
                </div>

                <div v-if="mostrarCampoEntradaOuSaida(form.tipo)">
                    <InputLabel value="Descrição" />
                    <TextArea v-model="form.descricao" :limit="500" />
                    <InputError
                        :message="form.errors.descricao"
                        class="mt-[-20px]"
                    />
                </div>
            </div>
            <div v-if="mostrarCampoMetas(form.tipo)">
                <InputLabel
                    value="Escolha a meta que deseja alocar o dinheiro"
                />
                <AutoSelect
                    @update:query="buscarMetasComDebounce"
                    v-model="form.meta_id"
                    :options="metasOptions"
                    placeholder="Selecione a meta"
                />
            </div>
            <div v-if="mostrarCampoEmergencia(form.tipo)">
                <InputLabel value="Escolha sua reserva de emergência" />
                <!-- <AutoSelect /> -->
            </div>
            <div class="flex justify-end gap-3">
                <SecondaryButton @click="emit('close')"
                    >Cancelar</SecondaryButton
                >
                <PrimaryButton @click="salvar" :disabled="form.processing"
                    >Salvar</PrimaryButton
                >
            </div>
        </div>
    </Modal>
</template>
<style>
.flatpickr-calendar {
    z-index: 9999 !important;
}
</style>
