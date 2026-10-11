<script setup lang="ts">
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { Bell, CheckCheck, ExternalLink, Sun, Moon, Monitor } from '@lucide/vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { useAppearance } from '@/composables/useAppearance';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const showNotifications = ref(false);
const { appearance, updateAppearance } = useAppearance();

const markRead = (notification: any) => {
    router.post(`/notifications/${notification.id}/read`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            showNotifications.value = false;
        },
    });
};

const markAllRead = () => {
    router.post('/notifications/read-all', {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between border-b border-[#EBE2D4] dark:border-neutral-800 bg-[#FCFAF7]/85 dark:bg-[#141816]/85 backdrop-blur-md px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-5"
    >
        <div class="flex items-center gap-3">
            <SidebarTrigger class="-ml-1 rounded-xl p-2 hover:bg-neutral-200/60 dark:hover:bg-neutral-800 transition-colors" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <!-- Right Side Actions: Theme Switcher & Notifications -->
        <div class="flex items-center gap-2.5 sm:gap-3">
            <!-- Theme Switcher Pill -->
            <div class="inline-flex items-center rounded-full bg-[#EAE2D5]/70 dark:bg-neutral-800/80 p-0.5 border border-[#DFD5C6] dark:border-neutral-700/60 shadow-2xs">
                <button
                    @click="updateAppearance('light')"
                    type="button"
                    class="flex size-7 items-center justify-center rounded-full transition-all duration-200 cursor-pointer"
                    :class="appearance === 'light'
                        ? 'bg-white text-amber-600 shadow-xs ring-1 ring-black/5 dark:bg-neutral-700 dark:text-amber-400'
                        : 'text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100'"
                    title="Mode Terang (Light Mode)"
                >
                    <Sun class="size-3.5" />
                    <span class="sr-only">Mode Terang</span>
                </button>
                <button
                    @click="updateAppearance('dark')"
                    type="button"
                    class="flex size-7 items-center justify-center rounded-full transition-all duration-200 cursor-pointer"
                    :class="appearance === 'dark'
                        ? 'bg-[#18181B] text-amber-300 shadow-xs ring-1 ring-white/10 dark:bg-neutral-700 dark:text-amber-300'
                        : 'text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100'"
                    title="Mode Gelap (Dark Mode)"
                >
                    <Moon class="size-3.5" />
                    <span class="sr-only">Mode Gelap</span>
                </button>
                <button
                    @click="updateAppearance('system')"
                    type="button"
                    class="flex size-7 items-center justify-center rounded-full transition-all duration-200 cursor-pointer"
                    :class="appearance === 'system'
                        ? 'bg-white text-foreground shadow-xs ring-1 ring-black/5 dark:bg-neutral-700 dark:text-neutral-200'
                        : 'text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100'"
                    title="Sesuai Sistem"
                >
                    <Monitor class="size-3.5" />
                    <span class="sr-only">Sistem</span>
                </button>
            </div>

            <!-- Notification Bell Dropdown -->
            <div class="relative flex items-center">
                <button
                    @click="showNotifications = !showNotifications"
                    class="relative rounded-full size-8.5 flex items-center justify-center border border-[#DFD5C6] dark:border-neutral-700/60 bg-white dark:bg-card text-foreground/80 hover:bg-neutral-100 dark:hover:bg-neutral-800 shadow-2xs transition-all"
                    title="Notifikasi"
                >
                <Bell class="size-4" />
                <span
                    v-if="(page.props.auth as any)?.unread_notifications_count > 0"
                    class="absolute -top-0.5 -right-0.5 flex size-2.5"
                >
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
                    <span class="relative inline-flex size-2.5 rounded-full bg-rose-500"></span>
                </span>
            </button>

            <!-- Dropdown panel -->
            <div
                v-if="showNotifications"
                class="absolute right-0 top-12 z-50 w-80 sm:w-96 rounded-2xl border border-border bg-card p-4 shadow-2xl animate-in fade-in slide-in-from-top-2 duration-150"
            >
                <div class="flex items-center justify-between border-b border-border pb-3 mb-2">
                    <div class="flex items-center gap-2">
                        <h4 class="font-bold text-foreground text-sm">Notifikasi</h4>
                        <span
                            v-if="(page.props.auth as any)?.unread_notifications_count > 0"
                            class="rounded-full bg-indigo-100 dark:bg-indigo-950 px-2 py-0.5 text-xs font-semibold text-indigo-700 dark:text-indigo-300"
                        >
                            {{ (page.props.auth as any)?.unread_notifications_count }} Baru
                        </span>
                    </div>

                    <button
                        v-if="(page.props.auth as any)?.unread_notifications_count > 0"
                        @click="markAllRead"
                        class="text-xs text-muted-foreground hover:text-indigo-600 transition-colors flex items-center gap-1"
                    >
                        <CheckCheck class="size-3.5" />
                        Tandai semua dibaca
                    </button>
                </div>

                <div class="max-h-80 overflow-y-auto divide-y divide-border/60">
                    <div
                        v-for="notif in ((page.props.auth as any)?.notifications || [])"
                        :key="notif.id"
                        @click="markRead(notif)"
                        class="p-3 cursor-pointer hover:bg-muted/40 transition-colors rounded-xl flex items-start gap-3"
                        :class="{ 'bg-indigo-50/40 dark:bg-indigo-950/20 font-medium': !notif.is_read }"
                    >
                        <div class="size-2 rounded-full mt-1.5 shrink-0" :class="notif.is_read ? 'bg-transparent' : 'bg-indigo-600'"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-foreground leading-tight">{{ notif.title }}</p>
                            <p class="text-xs text-muted-foreground mt-0.5 line-clamp-2 leading-relaxed">{{ notif.message }}</p>
                            <span class="text-[10px] text-muted-foreground/70 mt-1 block">{{ notif.created_at }}</span>
                        </div>
                    </div>

                    <div
                        v-if="!((page.props.auth as any)?.notifications?.length)"
                        class="py-8 text-center text-xs text-muted-foreground"
                    >
                        Tidak ada notifikasi saat ini.
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
</template>

