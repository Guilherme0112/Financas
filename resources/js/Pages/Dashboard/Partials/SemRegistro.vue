<script setup lang="ts">
import { computed } from 'vue';

const classe = computed(() => {
  const baseClasses = 'w-full h-[350px] flex items-center justify-center rounded-xl'
  const colorClasses = `bg-gray-50 text-gray-700`;
  return `${baseClasses} ${colorClasses}`
});

  const textClasses = computed(() => {
    return `text-center text-gray-700`
  })

</script>
<template>
  <div :class="classe">
    <div :class="textClasses">
      <p class="font-semibold">Sem dados neste período</p>
      <p class="text-sm opacity-80">
        Nenhum registro foi encontrado
      </p>
    </div>
  </div>
</template>
