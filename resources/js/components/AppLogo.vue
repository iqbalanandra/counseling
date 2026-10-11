<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const name = usePage().props.name;
</script>

<template>
    <div
        class="flex aspect-square size-9 items-center justify-center rounded-2xl bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 shadow-sm"
    >
        <span class="text-base font-extrabold tracking-tighter">BK</span>
    </div>
    <div class="ml-2 grid flex-1 text-left">
        <span class="truncate leading-tight font-bold text-sm tracking-tight text-foreground">{{
            name || 'Konseling-Yu'
        }}</span>
        <span class="text-[10px] text-muted-foreground font-medium">SMPN 1 BK Portal</span>
    </div>
</template>
