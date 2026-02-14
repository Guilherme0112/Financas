<script setup lang="ts">
defineProps<{
    show: boolean
    message?: string
}>()
</script>

<template>
    <transition name="fade">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
            <div class="flex flex-col items-center gap-4">
                <div class="spinner"></div>
                <p class="text-emerald-500 text-sm tracking-wide font-medium">
                    {{ message || 'Importando dados...' }}
                </p>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.spinner {
    width: 52px;
    height: 52px;
    border: 4px solid rgba(16, 185, 129, 0.2);
    /* verde suave */
    border-top: 4px solid #10b981;
    /* emerald 500 */
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
