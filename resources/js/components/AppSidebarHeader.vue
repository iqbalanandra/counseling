<script setup lang="ts">
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import {
    Bell,
    CheckCheck,
    ExternalLink,
    Sun,
    Moon,
    Monitor,
} from '@lucide/vue';
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
    router.post(
        `/notifications/${notification.id}/read`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                showNotifications.value = false;
            },
        },
    );
};

const markAllRead = () => {
    router.post(
        '/notifications/read-all',
        {},
        {
            preserveScroll: true,
        },
    );
};
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between border-b border-[#EBE2D4] bg-[#FCFAF7]/85 px-6 backdrop-blur-md transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-5 dark:border-neutral-800 dark:bg-[#141816]/85"
    >
        <div class="flex items-center gap-3">
            <SidebarTrigger
                class="-ml-1 rounded-xl p-2 transition-colors hover:bg-neutral-200/60 dark:hover:bg-neutral-800"
            />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <!-- Right Side Actions: Theme Switcher & Notifications -->
        <div class="flex items-center gap-2.5 sm:gap-3">
            <!-- Theme Switcher Pill -->
            <div
                class="inline-flex items-center rounded-full border border-[#DFD5C6] bg-[#EAE2D5]/70 p-0.5 shadow-2xs dark:border-neutral-700/60 dark:bg-neutral-800/80"
            >
                <button
                    @click="updateAppearance('light')"
                    type="button"
                    class="flex size-7 cursor-pointer items-center justify-center rounded-full transition-all duration-200"
                    :class="
                        appearance === 'light'
                            ? 'bg-white text-amber-600 shadow-xs ring-1 ring-black/5 dark:bg-neutral-700 dark:text-amber-400'
                            : 'text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100'
                    "
                    title="Mode Terang (Light Mode)"
                >
                    <Sun class="size-3.5" />
                    <span class="sr-only">Mode Terang</span>
                </button>
                <button
                    @click="updateAppearance('dark')"
                    type="button"
                    class="flex size-7 cursor-pointer items-center justify-center rounded-full transition-all duration-200"
                    :class="
                        appearance === 'dark'
                            ? 'bg-[#18181B] text-amber-300 shadow-xs ring-1 ring-white/10 dark:bg-neutral-700 dark:text-amber-300'
                            : 'text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100'
                    "
                    title="Mode Gelap (Dark Mode)"
                >
                    <Moon class="size-3.5" />
                    <span class="sr-only">Mode Gelap</span>
                </button>
                <button
                    @click="updateAppearance('system')"
                    type="button"
                    class="flex size-7 cursor-pointer items-center justify-center rounded-full transition-all duration-200"
                    :class="
                        appearance === 'system'
                            ? 'bg-white text-foreground shadow-xs ring-1 ring-black/5 dark:bg-neutral-700 dark:text-neutral-200'
                            : 'text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100'
                    "
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
                    class="relative flex size-8.5 items-center justify-center rounded-full border border-[#DFD5C6] bg-white text-foreground/80 shadow-2xs transition-all hover:bg-neutral-100 dark:border-neutral-700/60 dark:bg-card dark:hover:bg-neutral-800"
                    title="Notifikasi"
                >
                    <Bell class="size-4" />
                    <span
                        v-if="
                            (page.props.auth as any)
                                ?.unread_notifications_count > 0
                        "
                        class="absolute -top-0.5 -right-0.5 flex size-2.5"
                    >
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex size-2.5 rounded-full bg-rose-500"
                        ></span>
                    </span>
                </button>

                <!-- Dropdown panel -->
                <div
                    v-if="showNotifications"
                    class="absolute top-12 right-0 z-50 w-80 animate-in rounded-2xl border border-border bg-card p-4 shadow-2xl duration-150 fade-in slide-in-from-top-2 sm:w-96"
                >
                    <div
                        class="mb-2 flex items-center justify-between border-b border-border pb-3"
                    >
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-foreground">
                                Notifikasi
                            </h4>
                            <span
                                v-if="
                                    (page.props.auth as any)
                                        ?.unread_notifications_count > 0
                                "
                                class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300"
                            >
                                {{
                                    (page.props.auth as any)
                                        ?.unread_notifications_count
                                }}
                                Baru
                            </span>
                        </div>

                        <button
                            v-if="
                                (page.props.auth as any)
                                    ?.unread_notifications_count > 0
                            "
                            @click="markAllRead"
                            class="flex items-center gap-1 text-xs text-muted-foreground transition-colors hover:text-indigo-600"
                        >
                            <CheckCheck class="size-3.5" />
                            Tandai semua dibaca
                        </button>
                    </div>

                    <div
                        class="max-h-80 divide-y divide-border/60 overflow-y-auto"
                    >
                        <div
                            v-for="notif in (page.props.auth as any)
                                ?.notifications || []"
                            :key="notif.id"
                            @click="markRead(notif)"
                            class="flex cursor-pointer items-start gap-3 rounded-xl p-3 transition-colors hover:bg-muted/40"
                            :class="{
                                'bg-indigo-50/40 font-medium dark:bg-indigo-950/20':
                                    !notif.is_read,
                            }"
                        >
                            <div
                                class="mt-1.5 size-2 shrink-0 rounded-full"
                                :class="
                                    notif.is_read
                                        ? 'bg-transparent'
                                        : 'bg-indigo-600'
                                "
                            ></div>
                            <div class="min-w-0 flex-1">
                                <p
                                    class="text-xs leading-tight font-semibold text-foreground"
                                >
                                    {{ notif.title }}
                                </p>
                                <p
                                    class="mt-0.5 line-clamp-2 text-xs leading-relaxed text-muted-foreground"
                                >
                                    {{ notif.message }}
                                </p>
                                <span
                                    class="mt-1 block text-[10px] text-muted-foreground/70"
                                    >{{ notif.created_at }}</span
                                >
                            </div>
                        </div>

                        <div
                            v-if="
                                !(page.props.auth as any)?.notifications?.length
                            "
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
