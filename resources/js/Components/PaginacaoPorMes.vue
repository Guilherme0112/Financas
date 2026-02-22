<script setup lang="ts">
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'
import PrimaryButton from './PrimaryButton.vue'
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import debounce from 'debounce';
import { configInertia } from '@/inertia';

const props = defineProps<{
  routeName: string
}>()

const hoje = new Date()

const ano = ref(hoje.getFullYear())
const mes = ref(hoje.getMonth() + 1)

const trocarMesDebounce = debounce(() => {
  router.get(route(props.routeName), {
    ano: ano.value,
    mes: mes.value,
    page: 1
  }, {
    ...configInertia,
  })

}, 800);

const prev = () => {
  if (mes.value === 1) {
    mes.value = 12
    ano.value--
  } else {
    mes.value--
  }

  trocarMesDebounce()
}

const next = () => {
  if (mes.value === 12) {
    mes.value = 1
    ano.value++
  } else {
    mes.value++
  }

  trocarMesDebounce()
}

const monthName = computed(() => {
  return new Date(ano.value, mes.value - 1).toLocaleDateString('pt-BR', {
    month: 'long',
  })
})
</script>
<template>
  <div class="flex justify-center items-center gap-2 select-none mt-5">
    <PrimaryButton @click="prev" class="!px-2" aria-label="Mês anterior">
      <ChevronLeft :size="20" />
    </PrimaryButton>

    <div class="flex items-center bg-gray-100/50 border border-gray-200 px-4 py-2 rounded-xl shadow-inner">
      <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mr-2">
        Período
      </span>

      <span class="text-sm font-black text-emerald-600 capitalize">
        {{ monthName }}
      </span>

      <span class="text-sm font-medium text-gray-400 mx-1.5">
        /
      </span>

      <span class="text-sm font-bold text-gray-700">
        {{ ano }}
      </span>
    </div>

    <PrimaryButton @click="next" class="!px-2" aria-label="Próximo mês">
      <ChevronRight :size="20" />
    </PrimaryButton>
  </div>
</template>