<script setup lang="ts">
import { ref, computed } from 'vue';
import { PiggyBank, TrendingDown, Calendar, ChevronRight, Info } from 'lucide-vue-next';
import Icon from '@/Components/Icon.vue';

interface Categoria {
  id: number;
  nome: string;
  icone: string;
  gastoAtual: number;
  limite: number;
}

const categorias = ref<Categoria[]>([
  { id: 1, nome: 'Alimentação', icone: '🍕', gastoAtual: 1200, limite: 1200 },
  { id: 2, nome: 'Assinaturas/Lazer', icone: '🎬', gastoAtual: 450, limite: 450 },
  { id: 3, nome: 'Transporte', icone: '🚗', gastoAtual: 800, limite: 800 },
]);

// Cálculo de economia
const economiaMensalTotal = computed(() => {
  return categorias.value.reduce((acc, cat) => acc + (cat.gastoAtual - cat.limite), 0);
});

const formatarMoeda = (valor: number) => {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor);
};

// Projeções
const projecoes = computed(() => [
  { meses: 3, valor: economiaMensalTotal.value * 3, label: '3 meses' },
  { meses: 9, valor: economiaMensalTotal.value * 9, label: '9 meses' },
  { meses: 12, valor: economiaMensalTotal.value * 12, label: '1 ano' },
]);
</script>

<template>
  <div class="max-w-4xl mx-auto p-6 bg-gray-50 rounded-3xl shadow-lg font-sans">
    <header class="mb-8">
      <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
        <Icon>
          <PiggyBank :size="25" />
        </Icon>
        Simulador de Metas
      </h1>
      <p class="text-gray-500 mt-2">Ajuste seus limites e veja o impacto a longo prazo.</p>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      <div class="lg:col-span-2 space-y-4">
        <div v-for="cat in categorias" :key="cat.id"
          class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">

          <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-3">
              <span class="text-2xl p-2 bg-gray-50 rounded-lg">{{ cat.icone }}</span>
              <div>
                <h3 class="font-bold text-gray-800">{{ cat.nome }}</h3>
                <p class="text-xs text-gray-400">Gasto atual: {{ formatarMoeda(cat.gastoAtual) }}</p>
              </div>
            </div>
            <div class="text-right">
              <span class="text-sm font-semibold"
                :class="cat.limite < cat.gastoAtual ? 'text-emerald-600' : 'text-gray-500'">
                Novo Limite: {{ formatarMoeda(cat.limite) }}
              </span>
            </div>
          </div>

          <input type="range" v-model.number="cat.limite" :min="0" :max="cat.gastoAtual" step="10"
            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-emerald-500" />

          <div class="flex justify-between mt-2 text-[10px] uppercase tracking-wider font-bold text-gray-400">
            <span>Economia: {{ formatarMoeda(cat.gastoAtual - cat.limite) }}</span>
            <span>Redução de {{ Math.round((1 - cat.limite / cat.gastoAtual) * 100) }}%</span>
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-emerald-100 relative overflow-hidden">
          <div class="relative z-10">
            <h2 class="text-emerald-700 font-bold flex items-center gap-2 mb-6">
              <Icon>
                <TrendingDown :size="18" />
              </Icon>
              Economia Estimada
            </h2>

            <div class="space-y-6">
              <div v-for="proj in projecoes" :key="proj.meses"
                class="flex justify-between items-end border-b border-emerald-100 pb-4 last:border-0">
                <div>
                  <p class="text-emerald-600/70 text-xs font-bold uppercase tracking-wider">{{ proj.label }}</p>
                  <p class="text-3xl font-black text-emerald-900 tracking-tight">
                    {{ formatarMoeda(proj.valor) }}
                  </p>
                </div>
                <div class="bg-white px-2 py-1 rounded-md shadow-sm border border-emerald-50 mb-1">
                  <ChevronRight class="text-emerald-300" :size="14" />
                </div>
              </div>
            </div>

            <div class="mt-8 p-4 bg-white/60 backdrop-blur-sm border border-emerald-200/50 rounded-2xl shadow-inner">
              <p class="text-xs text-emerald-800 leading-relaxed flex gap-2">
                <Info :size="16" class="text-emerald-500 shrink-0" />
                <span>
                  Ao reduzir <b>{{ formatarMoeda(economiaMensalTotal) }}</b> por mês, você cria uma nova reserva
                  financeira.
                </span>
              </p>
            </div>
          </div>

          <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-emerald-200/40 rounded-full blur-3xl"></div>
          <div class="absolute -left-10 -top-10 w-32 h-32 bg-white rounded-full blur-2xl"></div>
        </div>
      </div>

    </div>
  </div>
</template>

<style scoped>
/* Estilo para o Slider no Chrome/Safari */
input[type='range']::-webkit-slider-thumb {
  appearance: none;
  width: 18px;
  height: 18px;
  background: #10b981;
  border-radius: 50%;
  border: 3px solid white;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
</style>