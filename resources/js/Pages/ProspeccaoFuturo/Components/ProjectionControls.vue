<script setup>
const props = defineProps({
    months: {
        type: Number,
        required: true
    },
    optimizationLevel: {
        type: Number,
        required: true
    }
});

const emit = defineEmits(['update:months', 'update:optimizationLevel']);

// Funções auxiliares para mudar os valores via botões
const adjustMonths = (amount) => {
    const newValue = Math.min(Math.max(props.months + amount, 0), 60);
    emit('update:months', newValue);
};

const adjustOptimization = (amount) => {
    const newValue = Math.min(Math.max(props.optimizationLevel + amount, 0), 50);
    emit('update:optimizationLevel', newValue);
};
</script>

<template>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
            <label class="block text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">
                Período de Projeção: 
                <span class="text-emerald-600">{{ months === 0 ? 'Saldo Atual' : `${months} meses` }}</span>
            </label>
            
            <div class="flex items-center gap-4">
                <button 
                    @click="adjustMonths(-1)"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-emerald-100 hover:text-emerald-600 transition-colors font-bold"
                >
                    −
                </button>

                <input
                    type="range"
                    :value="months"
                    @input="$emit('update:months', parseInt($event.target.value))"
                    min="0"
                    max="60"
                    class="flex-1 accent-emerald-600 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                />

                <button 
                    @click="adjustMonths(1)"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-emerald-100 hover:text-emerald-600 transition-colors font-bold"
                >
                    +
                </button>
            </div>

            <div class="flex justify-between text-[10px] text-gray-400 mt-2 font-bold px-12">
                <span>0 MÊS</span><span>2 ANOS</span><span>5 ANOS</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
            <label class="block text-sm font-bold text-gray-700 mb-4 uppercase tracking-wider">
                Otimizar Gastos: <span class="text-emerald-600">{{ optimizationLevel }}%</span>
            </label>

            <div class="flex items-center gap-4">
                <button 
                    @click="adjustOptimization(-5)"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-emerald-100 hover:text-emerald-600 transition-colors font-bold"
                >
                    −
                </button>

                <input
                    type="range"
                    :value="optimizationLevel"
                    @input="$emit('update:optimizationLevel', parseInt($event.target.value))"
                    min="0"
                    max="50"
                    step="5"
                    class="flex-1 accent-emerald-600 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                />

                <button 
                    @click="adjustOptimization(5)"
                    class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-emerald-100 hover:text-emerald-600 transition-colors font-bold"
                >
                    +
                </button>
            </div>
            <p class="text-[10px] text-gray-400 mt-2 uppercase text-center">Redução forçada de despesas</p>
        </div>
    </div>
</template>

<style scoped>
/* Remove o outline azul chato ao clicar nos botões no mobile */
button:focus {
    outline: none;
}
</style>