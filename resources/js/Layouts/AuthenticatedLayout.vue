<script setup lang="ts">
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import DangerButton from "@/Components/DangerButton.vue";
import { LayoutDashboard, Receipt, Target, BarChart3 } from "lucide-vue-next";

const open = ref(false);

const links = [
    {
        name: "Dashboard",
        href: "dashboard",
        icon: LayoutDashboard,
    },
    {
        name: "Lançamentos",
        href: "lancamentos.index",
        icon: Receipt,
    },
    {
        name: "Metas/Limites",
        href: "limites.index",
        icon: Target,
    },
    {
        name: "Prospecção de Gastos",
        href: "prospeccao-futuro.index",
        icon: BarChart3,
    },
];
</script>

<template>
    <div class="flex min-h-screen bg-gray-100">
        <aside
            class="hidden md:flex w-64 flex-col bg-white border-r border-gray-200 sticky top-0 h-screen z-40"
        >
            <div class="flex h-16 items-center px-6 border-b border-gray-100">
                <Link
                    :href="route('dashboard')"
                    class="flex items-center gap-2"
                >
                    <ApplicationLogo class="h-8 text-emerald-600" />
                    <span
                        class="text-xl font-bold tracking-tight text-emerald-900"
                        >Faturaí</span
                    >
                </Link>
            </div>

            <nav class="flex-1 space-y-1 px-4 py-4 overflow-y-auto">
                <Link
                    v-for="l in links"
                    :key="l.name"
                    :href="route(l.href)"
                    :class="[
                        route().current(l.href)
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-600',
                        'group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors',
                    ]"
                >
                    <component
                        :is="l.icon"
                        :class="[
                            route().current(l.href)
                                ? 'text-emerald-600'
                                : 'text-gray-400 group-hover:text-emerald-500',
                            'mr-3 h-5 w-5 flex-shrink-0 transition-colors',
                        ]"
                    />
                    {{ l.name }}
                </Link>
            </nav>

            <div class="border-t border-gray-100 p-4 space-y-2">
                <Link
                    :href="route('profile.edit')"
                    class="flex w-full items-center gap-3 rounded-lg p-2 hover:bg-emerald-50 transition-colors group"
                >
                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-r from-emerald-600 to-green-500 text-xs font-semibold text-white"
                    >
                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p
                            class="text-sm font-medium text-gray-900 truncate group-hover:text-emerald-700 transition-colors"
                        >
                            {{ $page.props.auth.user.name }}
                        </p>
                        <p class="text-xs text-gray-500">Ver meu perfil</p>
                    </div>
                </Link>

                <div class="flex justify-center w-full">
                    <Link :href="route('logout')" method="post" class="w-full">
                            <DangerButton class="w-full">
                                Sair do Sistema
                            </DangerButton>
                        </Link>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header
                class="md:hidden flex h-16 items-center justify-between bg-white px-4 shadow-sm z-50 sticky top-0"
            >
                <Link
                    :href="route('dashboard')"
                    class="flex items-center gap-2"
                >
                    <ApplicationLogo class="h-8 text-emerald-600" />
                    <span class="text-lg font-bold text-emerald-900"
                        >Faturaí</span
                    >
                </Link>

                <button
                    @click="open = !open"
                    class="rounded-lg p-2 text-emerald-700 hover:bg-emerald-100 focus:outline-none"
                >
                    <svg
                        class="h-6 w-6"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            v-if="!open"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            v-else
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </header>

            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="transform -translate-y-full opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="transform translate-y-0 opacity-100"
                leave-to-class="transform -translate-y-full opacity-0"
            >
                <div
                    v-if="open"
                    class="md:hidden fixed inset-0 z-40 pt-16 bg-white overflow-y-auto"
                >
                    <nav class="space-y-1 px-4 py-6">
                        <Link
                            v-for="l in links"
                            :key="l.name"
                            :href="route(l.href)"
                            @click="open = false"
                            class="flex items-center px-4 py-3 rounded-lg text-base font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700"
                        >
                            {{ l.name }}
                        </Link>

                        <div
                            class="pt-4 mt-4 border-t border-gray-100 text-gray-400 px-4 text-xs font-bold uppercase tracking-widest"
                        >
                            Configurações
                        </div>

                        <Link
                            :href="route('profile.edit')"
                            @click="open = false"
                            class="flex items-center px-4 py-3 rounded-lg text-base font-medium text-gray-700 hover:bg-emerald-50"
                        >
                            Meu Perfil
                        </Link>

                        <Link :href="route('logout')" method="post" class="w-full">
                            <DangerButton class="w-full">
                                Sair do Sistema
                            </DangerButton>
                        </Link>
                    </nav>
                </div>
            </transition>

            <main class="flex-1 p-4 md:p-8">
                <div class="mx-auto max-w-7xl">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
