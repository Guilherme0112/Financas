<template>
    <Head title="Assinatura Expirada" />
    <GuestLayout>
        <div class="flex items-center justify-center">
            <div
                class="min-h-[80vh] max-w-md p-10 flex flex-col rounded-2xl shadow-lg items-center justify-center bg-white"
            >
                <div class="max-w-md w-full text-center">
                    <div
                        class="mb-6 inline-flex items-center justify-center w-24 h-24 bg-emerald-50 rounded-full"
                    >
                        <Clock class="w-12 h-12 text-emerald-600" />
                    </div>

                    <h1 class="text-3xl font-extrabold text-slate-900 mb-3">
                        Assinatura Expirada
                    </h1>

                    <p class="text-slate-600 mb-8 leading-relaxed">
                        Sua licença do
                        <span class="font-bold text-emerald-600 text-lg"
                            >Faturaí</span
                        >
                        chegou ao fim. Para continuar registrando seus ganhos e
                        controlando seus gastos, renove seu acesso.
                    </p>

                    <div class="space-y-4">
                        <PrimaryButton @click="handleRenew" class="w-full py-3 gap-2">
                            <span>Renovar Agora</span>
                            <ArrowRight class="w-5 h-5" />
                        </PrimaryButton>

                        <Link
                            href="/suporte"
                            class="flex items-center justify-center gap-2 text-sm text-slate-500 hover:text-emerald-600 transition-colors font-medium py-2"
                        >
                            <SecondaryButton class="w-full py-3 gap-2">
                                <MessageCircle class="w-5 h-5" />
                                Falar com o suporte
                            </SecondaryButton>
                        </Link>
                    </div>

                    <p class="mt-6 text-xs text-slate-400">
                        Seus dados continuam salvos e seguros. Você terá acesso
                        total assim que a renovação for confirmada.
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
import { Head, Link, router } from "@inertiajs/vue3";
import { Clock, MessageCircle, ArrowRight } from "lucide-vue-next";

const handleRenew = () => {
    router.get("/faturas");
};
</script>
