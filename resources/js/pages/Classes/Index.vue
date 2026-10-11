<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import {
    School,
    Plus,
    Search,
    Trash2,
    Edit3,
    Users,
    UserCheck,
    GraduationCap,
    Sparkles,
    AlertCircle,
    CheckCircle2,
    Layers,
    BookOpen,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AppModal from '@/components/AppModal.vue';
import { usePermission } from '@/composables/usePermission';

interface TeacherItem {
    id: number;
    name: string;
    nip: string | null;
    position?: string | null;
}

interface AcademicYearItem {
    id: number;
    name: string;
    semester: string;
    is_active: boolean;
}

interface ClassItem {
    id: number;
    name: string;
    grade: string;
    students_count: number;
    academic_year_id: number | null;
    academic_year_name?: string | null;
    teachers: TeacherItem[];
    teacher_ids: number[];
}

const props = defineProps<{
    classes: ClassItem[];
    teachers: TeacherItem[];
    academicYears: AcademicYearItem[];
}>();

const { can } = usePermission();

// Search and Filter
const searchQuery = ref('');
const selectedGradeTier = ref<'all' | 'sd' | 'smp' | 'sma' | 'other'>('all');

const isSd = (grade: string) => ['1', '2', '3', '4', '5', '6'].includes(grade);
const isSmp = (grade: string) => ['7', '8', '9'].includes(grade);
const isSma = (grade: string) =>
    ['10', '11', '12', 'X', 'XI', 'XII'].includes(grade.toUpperCase());

const getTierLabel = (grade: string) => {
    if (isSd(grade))
        return {
            label: 'SD',
            color: 'bg-red-50 text-red-700 dark:bg-red-950/60 dark:text-red-300 border-red-200 dark:border-red-900/50',
        };
    if (isSmp(grade))
        return {
            label: 'SMP',
            color: 'bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300 border-sky-200 dark:border-sky-900/50',
        };
    if (isSma(grade))
        return {
            label: 'SMA/SMK',
            color: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-900/50',
        };
    return {
        label: 'Kustom',
        color: 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border-purple-200 dark:border-purple-900/50',
    };
};

const filteredClasses = computed(() => {
    return props.classes.filter((c) => {
        const matchesSearch =
            searchQuery.value.trim() === '' ||
            c.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            c.grade.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            c.teachers.some((t) =>
                t.name.toLowerCase().includes(searchQuery.value.toLowerCase()),
            );

        if (!matchesSearch) return false;

        if (selectedGradeTier.value === 'all') return true;
        if (selectedGradeTier.value === 'sd') return isSd(c.grade);
        if (selectedGradeTier.value === 'smp') return isSmp(c.grade);
        if (selectedGradeTier.value === 'sma') return isSma(c.grade);
        if (selectedGradeTier.value === 'other')
            return !isSd(c.grade) && !isSmp(c.grade) && !isSma(c.grade);

        return true;
    });
});

// Stats
const totalStudents = computed(() =>
    props.classes.reduce((acc, c) => acc + (c.students_count || 0), 0),
);
const classesWithHomeroom = computed(
    () => props.classes.filter((c) => c.teachers.length > 0).length,
);

// Preset grades for quick selection
const tierPresets = [
    { tier: 'SD', grades: ['1', '2', '3', '4', '5', '6'] },
    { tier: 'SMP', grades: ['7', '8', '9'] },
    { tier: 'SMA/SMK', grades: ['10', '11', '12'] },
];

// Create Class Modal
const showCreateModal = ref(false);
const createForm = useForm({
    name: '',
    grade: '7',
    academic_year_id:
        props.academicYears.find((y) => y.is_active)?.id ||
        (props.academicYears[0]?.id ?? null),
    teacher_ids: [] as number[],
});

const selectGradePreset = (formRef: any, grade: string) => {
    formRef.grade = grade;
    if (!formRef.name || formRef.name.trim() === '') {
        formRef.name = `${grade}A`;
    }
};

const submitCreate = () => {
    createForm.post('/classes', {
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
            createForm.name = '';
            createForm.grade = '7';
            createForm.academic_year_id =
                props.academicYears.find((y) => y.is_active)?.id ||
                (props.academicYears[0]?.id ?? null);
            createForm.teacher_ids = [];
        },
    });
};

