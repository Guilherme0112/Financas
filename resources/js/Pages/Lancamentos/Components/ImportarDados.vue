<script setup lang="ts">
import Modal from '@/Components/Modal.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import * as XLSX from 'xlsx'
import { Upload } from 'lucide-vue-next'
import Icon from '@/Components/Icon.vue'

const props = defineProps<{ show: boolean }>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success'): void
  (e: 'start'): void
}>()

const loading = ref(false);
const preview = ref<string[][]>([]);
const fileInput = ref<HTMLInputElement | null>(null);

const form = useForm<{ file: File | null }>({
  file: null,
})

const onFileChange = (event: Event) => {
  const input = event.target as HTMLInputElement
  if (!input.files?.length) return

  const file = input.files[0]
  form.file = file
  preview.value = []

  const extension = file.name.split('.').pop()?.toLowerCase()

  if (extension === 'csv') {
    readCSV(file)
  }

  if (extension === 'xlsx') {
    readXLSX(file)
  }
}

const readCSV = (file: File) => {
  const reader = new FileReader()
  reader.onload = e => {
    const text = e.target?.result as string
    preview.value = text
      .split('\n')
      .slice(0, 10)
      .map(row => row.split(','))
  }
  reader.readAsText(file)
}

const readXLSX = (file: File) => {
  const reader = new FileReader()
  reader.onload = e => {
    const data = new Uint8Array(e.target?.result as ArrayBuffer)
    const workbook = XLSX.read(data, { type: 'array' })
    const sheet = workbook.Sheets[workbook.SheetNames[0]]
    preview.value = XLSX.utils.sheet_to_json(sheet, {
      header: 1,
      blankrows: false,
    }).slice(0, 10) as string[][]
  }
  reader.readAsArrayBuffer(file)
}

const submit = () => {
  if (!form.file) return

  const extension = form.file.name.split('.').pop()?.toLowerCase()
  if (!['xlsx', 'csv'].includes(extension!)) {
    alert('Formato inválido')
    return
  }

  form.post(route(`importar.${extension}`), {
    forceFormData: true,
    onStart: () => {
      loading.value = true;
    },
    onSuccess: () => {
      emit('close');
      emit('start');
      form.reset();
      preview.value = [];
    },
    onFinish: () => {
      loading.value = false;
    }
  })
}

const cancel = () => {
  preview.value = []
  form.reset()
  loading.value = false

  if (fileInput.value) {
    fileInput.value.value = ''
  }

  emit('close')
}
</script>
<template>
  <Modal :show="props.show" @close="cancel">
    <form @submit.prevent="submit" class="px-8 py-6 space-y-6">
      <header class="mb-6">
        <h3 class="text-lg font-bold text-emerald-800 flex items-center gap-2">
          <Icon>
            <Upload :size="22" />
          </Icon>
          Importar Dados Financeiros
        </h3>

        <p class="text-sm text-emerald-700 mt-1 opacity-70">
          Envie um arquivo com seus lançamentos para atualizar seu controle financeiro.
        </p>
      </header>

      <div>
        <input ref="fileInput" type="file" accept=".xlsx,.csv" @change="onFileChange"
          class="block w-full text-sm file:mr-4 file:rounded-md file:border-0 file:bg-emerald-600 file:px-4 file:py-2 file:font-semibold file:text-white hover:file:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" />
        <p class="text-xs text-gray-800 mt-1 opacity-70">Somente são aceitos arquivos CSV e XLSX.</p>
      </div>

      <!-- PREVIEW -->
      <div v-if="preview.length" class="max-h-64 overflow-auto border rounded">
        <table class="w-full text-xs border-collapse">
          <tr v-for="(row, i) in preview" :key="i">
            <td v-for="(cell, j) in row" :key="j" class="border px-2 py-1">
              {{ cell }}
            </td>
          </tr>
        </table>
      </div>

      <div class="flex justify-end gap-3">
        <SecondaryButton type="button" @click="cancel">
          Cancelar
        </SecondaryButton>
        <PrimaryButton type="submit" :disabled="!form.file || loading">
          {{ loading ? 'Importando...' : 'Importar' }}
        </PrimaryButton>
      </div>
    </form>
  </Modal>
</template>
