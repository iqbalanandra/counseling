<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Users,
    ArrowLeft,
    Shield,
    Key,
    Save,
    Lock,
    Mail,
    Phone,
    Hash,
    CheckCircle2,
    UserCheck,
    UserX,
    School,
    GraduationCap,
    Search,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
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

interface UserDetail {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    identifier_number: string | null;
    gender?: 'L' | 'P';
    status: 'active' | 'inactive';
    is_system_user: boolean;
    roles: RoleOption[];
    permissions: string[];
    student: any | null;
    student_class_id?: number | null;
    teacher: any | null;
    homeroom_classes?: Array<{ id: number; name: string }>;
    homeroom_class_ids?: number[];
}

const props = defineProps<{
    user: UserDetail;
    roles: RoleOption[];
    classes: ClassOption[];
}>();

const { can } = usePermission();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone || '',
    identifier_number: props.user.identifier_number || '',
    gender: (props.user.gender || 'L') as 'L' | 'P',
    status: props.user.status,
    password: '',
    role_ids: props.user.roles.map((r) => r.id),
    class_ids: (props.user.homeroom_class_ids || []) as number[],
    student_class_id: (props.user.student_class_id || null) as number | null,
});

const isWaliKelasSelected = computed(() => {
    const waliRole = props.roles.find((r) => r.name === 'Wali Kelas');
    return waliRole ? form.role_ids.includes(waliRole.id) : false;
});

const isSiswaSelected = computed(() => {
    const siswaRole = props.roles.find((r) => r.name === 'Siswa');
    return siswaRole ? form.role_ids.includes(siswaRole.id) : false;
});

const editClassTier = ref<'all' | 'sd' | 'smp' | 'sma' | 'other'>('all');
const editClassSearch = ref('');

const isSd = (grade?: string) => !!grade && ['1', '2', '3', '4', '5', '6'].includes(grade);
const isSmp = (grade?: string) => !!grade && ['7', '8', '9'].includes(grade);
const isSma = (grade?: string) => !!grade && ['10', '11', '12', 'X', 'XI', 'XII'].includes(grade.toUpperCase());

const filteredEditClasses = computed(() => {
    return props.classes.filter((c) => {
        const matchesSearch = !editClassSearch.value || c.name.toLowerCase().includes(editClassSearch.value.toLowerCase());
        if (!matchesSearch) return false;

        if (editClassTier.value === 'all') return true;
        if (editClassTier.value === 'sd') return isSd(c.grade);
        if (editClassTier.value === 'smp') return isSmp(c.grade);
        if (editClassTier.value === 'sma') return isSma(c.grade);
        if (editClassTier.value === 'other') return !isSd(c.grade) && !isSmp(c.grade) && !isSma(c.grade);
        return true;
    });
});

const toggleSelectAllEditClasses = () => {
    const currentFilteredIds = filteredEditClasses.value.map((c) => c.id);
    const allSelected = currentFilteredIds.length > 0 && currentFilteredIds.every((id) => form.class_ids.includes(id));

    if (allSelected) {
        form.class_ids = form.class_ids.filter((id) => !currentFilteredIds.includes(id));
    } else {
        const toAdd = currentFilteredIds.filter((id) => !form.class_ids.includes(id));
        form.class_ids = [...form.class_ids, ...toAdd];
    }
};

