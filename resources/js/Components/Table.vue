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
  theme?: string
}>()

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
  <div class="w-full select-none">
    <div class="flex px-5 mb-1">
      <div 
        v-for="header in headers" 
        :key="header.key"
        class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400/70"
        :style="{ width: 100 / (headers.length + (actions ? 0.4 : 0)) + '%' }"
        :class="[
          header.align === 'center' ? 'text-center' : 
          header.align === 'right' ? 'text-right' : 'text-left'
        ]"
      >
        {{ header.label }}
      </div>
      <div v-if="actions" class="w-8"></div>
    </div>

    <div class="flex flex-col gap-1">
      <div v-if="rows.length === 0" class="p-6 text-center bg-zinc-50/30 rounded-xl border border-dashed border-zinc-200">
        <p class="text-zinc-400 text-[10px] uppercase font-bold tracking-tighter">Vazio</p>
      </div>

      <div
        v-else
        v-for="(row, index) in rows"
        :key="index"
        class="relative group bg-white border border-zinc-100 py-4 px-5 rounded-lg transition-all duration-200 hover:border-zinc-200 hover:shadow-sm flex items-center"
      >

        <div 
          v-for="(header, hIndex) in headers" 
          :key="header.key"
          :style="{ width: 100 / (headers.length + (actions ? 0.4 : 0)) + '%' }"
          class="px-2 truncate"
          :class="[
            header.align === 'center' ? 'text-center' : 
            header.align === 'right' ? 'text-right' : 'text-left',
            hIndex === 0 ? 'font-bold text-zinc-800 text-[13px]' : 'text-zinc-500 text-[13px] font-medium'
          ]"
        >
          <slot :name="`cell-${header.key}`" :row="row" :value="row[header.key]">
            {{ header.format ? header.format(row[header.key], row) : row[header.key] }}
          </slot>
        </div>

        <div v-if="actions" class="ml-auto relative" data-menu>
          <button
            @click.stop="toggle(index)"
            class="w-6 h-6 flex items-center justify-center rounded-md transition-all"
            :class="aberto === index ? `bg-${color}-100 text-${color}-600` : `text-zinc-700 hover:text-zinc-500 hover:bg-zinc-50`"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v.01M12 12v.01M12 19v.01" />
            </svg>
          </button>

          <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="transform scale-95 opacity-0 translate-y-1"
            enter-to-class="transform scale-100 opacity-100 translate-y-0"
          >
            <div
              v-if="aberto === index"
              class="absolute right-0 mt-1.5 w-36 bg-white rounded-xl shadow-xl p-1 z-[100] border border-zinc-100"
            >
              <button
                v-for="(action, i) in actions"
                :key="i"
                @click="() => { action.onClick(row); fechar() }"
                class="w-full text-left px-3 py-1.5 text-[10px] font-bold uppercase tracking-tight rounded-lg transition-all flex items-center justify-between group/item"
                :class="[`text-zinc-500 hover:bg-${color}-50 hover:text-${color}-700`, action.class]"
              >
                {{ action.label }}
                <svg 
                  class="w-3 h-3 opacity-0 -translate-x-1 group-hover/item:opacity-100 group-hover/item:translate-x-0 transition-all" 
                  fill="none" stroke="currentColor" viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                </svg>
              </button>
            </div>
          </transition>
        </div>
      </div>
    </div>
  </div>
</template>