<script setup lang="ts">
import { onMounted, ref, computed, useAttrs } from 'vue';

const model = defineModel<any>({ required: true });
const attrs = useAttrs();
const input = ref<HTMLInputElement | null>(null);

const isCheckbox = computed(() => attrs.type === 'checkbox');

const checkboxClass = `
  h-4 w-4
  appearance-none
  rounded
  border
  border-gray-300
  cursor-pointer
  hover:border-emerald-500
  checked:bg-emerald-600
  checked:hover:bg-emerald-600
  checked:focus:bg-emerald-600
  checked:border-emerald-600
  focus:ring-2
  focus:ring-emerald-500
`;

const inputClass = `
  rounded-md
  border-emerald-300
  shadow-sm
  focus:border-emerald-500
  focus:ring-emerald-500
`;

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input ref="input" v-model="model" v-bind="attrs"
        :class="isCheckbox ? checkboxClass : inputClass" />
</template>
