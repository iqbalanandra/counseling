<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    Users,
    Plus,
    Search,
    Trash2,
    Edit3,
    Shield,
    Key,
    Eye,
    UserCheck,
    UserX,
    Sparkles,
    GraduationCap,
    School,
    Check,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AppModal from '@/components/AppModal.vue';
import { usePermission } from '@/composables/usePermission';

interface RoleOption {
    id: number;
    name: string;
    display_name: string;
}

interface ClassOption {
    id: number;
    name: string;
    grade?: string;
    academic_year?: string;
}

interface UserItem {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    identifier_number: string | null;
    status: 'active' | 'inactive';
    is_system_user: boolean;
    roles: RoleOption[];
    class_name: string | null;
    homeroom_classes?: Array<{ id: number; name: string }>;
}

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    users: PaginatedData<UserItem>;
    filters: { search?: string; role?: string };
    roles: RoleOption[];
    classes: ClassOption[];
}>();

const { can } = usePermission();

const searchInput = ref(props.filters.search || '');
const selectedRole = ref(props.filters.role || '');

const handleFilter = () => {
    router.get(
        '/users',
        {
            search: searchInput.value || undefined,
            role: selectedRole.value || undefined,
        },
        { preserveState: true, replace: true }
    );
};

// Create User Modal
const showCreateModal = ref(false);
const createForm = useForm({
    name: '',
    email: '',
    phone: '',
    identifier_number: '',
    gender: 'L' as 'L' | 'P',
    password: '',
    password_confirmation: '',
    status: 'active' as 'active' | 'inactive',
    role_ids: [] as number[],
    class_ids: [] as number[],
    student_class_id: null as number | null,
});

const isWaliKelasSelected = computed(() => {
    const waliRole = props.roles.find((r) => r.name === 'Wali Kelas');
    return waliRole ? createForm.role_ids.includes(waliRole.id) : false;
});

const isSiswaSelected = computed(() => {
    const siswaRole = props.roles.find((r) => r.name === 'Siswa');
    return siswaRole ? createForm.role_ids.includes(siswaRole.id) : false;
});

const modalClassTier = ref<'all' | 'sd' | 'smp' | 'sma' | 'other'>('all');
const modalClassSearch = ref('');

const isSd = (grade?: string) => !!grade && ['1', '2', '3', '4', '5', '6'].includes(grade);
const isSmp = (grade?: string) => !!grade && ['7', '8', '9'].includes(grade);
const isSma = (grade?: string) => !!grade && ['10', '11', '12', 'X', 'XI', 'XII'].includes(grade.toUpperCase());

const filteredModalClasses = computed(() => {
    return props.classes.filter((c) => {
        const matchesSearch = !modalClassSearch.value || c.name.toLowerCase().includes(modalClassSearch.value.toLowerCase());
        if (!matchesSearch) return false;

        if (modalClassTier.value === 'all') return true;
        if (modalClassTier.value === 'sd') return isSd(c.grade);
        if (modalClassTier.value === 'smp') return isSmp(c.grade);
        if (modalClassTier.value === 'sma') return isSma(c.grade);
        if (modalClassTier.value === 'other') return !isSd(c.grade) && !isSmp(c.grade) && !isSma(c.grade);
        return true;
    });
});

const toggleSelectAllFilteredClasses = () => {
    const currentFilteredIds = filteredModalClasses.value.map((c) => c.id);
    const allSelected = currentFilteredIds.length > 0 && currentFilteredIds.every((id) => createForm.class_ids.includes(id));

    if (allSelected) {
        createForm.class_ids = createForm.class_ids.filter((id) => !currentFilteredIds.includes(id));
    } else {
        const toAdd = currentFilteredIds.filter((id) => !createForm.class_ids.includes(id));
        createForm.class_ids = [...createForm.class_ids, ...toAdd];
    }
};

const generateRandomPassword = () => {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
    let pwd = 'K!1';
    for (let i = 0; i < 7; i++) {
        pwd += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    createForm.password = pwd;
    createForm.password_confirmation = pwd;
};

const submitCreate = () => {
    createForm.post('/users', {
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
            modalClassSearch.value = '';
            modalClassTier.value = 'all';
        },
    });
};

