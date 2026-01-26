<script setup lang="ts">
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineProps<{
  canResetPassword?: boolean
  status?: string
}>()

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => {
      form.reset('password')
    },
  })
}
</script>

<template>
  <GuestLayout>
    <Head title="Entrar - Organizei" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-50 to-green-100 px-4">
      <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">

        <!-- LOGO / TÍTULO -->
        <div class="text-center mb-8">
          <h1 class="text-3xl font-bold text-green-700">Faturaí</h1>
          <p class="text-gray-500 mt-2">Controle financeiro inteligente</p>
        </div>


        <!-- FORM -->
        <form @submit.prevent="submit" class="space-y-5">

          <div>
            <InputLabel for="email" value="Email" class="text-gray-700" />

            <TextInput
              id="email"
              type="email"
              class="mt-1 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
              v-model="form.email"
              required
              autofocus
              autocomplete="username"
            />

            <InputError class="mt-2" :message="form.errors.email" />
          </div>

          <div>
            <InputLabel for="password" value="Senha" class="text-gray-700" />

            <TextInput
              id="password"
              type="password"
              class="mt-1 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
              v-model="form.password"
              required
              autocomplete="current-password"
            />

            <InputError class="mt-2" :message="form.errors.password" />
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center text-sm text-gray-600">
              <Checkbox name="remember" v-model:checked="form.remember" />
              <span class="ml-2">Lembrar de mim</span>
            </label>

            <Link
              v-if="canResetPassword"
              :href="route('password.request')"
              class="text-sm text-green-600 hover:text-green-700 font-medium"
            >
              Esqueci a senha
            </Link>
          </div>

          <div>
            <PrimaryButton
              class="w-full justify-center bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl text-lg font-semibold transition"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
              type="submit"
            >
              Entrar
            </PrimaryButton>
          </div>
        </form>

        <!-- FOOTER -->
        <p class="mt-8 text-center text-sm text-gray-500">
          Ainda não tem conta?
          <Link
            :href="route('register')"
            class="text-green-600 hover:text-green-700 font-semibold"
          >
            Criar agora
          </Link>
        </p>

      </div>
    </div>
  </GuestLayout>
</template>
