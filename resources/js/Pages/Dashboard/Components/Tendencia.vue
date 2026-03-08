<script setup lang="ts">
import { formatarDinheiro } from '@/utils/helpers';
import { computed } from 'vue';

const props = defineProps<{
  value: number;
  trend: 'up' | 'down' | 'stable';
  invert?: boolean;
}>();

const isPositive = computed(() => {
  if (props.trend === 'stable') return null;
  return props.invert ? props.trend === 'down' : props.trend === 'up';
});
</script>

<template>
  <div :class="[
    'flex items-center gap-1.5 rounded-xl px-2.5 py-1 text-[11px] font-bold transition-all',
    isPositive === true ? 'bg-emerald-50 text-emerald-600' : 
    isPositive === false ? 'bg-rose-50 text-rose-600' : 'bg-slate-50 text-slate-500'
  ]">
    <svg v-if="trend !== 'stable'" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" :class="trend === 'down' ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
      <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"></polyline>
      <polyline points="16 7 22 7 22 13"></polyline>
    </svg>
    <span>{{ formatarDinheiro(value) }}</span>
  </div>
</template>