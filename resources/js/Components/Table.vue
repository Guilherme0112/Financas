<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'
import Checkbox from './Checkbox.vue'

export interface TableHeader {
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
  actions?: TableAction[] | ((row: any) => TableAction[])
  theme?: string
  selectable?: boolean
}>()

const emit = defineEmits<{
  (e: 'selectionChange', selectedRows: Record<string, any>[]): void
  (e: 'rowClick', row: Record<string, any>): void
}>()

const color = props.theme || 'emerald'

const aberto = ref<number | null>(null)
const selectedRows = ref<Set<number>>(new Set())
const selectAll = ref(false)

const toggle = (index: number) => aberto.value = aberto.value === index ? null : index
const fechar = () => aberto.value = null

const toggleRowSelection = (index: number) => {
  if (selectedRows.value.has(index)) {
    selectedRows.value.delete(index)
  } else {
    selectedRows.value.add(index)
  }
  updateSelectAll()
  emitSelection()
}

const updateSelectAll = () => {
  if (selectedRows.value.size === props.rows.length && props.rows.length > 0) {
    selectAll.value = true
  } else {
    selectAll.value = false
  }
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedRows.value.clear()
    selectAll.value = false
  } else {
    selectedRows.value.clear()
    props.rows.forEach((_, index) => selectedRows.value.add(index))
    selectAll.value = true
  }
  emitSelection()
}

const emitSelection = () => {
  const selected = props.rows.filter((_, index) => selectedRows.value.has(index))
  emit('selectionChange', selected)
}

const handleClickOutside = (event: MouseEvent) => {
  const alvo = event.target as HTMLElement
  if (!alvo.closest('[data-menu]')) fechar()
}

const resolveActions = (row: any) => {
    if (!props.actions) return [];
    
    if (typeof props.actions === 'function') {
        const result = props.actions(row);
        return Array.isArray(result) ? result : [];
    }
    
    return props.actions;
};

onMounted(() => document.addEventListener('click', handleClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
</script>

<template>
  <div class="w-full select-none">
    
    <!-- HEADER (Oculto no Mobile, visível a partir do md) -->
    <div class="hidden md:flex px-6 mb-1">
      <div v-if="selectable" class="w-8 flex items-center justify-center">
        <Checkbox
          :checked="selectAll" 
          @change="toggleSelectAll"
          class="w-4 h-4 rounded cursor-pointer bg-white border-emerald-300 border-2 checked:bg-emerald-500 checked:border-emerald-500"
        />
      </div>
      <div 
        v-for="header in headers" 
        :key="header.key"
        class="text-xs font-black uppercase tracking-[0.2em] text-zinc-400/70 md:w-[var(--col-width)]"
        :style="{ '--col-width': 100 / (headers.length + (selectable ? 0.4 : 0) + (actions ? 0.4 : 0)) + '%' }"
        :class="[
          header.align === 'center' ? 'text-center' : 
          header.align === 'right' ? 'text-center' : 'text-center'
        ]"
      >
        {{ header.label }}
      </div>
      <div v-if="actions" class="w-8"></div>
    </div>

    <!-- CORPO DA TABELA -->
    <div class="flex flex-col gap-2 md:gap-1">
      
      <!-- ESTADO VAZIO -->
      <div v-if="rows.length === 0" class="p-6 text-center bg-white rounded-xl border border-dashed border-zinc-200">
        <p class="text-zinc-400 text-[10px] uppercase font-bold tracking-tighter">Vazio</p>
      </div>

      <!-- LINHAS (Cards no Mobile) -->
      <div
        v-else
        v-for="(row, index) in rows"
        :key="index"
        class="relative group bg-white border border-zinc-100 py-4 px-4 md:py-5 md:px-6 rounded-lg transition-all duration-200 hover:border-zinc-200 hover:shadow-sm flex flex-col md:flex-row md:items-center cursor-pointer gap-3 md:gap-0"
        @click="emit('rowClick', row)"
      >
        
        <!-- Checkbox -->
        <div v-if="selectable" class="w-full md:w-8 flex justify-between md:justify-center items-center border-b border-zinc-100 pb-2 md:border-none md:pb-0 mb-1 md:mb-0">
          <span class="text-xs font-bold text-zinc-400 md:hidden uppercase tracking-wider">Selecionar</span>
          <Checkbox 
            :checked="selectedRows.has(index)" 
            @change="toggleRowSelection(index)"
            @click.stop
            class="w-4 h-4 rounded cursor-pointer bg-white border-emerald-300 border-2 checked:bg-emerald-500 checked:border-emerald-500"
          />
        </div>

        <!-- Células de Dados -->
        <div 
          v-for="(header, hIndex) in headers" 
          :key="header.key"
          :style="{ '--col-width': 100 / (headers.length + (selectable ? 0.4 : 0) + (actions ? 0.4 : 0)) + '%' }"
          class="flex md:block items-center justify-between px-0 md:px-2 w-full md:w-[var(--col-width)] truncate"
          :class="[
            header.align === 'center' ? 'md:text-center' : 
            header.align === 'right' ? 'md:text-right' : 'md:text-center',
          ]"
        >
          <!-- Título da coluna visível APENAS no mobile -->
          <span class="md:hidden text-xs font-bold text-zinc-400 uppercase mr-4">
            {{ header.label }}
          </span>

          <!-- Valor da célula -->
          <span 
            class="text-right md:text-center truncate block w-full"
            :class="hIndex === 0 ? 'font-bold text-zinc-800 text-sm' : 'text-zinc-500 text-sm font-medium'"
          >
            <slot :name="`cell-${header.key}`" :row="row" :value="row[header.key]">
              {{ header.format ? header.format(row[header.key], row) : row[header.key] }}
            </slot>
          </span>
        </div>

        <!-- Ações (Menu 3 Pontinhos) -->
        <div v-if="resolveActions(row).length > 0" class="w-full md:w-8 md:ml-auto flex justify-end relative mt-2 md:mt-0 pt-3 md:pt-0 border-t border-zinc-100 md:border-none" data-menu @click.stop>
          <button
            @click.stop="toggle(index)"
            class="w-8 h-8 md:w-6 md:h-6 flex items-center justify-center rounded-md transition-all"
            :class="aberto === index ? `bg-${color}-100 text-${color}-600` : `text-zinc-700 hover:text-zinc-500 hover:bg-zinc-50`"
          >
            <svg class="w-5 h-5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M12 5v.01M12 12v.01M12 19v.01" />
            </svg>
          </button>

          <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="transform opacity-0 translate-y-1"
            enter-to-class="transform opacity-100 translate-y-0"
          >
            <div
              v-if="aberto === index"
              class="absolute right-0 bottom-full mb-2 md:bottom-auto md:mb-0 md:mt-1.5 w-[200px] bg-white rounded-xl shadow-xl p-1 z-[100] border border-zinc-100"
            >
              <button
                v-for="(action, i) in resolveActions(row)"
                :key="i"
                @click.stop="() => { action.onClick(row); fechar() }"
                class="w-full text-center px-4 py-2 text-xs font-bold uppercase tracking-tight rounded-lg transition-all flex items-center justify-between group/item"
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