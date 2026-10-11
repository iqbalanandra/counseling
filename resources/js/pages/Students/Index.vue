<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { GraduationCap, Plus, Search, Trash2, Edit3, Eye, AlertTriangle, Star } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AppModal from '@/components/AppModal.vue';
import { usePermission } from '@/composables/usePermission';

interface ClassOption {
    id: number;
    name: string;
}

interface StudentItem {
    id: number;
    nis: string;
    nisn: string | null;
    name: string;
    gender: 'L' | 'P';
    status: 'active' | 'graduated' | 'transferred';
    class_name: string | null;
    class_id: number | null;
    total_violation_points: number;
    total_achievement_points: number;
}

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    students: PaginatedData<StudentItem>;
    filters: { search?: string; class_id?: string };
    classes: ClassOption[];
}>();

const { can } = usePermission();

const searchInput = ref(props.filters.search || '');
const selectedClass = ref(props.filters.class_id || '');

const handleFilter = () => {
    router.get(
        '/students',
        {
            search: searchInput.value || undefined,
            class_id: selectedClass.value || undefined,
        },
        { preserveState: true, replace: true }
    );
};

// Create Student Modal
const showCreateModal = ref(false);
const createForm = useForm({
    nis: '',
    nisn: '',
    name: '',
    gender: 'L' as 'L' | 'P',
    birth_place: '',
    birth_date: '',
    address: '',
    parent_name: '',
    parent_phone: '',
    class_id: '' as string | number,
    status: 'active' as 'active' | 'graduated' | 'transferred',
});

const submitCreate = () => {
    createForm.post('/students', {
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
        },
    });
};

