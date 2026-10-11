<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Megaphone, Plus, Search, Trash2, Edit3, AlertTriangle, ShieldAlert } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AppModal from '@/components/AppModal.vue';
import SearchSelectBox from '@/components/SearchSelectBox.vue';
import { usePermission } from '@/composables/usePermission';

interface CategoryOption {
    id: number;
    name: string;
    points: number;
}

interface ClassOption {
    id: number;
    name: string;
}

interface StudentOption {
    id: number;
    name: string;
    nis: string;
    class_name: string;
}

interface ViolationItem {
    id: number;
    student_name: string;
    student_nis: string;
    class_name: string;
    category: string;
    violation_date: string;
    points: number;
    status: string;
    recorded_by: string;
}

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    violations: PaginatedData<ViolationItem>;
    filters: { search?: string; class_id?: string; category_id?: string };
    categories: CategoryOption[];
    classes: ClassOption[];
    students?: StudentOption[];
}>();

const { can } = usePermission();

const searchInput = ref(props.filters.search || '');
const selectedClass = ref(props.filters.class_id || '');
const selectedCategory = ref(props.filters.category_id || '');

const handleFilter = () => {
    router.get(
        '/violations',
        {
            search: searchInput.value || undefined,
            class_id: selectedClass.value || undefined,
            category_id: selectedCategory.value || undefined,
        },
        { preserveState: true, replace: true }
    );
};

const getTodayDate = () => {
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
};

const getYesterdayDate = () => {
    const d = new Date();
    d.setDate(d.getDate() - 1);
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
};

const todayDate = computed(() => getTodayDate());

// Create Violation Modal
const showCreateModal = ref(false);
const createForm = useForm({
    student_id: '' as string | number,
    violation_category_id: '' as string | number,
    violation_date: getTodayDate(),
    points: 5,
    description: '',
    sanction_given: '',
});

const onCategoryChange = () => {
    const cat = props.categories.find((c) => c.id === Number(createForm.violation_category_id));
    if (cat) {
        createForm.points = cat.points;
    }
};

const studentSelectOptions = computed(() =>
    (props.students || []).map((s) => ({
        id: s.id,
        name: s.name,
        identifier: s.nis,
        subtitle: `NIS: ${s.nis} · ${s.class_name}`,
        badge: s.class_name,
    }))
);

const submitCreate = () => {
    createForm.post('/violations', {
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
        },
    });
};

