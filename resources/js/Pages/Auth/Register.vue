<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('register'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation')
        },
    })
}
</script>

<template>
    <GuestLayout>

        <Head title="Criar conta - Organizei" />

        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 to-green-100 px-4">
            <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold text-green-700">Criar conta</h1>
                    <p class="text-gray-500 mt-1">Comece agora a controlar seus gastos</p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">

                    <div>
                        <InputLabel for="name" value="Nome" class="text-gray-700" />

                        <TextInput id="name" type="text"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                            v-model="form.name" required autofocus autocomplete="name" />

                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email" class="text-gray-700" />

                        <TextInput id="email" type="email"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                            v-model="form.email" required autocomplete="username" />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Senha" class="text-gray-700" />

                        <TextInput id="password" type="password"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                            v-model="form.password" required autocomplete="new-password" />

                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Confirmar senha" class="text-gray-700" />

                        <TextInput id="password_confirmation" type="password"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                            v-model="form.password_confirmation" required autocomplete="new-password" />

                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="pt-4">
                        <PrimaryButton
                            class="w-full justify-center bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl text-lg font-semibold transition"
                            :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                            type="submit"
                            >
                            Criar conta
                        </PrimaryButton>
                    </div>
                </form>

                <p class="mt-8 text-center text-sm text-gray-500">
                    Já tem uma conta?
                    <Link :href="route('login')" class="text-green-600 hover:text-green-700 font-semibold">
                        Entrar
                    </Link>
                </p>
            </div>
        </div>
    </GuestLayout>
</template>