const deleteForm = useForm({});
const confirmDelete = (student: StudentItem) => {
    if (confirm(`Yakin ingin menghapus data siswa "${student.name}"?`)) {
        deleteForm.delete(`/students/${student.id}`);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Data Siswa', href: '/students' }]">
        <Head title="Data Siswa" />

        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-foreground flex items-center gap-2.5">
                        <div class="size-10 rounded-2xl bg-[#EAE6FE] text-purple-700 flex items-center justify-center">
                            <GraduationCap class="size-6" />
                        </div>
                        Data Siswa
                    </h1>
                    <p class="text-sm text-muted-foreground mt-1">
                        Daftar induk siswa, monitoring pelanggaran, prestasi, dan perwalian kelas.
                    </p>
                </div>
                <button
                    v-if="can('student.create')"
                    @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 rounded-full bg-[#18181B] px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-neutral-800 transition-all cursor-pointer dark:bg-white dark:text-neutral-900"
                >
                    <Plus class="size-4" />
                    Tambah Siswa
                </button>
            </div>

            <!-- Filters Bar -->
            <div class="flex flex-col sm:flex-row gap-3 rounded-3xl border border-border/80 bg-white dark:bg-card p-4 shadow-xs">
                <div class="relative flex-1">
                    <Search class="absolute left-3.5 top-3 size-4 text-muted-foreground" />
                    <input
                        v-model="searchInput"
                        @keyup.enter="handleFilter"
                        type="text"
                        placeholder="Cari nama atau NIS/NISN siswa..."
                        class="w-full rounded-2xl border border-border bg-background pl-10 pr-4 py-2.5 text-sm text-foreground focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 transition-all"
                    />
                </div>
                <div class="w-full sm:w-56">
                    <select
                        v-model="selectedClass"
                        @change="handleFilter"
                        class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-sm text-foreground focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 transition-all cursor-pointer"
                    >
                        <option value="">Semua Kelas</option>
                        <option v-for="c in classes" :key="c.id" :value="c.id">
                            {{ c.name }}
                        </option>
                    </select>
                </div>
                <button
                    @click="handleFilter"
                    class="rounded-full border border-border bg-neutral-100/80 dark:bg-neutral-800 px-5 py-2.5 text-sm font-bold text-foreground hover:bg-[#EAE4DC] dark:hover:bg-neutral-700 transition-colors cursor-pointer"
                >
                    Terapkan
                </button>
            </div>

            <!-- Students Table -->
            <div class="rounded-3xl border border-border/80 bg-white dark:bg-card shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#F5EFE6]/70 dark:bg-neutral-900/80 border-b border-border/60">
                            <tr>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Siswa</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Kelas</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider text-center">Poin Pelanggaran</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider text-center">Poin Prestasi</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Status</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr v-for="student in students.data" :key="student.id" class="hover:bg-muted/30 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-full bg-blue-100 dark:bg-blue-950 flex items-center justify-center font-bold text-blue-700 dark:text-blue-300">
                                            {{ student.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <Link :href="`/students/${student.id}`" class="font-semibold text-foreground hover:text-indigo-600 transition-colors">
                                                {{ student.name }}
                                            </Link>
                                            <p class="text-xs text-muted-foreground">
                                                NIS: {{ student.nis }} · {{ student.gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-lg bg-muted px-2.5 py-1 text-xs font-semibold text-foreground">
                                        {{ student.class_name || 'Belum Ada Kelas' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex items-center gap-1 font-semibold text-xs"
                                        :class="student.total_violation_points > 0 ? 'text-red-600 dark:text-red-400' : 'text-muted-foreground'"
                                    >
                                        <AlertTriangle v-if="student.total_violation_points > 0" class="size-3.5" />
                                        {{ student.total_violation_points }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex items-center gap-1 font-semibold text-xs"
                                        :class="student.total_achievement_points > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-muted-foreground'"
                                    >
                                        <Star v-if="student.total_achievement_points > 0" class="size-3.5 fill-amber-500 text-amber-500" />
                                        {{ student.total_achievement_points }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        :class="student.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-950/60 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-950 dark:text-gray-300'"
                                    >
                                        {{ student.status === 'active' ? 'Aktif' : student.status === 'graduated' ? 'Lulus' : 'Pindah' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="`/students/${student.id}`"
                                            class="rounded-lg p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground transition-colors"
                                            title="Profil Siswa"
                                        >
                                            <Eye class="size-4" />
                                        </Link>
                                        <button
                                            v-if="can('student.delete')"
                                            @click="confirmDelete(student)"
                                            class="rounded-lg p-1.5 text-muted-foreground hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/50 dark:hover:text-red-400 transition-colors"
                                            title="Hapus Data Siswa"
                                        >
                                            <Trash2 class="size-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="students.data.length === 0">
                                <td colspan="6" class="py-12 text-center text-muted-foreground text-sm">
                                    Tidak ada data siswa yang sesuai.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="students.total > 15" class="flex items-center justify-between border-t border-border px-5 py-3 text-xs text-muted-foreground">
                    <span>Menampilkan {{ students.data.length }} dari total {{ students.total }} siswa</span>
                    <div class="flex gap-1">
                        <Link
                            v-for="(link, i) in students.links"
                            :key="i"
                            :href="link.url || '#'"
                            :class="[
                                'rounded-lg px-2.5 py-1 transition-colors',
                                link.active ? 'bg-indigo-600 text-white font-bold' : 'hover:bg-muted text-foreground',
                                !link.url && 'opacity-50 pointer-events-none'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>

            <!-- Create Student Modal -->
            <AppModal
                :show="showCreateModal"
                @close="showCreateModal = false"
                title="Tambah Siswa Baru"
                description="Lengkapi profil siswa baru untuk didaftarkan ke dalam sistem kesiswaan."
                max-width="lg"
            >
                <form @submit.prevent="submitCreate" class="space-y-4">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-foreground mb-1.5">
                                NIS <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="createForm.nis"
                                type="text"
                                required
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-foreground mb-1.5">NISN</label>
                            <input
                                v-model="createForm.nisn"
                                type="text"
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1.5">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="createForm.name"
                            type="text"
                            required
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        />
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-foreground mb-1.5">
                                Jenis Kelamin <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="createForm.gender"
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            >
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-foreground mb-1.5">Kelas</label>
                            <select
                                v-model="createForm.class_id"
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            >
                                <option value="">Pilih Kelas...</option>
                                <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-foreground mb-1.5">Tempat Lahir</label>
                            <input
                                v-model="createForm.birth_place"
                                type="text"
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-foreground mb-1.5">Tanggal Lahir</label>
                            <input
                                v-model="createForm.birth_date"
                                type="date"
                                @click="($event.target as HTMLInputElement).showPicker?.()"
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 cursor-pointer"
                            />
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-foreground mb-1.5">Nama Orang Tua / Wali</label>
                            <input
                                v-model="createForm.parent_name"
                                type="text"
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-foreground mb-1.5">No. Kontak Orang Tua</label>
                            <input
                                v-model="createForm.parent_phone"
                                type="text"
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1.5">Alamat Tinggal</label>
                        <textarea
                            v-model="createForm.address"
                            rows="2"
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-border/80">
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="rounded-xl border border-border px-4 py-2 text-sm font-medium text-muted-foreground hover:bg-muted transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="createForm.processing"
                            class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50 transition-colors cursor-pointer shadow-xs"
                        >
                            {{ createForm.processing ? 'Menyimpan...' : 'Simpan Siswa' }}
                        </button>
                    </div>
                </form>
            </AppModal>
        </div>
    </AppLayout>
</template>
