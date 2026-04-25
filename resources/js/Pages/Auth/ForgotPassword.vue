<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Recuperar Senha" />

        <div class="flex justify-center bg-gradient-to-br from-green-50 to-green-100 px-4 min-h-screen">
            <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 mt-20 h-fit">
                
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-green-700">SaldoUp</h1>
                    <p class="text-gray-500 mt-2 text-sm">Recuperação de acesso</p>
                </div>

                <div class="mb-6 text-sm text-gray-600 leading-relaxed text-center">
                    Esqueceu sua senha? Sem problemas. Informe seu e-mail abaixo e enviaremos um link para você escolher uma nova senha.
                </div>

                <div
                    v-if="status"
                    class="mb-6 p-3 bg-green-50 border border-green-200 rounded-lg text-sm font-medium text-green-600 text-center"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <TextInput
                            id="email"
                            type="email"
                            class="w-full"
                            placeholder="Seu e-mail cadastrado"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <PrimaryButton
                            type="submit"
                            class="w-full justify-center bg-green-600 hover:bg-green-700 text-white py-3 shadow-md transition-all"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Enviar link de recuperação
                        </PrimaryButton>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <Link
                        :href="route('login')"
                        class="text-sm text-green-600 hover:text-green-700 font-semibold flex items-center justify-center gap-1"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Voltar para o login
                    </Link>
                </div>

            </div>
        </div>
    </GuestLayout>
</template>