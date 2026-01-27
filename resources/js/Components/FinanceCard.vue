<script setup lang="ts">
import { formatarDinheiro } from '@/utils/helpers'
import { computed } from 'vue'

type Props = {
  title: string
  value: number
  type?: 'positive' | 'negative'
}

const props = withDefaults(defineProps<Props>(), {
  type: 'positive',
})

const isNegative = computed(() => props.type === 'negative')

const containerClass = computed(() =>
  isNegative.value
    ? 'bg-red-100'
    : 'bg-emerald-100'
)

const titleClass = computed(() =>
  isNegative.value ? 'text-red-700' : 'text-emerald-700'
)

const valueClass = computed(() =>
  isNegative.value ? 'text-red-600' : 'text-emerald-600'
)

const formattedValue = computed(() =>
  `${Number(props.value || 0).toFixed(2)}`
)
</script>

<template>
  <div :class="['p-6 rounded-xl shadow-sm transition', containerClass]">
    <p :class="['text-sm font-medium', titleClass]">
      {{ title }}
    </p>

    <p :class="['text-3xl font-bold mt-2', valueClass]">
      {{ formatarDinheiro(formattedValue) }}
    </p>
  </div>
</template>
