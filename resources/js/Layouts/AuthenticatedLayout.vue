<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'

const showingNavigationDropdown = ref(false)
</script>

<template>
    <div class="min-h-screen bg-emerald-50">
        <!-- Header -->
        <header class="bg-green-100 shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-14 items-center justify-between">

                    <!-- Left -->
                    <div class="flex items-center gap-8">
                        <Link :href="route('dashboard')" class="flex items-center gap-2">
                            <ApplicationLogo class="h-8 w-auto text-emerald-700" />
                            <span class="font-semibold text-emerald-800 tracking-tight">
                                Faturaí
                            </span>
                        </Link>

                        <nav class="hidden sm:flex gap-1">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')"
                                class="px-3 py-1.5 rounded-md text-sm transition-colors" :class="route().current('dashboard')
                                    ? 'bg-emerald-200/60 text-emerald-900'
                                    : 'text-emerald-800'">
                                Dashboard
                            </NavLink>

                            <NavLink :href="route('lancamentos.index')" :active="route().current('lancamentos.index')"
                                class="px-3 py-1.5 rounded-md text-sm transition-colors" :class="route().current('lancamentos.index')
                                    ? 'bg-emerald-200/60 text-emerald-900'
                                    : 'text-emerald-800'">
                                Lançamentos
                            </NavLink>
                        </nav>

                    </div>

                    <!-- Right -->
                    <div class="hidden sm:flex items-center">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    class="flex items-center gap-2 rounded-full bg-emerald-200/60 px-4 py-1.5 text-sm text-emerald-900 transition">
                                    <span class="font-medium">
                                        {{ $page.props.auth.user.name }}
                                    </span>

                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">
                                    Perfil
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Sair
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>

                    <!-- Mobile button -->
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="sm:hidden rounded-md p-2 text-emerald-700 hover:bg-emerald-200">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" v-if="!showingNavigationDropdown" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" v-else />
                        </svg>
                    </button>

                </div>
            </div>

            <!-- Mobile menu -->
            <div v-if="showingNavigationDropdown" class="sm:hidden bg-emerald-100 border-t">
                <div class="px-4 py-3 space-y-1">
                    <ResponsiveNavLink :href="route('dashboard')">
                        Dashboard
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('lancamentos.index')">
                        Lançamentos
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('profile.edit')">
                        Perfil
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                        Sair
                    </ResponsiveNavLink>
                </div>
            </div>
        </header>

        <!-- Conteúdo -->
        <main>
            <slot />
        </main>
    </div>
</template>
