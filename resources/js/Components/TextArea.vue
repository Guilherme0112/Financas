<script setup lang="ts">
import { onMounted, ref, computed } from 'vue';

const props = defineProps<{
    modelValue: string | null;
    error?: string;
    label?: string;
    limit?: number;
    rows?: number;
    placeholder?: string;
}>();

const emit = defineEmits(['update:modelValue']);

const input = ref<HTMLTextAreaElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

// Calcula a quantidade de caracteres atual
const characterCount = computed(() => {
    return props.modelValue?.length || 0;
});

// Estilo dinâmico baseado no erro
const classes = computed(() => {
    const base = "w-full rounded-md shadow-sm transition duration-200 ease-in-out ";
    const errorClasses = props.error
        ? "border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500"
        : "border-green-300 focus:border-green-500 focus:ring-green-500";
    return base + errorClasses;
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <div class="relative">
        <textarea ref="input" :value="modelValue"
            @input="emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)" :class="classes"
            :maxlength="limit" :rows="rows || 3" :placeholder="placeholder" class="pr-12"></textarea>

        <div v-if="limit" class="text-xs text-right"
            :class="characterCount >= limit ? 'text-red-500' : 'text-gray-400'">
            {{ characterCount }} / {{ limit }}
        </div>
    </div>
</template>