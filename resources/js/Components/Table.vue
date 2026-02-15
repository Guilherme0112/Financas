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

const props = defineProps<{
  headers: TableHeader[]
  rows: Record<string, any>[]
  actions?: TableAction[]
  theme?: string // Ex: 'emerald', 'blue', 'rose', 'amber'
}>()

// Define um padrão caso a prop não seja enviada
const color = props.theme || 'emerald'

const aberto = ref<number | null>(null)
const toggle = (index: number) => aberto.value = aberto.value === index ? null : index
const fechar = () => aberto.value = null

const handleClickOutside = (event: MouseEvent) => {
  const alvo = event.target as HTMLElement
  if (!alvo.closest('[data-menu]')) fechar()
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
</script>

<template>
  <div class="w-full p-1">
    <div 
      class="overflow-hidden bg-white rounded-2xl transition-all duration-300 shadow-lg"
    >
      <div class="overflow-x-auto">
        <table class="w-full text-left border-separate border-spacing-0">
          
          <thead>
            <tr :class="[`bg-${color}-100`]">
              <th
                v-for="(header, index) in headers"
                :key="header.key"
                class="px-6 py-4 text-[11px] font-bold uppercase tracking-widest border-b"
                :class="[
                  `text-${color}-800`,
                  `border-${color}-100`,
                  header.align === 'center' ? 'text-center' : 
                  header.align === 'right' ? 'text-right' : 'text-left',
                  index === 0 ? 'rounded-tl-2xl' : '',
                  !actions && index === headers.length - 1 ? 'rounded-tr-2xl' : ''
                ]"
              >
                {{ header.label }}
              </th>

              <th v-if="actions" 
                class="px-6 py-4 text-center text-[11px] font-bold uppercase tracking-widest border-b rounded-tr-2xl"
                :class="[`text-${color}-800`, `border-${color}-100`]"
              >
                Ações
              </th>
            </tr>
          </thead>

          <tbody class="divide-y" :class="[`divide-${color}-50/50`]">
            <tr v-if="rows.length === 0">
              <td :colspan="headers.length + (actions ? 1 : 0)" class="px-6 py-10 text-center">
                <div class="flex flex-col items-center justify-center" :class="[`text-gray-500`]">
                  <svg class="w-14 h-14 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <p class="font-medium italic" :class="[`text-${color}-800/50`]">Nenhum registro encontrado</p>
                </div>
              </td>
            </tr>

            <tr
              v-else
              v-for="(row, index) in rows"
              :key="index"
              class="group transition-colors duration-150"
              :class="[`hover:bg-${color}-50/30`]"
            >
              <td
                v-for="header in headers"
                :key="header.key"
                class="px-6 py-4 text-sm text-zinc-600 transition-colors"
                :class="[
                  `group-hover:text-${color}-900`,
                  header.align === 'center' ? 'text-center' : 
                  header.align === 'right' ? 'text-right font-semibold' : 'text-left'
                ]"
              >
                <slot :name="`cell-${header.key}`" :row="row" :value="row[header.key]">
                  {{ header.format ? header.format(row[header.key], row) : row[header.key] }}
                </slot>
              </td>

              <td v-if="actions" class="px-6 py-4 text-center relative" data-menu>
                <button
                  @click.stop="toggle(index)"
                  class="inline-flex items-center justify-center w-9 h-9 rounded-xl transition-all duration-200 active:scale-90"
                  :class="[
                    `text-${color}-600 hover:bg-${color}-100/80 hover:text-${color}-700`,
                    aberto === index ? `bg-${color}-100 text-${color}-800` : ''
                  ]"
                >
                  <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                  </svg>
                </button>

                <transition
                  enter-active-class="transition duration-200 ease-out"
                  enter-from-class="transform -translate-y-2 opacity-0"
                  enter-to-class="transform translate-y-0 opacity-100"
                >
                  <div
                    v-if="aberto === index"
                    class="absolute right-10 top-2 w-48 bg-white rounded-xl shadow-xl border py-1.5 z-50 mt-10 mr-[60px]"
                    :class="[`shadow-${color}-900/10`, `border-${color}-100`]"
                  >
                    <button
                      v-for="(action, i) in actions"
                      :key="i"
                      @click="() => { action.onClick(row); fechar() }"
                      class="w-full text-left px-2 py-2 text-[13px] text-zinc-600 transition-colors flex items-center gap-2"
                      :class="[`hover:bg-${color}-50 hover:text-${color}-700`, action.class]"
                    >
                      <div class="w-1.5 h-1.5 rounded-full" :class="[`bg-${color}-400`]"></div>
                      {{ action.label }}
                    </button>
                  </div>
                </transition>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>