<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ShieldCheck,
    ArrowLeft,
    CheckSquare,
    Square,
    Search,
    Save,
    Lock,
    Info,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { usePermission } from '@/composables/usePermission';

interface PermissionItem {
    id: number;
    name: string;
    display_name: string;
    description: string | null;
}

interface RoleData {
    id: number;
    name: string;
    display_name: string;
    description: string | null;
    is_protected: boolean;
    permission_ids: number[];
}

const props = defineProps<{
    role: RoleData;
    groupedPermissions: Record<string, PermissionItem[]>;
}>();

const { can } = usePermission();

const searchQuery = ref('');
const form = useForm({
    name: props.role.name,
    display_name: props.role.display_name,
    description: props.role.description || '',
    permission_ids: [...props.role.permission_ids],
});

// Permission toggle helpers
const isChecked = (id: number) => form.permission_ids.includes(id);

const togglePermission = (id: number) => {
    const index = form.permission_ids.indexOf(id);
    if (index > -1) {
        form.permission_ids.splice(index, 1);
    } else {
        form.permission_ids.push(id);
    }
};

const isGroupAllChecked = (groupItems: PermissionItem[]) => {
    return groupItems.every((item) => form.permission_ids.includes(item.id));
};

const toggleGroup = (groupItems: PermissionItem[]) => {
    if (isGroupAllChecked(groupItems)) {
        // Uncheck all in group
        const groupIds = groupItems.map((item) => item.id);
        form.permission_ids = form.permission_ids.filter(
            (id) => !groupIds.includes(id),
        );
    } else {
        // Check all in group
        const groupIds = groupItems.map((item) => item.id);
        const combined = new Set([...form.permission_ids, ...groupIds]);
        form.permission_ids = Array.from(combined);
    }
};

// Filtered groups based on search
const filteredGroupedPermissions = computed(() => {
    const q = searchQuery.value.toLowerCase().trim();
    if (!q) return props.groupedPermissions;

    const result: Record<string, PermissionItem[]> = {};

    Object.entries(props.groupedPermissions).forEach(([groupName, items]) => {
        const matchingItems = items.filter(
            (item) =>
                item.name.toLowerCase().includes(q) ||
                item.display_name.toLowerCase().includes(q) ||
                groupName.toLowerCase().includes(q) ||
                (item.description &&
                    item.description.toLowerCase().includes(q)),
        );

        if (matchingItems.length > 0) {
            result[groupName] = matchingItems;
        }
    });

    return result;
});

