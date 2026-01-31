<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'

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

type Theme = 'green' | 'red'

const props = defineProps<{
  headers: TableHeader[]
  rows: Record<string, any>[]
  actions?: TableAction[]
  theme?: Theme
}>()

const theme = computed(() => props.theme ?? 'green')

const themeClasses = computed(() => {
  const c = theme.value

  return {
    container: `border-${c}-200`,
    thead: `bg-${c}-100 border-${c}-200`,
    th: `text-${c}-900`,
    trHover: `hover:bg-${c}-50`,
    tdBorder: `border-${c}-100`,
    actionText: `text-${c}-800`,
    actionHover: `hover:bg-${c}-100`,
    menuBorder: `border-${c}-200`,
  }
})

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
  <div class="bg-white rounded-lg shadow border" :class="themeClasses.container">
    <table class="w-full text-sm">

      <!-- HEADER -->
      <thead class="border-b" :class="themeClasses.thead">
        <tr>
          <th
            v-for="header in headers"
            :key="header.key"
            :class="[
              'px-6 py-3 text-xs font-bold uppercase tracking-wider',
              themeClasses.th,
              header.align === 'center'
                ? 'text-center'
                : header.align === 'right'
                ? 'text-right'
                : 'text-left'
            ]"
          >
            {{ header.label }}
          </th>

          <th
            v-if="actions"
            class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider"
            :class="themeClasses.th"
          >
            Ações
          </th>
        </tr>
      </thead>

      <!-- BODY -->
      <tbody>
        <tr
          v-for="(row, index) in rows"
          :key="index"
          class="border-b last:border-0 transition"
          :class="[themeClasses.tdBorder, themeClasses.trHover]"
        >
          <td
            v-for="header in headers"
            :key="header.key"
            :class="[
              'px-6 py-3 text-zinc-800',
              header.align === 'center'
                ? 'text-center'
                : header.align === 'right'
                ? 'text-right font-semibold'
                : 'text-left'
            ]"
          >
            <slot
              :name="`cell-${header.key}`"
              :row="row"
              :value="row[header.key]"
            >
              {{ header.format ? header.format(row[header.key], row) : row[header.key] }}
            </slot>
          </td>

          <!-- AÇÕES -->
          <td
            v-if="actions"
            class="px-6 py-3 text-center relative"
            data-menu
          >
            <button
              @click="toggle(index)"
              class="p-2 rounded-full transition rotate-90"
              :class="[themeClasses.actionText, themeClasses.actionHover]"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <circle cx="5" cy="12" r="2" />
                <circle cx="12" cy="12" r="2" />
                <circle cx="19" cy="12" r="2" />
              </svg>
            </button>

            <div
              v-if="aberto === index"
              class="absolute right-4 mt-2 w-44 bg-white rounded-md shadow-xl z-50 border"
              :class="themeClasses.menuBorder"
            >
              <button
                v-for="(action, i) in actions"
                :key="i"
                class="w-full text-left px-4 py-2 text-sm transition"
                :class="action.class"
                @click="() => { action.onClick(row); fechar() }"
              >
                {{ action.label }}
              </button>
            </div>
          </td>
        </tr>
      </tbody>

    </table>
  </div>
</template>
