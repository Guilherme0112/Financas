<script setup lang="ts">
import { ref, watch } from 'vue'
import TextInput from './TextInput.vue'

const props = defineProps<{
  modelValue?: number
  label?: string
  icon?: any
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void
}>();

const displayValue = ref('')

// sincroniza quando vem do pai
watch(
  () => props.modelValue,
  (value) => {
    if (value === null || value === undefined || isNaN(Number(value))) {
      displayValue.value = ''
      return
    }

    displayValue.value = Number(value).toLocaleString('pt-BR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    })
  },
  { immediate: true }
)

// usuário digitando
function handleInput(v: string) {
  const numericValue = v.replace(/\D/g, '')
  const floatValue = Number(numericValue) / 100

  displayValue.value = floatValue.toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })

  emit(
    'update:modelValue',
    floatValue.toFixed(2)
  )


}
</script>

<template>
  <TextInput v-model="displayValue" @update:modelValue="handleInput" :label="label" :icon="icon" placeholder="R$ 0,00"
    inputmode="numeric" />
</template>
