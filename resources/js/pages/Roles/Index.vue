<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ShieldCheck,
    Plus,
    Trash2,
    Key,
    Users,
    Lock,
    Edit3,
} from '@lucide/vue';
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
    if (
        confirm(
            `Yakin ingin menghapus role "${role.display_name}"? Aksi ini tidak dapat dibatalkan.`,
        )
    ) {
        deleteForm.delete(`/roles/${role.id}`);
    }
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Administrasi', href: '#' },
            { title: 'Peran & Hak Akses', href: '/roles' },
        ]"
    >
        <Head title="Manajemen Peran & Hak Akses" />

        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1
                        class="flex items-center gap-2.5 text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl"
                    >
                        <div
                            class="flex size-10 items-center justify-center rounded-2xl bg-[#EAE6FE] text-purple-700"
                        >
                            <ShieldCheck class="size-6" />
                        </div>
                        Peran & Hak Akses (RBAC)
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Kelola peran pengguna dan tentukan hak akses
                        (permissions) secara dinamis & fleksibel.
                    </p>
                </div>
                <button
                    v-if="can('role.create')"
                    @click="showCreateModal = true"
                    class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-[#18181B] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-neutral-800 dark:bg-white dark:text-neutral-900"
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
                    class="group relative flex flex-col justify-between rounded-3xl border border-border/80 bg-white p-6 shadow-xs transition-all duration-300 hover:-translate-y-1 hover:shadow-lg dark:bg-card"
                >
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex size-11 shrink-0 items-center justify-center rounded-2xl bg-[#EAE6FE] text-purple-700 dark:bg-purple-950/60 dark:text-purple-300"
                                >
                                    <ShieldCheck class="size-6" />
                                </div>
                                <div>
                                    <h3
                                        class="text-lg font-extrabold text-foreground transition-colors group-hover:text-purple-700 dark:group-hover:text-purple-400"
                                    >
                                        {{ role.display_name }}
                                    </h3>
                                    <span
                                        class="inline-block font-mono text-xs text-muted-foreground"
                                    >
                                        @{{ role.name }}
                                    </span>
                                </div>
                            </div>
                            <span
                                v-if="role.is_protected"
                                class="inline-flex items-center gap-1 rounded-full border border-amber-200/60 bg-[#FDF1D6] px-2.5 py-0.5 text-xs font-bold text-amber-900 dark:bg-amber-950/50 dark:text-amber-300"
                                title="Role sistem bawaan yang terproteksi"
                            >
                                <Lock class="size-3" />
                                Sistem
                            </span>
                        </div>

                        <p
                            class="mt-4 line-clamp-2 text-sm leading-relaxed text-muted-foreground"
                        >
                            {{
                                role.description ||
                                'Tidak ada deskripsi untuk peran ini.'
                            }}
                        </p>
                    </div>

                    <div
                        class="mt-6 flex items-center justify-between border-t border-border/60 pt-4"
                    >
                        <div
                            class="flex items-center gap-4 text-xs text-muted-foreground"
                        >
                            <span
                                class="flex items-center gap-1"
                                title="Pengguna aktif dengan peran ini"
                            >
                                <Users
                                    class="size-3.5 text-muted-foreground/70"
                                />
                                {{ role.users_count }} Pengguna
                            </span>
                            <span
                                class="flex items-center gap-1"
                                title="Jumlah permissions yang diberikan"
                            >
                                <Key
                                    class="size-3.5 text-muted-foreground/70"
                                />
                                {{ role.permissions_count }} Izin
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <Link
                                :href="`/roles/${role.id}`"
                                class="inline-flex items-center gap-1.5 rounded-full border border-border bg-neutral-100/80 px-3.5 py-1.5 text-xs font-bold text-foreground transition-colors hover:bg-[#EAE4DC] dark:bg-neutral-800 dark:hover:bg-neutral-700"
                            >
                                <Edit3 class="size-3" />
                                Kelola Akses
                            </Link>
                            <button
                                v-if="!role.is_protected && can('role.delete')"
                                @click="confirmDelete(role)"
                                class="cursor-pointer rounded-full p-1.5 text-muted-foreground transition-colors hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-950/40 dark:hover:text-rose-400"
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
                        <label
                            class="mb-1.5 block text-xs font-bold tracking-wider text-foreground uppercase"
                        >
                            Nama Tampilan (Label)
                            <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.display_name"
                            type="text"
                            placeholder="Contoh: Koordinator Ekstrakurikuler"
                            required
                            class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-sm text-foreground transition-all focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 focus:outline-none"
                        />
                        <p
                            v-if="form.errors.display_name"
                            class="mt-1 text-xs text-rose-500"
                        >
                            {{ form.errors.display_name }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="mb-1.5 block text-xs font-bold tracking-wider text-foreground uppercase"
                        >
                            Identitas Unik (Slug)
                            <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Contoh: koordinator_ekskul"
                            required
                            class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 font-mono text-sm text-foreground transition-all focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 focus:outline-none"
                        />
                        <p
                            v-if="form.errors.name"
                            class="mt-1 text-xs text-rose-500"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="mb-1.5 block text-xs font-bold tracking-wider text-foreground uppercase"
                        >
                            Deskripsi Peran
                        </label>
                        <textarea
                            v-model="form.description"
                            rows="3"
                            placeholder="Jelaskan ruang lingkup tugas peran ini..."
                            class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-sm text-foreground transition-all focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 focus:outline-none"
                        ></textarea>
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 border-t border-border/80 pt-4"
                    >
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="cursor-pointer rounded-full border border-border px-5 py-2.5 text-sm font-semibold text-muted-foreground transition-colors hover:bg-[#EAE4DC] dark:hover:bg-neutral-800"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="cursor-pointer rounded-full bg-[#18181B] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-colors hover:bg-neutral-800 disabled:opacity-50 dark:bg-white dark:text-neutral-900"
                        >
                            {{
                                form.processing
                                    ? 'Menyimpan...'
                                    : 'Simpan Peran'
                            }}
                        </button>
                    </div>
                </form>
            </AppModal>
        </div>
    </AppLayout>
</template>
