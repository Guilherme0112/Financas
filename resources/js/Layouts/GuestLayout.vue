<script setup lang="ts">
</script>

<template>
  <div
    class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-green-50 to-green-100 px-4"
  >
    <!-- CONTEÚDO -->
    <div
    class="w-full"
    >
      <slot />
    </div>

    <!-- FOOTER -->
    <p class="mt-6 text-sm text-gray-500 text-center">
      © {{ new Date().getFullYear() }} Organizei — Controle Financeiro Inteligente
    </p>
  </div>
</template>
