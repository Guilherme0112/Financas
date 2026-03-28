<template>
    <Head title="Página não encontrada" />
    <GuestLayout>
        <div class="flex items-center justify-center">
            <div
                class="min-h-[80vh] max-w-md p-10 flex flex-col rounded-2xl shadow-lg items-center justify-center bg-white"
            >
                <div class="max-w-md w-full text-center">
                    <div class="flex justify-center gap-2">
                        <span
                            class="text-sm font-bold text-emerald-600 uppercase tracking-widest"
                            >404</span
                        >
                    </div>
                    <h1
                        class="text-3xl font-extrabold text-slate-900 mb-3 mt-2"
                    >
                        Página não encontrada
                    </h1>

                    <p class="text-slate-600 mb-8 leading-relaxed">
                        Parece que você se perdeu entre as contas. A página que
                        você está procurando não existe ou foi movida para outro
                        endereço.
                    </p>

                    <div class="space-y-4">
                        <Link href="/">
                            <PrimaryButton class="w-full py-3 gap-2">
                                <Home class="w-5 h-5" />
                                <span>Voltar ao Início</span>
                            </PrimaryButton>
                        </Link>

                        <SecondaryButton
                            @click="goBack"
                            class="w-full py-3 gap-2"
                        >
                            <ArrowLeft class="w-5 h-5" />
                            Retornar à página anterior
                        </SecondaryButton>
                    </div>

                    <p class="mt-8 text-xs text-slate-400">
                        Se o problema persistir, entre em contato com o suporte
                        do Faturaí.
                    </p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { SearchX, Home, ArrowLeft } from "lucide-vue-next";

// Função para voltar à página anterior no histórico do navegador
const goBack = () => {
    window.history.back();
};
</script>
