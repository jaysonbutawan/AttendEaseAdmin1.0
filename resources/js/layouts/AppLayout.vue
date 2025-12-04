<script setup lang="ts">
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />

    </AppLayout>
    
         <footer
            class="w-full py-2 text-center text-lg text-gray-500 dark:border-gray-700"
        >
            <p class="text-sm text-gray-500">
                <span class="text-2xl font-bold text-black">Attend</span>
                <span class="text-2xl font-bold text-indigo-600">Ease</span>.
                All rights reserved. © {{ new Date().getFullYear() }}
            </p>
        </footer>
</template>
