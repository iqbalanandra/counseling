<script setup lang="ts">
import { ref, watch, nextTick } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ChevronRight } from '@lucide/vue';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
    useSidebar,
} from '@/components/ui/sidebar';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

const props = defineProps<{
    items: NavItem[];
}>();

const { state, setOpen, isMobile, setOpenMobile } = useSidebar();
const { isCurrentUrl, isCurrentOrParentUrl } = useCurrentUrl();

const isItemActive = (item: NavItem): boolean => {
    if (item.href !== '#' && item.href !== '/admin' && isCurrentOrParentUrl(item.href)) {
        return true;
    }
    if (item.children && item.children.length > 0) {
        return item.children.some((child) => 
            child.href !== '#' && isCurrentOrParentUrl(child.href)
        );
    }
    return false;
};

// Track open state of each collapsible accordion
const openMap = ref<Record<string, boolean>>({});

// Initialize open states: active items are opened by default
const initOpenStates = () => {
    props.items.forEach((item) => {
        if (item.children && item.children.length > 0) {
            if (openMap.value[item.title] === undefined) {
                openMap.value[item.title] = isItemActive(item);
            }
        }
    });
};

initOpenStates();

// Keep active items open if routes change
watch(
    () => props.items,
    () => {
        props.items.forEach((item) => {
            if (item.children && item.children.length > 0 && isItemActive(item)) {
                openMap.value[item.title] = true;
            }
        });
    },
    { deep: true }
);

// When a menu with children is clicked while the sidebar is collapsed:
// 1. Expand the sidebar
// 2. Open this item's accordion submenu
const handleTriggerClick = (item: NavItem) => {
    if (state.value === 'collapsed') {
        if (isMobile.value) {
            setOpenMobile(true);
        } else {
            setOpen(true);
        }
        nextTick(() => {
            openMap.value[item.title] = true;
        });
    }
};
</script>

<template>
    <SidebarGroup class="px-2.5 py-1">
        <SidebarGroupLabel class="text-[11px] font-bold uppercase tracking-wider text-muted-foreground/70 px-2 mb-1">
            Menu Utama
        </SidebarGroupLabel>
        <SidebarMenu class="gap-1.5">
            <template v-for="item in items" :key="item.title">
                <!-- Collapsible item with children -->
                <Collapsible
                    v-if="item.children && item.children.length > 0"
                    as-child
                    v-model:open="openMap[item.title]"
                    class="group/collapsible"
                >
                    <SidebarMenuItem>
                        <CollapsibleTrigger as-child @click="handleTriggerClick(item)">
                            <SidebarMenuButton
                                :tooltip="item.title"
                                :is-active="isItemActive(item)"
                                class="h-10 rounded-2xl px-3 transition-all font-medium data-[active=true]:bg-[#18181B] data-[active=true]:text-white dark:data-[active=true]:bg-white dark:data-[active=true]:text-neutral-900 data-[active=true]:shadow-sm hover:bg-[#EAE4DC] dark:hover:bg-neutral-800 cursor-pointer"
                            >
                                <component :is="item.icon" v-if="item.icon" class="size-4 shrink-0" />
                                <span class="text-sm">{{ item.title }}</span>
                                <ChevronRight class="ml-auto size-4 transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                            </SidebarMenuButton>
                        </CollapsibleTrigger>
                        <CollapsibleContent>
                            <SidebarMenuSub class="border-l-2 border-border/70 ml-4 pl-2 my-1 space-y-1">
                                <SidebarMenuSubItem
                                    v-for="subItem in item.children"
                                    :key="subItem.title"
                                >
                                    <SidebarMenuSubButton
                                        as-child
                                        :is-active="isCurrentOrParentUrl(subItem.href)"
                                        class="h-8.5 rounded-xl px-2.5 text-xs font-medium transition-all data-[active=true]:bg-[#18181B] data-[active=true]:text-white dark:data-[active=true]:bg-white dark:data-[active=true]:text-neutral-900 data-[active=true]:shadow-sm hover:bg-[#EAE4DC] dark:hover:bg-neutral-800"
                                    >
                                        <Link :href="subItem.href" class="flex items-center gap-2">
                                            <component :is="subItem.icon" v-if="subItem.icon" class="size-3.5" />
                                            <span>{{ subItem.title }}</span>
                                        </Link>
                                    </SidebarMenuSubButton>
                                </SidebarMenuSubItem>
                            </SidebarMenuSub>
                        </CollapsibleContent>
                    </SidebarMenuItem>
                </Collapsible>

                <!-- Regular single menu item -->
                <SidebarMenuItem v-else>
                    <SidebarMenuButton
                        as-child
                        :is-active="isCurrentUrl(item.href)"
                        :tooltip="item.title"
                        class="h-10 rounded-2xl px-3 transition-all font-medium data-[active=true]:bg-[#18181B] data-[active=true]:text-white dark:data-[active=true]:bg-white dark:data-[active=true]:text-neutral-900 data-[active=true]:shadow-sm hover:bg-[#EAE4DC] dark:hover:bg-neutral-800"
                    >
                        <Link :href="item.href" class="flex items-center gap-2.5">
                            <component :is="item.icon" v-if="item.icon" class="size-4 shrink-0" />
                            <span class="text-sm">{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
