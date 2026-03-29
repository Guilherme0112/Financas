<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-green-100 flex flex-col">
        <header class="w-full bg-white/80 backdrop-blur-md sticky top-0 z-50 shadow-sm border-b border-green-100">
            <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
                <Link :href="route('welcome')" class="text-2xl font-extrabold text-green-700 tracking-tight">
                    SaldoUp
                </Link>

                <nav class="flex items-center gap-4" v-if="canLogin">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium"
                    >
                        Acessar Dashboard
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="px-4 py-2 text-green-700 font-semibold hover:text-green-800 transition"
                        >
                            Entrar
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 shadow-md shadow-green-200 transition font-medium"
                        >
                            Começar agora
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="flex-grow w-full">
            <slot />
        </main>

        <footer class="py-6 border-t border-green-100">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <p class="text-gray-900 font-bold text-lg mb-2 text-green-700">SaldoUp</p>
                <p class="text-gray-500 text-sm">
                    © {{ new Date().getFullYear() }} — Controle Financeiro Inteligente.
                </p>
                <div class="mt-4 flex justify-center gap-6 text-gray-400 text-xs">
                    <a href="#" class="hover:text-green-600">Termos de Uso</a>
                    <a href="#" class="hover:text-green-600">Privacidade</a>
                    <a href="#" class="hover:text-green-600">Suporte</a>
                </div>
            </div>
        </footer>
    </div>
</template>