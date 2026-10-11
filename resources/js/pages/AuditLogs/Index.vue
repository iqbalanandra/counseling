<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { History, Search, Filter, ShieldCheck } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface AuditLogItem {
    id: number;
    user_name: string;
    action: string;
    module: string;
    description: string;
    resource_id: string | null;
    ip_address: string | null;
    created_at: string;
}

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    logs: PaginatedData<AuditLogItem>;
    filters: { module?: string; action?: string; search?: string };
}>();

const searchInput = ref(props.filters.search || '');
const selectedModule = ref(props.filters.module || '');
const selectedAction = ref(props.filters.action || '');

const handleFilter = () => {
    router.get(
        '/audit-logs',
        {
            search: searchInput.value || undefined,
            module: selectedModule.value || undefined,
            action: selectedAction.value || undefined,
        },
        { preserveState: true, replace: true },
    );
};

const actionBadgeClass: Record<string, string> = {
    create: 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-300',
    update: 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300',
    delete: 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300',
    approve:
        'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300',
    record: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300',
    login: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Administrasi', href: '#' },
            { title: 'Audit Log Sistem', href: '/audit-logs' },
        ]"
    >
        <Head title="Audit Log Sistem" />

        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div>
                <h1
                    class="flex items-center gap-2 text-2xl font-bold tracking-tight text-foreground"
                >
                    <History
                        class="size-7 text-indigo-600 dark:text-indigo-400"
                    />
                    Audit Log Aktivitas Sistem
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Catatan rekaman aktivitas yang tidak dapat diubah
                    (immutable) untuk audit keamanan dan kepatuhan.
                </p>
            </div>

            <!-- Filters Bar -->
            <div
                class="grid gap-3 rounded-2xl border border-border bg-card p-4 shadow-sm sm:grid-cols-4"
            >
                <div class="relative sm:col-span-2">
                    <Search
                        class="absolute top-2.5 left-3 size-4 text-muted-foreground"
                    />
                    <input
                        v-model="searchInput"
                        @keyup.enter="handleFilter"
                        type="text"
                        placeholder="Cari pelaku atau deskripsi aktivitas..."
                        class="w-full rounded-xl border border-input bg-background py-2 pr-3.5 pl-9 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                    />
                </div>
                <div>
                    <select
                        v-model="selectedModule"
                        @change="handleFilter"
                        class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                    >
                        <option value="">Semua Modul</option>
                        <option value="role">Peran & Hak Akses</option>
                        <option value="user">Pengguna</option>
                        <option value="student">Siswa</option>
                        <option value="counseling">Konseling</option>
                        <option value="violation">Pelanggaran</option>
                        <option value="achievement">Prestasi</option>
                        <option value="auth">Autentikasi</option>
                    </select>
                </div>
                <div>
                    <select
                        v-model="selectedAction"
                        @change="handleFilter"
                        class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                    >
                        <option value="">Semua Aksi</option>
                        <option value="create">Create</option>
                        <option value="update">Update</option>
                        <option value="delete">Delete</option>
                        <option value="approve">Approve</option>
                        <option value="record">Record</option>
                        <option value="login">Login</option>
                    </select>
                </div>
            </div>

            <!-- Audit Logs Table -->
            <div
                class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-border bg-muted/50">
                            <tr>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    Waktu
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    Pelaku
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    Aksi
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    Modul
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    Deskripsi Aktivitas
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    IP Address
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="log in logs.data"
                                :key="log.id"
                                class="transition-colors hover:bg-muted/30"
                            >
                                <td
                                    class="px-5 py-4 font-mono text-xs whitespace-nowrap text-muted-foreground"
                                >
                                    {{ log.created_at }}
                                </td>
                                <td
                                    class="px-5 py-4 font-semibold text-foreground"
                                >
                                    {{ log.user_name }}
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="rounded-full px-2 py-0.5 text-xs font-semibold capitalize"
                                        :class="
                                            actionBadgeClass[log.action] ||
                                            'bg-muted'
                                        "
                                    >
                                        {{ log.action }}
                                    </span>
                                </td>
                                <td
                                    class="px-5 py-4 font-mono text-xs text-muted-foreground uppercase"
                                >
                                    {{ log.module }}
                                </td>
                                <td
                                    class="max-w-md px-5 py-4 text-sm text-foreground"
                                >
                                    {{ log.description }}
                                    <span
                                        v-if="log.resource_id"
                                        class="ml-1 font-mono text-xs text-muted-foreground"
                                        >#{{ log.resource_id }}</span
                                    >
                                </td>
                                <td
                                    class="px-5 py-4 font-mono text-xs text-muted-foreground"
                                >
                                    {{ log.ip_address || '—' }}
                                </td>
                            </tr>

                            <tr v-if="logs.data.length === 0">
                                <td
                                    colspan="6"
                                    class="py-12 text-center text-sm text-muted-foreground"
                                >
                                    Tidak ada catatan audit log.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="logs.total > 25"
                    class="flex items-center justify-between border-t border-border px-5 py-3 text-xs text-muted-foreground"
                >
                    <span
                        >Menampilkan {{ logs.data.length }} dari total
                        {{ logs.total }} catatan</span
                    >
                    <div class="flex gap-1">
                        <Link
                            v-for="(link, i) in logs.links"
                            :key="i"
                            :href="link.url || '#'"
                            :class="[
                                'rounded-lg px-2.5 py-1 transition-colors',
                                link.active
                                    ? 'bg-indigo-600 font-bold text-white'
                                    : 'text-foreground hover:bg-muted',
                                !link.url && 'pointer-events-none opacity-50',
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
