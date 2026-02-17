<script setup lang="ts">
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps<{
    checked: boolean;
    value?: any;
}>();

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <input type="checkbox" :value="value" v-model="proxyChecked" class=" h-4 w-4
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
  focus:ring-emerald-500" />
</template>
