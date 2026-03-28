<script setup lang="ts">
import { Head, Link, useForm } from "@inertiajs/vue3";
import { computed, onMounted } from "vue";
import { formatPhone, unformatPhone } from "@/utils/helpers";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

interface PlanoOption {
    value: number;
    label: string;
    price?: string;
}

const props = defineProps<{ planos: PlanoOption[] }>();

const form = useForm({
    name: "",
    email: "",
    phone: "",
    password: "",
    password_confirmation: "",
    plano: "",
});

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const planoDaURL = urlParams.get("plano");

    const encontrado = props.planos.find(
        (p) => p.label.toLowerCase() === planoDaURL?.toLowerCase(),
    );
    form.plano = encontrado ? encontrado.value.toString() : props.planos[0]?.value.toString();
});
const phoneMasked = computed({
    get: () => formatPhone(form.phone || ""),
    set: (value) => {
        form.phone = unformatPhone(value);
    },
});

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
            class="w-full bg-white max-w-[440px] mx-auto px-6 py-12 shadow-lg rounded"
        >
            <div class="mb-10 text-center">
                <h1
                    class="text-2xl font-semibold text-slate-900 tracking-tight"
                >
                    Criar sua conta
                </h1>
                <p class="text-slate-500 mt-2">
                    Comece a gerenciar suas finanças hoje mesmo.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="space-y-2">
                    <label
                        class="text-xs font-semibold uppercase tracking-wider text-slate-400 ml-1"
                        >Plano selecionado</label
                    >
                    <div
                        class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl"
                    >
                        <button
                            v-for="option in planos"
                            :key="option.value"
                            type="button"
                            @click="form.plano = option.value.toString()"
                            :class="[
                                'py-2.5 px-4 rounded-lg text-sm font-medium transition-all duration-200',
                                form.plano === option.value.toString()
                                    ? 'bg-white text-emerald-600 shadow-sm'
                                    : 'text-slate-500 hover:text-slate-700',
                            ]"
                        >
                            {{ option.label }}
                            <span
                                v-if="option.price"
                                class="block text-[10px] opacity-70"
                                >{{ option.price }}</span
                            >
                        </button>
                    </div>
                    <InputError :message="form.errors.plano" />
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-4">
                        <TextInput
                            v-model="form.name"
                            type="text"
                            placeholder="Nome completo"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <TextInput
                            v-model="form.email"
                            type="email"
                            placeholder="E-mail"
                            required
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <TextInput
                            v-model="phoneMasked"
                            type="text"
                            placeholder="WhatsApp"
                            :maxlength="15"
                            required
                        />
                        <InputError :message="form.errors.phone" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <TextInput
                            v-model="form.password"
                            type="password"
                            placeholder="Senha"
                            required
                        />
                        <TextInput
                            v-model="form.password_confirmation"
                            type="password"
                            placeholder="Confirmar"
                            required
                        />
                    </div>
                    <InputError :message="form.errors.password" />
                </div>

                <div class="pt-2">
                    <PrimaryButton
                        class="w-full justify-center py-4"
                        :disabled="form.processing"
                        type="submit"
                    >
                        <span v-if="!form.processing"
                            >Criar conta gratuita</span
                        >
                        <span v-else class="flex items-center gap-2">
                            <svg
                                class="animate-spin h-4 w-4"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                    fill="none"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            Criando conta...
                        </span>
                    </PrimaryButton>
                </div>
            </form>

            <footer class="mt-10 text-center">
                <p class="text-sm text-slate-500">
                    Já tem uma conta?
                    <Link
                        :href="route('login')"
                        class="text-emerald-600 font-semibold hover:text-emerald-700 ml-1"
                    >
                        Entrar
                    </Link>
                </p>
            </footer>
        </div>
    </GuestLayout>
</template>
