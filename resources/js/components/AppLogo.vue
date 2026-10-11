<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const school = computed(() => (page.props as any).school || {});
const portalName = computed(
    () =>
        school.value.portal_name || (page.props as any).name || 'Konseling-Yu',
);
const schoolName = computed(
    () => school.value.school_name || 'SMPN 1 BK Portal',
);
const logoUrl = computed(() => school.value.logo_url || null);
</script>

<template>
    <div
        v-if="logoUrl"
        class="flex aspect-square size-9 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-border bg-white shadow-sm"
    >
        <img :src="logoUrl" :alt="portalName" class="size-full object-cover" />
    </div>
    <div
        v-else
        class="flex aspect-square size-9 shrink-0 items-center justify-center rounded-2xl bg-[#18181B] text-white shadow-sm dark:bg-white dark:text-neutral-900"
    >
        <span class="text-base font-extrabold tracking-tighter">BK</span>
    </div>
    <div class="ml-2 grid min-w-0 flex-1 text-left">
        <span
            class="truncate text-sm leading-tight font-bold tracking-tight text-foreground"
            >{{ portalName }}</span
        >
        <span class="truncate text-[10px] font-medium text-muted-foreground">{{
            schoolName
        }}</span>
    </div>
</template>
