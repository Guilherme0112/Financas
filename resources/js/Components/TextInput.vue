<script setup lang="ts">
import { onMounted, ref, useAttrs, computed, watch } from 'vue';

const props = defineProps<{
  limit?: number
  maxlength?: number
}>()

const model = defineModel<string>({ required: true })
const attrs = useAttrs()
const input = ref<HTMLInputElement | null>(null)

const inputClass = `
  border
  rounded-md
  border-emerald-300
  shadow-sm
  focus:outline-none
  focus:border-emerald-500
  focus:ring-1
  focus:ring-emerald-500
`;

// quantidade atual de caracteres
const length = computed(() => model.value?.length || 0)

// corta se ultrapassar
watch(model, (val) => {
  if (props.limit && val && val.length > props.limit) {
    model.value = val.slice(0, props.limit)
  }
})

onMounted(() => {
  if (input.value?.hasAttribute('autofocus')) {
    input.value.focus()
  }
})

defineExpose({ focus: () => input.value?.focus() })
</script>

<template>
    <input
      ref="input"
      v-model="model"
      v-bind="attrs"
      :maxlength="maxlength ?? limit"
      :class="inputClass"
    />
    <div
      v-if="limit"
      class="text-xs text-right mt-1"
      :class="length >= limit ? 'text-red-500' : 'text-gray-400'"
    >
      {{ length }} / {{ limit }}
    </div>
</template>
