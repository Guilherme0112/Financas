<script setup>
defineProps({
    balance: {
        type: Number,
        required: true
    }
});
</script>

<template>
    <div class="bg-emerald-600 p-6 rounded-2xl shadow-lg text-white">
        <p class="text-xs font-bold opacity-80 uppercase">Saldo Final Projetado</p>
        <h2 class="text-3xl font-black mt-1">R$ {{ balance.toLocaleString() }}</h2>
        <p class="text-[10px] mt-2 opacity-90 italic">* Incluindo saldo atual e economia</p>
    </div>
</template>