const deleteForm = useForm({});
const confirmDelete = (user: UserItem) => {
    if (confirm(`Yakin ingin menghapus pengguna "${user.name}"?`)) {
        deleteForm.delete(`/users/${user.id}`);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Administrasi', href: '#' }, { title: 'Manajemen Pengguna', href: '/users' }]">
        <Head title="Manajemen Pengguna" />

        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-foreground flex items-center gap-2.5">
                        <div class="size-10 rounded-2xl bg-[#EAE6FE] text-purple-700 flex items-center justify-center">
                            <Users class="size-6" />
                        </div>
                        Pengguna Sistem
                    </h1>
                    <p class="text-sm text-muted-foreground mt-1">
                        Kelola akun pengguna, penetapan peran (role), dan status aktivasi.
                    </p>
                </div>
                <button
                    v-if="can('user.create')"
                    @click="showCreateModal = true"
                    class="inline-flex items-center gap-2 rounded-full bg-[#18181B] px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-neutral-800 transition-all cursor-pointer dark:bg-white dark:text-neutral-900"
                >
                    <Plus class="size-4" />
                    Tambah Pengguna
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
                        placeholder="Cari nama, email, atau NIS/NIP..."
                        class="w-full rounded-2xl border border-border bg-background pl-10 pr-4 py-2.5 text-sm text-foreground focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 transition-all"
                    />
                </div>
                <div class="w-full sm:w-56">
                    <select
                        v-model="selectedRole"
                        @change="handleFilter"
                        class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-sm text-foreground focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 transition-all cursor-pointer"
                    >
                        <option value="">Semua Peran</option>
                        <option v-for="r in roles" :key="r.id" :value="r.name">
                            {{ r.display_name }}
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

            <!-- Users Table -->
            <div class="rounded-3xl border border-border/80 bg-white dark:bg-card shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#F5EFE6]/70 dark:bg-neutral-900/80 border-b border-border/60">
                            <tr>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Nama & Identitas</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Peran (Roles)</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Status</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Kontak</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-muted/30 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-full bg-indigo-100 dark:bg-indigo-950 flex items-center justify-center font-bold text-indigo-700 dark:text-indigo-300">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-foreground">{{ user.name }}</p>
                                            <p class="text-xs text-muted-foreground">
                                                {{ user.email }}
                                                <span v-if="user.identifier_number"> · {{ user.identifier_number }}</span>
                                                <span v-if="user.class_name" class="font-medium text-indigo-600 dark:text-indigo-400"> ({{ user.class_name }})</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        <span
                                            v-for="role in user.roles"
                                            :key="role.id"
                                            class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300"
                                        >
                                            <Shield class="size-3" />
                                            {{ role.display_name }}
                                        </span>
                                        <span v-if="user.roles.length === 0" class="text-xs text-muted-foreground italic">
                                            Tanpa Peran
                                        </span>
                                    </div>
                                    <div v-if="user.homeroom_classes && user.homeroom_classes.length > 0" class="mt-2 flex flex-wrap gap-1">
                                        <span
                                            v-for="hc in user.homeroom_classes"
                                            :key="hc.id"
                                            class="inline-flex items-center rounded-md bg-amber-50 px-1.5 py-0.5 text-[10px] font-semibold text-amber-700 border border-amber-200/60 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800/40"
                                        >
                                            Wali: {{ hc.name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        :class="user.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-950/60 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300'"
                                    >
                                        <component :is="user.status === 'active' ? UserCheck : UserX" class="size-3" />
                                        {{ user.status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-muted-foreground">
                                    {{ user.phone || '—' }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="`/users/${user.id}`"
                                            class="rounded-lg p-1.5 text-muted-foreground hover:bg-muted hover:text-foreground transition-colors"
                                            title="Lihat Detail & Hak Akses"
                                        >
                                            <Eye class="size-4" />
                                        </Link>
                                        <button
                                            v-if="!user.is_system_user && can('user.delete')"
                                            @click="confirmDelete(user)"
                                            class="rounded-lg p-1.5 text-muted-foreground hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/50 dark:hover:text-red-400 transition-colors"
                                            title="Hapus Pengguna"
                                        >
                                            <Trash2 class="size-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="users.data.length === 0">
                                <td colspan="5" class="py-12 text-center text-muted-foreground text-sm">
                                    Tidak ada pengguna yang ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="users.total > 15" class="flex items-center justify-between border-t border-border px-5 py-3 text-xs text-muted-foreground">
                    <span>Menampilkan {{ users.data.length }} dari total {{ users.total }} pengguna</span>
                    <div class="flex gap-1">
                        <Link
                            v-for="(link, i) in users.links"
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

            <!-- Create User Modal -->
            <AppModal
                :show="showCreateModal"
                @close="showCreateModal = false"
                title="Tambah Pengguna Baru"
                description="Lengkapi data akun pengguna, peran akses, dan relasi profil sekolah."
                max-width="xl"
            >
                <form @submit.prevent="submitCreate" class="space-y-5">
                    <!-- Step 1: Role Selection First -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-muted-foreground">
                                1. Pilih Peran Akses (Role) <span class="text-rose-500">*</span>
                            </label>
                            <span class="text-[11px] text-muted-foreground">Bisa memilih lebih dari satu peran</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-3 border border-border rounded-2xl bg-muted/20">
                            <label
                                v-for="r in roles"
                                :key="r.id"
                                class="flex items-center gap-2 p-2.5 rounded-xl border border-border bg-card text-xs font-semibold cursor-pointer hover:bg-muted/40 transition-all select-none"
                                :class="{ 'border-indigo-600 bg-indigo-50/70 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-500': createForm.role_ids.includes(r.id) }"
                            >
                                <input
                                    type="checkbox"
                                    :value="r.id"
                                    v-model="createForm.role_ids"
                                    class="size-4 rounded border-input text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="truncate">{{ r.display_name }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Step 2: Basic User Info -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-muted-foreground mb-2">
                            2. Informasi Pribadi & Kontak
                        </label>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-foreground mb-1.5">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <input
                                    v-model="createForm.name"
                                    type="text"
                                    required
                                    placeholder="Contoh: Budi Santoso, S.Pd atau Siti Rahma"
                                    class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                />
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-semibold text-foreground mb-1.5">
                                        Email Login <span class="text-rose-500">*</span>
                                    </label>
                                    <input
                                        v-model="createForm.email"
                                        type="email"
                                        required
                                        placeholder="nama@sekolah.sch.id"
                                        class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    />
                                    <p v-if="createForm.errors.email" class="mt-1 text-xs text-rose-500">{{ createForm.errors.email }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-foreground mb-1.5">
                                        {{ isSiswaSelected ? 'NIS (Nomor Induk Siswa)' : (isWaliKelasSelected ? 'NIP (Nomor Induk Pegawai)' : 'Nomor Induk (NIS / NIP)') }}
                                    </label>
                                    <input
                                        v-model="createForm.identifier_number"
                                        type="text"
                                        :placeholder="isSiswaSelected ? 'Contoh: 242507001' : 'Contoh: 198802202015031002'"
                                        class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3">
                                <div>
                                    <label class="block text-xs font-semibold text-foreground mb-1.5">
                                        Jenis Kelamin
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
                                    <label class="block text-xs font-semibold text-foreground mb-1.5">
                                        Nomor Telepon / WA
                                    </label>
                                    <input
                                        v-model="createForm.phone"
                                        type="text"
                                        placeholder="081234567890"
                                        class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-foreground mb-1.5">
                                        Status Akun <span class="text-rose-500">*</span>
                                    </label>
                                    <select
                                        v-model="createForm.status"
                                        class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    >
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3A: Homeroom Class Selection for Wali Kelas -->
                    <div v-if="isWaliKelasSelected" class="rounded-2xl border border-amber-300 bg-amber-50/60 p-4 dark:border-amber-900/60 dark:bg-amber-950/20">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                            <div>
                                <label class="text-xs font-bold text-amber-950 dark:text-amber-200 flex items-center gap-1.5">
                                    <School class="size-4 text-amber-600" />
                                    Pilih Kelas Perwalian (1 Guru dapat me-walikan banyak kelas)
                                </label>
                                <p class="text-[11px] text-muted-foreground mt-0.5">
                                    Wali kelas hanya dapat melihat data siswa pada kelas-kelas yang dipilih di bawah ini.
                                </p>
                            </div>
                            <span class="inline-flex items-center gap-1 self-start sm:self-auto text-xs font-bold px-2.5 py-1 rounded-full bg-amber-200/80 dark:bg-amber-900/70 text-amber-900 dark:text-amber-200">
                                {{ createForm.class_ids.length }} kelas dipilih
                            </span>
                        </div>

                        <!-- Class filters & search -->
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 mb-3">
                            <div class="relative flex-1">
                                <Search class="absolute left-3 top-2.5 size-3.5 text-muted-foreground" />
                                <input
                                    v-model="modalClassSearch"
                                    type="text"
                                    placeholder="Cari kelas..."
                                    class="w-full rounded-xl border border-border bg-card pl-8 pr-3 py-1.5 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-amber-500"
                                />
                            </div>
                            <div class="flex items-center gap-1 overflow-x-auto pb-1 sm:pb-0">
                                <button
                                    type="button"
                                    @click="modalClassTier = 'all'"
                                    class="rounded-lg px-2 py-1 text-[11px] font-bold transition-colors cursor-pointer"
                                    :class="modalClassTier === 'all' ? 'bg-amber-800 text-white dark:bg-amber-300 dark:text-neutral-900' : 'bg-card border border-border text-foreground hover:bg-muted'"
                                >
                                    Semua
                                </button>
                                <button
                                    type="button"
                                    @click="modalClassTier = 'sd'"
                                    class="rounded-lg px-2 py-1 text-[11px] font-bold transition-colors cursor-pointer"
                                    :class="modalClassTier === 'sd' ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300 border border-red-200'"
                                >
                                    SD
                                </button>
                                <button
                                    type="button"
                                    @click="modalClassTier = 'smp'"
                                    class="rounded-lg px-2 py-1 text-[11px] font-bold transition-colors cursor-pointer"
                                    :class="modalClassTier === 'smp' ? 'bg-sky-600 text-white' : 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200'"
                                >
                                    SMP
                                </button>
                                <button
                                    type="button"
                                    @click="modalClassTier = 'sma'"
                                    class="rounded-lg px-2 py-1 text-[11px] font-bold transition-colors cursor-pointer"
                                    :class="modalClassTier === 'sma' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200'"
                                >
                                    SMA/SMK
                                </button>
                                <button
                                    type="button"
                                    @click="toggleSelectAllFilteredClasses"
                                    class="rounded-lg px-2 py-1 text-[11px] font-semibold bg-amber-100 hover:bg-amber-200 text-amber-900 dark:bg-amber-900/60 dark:text-amber-200 transition-colors cursor-pointer ml-auto"
                                >
                                    Pilih/Batal Semua
                                </button>
                            </div>
                        </div>

                        <!-- Class Checkbox Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 max-h-40 overflow-y-auto p-1">
                            <label
                                v-for="cls in filteredModalClasses"
                                :key="cls.id"
                                class="flex items-center gap-2 rounded-xl border border-border bg-card p-2 text-xs cursor-pointer hover:bg-muted/50 transition-colors select-none"
                                :class="{ 'border-amber-600 bg-amber-100/60 dark:bg-amber-950/50 font-bold': createForm.class_ids.includes(cls.id) }"
                            >
                                <input
                                    type="checkbox"
                                    :value="cls.id"
                                    v-model="createForm.class_ids"
                                    class="size-3.5 rounded border-input text-amber-600 focus:ring-amber-500"
                                />
                                <span class="truncate text-foreground">{{ cls.name }}</span>
                            </label>
                            <div v-if="filteredModalClasses.length === 0" class="col-span-full py-4 text-center text-xs text-muted-foreground">
                                Tidak ada kelas yang cocok.
                            </div>
                        </div>
                    </div>

                    <!-- Step 3B: Single Class Selection for Siswa -->
                    <div v-if="isSiswaSelected" class="rounded-2xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-900/60 dark:bg-sky-950/20">
                        <label class="block text-xs font-bold text-sky-950 dark:text-sky-200 mb-1.5 flex items-center gap-1.5">
                            <GraduationCap class="size-4 text-sky-600" />
                            Penempatan Kelas Siswa
                        </label>
                        <select
                            v-model="createForm.student_class_id"
                            class="w-full rounded-2xl border border-input bg-card px-3.5 py-2.5 text-sm text-foreground focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                        >
                            <option :value="null">-- Belum Ditentukan / Pilih Kelas --</option>
                            <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                                {{ cls.name }} {{ cls.academic_year ? `(${cls.academic_year})` : '' }}
                            </option>
                        </select>
                    </div>

                    <!-- Step 4: Security Credentials -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-muted-foreground">
                                3. Kata Sandi Akun <span class="text-rose-500">*</span>
                            </label>
                            <button
                                type="button"
                                @click="generateRandomPassword"
                                class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 hover:underline cursor-pointer"
                            >
                                <Sparkles class="size-3" />
                                Generate Sandi Otomatis
                            </button>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <input
                                    v-model="createForm.password"
                                    type="text"
                                    required
                                    minlength="8"
                                    placeholder="Kata Sandi (min. 8 karakter)"
                                    class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground font-mono focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                />
                            </div>
                            <div>
                                <input
                                    v-model="createForm.password_confirmation"
                                    type="text"
                                    required
                                    minlength="8"
                                    placeholder="Konfirmasi Kata Sandi"
                                    class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground font-mono focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
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
                            {{ createForm.processing ? 'Menyimpan...' : 'Buat Pengguna' }}
                        </button>
                    </div>
                </form>
            </AppModal>
        </div>
    </AppLayout>
</template>
