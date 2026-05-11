<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    email: string;
    token: string;
}>();

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation');
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Redefinir Senha" />

        <div class="flex justify-center bg-gradient-to-br from-green-50 to-green-100 px-4 min-h-screen">
            <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 mt-20 h-fit">
                
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-green-700">SaldUp</h1>
                    <p class="text-gray-500 mt-2 text-sm">Crie sua nova senha de acesso</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1">E-mail</label>
                        <TextInput
                            id="email"
                            type="email"
                            class="w-full bg-gray-50 border-gray-200 text-gray-500 cursor-not-allowed"
                            v-model="form.email"
                            required
                            readonly
                            autocomplete="username"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1">Nova Senha</label>
                        <TextInput
                            id="password"
                            type="password"
                            class="w-full focus:ring-green-500 focus:border-green-500 shadow-sm"
                            v-model="form.password"
                            placeholder="Mínimo 8 caracteres"
                            required
                            autofocus
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-1">Confirmar Nova Senha</label>
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="w-full focus:ring-green-500 focus:border-green-500 shadow-sm"
                            v-model="form.password_confirmation"
                            placeholder="Repita a senha"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="pt-2">
                        <PrimaryButton
                            type="submit"
                            class="w-full justify-center bg-green-600 hover:bg-green-700 text-white py-3 shadow-md transition-all rounded-lg font-bold"
                            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Atualizando...</span>
                            <span v-else>Redefinir Senha</span>
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>