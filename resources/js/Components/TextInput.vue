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
  hover:border-green-500
  checked:bg-green-600
  checked:hover:bg-green-600
  checked:focus:bg-green-600
  checked:border-green-600
  focus:ring-2
  focus:ring-green-500
`;

const inputClass = `
  rounded-md
  border-green-300
  shadow-sm
  focus:border-green-500
  focus:ring-green-500
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
