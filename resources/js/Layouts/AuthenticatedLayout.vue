<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NavLink from '@/Components/NavLink.vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'

const open = ref(false);
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Header -->
        <header class="sticky top-0 z-50 shadow-lg backdrop-blur-md">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">

                    <!-- Logo -->
                    <Link :href="route('dashboard')" class="flex items-center gap-2">
                        <ApplicationLogo class="h-8 text-emerald-600" />
                        <span class="text-lg font-bold tracking-tight text-emerald-900">
                            Faturaí
                        </span>
                    </Link>

                    <!-- Desktop Nav -->
                    <nav class="hidden sm:flex items-center gap-2">
                        <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="relative px-4 py-2 text-sm font-medium text-emerald-800
           after:absolute after:left-0 after:-bottom-0.5 after:h-0.5 after:w-0
           after:bg-emerald-600 after:transition-all after:duration-300
           hover:after:w-full focus:after:w-full" :class="route().current('dashboard') && 'after:w-full'">
                            Dashboard
                        </NavLink>

                        <NavLink :href="route('lancamentos.index')" :active="route().current('lancamentos.index')"
                            class="relative px-4 py-2 text-sm font-medium text-emerald-800
           after:absolute after:left-0 after:-bottom-0.5 after:h-0.5 after:w-0
           after:bg-emerald-600 after:transition-all after:duration-300
           hover:after:w-full hover:after:w-full" :class="route().current('lancamentos.index') && 'after:w-full'">
                            Lançamentos
                        </NavLink>
                    </nav>

                    <!-- User -->
                    <div class="hidden sm:flex items-center">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button
                                    class="flex items-center gap-3 rounded-full bg-emerald-50 px-3 py-1.5 hover:bg-emerald-100 transition">

                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-sm font-semibold text-white">
                                        {{ $page.props.auth.user.name.charAt(0) }}
                                    </div>

                                    <span class="text-sm font-medium text-emerald-900">
                                        {{ $page.props.auth.user.name }}
                                    </span>
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

                    <!-- Mobile Button -->
                    <button @click="open = !open"
                        class="sm:hidden rounded-lg p-2 text-emerald-700 hover:bg-emerald-100">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-if="open" class="sm:hidden border-t bg-white">
                <div class="space-y-1 px-4 py-3">
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

        <!-- Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
