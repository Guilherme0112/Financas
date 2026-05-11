<script setup lang="ts">
import { ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import DangerButton from "@/Components/DangerButton.vue";
import {
    LayoutDashboard,
    Receipt,
    Target,
    BarChart3,
    File,
    ChevronLeft,
    ChevronRight,
    LogOut,
} from "lucide-vue-next";
import UpgradePlano from "@/Pages/Profile/Components/UpgradePlano.vue";

const open = ref(false);
const isCollapsed = ref(true);
const page = usePage();
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
        name: "Prospecção",
        href: "prospeccao-futuro.index",
        icon: BarChart3,
    },
    {
        name: "Faturas",
        href: "faturas.index",
        icon: File,
    },
];

const confirmingUpgrade = ref(false);
const planos = usePage().props.planos;

</script>

<template>
    <div class="flex min-h-screen bg-gray-100 font-sans antialiased">
        <aside
            :class="[
                isCollapsed ? 'w-20' : 'w-64',
                'hidden md:flex flex-col bg-white border-r border-gray-200 sticky top-0 h-screen z-40 transition-all duration-300 ease-in-out'
            ]"
        >
            <div class="relative flex h-16 items-center border-b border-gray-100 px-4">
                <Link :href="route('dashboard')" class="flex items-center gap-3 overflow-hidden">
                    <ApplicationLogo class="h-8 w-8 flex-shrink-0 text-emerald-600" />
                    <span 
                        v-show="!isCollapsed"
                        class="text-xl font-bold tracking-tight text-emerald-900 whitespace-nowrap transition-opacity duration-300"
                    >
                        SaldUp
                    </span>
                </Link>

                <button 
                    @click="isCollapsed = !isCollapsed"
                    class="absolute -right-3 top-8 flex h-6 w-6 items-center justify-center bg-white border border-gray-200 rounded-full text-gray-500 hover:text-emerald-600 shadow-sm transition-colors hover:bg-emerald-50"
                >
                    <ChevronLeft v-if="!isCollapsed" class="h-4 w-4" />
                    <ChevronRight v-else class="h-4 w-4" />
                </button>
            </div>

            <nav class="flex-1 space-y-1 px-3 py-4 overflow-y-auto overflow-x-hidden">
                <Link
                    v-for="l in links"
                    :key="l.name"
                    :href="route(l.href)"
                    :title="isCollapsed ? l.name : ''"
                    :class="[
                        route().current(l.href)
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-600',
                        isCollapsed ? 'justify-center' : 'px-3',
                        'group flex items-center py-2.5 text-sm font-medium rounded-lg transition-all duration-200',
                    ]"
                >
                    <component
                        :is="l.icon"
                        :class="[
                            route().current(l.href)
                                ? 'text-emerald-600'
                                : 'text-gray-400 group-hover:text-emerald-500',
                            isCollapsed ? 'mr-0' : 'mr-3',
                            'h-5 w-5 flex-shrink-0 transition-colors',
                        ]"
                    />
                    <span 
                        v-show="!isCollapsed" 
                        class="whitespace-nowrap transition-opacity duration-300"
                    >
                        {{ l.name }}
                    </span>
                </Link>
            </nav>

            <div class="border-t border-gray-100 p-3 space-y-3">
                <Link
                    :href="route('profile.edit')"
                    :title="isCollapsed ? 'Meu Perfil' : ''"
                    class="flex items-center gap-3 rounded-lg transition-colors group"
                    :class="isCollapsed ? 'justify-center p-1' : 'p-2 hover:bg-emerald-50'"
                >
                    <div
                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-r from-emerald-600 to-green-500 text-xs font-semibold text-white shadow-sm"
                    >
                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div v-show="!isCollapsed" class="flex-1 overflow-hidden">
                        <p class="text-sm font-medium text-gray-900 truncate group-hover:text-emerald-700">
                            {{ $page.props.auth.user.name }}
                        </p>
                        <p class="text-xs text-gray-500">Ver perfil</p>
                    </div>
                </Link>

                <Link :href="route('logout')" method="post" class="w-full block">
                    <DangerButton 
                        :class="['w-full flex items-center justify-center', isCollapsed ? 'px-0 h-10' : 'gap-2']"
                        :title="isCollapsed ? 'Sair do Sistema' : ''"
                    >
                        <LogOut class="h-4 w-4" />
                        <span v-show="!isCollapsed">Sair</span>
                    </DangerButton>
                </Link>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <header class="md:hidden flex h-16 items-center justify-between bg-white px-4 shadow-sm z-50 sticky top-0">
                <Link :href="route('dashboard')" class="flex items-center gap-2">
                    <ApplicationLogo class="h-8 text-emerald-600" />
                    <span class="text-lg font-bold text-emerald-900">SaldUp</span>
                </Link>

                <button @click="open = !open" class="rounded-lg p-2 text-emerald-700 hover:bg-emerald-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                <div v-if="open" class="md:hidden fixed inset-0 z-40 pt-16 bg-white overflow-y-auto">
                    <nav class="space-y-1 px-4 py-6">
                        <Link
                            v-for="l in links"
                            :key="l.name"
                            :href="route(l.href)"
                            @click="open = false"
                            class="flex items-center px-4 py-3 rounded-lg text-base font-medium text-gray-700 hover:bg-emerald-50"
                        >
                            {{ l.name }}
                        </Link>
                        <div class="pt-4 mt-4 border-t border-gray-100 px-4 text-xs font-bold text-gray-400 uppercase">Configurações</div>
                        <Link :href="route('profile.edit')" class="block px-4 py-3 text-gray-700">Meu Perfil</Link>
                        <Link :href="route('logout')" method="post" class="w-full block pt-2">
                            <DangerButton class="w-full">Sair do Sistema</DangerButton>
                        </Link>
                    </nav>
                </div>
            </transition>

            <main class="flex-1 p-4 md:p-8 overflow-y-auto">
                <div class="mx-auto max-w-7xl">
                    <!-- TODO: corrigir parametros -->
                    <div
                        v-if="page.props.trial_info?.is_trial"
                        class="mb-6 rounded-xl bg-amber-50 border border-amber-200 p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
                    >
                        <div class="flex items-center gap-3">
                            <div class="bg-amber-100 p-2 rounded-lg text-amber-600">
                                <BarChart3 class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-sm font-bold text-amber-900">Período de Teste Grátis</p>
                                <p class="text-xs text-amber-700">
                                    Expira em <strong>{{ page.props.trial_info.days_remaining }} {{ page.props.trial_info.days_remaining === 1 ? 'dia' : 'dias' }}</strong>.
                                </p>
                            </div>
                        </div>
                        <a
                            v-on:click="confirmingUpgrade = true"
                            target="_blank"
                            class="cursor-pointer text-xs font-bold uppercase bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700 transition-colors shadow-sm"
                        >
                            Assinar Agora
                        </a>
                    </div>

                    <slot />
                </div>
            </main>
        </div>
    </div>


    <UpgradePlano
        :show="confirmingUpgrade"
        :planos="planos"
        @close="confirmingUpgrade = false"
    />
</template>

<style scoped>
/* Evita que o texto "pule" durante a animação de largura da sidebar */
.whitespace-nowrap {
    white-space: nowrap;
}
</style>