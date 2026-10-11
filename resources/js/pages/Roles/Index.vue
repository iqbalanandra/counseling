<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ShieldCheck, Plus, Trash2, Key, Users, Lock, Edit3 } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AppModal from '@/components/AppModal.vue';
import { usePermission } from '@/composables/usePermission';

interface RoleItem {
    id: number;
    name: string;
    display_name: string;
    description: string | null;
    is_protected: boolean;
    users_count: number;
    permissions_count: number;
}

defineProps<{
    roles: RoleItem[];
}>();

const { can } = usePermission();

const showCreateModal = ref(false);
const form = useForm({
    name: '',
    display_name: '',
    description: '',
});

const submitCreate = () => {
    form.post('/roles', {
        onSuccess: () => {
            showCreateModal.value = false;
            form.reset();
        },
    });
};

const deleteForm = useForm({});
const confirmDelete = (role: RoleItem) => {
    if (confirm(`Yakin ingin menghapus role "${role.display_name}"? Aksi ini tidak dapat dibatalkan.`)) {
        deleteForm.delete(`/roles/${role.id}`);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Administrasi', href: '#' }, { title: 'Peran & Hak Akses', href: '/roles' }]">
        <Head title="Manajemen Peran & Hak Akses" />

        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-foreground flex items-center gap-2.5">
                        <div class="size-10 rounded-2xl bg-[#EAE6FE] text-purple-700 flex items-center justify-center">
                            <ShieldCheck class="size-6" />
                        </div>
                        Peran & Hak Akses (RBAC)
                    </h1>
                    <p class="text-sm text-muted-foreground mt-1">
                        Kelola peran pengguna dan tentukan hak akses (permissions) secara dinamis & fleksibel.
                    </p>
                </div>
                <button
                    v-if="can('role.create')"
                    @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 rounded-full bg-[#18181B] px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-neutral-800 transition-all cursor-pointer dark:bg-white dark:text-neutral-900"
                >
                    <Plus class="size-4" />
                    Tambah Peran Baru
                </button>
            </div>

            <!-- Role Cards Grid -->
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="role in roles"
                    :key="role.id"
                    class="group relative flex flex-col justify-between rounded-3xl border border-border/80 bg-white dark:bg-card p-6 shadow-xs hover:shadow-lg transition-all duration-300 hover:-translate-y-1"
                >
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="size-11 rounded-2xl bg-[#EAE6FE] dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 flex items-center justify-center shrink-0">
                                    <ShieldCheck class="size-6" />
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-foreground text-lg group-hover:text-purple-700 dark:group-hover:text-purple-400 transition-colors">
                                        {{ role.display_name }}
                                    </h3>
                                    <span class="inline-block text-xs font-mono text-muted-foreground">
                                        @{{ role.name }}
                                    </span>
                                </div>
                            </div>
                            <span
                                v-if="role.is_protected"
                                class="inline-flex items-center gap-1 rounded-full bg-[#FDF1D6] px-2.5 py-0.5 text-xs font-bold text-amber-900 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200/60"
                                title="Role sistem bawaan yang terproteksi"
                            >
                                <Lock class="size-3" />
                                Sistem
                            </span>
                        </div>

                        <p class="mt-4 text-sm text-muted-foreground line-clamp-2 leading-relaxed">
                            {{ role.description || 'Tidak ada deskripsi untuk peran ini.' }}
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-border/60 flex items-center justify-between">
                        <div class="flex items-center gap-4 text-xs text-muted-foreground">
                            <span class="flex items-center gap-1" title="Pengguna aktif dengan peran ini">
                                <Users class="size-3.5 text-muted-foreground/70" />
                                {{ role.users_count }} Pengguna
                            </span>
                            <span class="flex items-center gap-1" title="Jumlah permissions yang diberikan">
                                <Key class="size-3.5 text-muted-foreground/70" />
                                {{ role.permissions_count }} Izin
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <Link
                                :href="`/roles/${role.id}`"
                                class="inline-flex items-center gap-1.5 rounded-full border border-border bg-neutral-100/80 dark:bg-neutral-800 px-3.5 py-1.5 text-xs font-bold text-foreground hover:bg-[#EAE4DC] dark:hover:bg-neutral-700 transition-colors"
                            >
                                <Edit3 class="size-3" />
                                Kelola Akses
                            </Link>
                            <button
                                v-if="!role.is_protected && can('role.delete')"
                                @click="confirmDelete(role)"
                                class="rounded-full p-1.5 text-muted-foreground hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-950/40 dark:hover:text-rose-400 transition-colors cursor-pointer"
                                title="Hapus Peran"
                            >
                                <Trash2 class="size-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create Modal -->
            <AppModal
                :show="showCreateModal"
                @close="showCreateModal = false"
                title="Tambah Peran Baru"
                description="Masukkan nama teknis unik dan nama tampilan untuk peran baru."
                max-width="md"
            >
                <form @submit.prevent="submitCreate" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-foreground uppercase tracking-wider mb-1.5">
                            Nama Tampilan (Label) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.display_name"
                            type="text"
                            placeholder="Contoh: Koordinator Ekstrakurikuler"
                            required
                            class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-sm text-foreground focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 transition-all"
                        />
                        <p v-if="form.errors.display_name" class="mt-1 text-xs text-rose-500">{{ form.errors.display_name }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-foreground uppercase tracking-wider mb-1.5">
                            Identitas Unik (Slug) <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Contoh: koordinator_ekskul"
                            required
                            class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-sm font-mono text-foreground focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 transition-all"
                        />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-rose-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-foreground uppercase tracking-wider mb-1.5">
                            Deskripsi Peran
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            placeholder="Jelaskan ruang lingkup tugas peran ini..."
                            class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-sm text-foreground focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 transition-all"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-border/80">
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="rounded-full border border-border px-5 py-2.5 text-sm font-semibold text-muted-foreground hover:bg-[#EAE4DC] dark:hover:bg-neutral-800 transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-full bg-[#18181B] px-5 py-2.5 text-sm font-bold text-white hover:bg-neutral-800 disabled:opacity-50 transition-colors cursor-pointer dark:bg-white dark:text-neutral-900 shadow-sm"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Peran' }}
                        </button>
                    </div>
                </form>
            </AppModal>
        </div>
    </AppLayout>
</template>