const submitUpdate = () => {
    form.patch(`/users/${props.user.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            form.password = '';
        },
    });
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Administrasi', href: '#' },
            { title: 'Manajemen Pengguna', href: '/users' },
            { title: user.name, href: `/users/${user.id}` },
        ]"
    >
        <Head :title="`Detail Pengguna — ${user.name}`" />

        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <Link
                        href="/users"
                        class="rounded-xl border border-border p-2 text-muted-foreground hover:bg-muted hover:text-foreground transition-colors"
                    >
                        <ArrowLeft class="size-5" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl font-bold text-foreground">{{ user.name }}</h1>
                            <span
                                v-if="user.is_system_user"
                                class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/40 dark:text-amber-300"
                            >
                                <Lock class="size-3" />
                                Akun Sistem
                            </span>
                        </div>
                        <p class="text-sm text-muted-foreground">{{ user.email }}</p>
                    </div>
                </div>

                <button
                    v-if="can('user.update')"
                    @click="submitUpdate"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50 transition-colors"
                >
                    <Save class="size-4" />
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- User Form Details -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                        <h2 class="text-base font-semibold text-foreground mb-4">Informasi Profil Akun</h2>

                        <form @submit.prevent="submitUpdate" class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-foreground mb-1">Nama Lengkap</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                />
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-medium text-foreground mb-1">Email</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-foreground mb-1">
                                        {{ isSiswaSelected ? 'NIS (Nomor Induk Siswa)' : (isWaliKelasSelected ? 'NIP (Nomor Induk Pegawai)' : 'Nomor Induk (NIS/NIP)') }}
                                    </label>
                                    <input
                                        v-model="form.identifier_number"
                                        type="text"
                                        class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3">
                                <div>
                                    <label class="block text-xs font-medium text-foreground mb-1">Jenis Kelamin</label>
                                    <select
                                        v-model="form.gender"
                                        class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    >
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-foreground mb-1">Telepon / WhatsApp</label>
                                    <input
                                        v-model="form.phone"
                                        type="text"
                                        class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-foreground mb-1">Status Akun</label>
                                    <select
                                        v-model="form.status"
                                        class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                    >
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Nonaktif</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-foreground mb-1">
                                    Ganti Sandi (Biarkan kosong jika tidak ingin mengubah)
                                </label>
                                <input
                                    v-model="form.password"
                                    type="password"
                                    placeholder="Masukkan sandi baru..."
                                    class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                />
                            </div>

                            <!-- Roles -->
                            <div class="pt-2">
                                <label class="block text-xs font-medium text-foreground mb-2">
                                    Peran Akses (Roles)
                                </label>
                                <div class="grid gap-2 sm:grid-cols-2 p-3 border border-border rounded-xl bg-muted/20">
                                    <label
                                        v-for="r in roles"
                                        :key="r.id"
                                        class="flex items-center gap-2 text-xs text-foreground cursor-pointer select-none"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="r.id"
                                            v-model="form.role_ids"
                                            class="size-4 rounded border-input text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <span class="font-medium">{{ r.display_name }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Single Class Selection for Siswa -->
                            <div v-if="isSiswaSelected" class="rounded-xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-900/60 dark:bg-sky-950/20">
                                <label class="block text-xs font-bold text-sky-950 dark:text-sky-200 mb-1.5 flex items-center gap-1.5">
                                    <GraduationCap class="size-4 text-sky-600" />
                                    Penempatan Kelas Siswa
                                </label>
                                <select
                                    v-model="form.student_class_id"
                                    class="w-full rounded-xl border border-input bg-card px-3.5 py-2 text-sm text-foreground focus:border-sky-500 focus:outline-none focus:ring-1 focus:ring-sky-500"
                                >
                                    <option :value="null">-- Belum Ditentukan / Pilih Kelas --</option>
                                    <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                                        {{ cls.name }} {{ cls.academic_year ? `(${cls.academic_year})` : '' }}
                                    </option>
                                </select>
                            </div>

                            <!-- Homeroom Class Selection for Wali Kelas -->
                            <div v-if="isWaliKelasSelected" class="rounded-xl border border-amber-300 bg-amber-50/60 p-4 dark:border-amber-900/60 dark:bg-amber-950/20">
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
                                        {{ form.class_ids.length }} kelas dipilih
                                    </span>
                                </div>

                                <!-- Class filters & search -->
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 mb-3">
                                    <div class="relative flex-1">
                                        <Search class="absolute left-3 top-2.5 size-3.5 text-muted-foreground" />
                                        <input
                                            v-model="editClassSearch"
                                            type="text"
                                            placeholder="Cari kelas..."
                                            class="w-full rounded-xl border border-border bg-card pl-8 pr-3 py-1.5 text-xs text-foreground focus:outline-none focus:ring-1 focus:ring-amber-500"
                                        />
                                    </div>
                                    <div class="flex items-center gap-1 overflow-x-auto pb-1 sm:pb-0">
                                        <button
                                            type="button"
                                            @click="editClassTier = 'all'"
                                            class="rounded-lg px-2 py-1 text-[11px] font-bold transition-colors cursor-pointer"
                                            :class="editClassTier === 'all' ? 'bg-amber-800 text-white dark:bg-amber-300 dark:text-neutral-900' : 'bg-card border border-border text-foreground hover:bg-muted'"
                                        >
                                            Semua
                                        </button>
                                        <button
                                            type="button"
                                            @click="editClassTier = 'sd'"
                                            class="rounded-lg px-2 py-1 text-[11px] font-bold transition-colors cursor-pointer"
                                            :class="editClassTier === 'sd' ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300 border border-red-200'"
                                        >
                                            SD
                                        </button>
                                        <button
                                            type="button"
                                            @click="editClassTier = 'smp'"
                                            class="rounded-lg px-2 py-1 text-[11px] font-bold transition-colors cursor-pointer"
                                            :class="editClassTier === 'smp' ? 'bg-sky-600 text-white' : 'bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200'"
                                        >
                                            SMP
                                        </button>
                                        <button
                                            type="button"
                                            @click="editClassTier = 'sma'"
                                            class="rounded-lg px-2 py-1 text-[11px] font-bold transition-colors cursor-pointer"
                                            :class="editClassTier === 'sma' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200'"
                                        >
                                            SMA/SMK
                                        </button>
                                        <button
                                            type="button"
                                            @click="toggleSelectAllEditClasses"
                                            class="rounded-lg px-2 py-1 text-[11px] font-semibold bg-amber-100 hover:bg-amber-200 text-amber-900 dark:bg-amber-900/60 dark:text-amber-200 transition-colors cursor-pointer ml-auto"
                                        >
                                            Pilih/Batal Semua
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-48 overflow-y-auto p-1">
                                    <label
                                        v-for="cls in filteredEditClasses"
                                        :key="cls.id"
                                        class="flex items-center gap-2 rounded-lg border border-border bg-card p-2 text-xs cursor-pointer hover:bg-muted/50 transition-colors select-none"
                                        :class="{ 'border-amber-600 bg-amber-100/60 dark:bg-amber-950/50 font-bold': form.class_ids.includes(cls.id) }"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="cls.id"
                                            v-model="form.class_ids"
                                            class="size-3.5 rounded border-input text-amber-600 focus:ring-amber-500"
                                        />
                                        <span class="font-medium text-foreground truncate">{{ cls.name }}</span>
                                    </label>
                                    <div v-if="filteredEditClasses.length === 0" class="col-span-full py-4 text-center text-xs text-muted-foreground">
                                        Tidak ada kelas yang cocok.
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Effective Permissions Preview -->
                <div class="space-y-6">
                    <div class="rounded-2xl border border-border bg-card p-6 shadow-sm">
                        <div class="flex items-center gap-2 mb-3">
                            <Key class="size-5 text-indigo-600" />
                            <h2 class="text-base font-semibold text-foreground">Hak Akses Efektif</h2>
                        </div>
                        <p class="text-xs text-muted-foreground mb-4">
                            Daftar hak akses yang diwarisi pengguna dari peran-peran yang dimilikinya saat ini ({{ user.permissions.length }} izin aktif).
                        </p>

                        <div class="max-h-96 overflow-y-auto space-y-1.5 pr-1">
                            <div
                                v-for="perm in user.permissions"
                                :key="perm"
                                class="flex items-center gap-2 rounded-lg bg-muted/40 px-3 py-1.5 text-xs font-mono text-foreground"
                            >
                                <CheckCircle2 class="size-3.5 text-emerald-500 shrink-0" />
                                <span class="truncate">{{ perm }}</span>
                            </div>

                            <div v-if="user.permissions.length === 0" class="text-center py-6 text-xs text-muted-foreground">
                                Pengguna belum memiliki hak akses apapun.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
