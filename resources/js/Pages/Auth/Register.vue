<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed, onMounted, ref, watch } from "vue";
import { formatarDinheiro, formatPhone, unformatPhone } from "@/utils/helpers";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Check } from "lucide-vue-next";

interface PlanoOption {
    id: number;
    plano: string;
    nome: string;
    preco: number;
    descricao: string;
}

const props = defineProps<{ planos: PlanoOption[] }>();

const step = ref(1);

const form = useForm({
    name: "",
    email: "",
    phone: "",
    password: "",
    password_confirmation: "",
    plano: "",
});

const planoSelecionadoId = ref<number | null>(null);

watch(planoSelecionadoId, (newId) => {
    if (newId) form.plano = newId.toString();
});

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const planoDaURL = urlParams.get("plano");

    const encontrado = props.planos.find(
        (p) => p.plano.toLowerCase() === planoDaURL?.toLowerCase(),
    );

    // Seleciona o plano da URL ou o primeiro da lista por padrão
    const inicial = encontrado || props.planos[0];
    if (inicial) {
        planoSelecionadoId.value = inicial.id;
        form.plano = inicial.id.toString();
    }
});

const phoneMasked = computed({
    get: () => formatPhone(form.phone || ""),
    set: (value) => {
        form.phone = unformatPhone(value);
    },
});

const nextStep = () => {
    // Aqui você pode adicionar uma validação manual antes de mudar de step
    step.value = 2;
};

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Cadastro" />

        <div
            class="w-full bg-white max-w-[440px] mx-auto px-6 py-12 mt-6 shadow-lg rounded-2xl border border-slate-100"
        >
            <div class="flex items-center justify-center gap-2 mb-8">
                <div
                    :class="[
                        'h-1.5 w-10 rounded-full transition-colors',
                        step >= 1 ? 'bg-emerald-500' : 'bg-slate-200',
                    ]"
                ></div>
                <div
                    :class="[
                        'h-1.5 w-10 rounded-full transition-colors',
                        step >= 2 ? 'bg-emerald-500' : 'bg-slate-200',
                    ]"
                ></div>
            </div>

            <div class="mb-10 text-center">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                    {{ step === 1 ? "Crie sua conta" : "Escolha seu plano" }}
                </h1>
                <p class="text-slate-500 mt-2 text-sm">
                    {{
                        step === 1
                            ? "Comece a gerenciar suas finanças hoje mesmo."
                            : "Selecione a melhor opção para você."
                    }}
                </p>
            </div>

            <div v-show="step === 1" class="space-y-4">
                <TextInput
                    v-model="form.name"
                    type="text"
                    placeholder="Nome completo"
                    class="w-full"
                />
                <InputError :message="form.errors.name" />

                <TextInput
                    v-model="form.email"
                    type="email"
                    placeholder="E-mail"
                    class="w-full"
                />
                <InputError :message="form.errors.email" />

                <TextInput
                    v-model="phoneMasked"
                    type="text"
                    placeholder="WhatsApp"
                    class="w-full"
                    :maxlength="15"
                />
                <InputError :message="form.errors.phone" />

                <div class="grid grid-cols-2 gap-3">
                    <TextInput
                        v-model="form.password"
                        type="password"
                        placeholder="Senha"
                    />
                    <TextInput
                        v-model="form.password_confirmation"
                        type="password"
                        placeholder="Confirmar"
                    />
                </div>
                <InputError :message="form.errors.password" />

                <PrimaryButton
                    type="button"
                    class="w-full justify-center py-4 mt-4"
                    @click="step = 2"
                >
                    Próximo passo
                </PrimaryButton>
            </div>

            <form
                v-show="step === 2"
                @submit.prevent="submit"
                class="space-y-6"
            ></form>

            <form
                v-show="step === 2"
                @submit.prevent="submit"
                class="space-y-6"
            >
                <div class="space-y-3">
                    <div
                        v-for="plano in planos"
                        :key="plano.id"
                        @click="planoSelecionadoId = plano.id"
                        :class="[
                            planoSelecionadoId === plano.id
                                ? 'border-emerald-500 bg-emerald-50/50 ring-1 ring-emerald-500'
                                : 'border-gray-100 bg-white hover:border-emerald-200',
                            'relative p-4 border-2 rounded-2xl transition-all cursor-pointer',
                        ]"
                    >
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div
                                    :class="[
                                        planoSelecionadoId === plano.id
                                            ? 'bg-emerald-500 border-emerald-500'
                                            : 'border-gray-300',
                                        'w-5 h-5 border rounded-full flex items-center justify-center transition-colors',
                                    ]"
                                >
                                    <Check
                                        v-if="planoSelecionadoId === plano.id"
                                        :size="12"
                                        class="text-white"
                                    />
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-sm">
                                        {{ plano.nome }}
                                    </h3>
                                    <p class="text-[11px] text-gray-500">
                                        {{ plano.descricao }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span
                                    class="block text-sm font-black text-emerald-600"
                                >
                                    {{
                                        plano.preco > 0
                                            ? formatarDinheiro(plano.preco)
                                            : "Grátis"
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" v-model="form.plano" />
                    <InputError :message="form.errors.plano" />
                </div>

                <div class="flex flex-col gap-3">
                    <PrimaryButton
                        class="w-full justify-center py-4 shadow-lg shadow-emerald-100"
                        :disabled="form.processing"
                        type="submit"
                    >
                        {{
                            form.processing
                                ? "Processando..."
                                : "Concluir cadastro"
                        }}
                    </PrimaryButton>

                    <SecondaryButton
                        type="button"
                        @click="step = 1"
                        class="py-2"
                    >
                        Voltar para os meus dados
                    </SecondaryButton>
                </div>
            </form>

            <footer class="mt-5 text-center border-t border-slate-50">
                <p class="text-sm text-slate-500">
                    Já tem uma conta?
                    <Link
                        :href="route('login')"
                        class="text-emerald-600 font-semibold hover:text-emerald-700 ml-1"
                        >Entrar</Link
                    >
                </p>
            </footer>
        </div>
    </GuestLayout>
</template>
