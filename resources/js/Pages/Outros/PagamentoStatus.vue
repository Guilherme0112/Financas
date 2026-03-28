<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import { CheckCircle, XCircle, Clock, ArrowRight, MessageCircle, RefreshCw } from "lucide-vue-next";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

interface Props {
    status: 'success' | 'failed' | 'pending';
    fatura_id?: string | number;
    checkout_url?: string; // Para o botão de "Tentar novamente"
}

const props = defineProps<Props>();

// Configurações dinâmicas baseadas no status
const config = {
    success: {
        title: "Pagamento Confirmado!",
        description: "Tudo pronto! Sua assinatura foi ativada e você já tem acesso total a todos os recursos do Faturaí.",
        icon: CheckCircle,
        iconClass: "bg-emerald-50 text-emerald-600",
        buttonText: "Ir para o Dashboard",
        buttonLink: route('dashboard'),
        isExternal: false
    },
    failed: {
        title: "Ops! Algo deu errado",
        description: "Não conseguimos processar seu pagamento. Pode ter sido um problema com o cartão ou instabilidade no gateway.",
        icon: XCircle,
        iconClass: "bg-rose-50 text-rose-600",
        buttonText: "Tentar Novamente",
        buttonLink: props.checkout_url || route('dashboard'), // Volta pro checkout ou início
        isExternal: true
    },
    pending: {
        title: "Pagamento em Análise",
        description: "Estamos aguardando a confirmação do seu pagamento. Isso acontece geralmente com Pix ou Boleto. Assim que aprovado, seu acesso será liberado.",
        icon: Clock,
        iconClass: "bg-amber-50 text-amber-600",
        buttonText: "Voltar para o Início",
        buttonLink: route('dashboard'),
        isExternal: false
    }
}[props.status];
</script>

<template>
    <Head :title="config.title" />

    <GuestLayout>
        <div class="flex items-center justify-center px-4">
            <div class="w-full max-w-[480px] bg-white rounded-[2.5rem] shadow-2xl p-10 md:p-14 border border-slate-50 text-center">
                
                <div :class="['inline-flex items-center justify-center w-24 h-24 rounded-full mb-8 transition-transform duration-500 hover:scale-110', config.iconClass]">
                    <component :is="config.icon" class="w-12 h-12" />
                </div>

                <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-4">
                    {{ config.title }}
                </h1>
                
                <p class="text-slate-500 leading-relaxed mb-10 font-medium">
                    {{ config.description }}
                </p>

                <div class="space-y-4">
                    <template v-if="config.isExternal">
                        <a :href="config.buttonLink" class="block w-full">
                            <PrimaryButton class="w-full justify-center py-4 gap-2 text-base shadow-xl">
                                <RefreshCw class="w-5 h-5" />
                                {{ config.buttonText }}
                            </PrimaryButton>
                        </a>
                    </template>
                    <template v-else>
                        <Link :href="config.buttonLink" class="block w-full">
                            <PrimaryButton class="w-full justify-center py-4 gap-2 text-base shadow-xl">
                                {{ config.buttonText }}
                                <ArrowRight class="w-5 h-5" />
                            </PrimaryButton>
                        </Link>
                    </template>

                    <Link href="/suporte" class="block w-full">
                        <SecondaryButton class="w-full justify-center py-4 gap-2 border-none bg-slate-50 hover:bg-slate-100 text-slate-600">
                            <MessageCircle class="w-5 h-5" />
                            Falar com suporte
                        </SecondaryButton>
                    </Link>
                </div>

                <div v-if="fatura_id" class="mt-10 pt-8 border-t border-slate-50">
                    <p class="text-[10px] uppercase tracking-widest font-bold text-slate-300">
                        Referência da Fatura: #{{ fatura_id }}
                    </p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>