// Edit Class Modal
const showEditModal = ref(false);
const editingClassId = ref<number | null>(null);
const editForm = useForm({
    name: '',
    grade: '',
    academic_year_id: null as number | null,
    teacher_ids: [] as number[],
});

const openEditModal = (c: ClassItem) => {
    editingClassId.value = c.id;
    editForm.name = c.name;
    editForm.grade = c.grade;
    editForm.academic_year_id = c.academic_year_id;
    editForm.teacher_ids = [...c.teacher_ids];
    showEditModal.value = true;
};

const submitEdit = () => {
    if (!editingClassId.value) return;
    editForm.patch(`/classes/${editingClassId.value}`, {
        onSuccess: () => {
            showEditModal.value = false;
            editingClassId.value = null;
        },
    });
};

// Delete Class
const deleteForm = useForm({});
const confirmDelete = (c: ClassItem) => {
    if (c.students_count > 0) {
        alert(
            `Kelas "${c.name}" tidak dapat dihapus karena masih memiliki ${c.students_count} siswa terdaftar. Pindahkan siswa terlebih dahulu.`,
        );
        return;
    }
    if (confirm(`Yakin ingin menghapus kelas "${c.name}"?`)) {
        deleteForm.delete(`/classes/${c.id}`);
    }
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Administrasi', href: '#' },
            { title: 'Manajemen Kelas', href: '/classes' },
        ]"
    >
        <Head title="Manajemen Kelas & Wali Kelas" />

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
                            <School class="size-6" />
                        </div>
                        Manajemen Kelas
                    </h1>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Konfigurasi jenjang sekolah (SD, SMP, SMA/SMK) dan
                        penetapan wali kelas (1 guru dapat me-walikan beberapa
                        kelas).
                    </p>
                </div>
                <button
                    v-if="can('student.create') || can('user.view')"
                    @click="showCreateModal = true"
                    class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-[#18181B] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-neutral-800 dark:bg-white dark:text-neutral-900"
                >
                    <Plus class="size-4" />
                    Tambah Kelas Baru
                </button>
            </div>

            <!-- Stats Bar -->
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div
                    class="rounded-3xl border border-border/80 bg-white p-5 shadow-xs dark:bg-card"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-10 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400"
                        >
                            <Layers class="size-5" />
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold text-muted-foreground"
                            >
                                Total Kelas
                            </p>
                            <p class="text-2xl font-black text-foreground">
                                {{ classes.length }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-3xl border border-border/80 bg-white p-5 shadow-xs dark:bg-card"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400"
                        >
                            <GraduationCap class="size-5" />
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold text-muted-foreground"
                            >
                                Siswa Terdaftar
                            </p>
                            <p class="text-2xl font-black text-foreground">
                                {{ totalStudents }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-3xl border border-border/80 bg-white p-5 shadow-xs dark:bg-card"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-10 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/60 dark:text-amber-400"
                        >
                            <UserCheck class="size-5" />
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold text-muted-foreground"
                            >
                                Dengan Wali Kelas
                            </p>
                            <p class="text-2xl font-black text-foreground">
                                {{ classesWithHomeroom }}
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-3xl border border-border/80 bg-white p-5 shadow-xs dark:bg-card"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-10 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400"
                        >
                            <AlertCircle class="size-5" />
                        </div>
                        <div>
                            <p
                                class="text-xs font-semibold text-muted-foreground"
                            >
                                Belum Berwali
                            </p>
                            <p class="text-2xl font-black text-foreground">
                                {{ classes.length - classesWithHomeroom }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Bar -->
            <div
                class="flex flex-col gap-3 rounded-3xl border border-border/80 bg-white p-4 shadow-xs sm:flex-row dark:bg-card"
            >
                <div class="relative flex-1">
                    <Search
                        class="absolute top-3 left-3.5 size-4 text-muted-foreground"
                    />
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari nama kelas atau wali kelas..."
                        class="w-full rounded-2xl border border-border bg-background py-2.5 pr-4 pl-10 text-sm text-foreground transition-all focus:border-neutral-900 focus:ring-1 focus:ring-neutral-900 focus:outline-none"
                    />
                </div>
                <div
                    class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0"
                >
                    <button
                        @click="selectedGradeTier = 'all'"
                        class="cursor-pointer rounded-full px-3.5 py-1.5 text-xs font-bold transition-colors"
                        :class="
                            selectedGradeTier === 'all'
                                ? 'bg-[#18181B] text-white dark:bg-white dark:text-neutral-900'
                                : 'bg-muted/50 text-muted-foreground hover:bg-muted'
                        "
                    >
                        Semua Jenjang
                    </button>
                    <button
                        @click="selectedGradeTier = 'sd'"
                        class="cursor-pointer rounded-full px-3.5 py-1.5 text-xs font-bold transition-colors"
                        :class="
                            selectedGradeTier === 'sd'
                                ? 'bg-red-600 text-white'
                                : 'bg-red-50 text-red-700 hover:bg-red-100 dark:bg-red-950/40 dark:text-red-300'
                        "
                    >
                        SD (1-6)
                    </button>
                    <button
                        @click="selectedGradeTier = 'smp'"
                        class="cursor-pointer rounded-full px-3.5 py-1.5 text-xs font-bold transition-colors"
                        :class="
                            selectedGradeTier === 'smp'
                                ? 'bg-sky-600 text-white'
                                : 'bg-sky-50 text-sky-700 hover:bg-sky-100 dark:bg-sky-950/40 dark:text-sky-300'
                        "
                    >
                        SMP (7-9)
                    </button>
                    <button
                        @click="selectedGradeTier = 'sma'"
                        class="cursor-pointer rounded-full px-3.5 py-1.5 text-xs font-bold transition-colors"
                        :class="
                            selectedGradeTier === 'sma'
                                ? 'bg-emerald-600 text-white'
                                : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300'
                        "
                    >
                        SMA/SMK (10-12)
                    </button>
                    <button
                        @click="selectedGradeTier = 'other'"
                        class="cursor-pointer rounded-full px-3.5 py-1.5 text-xs font-bold transition-colors"
                        :class="
                            selectedGradeTier === 'other'
                                ? 'bg-purple-600 text-white'
                                : 'bg-purple-50 text-purple-700 hover:bg-purple-100 dark:bg-purple-950/40 dark:text-purple-300'
                        "
                    >
                        Lainnya
                    </button>
                </div>
            </div>

            <!-- Classes Table -->
            <div
                class="overflow-hidden rounded-3xl border border-border/80 bg-white shadow-xs dark:bg-card"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="border-b border-border/60 bg-[#F5EFE6]/70 dark:bg-neutral-900/80"
                        >
                            <tr>
                                <th
                                    class="px-5 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase"
                                >
                                    Nama Kelas
                                </th>
                                <th
                                    class="px-5 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase"
                                >
                                    Jenjang & Tingkat
                                </th>
                                <th
                                    class="px-5 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase"
                                >
                                    Tahun Ajaran
                                </th>
                                <th
                                    class="px-5 py-4 text-xs font-bold tracking-wider text-muted-foreground uppercase"
                                >
                                    Wali Kelas
                                </th>
                                <th
                                    class="px-5 py-4 text-center text-xs font-bold tracking-wider text-muted-foreground uppercase"
                                >
                                    Jumlah Siswa
                                </th>
                                <th
                                    class="px-5 py-4 text-right text-xs font-bold tracking-wider text-muted-foreground uppercase"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr
                                v-for="c in filteredClasses"
                                :key="c.id"
                                class="transition-colors hover:bg-muted/30"
                            >
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex size-10 items-center justify-center rounded-2xl bg-indigo-100 font-bold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300"
                                        >
                                            {{ c.name.substring(0, 3) }}
                                        </div>
                                        <div>
                                            <p
                                                class="text-base font-bold text-foreground"
                                            >
                                                {{ c.name }}
                                            </p>
                                            <p
                                                class="text-xs text-muted-foreground"
                                            >
                                                ID Kelas: #{{ c.id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="inline-flex items-center rounded-md border px-2 py-0.5 text-xs font-semibold"
                                            :class="getTierLabel(c.grade).color"
                                        >
                                            {{ getTierLabel(c.grade).label }}
                                        </span>
                                        <span
                                            class="text-xs font-medium text-muted-foreground"
                                        >
                                            Tingkat {{ c.grade }}
                                        </span>
                                    </div>
                                </td>

                                <td
                                    class="px-5 py-4 text-xs font-medium text-foreground"
                                >
                                    {{ c.academic_year_name || '—' }}
                                </td>

                                <td class="px-5 py-4">
                                    <div
                                        v-if="c.teachers.length > 0"
                                        class="flex flex-wrap gap-1.5"
                                    >
                                        <span
                                            v-for="t in c.teachers"
                                            :key="t.id"
                                            class="inline-flex items-center gap-1 rounded-lg border border-amber-200/80 bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-800 dark:border-amber-800/40 dark:bg-amber-950/50 dark:text-amber-300"
                                        >
                                            <UserCheck
                                                class="size-3 shrink-0"
                                            />
                                            <span>{{ t.name }}</span>
                                            <span
                                                v-if="t.nip"
                                                class="font-mono text-[10px] opacity-75"
                                                >({{ t.nip }})</span
                                            >
                                        </span>
                                    </div>
                                    <span
                                        v-else
                                        class="flex items-center gap-1 text-xs text-rose-500 italic"
                                    >
                                        <AlertCircle class="size-3" />
                                        Belum ada wali kelas
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-foreground dark:bg-slate-800"
                                    >
                                        <Users class="size-3" />
                                        {{ c.students_count }} Siswa
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <div
                                        class="flex items-center justify-end gap-1.5"
                                    >
                                        <button
                                            v-if="
                                                can('student.update') ||
                                                can('user.view')
                                            "
                                            @click="openEditModal(c)"
                                            class="cursor-pointer rounded-xl p-2 text-muted-foreground transition-colors hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-950/50 dark:hover:text-indigo-400"
                                            title="Ubah Kelas"
                                        >
                                            <Edit3 class="size-4" />
                                        </button>
                                        <button
                                            v-if="
                                                can('student.delete') ||
                                                can('user.view')
                                            "
                                            @click="confirmDelete(c)"
                                            class="cursor-pointer rounded-xl p-2 text-muted-foreground transition-colors hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/50 dark:hover:text-rose-400"
                                            title="Hapus Kelas"
                                        >
                                            <Trash2 class="size-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="filteredClasses.length === 0">
                                <td
                                    colspan="6"
                                    class="py-12 text-center text-sm text-muted-foreground"
                                >
                                    Tidak ada kelas yang sesuai dengan pencarian
                                    atau filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Create Class Modal -->
            <AppModal
                :show="showCreateModal"
                @close="showCreateModal = false"
                title="Tambah Kelas Baru"
                description="Tambahkan kelas untuk jenjang SD, SMP, SMA/SMK atau jenjang kustom lainnya."
                max-width="lg"
            >
                <form @submit.prevent="submitCreate" class="space-y-4">
                    <!-- Jenjang Quick Selector -->
                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-foreground"
                        >
                            Pilih Preset Jenjang / Tingkat
                        </label>
                        <div
                            class="space-y-2 rounded-2xl border border-border bg-muted/30 p-3"
                        >
                            <div
                                v-for="preset in tierPresets"
                                :key="preset.tier"
                                class="flex items-center gap-2"
                            >
                                <span
                                    class="w-16 text-xs font-bold text-muted-foreground"
                                    >{{ preset.tier }}:</span
                                >
                                <div class="flex flex-wrap gap-1.5">
                                    <button
                                        v-for="gr in preset.grades"
                                        :key="gr"
                                        type="button"
                                        @click="
                                            selectGradePreset(createForm, gr)
                                        "
                                        class="cursor-pointer rounded-lg border px-2.5 py-1 text-xs font-bold transition-all"
                                        :class="
                                            createForm.grade === gr
                                                ? 'border-indigo-600 bg-indigo-600 text-white shadow-xs'
                                                : 'border-border bg-card text-foreground hover:bg-muted'
                                        "
                                    >
                                        Kelas {{ gr }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold text-foreground"
                            >
                                Tingkat / Grade
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="createForm.grade"
                                type="text"
                                required
                                placeholder="Contoh: 1, 7, 10, X, TK-A"
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                            />
                            <p class="mt-1 text-[11px] text-muted-foreground">
                                Dapat berupa angka tingkat atau teks kustom.
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold text-foreground"
                            >
                                Nama Kelas <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="createForm.name"
                                type="text"
                                required
                                placeholder="Contoh: 1A, 7B, X MIPA 1"
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-foreground"
                        >
                            Tahun Ajaran
                        </label>
                        <select
                            v-model="createForm.academic_year_id"
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        >
                            <option :value="null">
                                Pilih Tahun Ajaran (Opsional)
                            </option>
                            <option
                                v-for="ay in academicYears"
                                :key="ay.id"
                                :value="ay.id"
                            >
                                {{ ay.name }} (Semester {{ ay.semester }})
                                {{ ay.is_active ? '— Aktif' : '' }}
                            </option>
                        </select>
                    </div>

                    <!-- Homeroom Teacher Assignment -->
                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label
                                class="block text-xs font-semibold text-foreground"
                            >
                                Pilih Guru Wali Kelas
                            </label>
                            <span class="text-[11px] text-muted-foreground">
                                1 Guru dapat me-walikan beberapa kelas
                            </span>
                        </div>
                        <div
                            class="grid max-h-40 grid-cols-1 gap-2 overflow-y-auto rounded-2xl border border-border bg-muted/20 p-2 sm:grid-cols-2"
                        >
                            <label
                                v-for="t in teachers"
                                :key="t.id"
                                class="flex cursor-pointer items-center gap-2 rounded-xl border border-border bg-card p-2 transition-colors hover:bg-muted/40"
                                :class="{
                                    'border-amber-500 bg-amber-50 dark:bg-amber-950/40':
                                        createForm.teacher_ids.includes(t.id),
                                }"
                            >
                                <input
                                    type="checkbox"
                                    :value="t.id"
                                    v-model="createForm.teacher_ids"
                                    class="size-3.5 rounded border-input text-amber-600 focus:ring-amber-500"
                                />
                                <div class="truncate">
                                    <p
                                        class="truncate text-xs font-bold text-foreground"
                                    >
                                        {{ t.name }}
                                    </p>
                                    <p
                                        class="text-[10px] text-muted-foreground"
                                    >
                                        {{ t.nip || t.position || 'Guru' }}
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 border-t border-border/80 pt-4"
                    >
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="cursor-pointer rounded-xl border border-border px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="createForm.processing"
                            class="cursor-pointer rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-xs transition-colors hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{
                                createForm.processing
                                    ? 'Menyimpan...'
                                    : 'Simpan Kelas'
                            }}
                        </button>
                    </div>
                </form>
            </AppModal>

            <!-- Edit Class Modal -->
            <AppModal
                :show="showEditModal"
                @close="showEditModal = false"
                title="Ubah Data Kelas"
                description="Perbarui informasi kelas, tingkat, atau penetapan guru wali kelas."
                max-width="lg"
            >
                <form @submit.prevent="submitEdit" class="space-y-4">
                    <!-- Jenjang Quick Selector -->
                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-foreground"
                        >
                            Pilih Preset Jenjang / Tingkat
                        </label>
                        <div
                            class="space-y-2 rounded-2xl border border-border bg-muted/30 p-3"
                        >
                            <div
                                v-for="preset in tierPresets"
                                :key="preset.tier"
                                class="flex items-center gap-2"
                            >
                                <span
                                    class="w-16 text-xs font-bold text-muted-foreground"
                                    >{{ preset.tier }}:</span
                                >
                                <div class="flex flex-wrap gap-1.5">
                                    <button
                                        v-for="gr in preset.grades"
                                        :key="gr"
                                        type="button"
                                        @click="selectGradePreset(editForm, gr)"
                                        class="cursor-pointer rounded-lg border px-2.5 py-1 text-xs font-bold transition-all"
                                        :class="
                                            editForm.grade === gr
                                                ? 'border-indigo-600 bg-indigo-600 text-white shadow-xs'
                                                : 'border-border bg-card text-foreground hover:bg-muted'
                                        "
                                    >
                                        Kelas {{ gr }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold text-foreground"
                            >
                                Tingkat / Grade
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="editForm.grade"
                                type="text"
                                required
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                            />
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold text-foreground"
                            >
                                Nama Kelas <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="editForm.name"
                                type="text"
                                required
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-foreground"
                        >
                            Tahun Ajaran
                        </label>
                        <select
                            v-model="editForm.academic_year_id"
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        >
                            <option :value="null">
                                Pilih Tahun Ajaran (Opsional)
                            </option>
                            <option
                                v-for="ay in academicYears"
                                :key="ay.id"
                                :value="ay.id"
                            >
                                {{ ay.name }} (Semester {{ ay.semester }})
                                {{ ay.is_active ? '— Aktif' : '' }}
                            </option>
                        </select>
                    </div>

                    <!-- Homeroom Teacher Assignment -->
                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label
                                class="block text-xs font-semibold text-foreground"
                            >
                                Pilih Guru Wali Kelas
                            </label>
                            <span class="text-[11px] text-muted-foreground">
                                1 Guru dapat me-walikan beberapa kelas
                            </span>
                        </div>
                        <div
                            class="grid max-h-40 grid-cols-1 gap-2 overflow-y-auto rounded-2xl border border-border bg-muted/20 p-2 sm:grid-cols-2"
                        >
                            <label
                                v-for="t in teachers"
                                :key="t.id"
                                class="flex cursor-pointer items-center gap-2 rounded-xl border border-border bg-card p-2 transition-colors hover:bg-muted/40"
                                :class="{
                                    'border-amber-500 bg-amber-50 dark:bg-amber-950/40':
                                        editForm.teacher_ids.includes(t.id),
                                }"
                            >
                                <input
                                    type="checkbox"
                                    :value="t.id"
                                    v-model="editForm.teacher_ids"
                                    class="size-3.5 rounded border-input text-amber-600 focus:ring-amber-500"
                                />
                                <div class="truncate">
                                    <p
                                        class="truncate text-xs font-bold text-foreground"
                                    >
                                        {{ t.name }}
                                    </p>
                                    <p
                                        class="text-[10px] text-muted-foreground"
                                    >
                                        {{ t.nip || t.position || 'Guru' }}
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 border-t border-border/80 pt-4"
                    >
                        <button
                            type="button"
                            @click="showEditModal = false"
                            class="cursor-pointer rounded-xl border border-border px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="editForm.processing"
                            class="cursor-pointer rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-xs transition-colors hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{
                                editForm.processing
                                    ? 'Menyimpan...'
                                    : 'Perbarui Kelas'
                            }}
                        </button>
                    </div>
                </form>
            </AppModal>
        </div>
    </AppLayout>
</template>
