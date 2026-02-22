<template>
    <div class="flex items-center justify-between py-3 bg-white rounded-md">
        <div class="flex items-center space-x-3">
            <Icon :class="iconColor">
                <component :is="icon" :size="24" />
            </Icon>
            <div>
                <h1 class="text-lg font-semibold text-gray-800">
                    {{ title }}
                </h1>
                <p class="text-sm text-gray-500">{{ description }}</p>
            </div>
        </div>
        <slot></slot>
    </div>
</template>

<script setup lang="ts">
import Icon from '@/Components/Icon.vue';

defineProps<{
    icon: any;
    title: string;
    description: string;
    iconColor?: string;
}>();
</script>
