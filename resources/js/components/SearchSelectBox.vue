<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue';
import {
    Search,
    ChevronDown,
    Check,
    X,
    GraduationCap,
    User,
    UserCheck,
} from '@lucide/vue';

export interface SearchOption {
    id: number | string;
    name: string;
    identifier?: string;
    subtitle?: string;
    badge?: string;
    gender?: 'L' | 'P' | string;
    meta?: Record<string, any>;
}

const props = withDefaults(
    defineProps<{
        modelValue: number | string | null | undefined;
        options: SearchOption[];
        label?: string;
        placeholder?: string;
        required?: boolean;
        disabled?: boolean;
        error?: string;
        iconType?: 'student' | 'teacher' | 'user';
        emptyText?: string;
    }>(),
    {
        placeholder: 'Pilih atau cari...',
        required: false,
        disabled: false,
        iconType: 'student',
        emptyText: 'Tidak ada data ditemukan',
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: number | string | null): void;
    (e: 'change', option: SearchOption | null): void;
}>();

const isOpen = ref(false);
const searchQuery = ref('');
const rootRef = ref<HTMLElement | null>(null);
const searchInputRef = ref<HTMLInputElement | null>(null);
const highlightedIndex = ref(0);

const selectedOption = computed(() => {
    if (
        props.modelValue === null ||
        props.modelValue === undefined ||
        props.modelValue === ''
    ) {
        return null;
    }
    return (
        props.options.find(
            (opt) => String(opt.id) === String(props.modelValue),
        ) || null
    );
});

const filteredOptions = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) {
        return props.options;
    }
    return props.options.filter((opt) => {
        const nameMatch = opt.name.toLowerCase().includes(q);
        const idMatch = opt.identifier
            ? opt.identifier.toLowerCase().includes(q)
            : false;
        const subMatch = opt.subtitle
            ? opt.subtitle.toLowerCase().includes(q)
            : false;
        const badgeMatch = opt.badge
            ? opt.badge.toLowerCase().includes(q)
            : false;
        return nameMatch || idMatch || subMatch || badgeMatch;
    });
});

const toggleDropdown = () => {
    if (props.disabled) return;
    if (isOpen.value) {
        closeDropdown();
    } else {
        openDropdown();
    }
};

const openDropdown = () => {
    if (props.disabled) return;
    isOpen.value = true;
    searchQuery.value = '';
    highlightedIndex.value = 0;
    nextTick(() => {
        searchInputRef.value?.focus();
    });
};

const closeDropdown = () => {
    isOpen.value = false;
    searchQuery.value = '';
};

const selectOption = (option: SearchOption) => {
    emit('update:modelValue', option.id);
    emit('change', option);
    closeDropdown();
};

const clearSelection = () => {
    if (props.disabled) return;
    emit('update:modelValue', null);
    emit('change', null);
    closeDropdown();
};

const handleKeyDown = (e: KeyboardEvent) => {
    if (!isOpen.value) {
        if (e.key === 'Enter' || e.key === 'ArrowDown' || e.key === ' ') {
            e.preventDefault();
            openDropdown();
        }
        return;
    }

    if (e.key === 'Escape') {
        e.preventDefault();
        e.stopPropagation();
        closeDropdown();
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (highlightedIndex.value < filteredOptions.value.length - 1) {
            highlightedIndex.value++;
        }
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (highlightedIndex.value > 0) {
            highlightedIndex.value--;
        }
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (filteredOptions.value[highlightedIndex.value]) {
            selectOption(filteredOptions.value[highlightedIndex.value]);
        }
    }
};