const deleteForm = useForm({});
const confirmDelete = (v: ViolationItem) => {
    if (confirm(`Hapus catatan pelanggaran untuk ${v.student_name}?`)) {
        deleteForm.delete(`/violations/${v.id}`);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Kedisiplinan', href: '#' }, { title: 'Pelanggaran Siswa', href: '/violations' }]">
        <Head title="Catatan Pelanggaran Siswa" />

        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-foreground flex items-center gap-2.5">
                        <div class="size-10 rounded-2xl bg-[#FDE5E5] text-rose-700 flex items-center justify-center">
                            <Megaphone class="size-6" />
                        </div>
                        Pelanggaran & Kedisiplinan
                    </h1>
                    <p class="text-sm text-muted-foreground mt-1">
                        Pencatatan pelanggaran tata tertib, akumulasi poin, dan tindak lanjut sanksi pembinaan.
                    </p>
                </div>
                <button
                    v-if="can('violation.create')"
                    @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 rounded-full bg-[#18181B] px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-neutral-800 transition-all cursor-pointer dark:bg-white dark:text-neutral-900"
                >
                    <Plus class="size-4" />
                    Catat Pelanggaran
                </button>
            </div>

            <!-- Filters Bar -->
            <div class="grid gap-3 sm:grid-cols-4 rounded-3xl border border-border/80 bg-white dark:bg-card p-4 shadow-xs">
                <div class="relative sm:col-span-2">
                    <Search class="absolute left-3.5 top-3 size-4 text-muted-foreground" />
                    <input
                        v-model="searchInput"
                        @keyup.enter="handleFilter"
                        type="text"
                        placeholder="Cari siswa atau keterangan..."
                        class="w-full rounded-2xl border border-border bg-background pl-10 pr-4 py-2.5 text-sm text-foreground focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 transition-all"
                    />
                </div>
                <div>
                    <select
                        v-model="selectedClass"
                        @change="handleFilter"
                        class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-sm text-foreground focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 transition-all cursor-pointer"
                    >
                        <option value="">Semua Kelas</option>
                        <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>
                <div>
                    <select
                        v-model="selectedCategory"
                        @change="handleFilter"
                        class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-sm text-foreground focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 transition-all cursor-pointer"
                    >
                        <option value="">Semua Kategori</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>
            </div>

            <!-- Violations Table -->
            <div class="rounded-3xl border border-border/80 bg-white dark:bg-card shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#F5EFE6]/70 dark:bg-neutral-900/80 border-b border-border/60">
                            <tr>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Tanggal</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Siswa & Kelas</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Kategori Pelanggaran</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider text-center">Poin</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Status</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr v-for="v in violations.data" :key="v.id" class="hover:bg-muted/30 transition-colors">
                                <td class="px-5 py-4 text-xs text-muted-foreground whitespace-nowrap">{{ v.violation_date }}</td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-foreground">{{ v.student_name }}</p>
                                    <p class="text-xs text-muted-foreground">NIS: {{ v.student_nis }} · Kelas {{ v.class_name }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-lg bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-950 dark:text-red-300">
                                        {{ v.category }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-red-600 dark:text-red-400">
                                    +{{ v.points }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full bg-muted px-2.5 py-0.5 text-xs font-medium capitalize">
                                        {{ v.status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button
                                        v-if="can('violation.delete')"
                                        @click="confirmDelete(v)"
                                        class="rounded-lg p-1.5 text-muted-foreground hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950 dark:hover:text-red-400 transition-colors"
                                        title="Hapus Catatan"
                                    >
                                        <Trash2 class="size-4" />
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="violations.data.length === 0">
                                <td colspan="6" class="py-12 text-center text-muted-foreground text-sm">
                                    Tidak ada catatan pelanggaran.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="violations.total > 15" class="flex items-center justify-between border-t border-border px-5 py-3 text-xs text-muted-foreground">
                    <span>Menampilkan {{ violations.data.length }} dari total {{ violations.total }} pelanggaran</span>
                    <div class="flex gap-1">
                        <Link
                            v-for="(link, i) in violations.links"
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

            <!-- Create Modal -->
            <AppModal
                :show="showCreateModal"
                @close="showCreateModal = false"
                title="Catat Pelanggaran Siswa"
                description="Pilih siswa dan masukkan rincian pelanggaran tata tertib serta sanksi pembinaan."
                max-width="lg"
            >
                <form @submit.prevent="submitCreate" class="space-y-4">
                    <div>
                        <SearchSelectBox
                            v-model="createForm.student_id"
                            label="Pilih Siswa yang Melanggar"
                            placeholder="Cari nama siswa, NIS, atau kelas..."
                            :options="studentSelectOptions"
                            icon-type="student"
                            required
                            :error="createForm.errors.student_id"
                        />
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-foreground mb-1.5">
                                Kategori Pelanggaran <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="createForm.violation_category_id"
                                @change="onCategoryChange"
                                required
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"
                            >
                                <option value="">Pilih Kategori...</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }} ({{ cat.points }} poin)
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-foreground mb-1.5">Poin</label>
                            <input
                                v-model="createForm.points"
                                type="number"
                                required
                                min="1"
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1.5">Tanggal Kejadian <span class="text-rose-500">*</span></label>
                        <input
                            v-model="createForm.violation_date"
                            type="date"
                            :max="todayDate"
                            required
                            @click="($event.target as HTMLInputElement).showPicker?.()"
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500 cursor-pointer"
                        />
                        <div class="mt-1.5 flex items-center gap-1.5">
                            <span class="text-[11px] text-muted-foreground mr-1">Pilih Cepat:</span>
                            <button
                                type="button"
                                @click="createForm.violation_date = getTodayDate()"
                                class="rounded-lg border border-border bg-muted/40 px-2.5 py-0.5 text-[11px] font-medium text-foreground hover:bg-red-50 hover:border-red-300 hover:text-red-600 dark:hover:bg-red-950/40 dark:hover:text-red-400 transition-colors cursor-pointer"
                            >
                                Hari Ini
                            </button>
                            <button
                                type="button"
                                @click="createForm.violation_date = getYesterdayDate()"
                                class="rounded-lg border border-border bg-muted/40 px-2.5 py-0.5 text-[11px] font-medium text-foreground hover:bg-red-50 hover:border-red-300 hover:text-red-600 dark:hover:bg-red-950/40 dark:hover:text-red-400 transition-colors cursor-pointer"
                            >
                                Kemarin
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1.5">Keterangan / Kronologi</label>
                        <textarea
                            v-model="createForm.description"
                            rows="2"
                            placeholder="Jelaskan secara singkat kronologi pelanggaran..."
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1.5">Sanksi Pembinaan yang Diberikan</label>
                        <input
                            v-model="createForm.sanction_given"
                            type="text"
                            placeholder="Contoh: Teguran lisan, membersihkan lingkungan sekolah"
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"
                        />
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
                            :disabled="createForm.processing || !createForm.student_id"
                            class="rounded-xl bg-red-600 px-5 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:opacity-50 transition-colors cursor-pointer shadow-xs"
                        >
                            {{ createForm.processing ? 'Menyimpan...' : 'Simpan Pelanggaran' }}
                        </button>
                    </div>
                </form>
            </AppModal>
        </div>
    </AppLayout>
</template>
