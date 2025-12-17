<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'

interface TableHeader {
  label: string
  key: string
  align?: 'left' | 'center' | 'right'
  format?: (value: any, row?: any) => string
}

interface TableAction {
  label: string
  class?: string
  onClick: (row: any) => void
}


defineProps<{
  headers: TableHeader[]
  rows: Record<string, any>[]
  actions?: TableAction[]
}>()


const aberto = ref<number | null>(null)

const toggle = (index: number) => {
  aberto.value = aberto.value === index ? null : index
}

const fechar = () => {
  aberto.value = null
}

const handleClickOutside = (event: MouseEvent) => {
  const alvo = event.target as HTMLElement
  if (!alvo.closest('[data-menu]')) {
    fechar()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div class="bg-white border border-green-200 rounded-lg shadow">
    <table class="w-full text-sm">

      <!-- HEADER -->
      <thead class="bg-green-100 border-b border-green-200">
        <tr>
          <th v-for="header in headers" :key="header.key" :class="[
            'px-6 py-3 text-xs font-bold uppercase tracking-wider text-green-900',
            header.align === 'center' ? 'text-center' :
              header.align === 'right' ? 'text-right' : 'text-left'
          ]">
            {{ header.label }}
          </th>

          <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider text-green-900">
            Ações
          </th>
        </tr>
      </thead>

      <!-- BODY -->
      <tbody>
        <tr v-for="(row, index) in rows" :key="index"
          class="border-b last:border-0 border-green-100 hover:bg-green-50 transition">
          <td v-for="header in headers" :key="header.key" :class="[
            'px-6 py-3 text-zinc-800',
            header.align === 'center' ? 'text-center' :
              header.align === 'right' ? 'text-right font-semibold' : 'text-left'
          ]">
            <slot :name="`cell-${header.key}`" :row="row" :value="row[header.key]">
              {{ header.format ? header.format(row[header.key], row) : row[header.key] }}
            </slot>
          </td>


          <!-- AÇÕES -->
          <td class="px-6 py-3 text-center relative" data-menu>
            <button @click="toggle(index)"
              class="p-2 rounded-full hover:bg-green-100 transition text-green-800 rotate-90">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <circle cx="5" cy="12" r="2" />
                <circle cx="12" cy="12" r="2" />
                <circle cx="19" cy="12" r="2" />
              </svg>
            </button>

            <div v-if="aberto === index"
              class="absolute right-4 mt-2 w-44 bg-white border border-green-200 rounded-md shadow-xl z-50">
              <button v-for="(action, i) in actions" :key="i" class="w-full text-left px-4 py-2 text-sm transition"
                :class="action.class" @click="() => { action.onClick(row); fechar() }">
                {{ action.label }}
              </button>
            </div>

          </td>

        </tr>
      </tbody>
    </table>
  </div>
</template>
