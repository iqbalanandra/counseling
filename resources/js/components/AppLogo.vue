<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const school = computed(() => (page.props as any).school || {});
const portalName = computed(() => school.value.portal_name || (page.props as any).name || 'Konseling-Yu');
const schoolName = computed(() => school.value.school_name || 'SMPN 1 BK Portal');
const logoUrl = computed(() => school.value.logo_url || null);
</script>

<template>
    <div
        v-if="logoUrl"
        class="flex aspect-square size-9 items-center justify-center rounded-2xl overflow-hidden border border-border shadow-sm bg-white shrink-0"
    >
        <img :src="logoUrl" :alt="portalName" class="size-full object-cover" />
    </div>
    <div
        v-else
        class="flex aspect-square size-9 items-center justify-center rounded-2xl bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 shadow-sm shrink-0"
    >
        <span class="text-base font-extrabold tracking-tighter">BK</span>
    </div>
    <div class="ml-2 grid flex-1 text-left min-w-0">
        <span class="truncate leading-tight font-bold text-sm tracking-tight text-foreground">{{
            portalName
        }}</span>
        <span class="truncate text-[10px] text-muted-foreground font-medium">{{ schoolName }}</span>
    </div>
</template>
