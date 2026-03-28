<script setup lang="ts">
import { ref, computed, watch, onMounted, useAttrs } from 'vue'

const emit = defineEmits(['update:query'])

const props = defineProps<{
  options?: Array<string | { value: any; label: string }>
  placeholder?: string
  clearable?: boolean
}>()

const model = defineModel<number | null>({ required: true });
const attrs = useAttrs();
const input = ref<HTMLInputElement | null>(null);
const open = ref(false);
const query = ref('');
const highlighted = ref(-1);
const isUserTyping = ref(false)

function onInput(e: Event) {
  isUserTyping.value = true
  query.value = (e.target as HTMLInputElement).value
}

const inputClass = `
  w-full 
  border
  rounded-md
  border-emerald-300
  shadow-sm
  focus:outline-none
  focus:border-emerald-500
  focus:ring-1
  focus:ring-emerald-500
`;

// normalize options to {value,label} and fallback to mock data when empty
const normOptions = computed(() => {
  const base = (props.options || []).map(o =>
    typeof o === 'string' ? { value: o, label: o } : o
  )
  return base;
})

const filtered = computed(() => {
  if (!query.value) return normOptions.value
  return normOptions.value.filter(o =>
    o.label.toString().toLowerCase().includes(query.value.toLowerCase())
  )
})

watch(query, (value) => {
  if (!isUserTyping.value) return

  emit('update:query', value)
})

watch(model, (v) => {
  const found = normOptions.value.find(o => o.value === v)
  query.value = found ? found.label : (v?.toString() ?? '')
})

onMounted(() => {
  if (input.value?.hasAttribute('autofocus')) {
    input.value.focus()
  }
})

function selectOption(opt: { value: any; label: string }) {
  model.value = opt.value;
  query.value = opt.label;
  open.value = false;
}

function clear() {
  model.value = null
  query.value = ''
  open.value = false
}

function onFocus() {
  open.value = true
  highlighted.value = -1
}

function onBlur() {
  setTimeout(() => (open.value = false), 150)
}

function onKeydown(e: KeyboardEvent) {
  if (!open.value) open.value = true
  if (e.key === 'ArrowDown') {
    highlighted.value = Math.min(highlighted.value + 1, filtered.value.length - 1)
    e.preventDefault()
  } else if (e.key === 'ArrowUp') {
    highlighted.value = Math.max(highlighted.value - 1, 0)
    e.preventDefault()
  } else if (e.key === 'Enter') {
    if (filtered.value[highlighted.value]) selectOption(filtered.value[highlighted.value])
    e.preventDefault()
  } else if (e.key === 'Escape') {
    open.value = false
  }
}

onMounted(() => {
  if (model.value != null) {
    const found = normOptions.value.find(o => o.value === model.value)
    if (found) {
      query.value = found.label
    }
  }

  if (input.value?.hasAttribute('autofocus')) {
    input.value.focus()
  }
})

defineExpose({ focus: () => input.value?.focus() })
</script>

<template>
  <div class="relative">
    <input
      ref="input"
      v-model="query"
      v-bind="attrs"
      :placeholder="placeholder"
      @focus="onFocus"
      @blur="onBlur"
      @input="onInput"
      @keydown="onKeydown"
      :class="inputClass"
    />

    <button v-if="clearable && query" type="button" class="absolute right-2 top-2 text-sm text-gray-500" @click="clear">✕</button>

    <ul v-if="open" class="absolute z-20 bg-white mt-1 w-full rounded-md shadow-lg max-h-48 overflow-auto">
      <li v-if="!filtered.length" class="px-3 py-2 text-sm text-gray-500">Nenhum resultado</li>
      <li v-for="(opt, idx) in filtered" :key="opt.value"
        @mousedown.prevent="selectOption(opt)"
        :class="['px-3 py-2 cursor-pointer', idx === highlighted ? 'bg-emerald-50' : 'hover:bg-gray-50']"
      >
        {{ opt.label }}
      </li>
    </ul>
  </div>
</template>
