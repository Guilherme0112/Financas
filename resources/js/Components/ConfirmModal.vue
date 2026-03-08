<script setup lang="ts">
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "./PrimaryButton.vue";
import { Check } from "lucide-vue-next";
import Icon from "./Icon.vue";

defineProps<{
    show: boolean;
    isDisabled?: boolean;
    message: string;
    title?: string;
    confirmText?: string;
    cancelText?: string;
}>();

const emit = defineEmits<{
    (e: "confirm"): void;
    (e: "close"): void;
}>();
</script>

<template>
    <Modal :show="show" @close="emit('close')">
        <div class="p-6 space-y-4">
            <header class="mb-6">
                <h3
                    class="text-lg font-bold text-emerald-800 flex items-center gap-2"
                >
                    <Icon>
                        <Check :size="22" />
                    </Icon>
                    {{ title || 'Confirmar ação' }}
                </h3>

                <p class="text-sm text-emerald-700 mt-1 opacity-70">
                    {{ message }}
                </p>
            </header>

            <div class="flex justify-end gap-3 pt-4">
                <SecondaryButton @click="emit('close')">
                    {{ cancelText || "Cancelar" }}
                </SecondaryButton>

                <PrimaryButton :disabled="isDisabled" @click="emit('confirm')">
                    {{ confirmText || "Confirmar" }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