const submitUpdate = () => {
    form.patch(`/roles/${props.role.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Administrasi', href: '#' },
            { title: 'Peran & Hak Akses', href: '/roles' },
            { title: role.display_name, href: `/roles/${role.id}` },
        ]"
    >
        <Head :title="`Hak Akses — ${role.display_name}`" />

        <div class="flex flex-col gap-6 p-6">
            <!-- Header bar -->
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-3">
                    <Link
                        href="/roles"
                        class="rounded-xl border border-border p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                    >
                        <ArrowLeft class="size-5" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl font-bold text-foreground">
                                {{ role.display_name }}
                            </h1>
                            <span
                                v-if="role.is_protected"
                                class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/40 dark:text-amber-300"
                            >
                                <Lock class="size-3" />
                                Terproteksi
                            </span>
                        </div>
                        <p class="font-mono text-sm text-muted-foreground">
                            @{{ role.name }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        v-if="can('role.update')"
                        @click="submitUpdate"
                        :disabled="form.processing || !form.isDirty"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700 disabled:opacity-50"
                    >
                        <Save class="size-4" />
                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : 'Simpan Perubahan'
                        }}
                    </button>
                </div>
            </div>

            <!-- Role Metadata Form -->
            <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                <h2 class="mb-4 text-base font-semibold text-foreground">
                    Informasi Peran
                </h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label
                            class="mb-1 block text-xs font-medium text-foreground"
                        >
                            Nama Tampilan (Display Name)
                        </label>
                        <input
                            v-model="form.display_name"
                            type="text"
                            class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>
                    <div>
                        <label
                            class="mb-1 block text-xs font-medium text-foreground"
                        >
                            Kode Sistem (Slug)
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            :disabled="role.is_protected"
                            class="w-full rounded-xl border border-input bg-background px-3.5 py-2 font-mono text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none disabled:opacity-60"
                        />
                        <p
                            v-if="role.is_protected"
                            class="mt-1 flex items-center gap-1 text-xs text-muted-foreground"
                        >
                            <Info class="size-3" /> Kode sistem peran bawaan
                            terproteksi dan tidak dapat diubah.
                        </p>
                    </div>
                    <div class="sm:col-span-2">
                        <label
                            class="mb-1 block text-xs font-medium text-foreground"
                            >Deskripsi</label
                        >
                        <input
                            v-model="form.description"
                            type="text"
                            placeholder="Deskripsi singkat mengenai peran ini"
                            class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>
                </div>
            </div>

            <!-- Discord-like Permission Matrix -->
            <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                <div
                    class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h2
                            class="flex items-center gap-2 text-lg font-bold text-foreground"
                        >
                            <ShieldCheck class="size-5 text-indigo-600" />
                            Matriks Hak Akses (Permissions)
                        </h2>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            Centang izin yang diberikan kepada peran ini. Total
                            izin aktif:
                            <span
                                class="font-bold text-indigo-600 dark:text-indigo-400"
                                >{{ form.permission_ids.length }}</span
                            >
                        </p>
                    </div>

                    <!-- Search filter -->
                    <div class="relative w-full sm:w-72">
                        <Search
                            class="absolute top-2.5 left-3 size-4 text-muted-foreground"
                        />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari izin atau modul..."
                            class="w-full rounded-xl border border-input bg-background py-2 pr-3.5 pl-9 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>
                </div>

                <!-- Permission Groups -->
                <div class="space-y-6">
                    <div
                        v-for="(items, groupName) in filteredGroupedPermissions"
                        :key="groupName"
                        class="rounded-xl border border-border/80 bg-muted/20 p-5"
                    >
                        <!-- Group Header with bulk toggle -->
                        <div
                            class="mb-4 flex items-center justify-between border-b border-border/60 pb-3"
                        >
                            <div class="flex items-center gap-2">
                                <span
                                    class="rounded-md bg-indigo-100 px-2 py-0.5 text-xs font-bold tracking-wider text-indigo-800 uppercase dark:bg-indigo-950 dark:text-indigo-300"
                                >
                                    Modul
                                </span>
                                <h3
                                    class="text-sm font-bold text-foreground capitalize"
                                >
                                    {{ groupName }}
                                </h3>
                                <span class="text-xs text-muted-foreground"
                                    >({{ items.length }} izin)</span
                                >
                            </div>

                            <button
                                type="button"
                                @click="toggleGroup(items)"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-indigo-600 transition-colors hover:text-indigo-700 dark:text-indigo-400"
                            >
                                <component
                                    :is="
                                        isGroupAllChecked(items)
                                            ? CheckSquare
                                            : Square
                                    "
                                    class="size-4"
                                />
                                {{
                                    isGroupAllChecked(items)
                                        ? 'Batal Pilih Semua'
                                        : 'Pilih Semua Modul'
                                }}
                            </button>
                        </div>

                        <!-- Group Items Grid -->
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <label
                                v-for="item in items"
                                :key="item.id"
                                class="flex cursor-pointer items-start gap-3 rounded-xl border border-border bg-card p-3.5 transition-all select-none hover:border-indigo-500/50 hover:bg-muted/30"
                                :class="{
                                    'border-indigo-500 bg-indigo-50/20 dark:bg-indigo-950/20':
                                        isChecked(item.id),
                                }"
                            >
                                <input
                                    type="checkbox"
                                    :checked="isChecked(item.id)"
                                    @change="togglePermission(item.id)"
                                    class="mt-0.5 size-4 rounded border-input text-indigo-600 focus:ring-indigo-500"
                                />
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="text-sm leading-tight font-semibold text-foreground"
                                    >
                                        {{ item.display_name }}
                                    </p>
                                    <p
                                        class="mt-0.5 font-mono text-xs text-muted-foreground"
                                    >
                                        {{ item.name }}
                                    </p>
                                    <p
                                        v-if="item.description"
                                        class="mt-1 line-clamp-2 text-xs text-muted-foreground/80"
                                    >
                                        {{ item.description }}
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div
                        v-if="
                            Object.keys(filteredGroupedPermissions).length === 0
                        "
                        class="py-12 text-center text-sm text-muted-foreground"
                    >
                        Tidak ada izin yang cocok dengan kata kunci pencarian
                        "{{ searchQuery }}".
                    </div>
                </div>

                <!-- Sticky bottom bar if changes are unsaved -->
                <div
                    v-if="form.isDirty"
                    class="fixed right-6 bottom-6 z-40 flex animate-in items-center gap-3 rounded-2xl border border-border bg-card/95 px-5 py-3 shadow-2xl backdrop-blur-md duration-200 slide-in-from-bottom"
                >
                    <span
                        class="text-xs font-medium text-amber-600 dark:text-amber-400"
                    >
                        Ada perubahan yang belum disimpan!
                    </span>
                    <button
                        @click="submitUpdate"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow transition-colors hover:bg-indigo-700 disabled:opacity-50"
                    >
                        <Save class="size-3.5" />
                        {{
                            form.processing ? 'Menyimpan...' : 'Simpan Sekarang'
                        }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