const handleClickOutside = (e: MouseEvent) => {
    if (rootRef.value && !rootRef.value.contains(e.target as Node)) {
        closeDropdown();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

const getInitials = (name: string): string => {
    const parts = name.trim().split(/\s+/);
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.slice(0, 2).toUpperCase();
};
</script>

<template>
    <div
        ref="rootRef"
        class="relative w-full text-left"
        @keydown="handleKeyDown"
    >
        <!-- Label -->
        <label
            v-if="label"
            class="mb-1.5 block flex items-center justify-between text-xs font-semibold text-foreground"
        >
            <span>
                {{ label }}
                <span v-if="required" class="ml-0.5 font-bold text-rose-500"
                    >*</span
                >
            </span>
            <span
                v-if="selectedOption"
                class="text-[11px] font-normal text-muted-foreground"
            >
                Terpilih: 1
            </span>
        </label>

        <!-- Selected Card (If an item is selected and dropdown is not currently open) -->
        <div
            v-if="selectedOption && !isOpen"
            class="group relative flex items-center justify-between gap-3 rounded-2xl border border-indigo-200 bg-indigo-50/40 p-2.5 transition-all hover:border-indigo-300 hover:bg-indigo-50/70 dark:border-indigo-900/50 dark:bg-indigo-950/20 dark:hover:bg-indigo-950/40"
        >
            <div
                class="flex flex-1 cursor-pointer items-center gap-3 overflow-hidden"
                @click="openDropdown"
            >
                <div
                    class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-xs font-bold text-white shadow-xs"
                >
                    <component
                        :is="iconType === 'teacher' ? UserCheck : GraduationCap"
                        class="size-4.5"
                    />
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <p class="truncate text-sm font-bold text-foreground">
                            {{ selectedOption.name }}
                        </p>
                        <span
                            v-if="selectedOption.badge"
                            class="shrink-0 rounded-md bg-indigo-100/90 px-2 py-0.5 text-[10px] font-bold text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300"
                        >
                            {{ selectedOption.badge }}
                        </span>
                    </div>
                    <p class="truncate text-xs text-muted-foreground">
                        {{
                            selectedOption.subtitle ||
                            (selectedOption.identifier
                                ? `ID/NIS: ${selectedOption.identifier}`
                                : 'Telah dipilih')
                        }}
                    </p>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-1">
                <button
                    type="button"
                    @click="openDropdown"
                    class="cursor-pointer rounded-lg px-2 py-1 text-xs font-medium text-indigo-600 transition-colors hover:bg-indigo-100/60 dark:text-indigo-400 dark:hover:bg-indigo-900/40"
                >
                    Ganti
                </button>
                <button
                    type="button"
                    @click.stop="clearSelection"
                    class="cursor-pointer rounded-lg p-1 text-muted-foreground transition-colors hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-950/50 dark:hover:text-rose-400"
                    title="Hapus pilihan"
                >
                    <X class="size-4" />
                </button>
            </div>
        </div>

        <!-- Trigger Input Button (When not selected or dropdown is open) -->
        <div v-else>
            <div
                @click="toggleDropdown"
                :class="[
                    'flex w-full cursor-pointer items-center justify-between rounded-2xl border bg-background px-3.5 py-2.5 text-sm transition-all select-none',
                    isOpen
                        ? 'border-indigo-600 ring-2 ring-indigo-500/20 dark:border-indigo-500'
                        : 'border-border/80 hover:border-neutral-400 dark:hover:border-neutral-600',
                    disabled ? 'cursor-not-allowed bg-muted/30 opacity-60' : '',
                    error ? 'border-rose-500 ring-1 ring-rose-500/30' : '',
                ]"
            >
                <div
                    class="flex items-center gap-2.5 overflow-hidden text-muted-foreground"
                >
                    <component
                        :is="iconType === 'teacher' ? User : GraduationCap"
                        class="size-4.5 shrink-0 text-muted-foreground/70"
                    />
                    <span class="truncate text-sm font-medium">
                        {{ selectedOption ? selectedOption.name : placeholder }}
                    </span>
                </div>
                <ChevronDown
                    :class="[
                        'size-4 shrink-0 text-muted-foreground transition-transform duration-200',
                        isOpen ? 'rotate-180 text-foreground' : '',
                    ]"
                />
            </div>
        </div>

        <!-- Dropdown Menu -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-95 -translate-y-1"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 -translate-y-1"
        >
            <div
                v-if="isOpen"
                class="absolute z-50 mt-1.5 w-full animate-in overflow-hidden rounded-2xl border border-border bg-white shadow-xl fade-in-50 dark:bg-neutral-900"
            >
                <!-- Search Filter Input Box -->
                <div class="border-b border-border/80 bg-muted/30 p-2.5">
                    <div class="relative flex items-center">
                        <Search
                            class="pointer-events-none absolute left-3 size-4 text-muted-foreground"
                        />
                        <input
                            ref="searchInputRef"
                            v-model="searchQuery"
                            type="text"
                            class="w-full rounded-xl border border-border bg-background py-1.5 pr-8 pl-9 text-xs text-foreground placeholder:text-muted-foreground focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 focus:outline-none"
                            :placeholder="`Ketik nama atau ${iconType === 'teacher' ? 'NIP' : 'NIS'}...`"
                            @click.stop
                        />
                        <button
                            v-if="searchQuery"
                            type="button"
                            @click.stop="searchQuery = ''"
                            class="absolute right-2.5 cursor-pointer text-muted-foreground hover:text-foreground"
                        >
                            <X class="size-3.5" />
                        </button>
                    </div>
                </div>

                <!-- Options List -->
                <div
                    class="max-h-60 space-y-0.5 divide-y divide-border/30 overflow-y-auto p-1.5"
                >
                    <div
                        v-for="(option, idx) in filteredOptions"
                        :key="option.id"
                        @click.stop="selectOption(option)"
                        :class="[
                            'group flex cursor-pointer items-center justify-between gap-2.5 rounded-xl px-3 py-2 text-xs transition-colors',
                            String(modelValue) === String(option.id)
                                ? 'bg-indigo-50 font-semibold text-indigo-900 dark:bg-indigo-950/60 dark:text-indigo-200'
                                : idx === highlightedIndex
                                  ? 'bg-muted/80 text-foreground'
                                  : 'text-foreground hover:bg-muted/50',
                        ]"
                    >
                        <div class="flex min-w-0 flex-1 items-center gap-2.5">
                            <!-- Initials Avatar -->
                            <div
                                :class="[
                                    'flex size-8 shrink-0 items-center justify-center rounded-lg text-[11px] font-bold',
                                    String(modelValue) === String(option.id)
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-muted text-foreground group-hover:bg-indigo-100 group-hover:text-indigo-700 dark:group-hover:bg-indigo-950',
                                ]"
                            >
                                {{ getInitials(option.name) }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <p
                                        class="truncate text-xs font-semibold text-foreground"
                                    >
                                        {{ option.name }}
                                    </p>
                                    <span
                                        v-if="option.badge"
                                        class="py-0.2 shrink-0 rounded bg-neutral-100 px-1.5 text-[9px] font-bold text-neutral-700 dark:bg-neutral-800 dark:text-neutral-300"
                                    >
                                        {{ option.badge }}
                                    </span>
                                </div>
                                <p
                                    class="mt-0.5 truncate text-[11px] text-muted-foreground"
                                >
                                    {{
                                        option.subtitle ||
                                        (option.identifier
                                            ? `No: ${option.identifier}`
                                            : '')
                                    }}
                                </p>
                            </div>
                        </div>

                        <!-- Checkmark Icon -->
                        <Check
                            v-if="String(modelValue) === String(option.id)"
                            class="size-4 shrink-0 text-indigo-600 dark:text-indigo-400"
                        />
                    </div>

                    <!-- Empty state -->
                    <div
                        v-if="filteredOptions.length === 0"
                        class="flex flex-col items-center gap-1.5 p-6 text-center text-xs text-muted-foreground"
                    >
                        <Search class="mb-1 size-5 text-muted-foreground/50" />
                        <p class="font-medium text-foreground">
                            {{ emptyText }}
                        </p>
                        <p class="text-[11px] text-muted-foreground">
                            Tidak ditemukan hasil untuk "{{ searchQuery }}"
                        </p>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Error Message -->
        <p v-if="error" class="mt-1 text-xs font-medium text-rose-500">
            {{ error }}
        </p>
    </div>
</template>
