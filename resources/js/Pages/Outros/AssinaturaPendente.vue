<template>
    <Head title="Pagamento Pendente" />
    <GuestLayout>
        <div class="flex items-center justify-center">
            <div
                class="min-h-[80vh] max-w-md p-10 flex flex-col rounded-2xl shadow-lg items-center justify-center bg-white"
            >
                <div class="max-w-md w-full text-center">
                    <div
                        class="mb-6 inline-flex items-center justify-center w-24 h-24 bg-amber-50 rounded-full animate-pulse"
                    >
                        <CreditCard class="w-12 h-12 text-amber-600" />
                    </div>

                    <h1 class="text-3xl font-extrabold text-slate-900 mb-3">
                        Pagamento Pendente
                    </h1>

                    <p class="text-slate-600 mb-8 leading-relaxed">
                        Quase lá! Recebemos seu pedido, mas ainda estamos 
                        <span class="font-bold text-amber-600">aguardando a confirmação</span> 
                        do pagamento pelo seu banco ou operadora de cartão.
                    </p>

                    <div class="space-y-4">
                        <PrimaryButton @click="handleGoToPayment" class="w-full py-3 gap-2 !bg-amber-600 hover:!bg-amber-700">
                            <span>Ver Detalhes do Pagamento</span>
                            <ExternalLink class="w-5 h-5" />
                        </PrimaryButton>

                        <Link
                            href="/suporte"
                            class="flex items-center justify-center gap-2 text-sm text-slate-500 hover:text-amber-600 transition-colors font-medium py-2"
                        >
                            <SecondaryButton class="w-full py-3 gap-2">
                                <MessageCircle class="w-5 h-5" />
                                Já paguei, falar com suporte
                            </SecondaryButton>
                        </Link>
                    </div>

                    <div class="mt-8 p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <p class="text-xs text-slate-500">
                            <strong>Dica:</strong> Pagamentos via PIX ou Cartão costumam ser aprovados em instantes. Boletos podem levar até 3 dias úteis.
                        </p>
                    </div>
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
import { CreditCard, MessageCircle, ExternalLink } from "lucide-vue-next";

// Você pode passar a URL da fatura via props se vier do backend
const props = defineProps({
    checkoutUrl: {
        type: String,
        default: '#'
    }
});

const handleGoToPayment = () => {
    // Redireciona para o link do checkout/fatura (Stripe, Pagar.me, etc)
    window.location.href = props.checkoutUrl;
};
</script>