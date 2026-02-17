<script setup lang="ts">
import { HelpCircle } from 'lucide-vue-next';

const props = defineProps<{
    message: string
}>();

</script>

<template>
    <div class="absolute group">
        <HelpCircle class="text-gray-400 cursor-pointer" :size="16" />

        <div class="absolute left-1/2 -translate-x-1/2 top-6 w-64 p-3 
                    bg-gray-800 text-white text-xs rounded-lg shadow-lg 
                    opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50">
            {{ message }}
        </div>
    </div>
</template>