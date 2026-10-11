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

const isSd = (grade?: string) =>
    !!grade && ['1', '2', '3', '4', '5', '6'].includes(grade);
const isSmp = (grade?: string) => !!grade && ['7', '8', '9'].includes(grade);
const isSma = (grade?: string) =>
    !!grade &&
    ['10', '11', '12', 'X', 'XI', 'XII'].includes(grade.toUpperCase());

const filteredEditClasses = computed(() => {
    return props.classes.filter((c) => {
        const matchesSearch =
            !editClassSearch.value ||
            c.name.toLowerCase().includes(editClassSearch.value.toLowerCase());
        if (!matchesSearch) return false;

        if (editClassTier.value === 'all') return true;
        if (editClassTier.value === 'sd') return isSd(c.grade);
        if (editClassTier.value === 'smp') return isSmp(c.grade);
        if (editClassTier.value === 'sma') return isSma(c.grade);
        if (editClassTier.value === 'other')
            return !isSd(c.grade) && !isSmp(c.grade) && !isSma(c.grade);
        return true;
    });
});

const toggleSelectAllEditClasses = () => {
    const currentFilteredIds = filteredEditClasses.value.map((c) => c.id);
    const allSelected =
        currentFilteredIds.length > 0 &&
        currentFilteredIds.every((id) => form.class_ids.includes(id));

    if (allSelected) {
        form.class_ids = form.class_ids.filter(
            (id) => !currentFilteredIds.includes(id),
        );
    } else {
        const toAdd = currentFilteredIds.filter(
            (id) => !form.class_ids.includes(id),
        );
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
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-3">
                    <Link
                        href="/users"
                        class="rounded-xl border border-border p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                    >
                        <ArrowLeft class="size-5" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl font-bold text-foreground">
                                {{ user.name }}
                            </h1>
                            <span
                                v-if="user.is_system_user"
                                class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/40 dark:text-amber-300"
                            >
                                <Lock class="size-3" />
                                Akun Sistem
                            </span>
                        </div>
                        <p class="text-sm text-muted-foreground">
                            {{ user.email }}
                        </p>
                    </div>
                </div>

                <button
                    v-if="can('user.update')"
                    @click="submitUpdate"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700 disabled:opacity-50"
                >
                    <Save class="size-4" />
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                </button>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <!-- User Form Details -->
                <div class="space-y-6 lg:col-span-2">
                    <div
                        class="rounded-2xl border border-border bg-card p-6 shadow-sm"
                    >
                        <h2
                            class="mb-4 text-base font-semibold text-foreground"
                        >
                            Informasi Profil Akun
                        </h2>

                        <form @submit.prevent="submitUpdate" class="space-y-4">
                            <div>
                                <label
                                    class="mb-1 block text-xs font-medium text-foreground"
                                    >Nama Lengkap</label
                                >
                                <input
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                />
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-1 block text-xs font-medium text-foreground"
                                        >Email</label
                                    >
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="mb-1 block text-xs font-medium text-foreground"
                                    >
                                        {{
                                            isSiswaSelected
                                                ? 'NIS (Nomor Induk Siswa)'
                                                : isWaliKelasSelected
                                                  ? 'NIP (Nomor Induk Pegawai)'
                                                  : 'Nomor Induk (NIS/NIP)'
                                        }}
                                    </label>
                                    <input
                                        v-model="form.identifier_number"
                                        type="text"
                                        class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                    />
                                </div>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3">
                                <div>
                                    <label
                                        class="mb-1 block text-xs font-medium text-foreground"
                                        >Jenis Kelamin</label
                                    >
                                    <select
                                        v-model="form.gender"
                                        class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                    >
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="mb-1 block text-xs font-medium text-foreground"
                                        >Telepon / WhatsApp</label
                                    >
                                    <input
                                        v-model="form.phone"
                                        type="text"
                                        class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="mb-1 block text-xs font-medium text-foreground"
                                        >Status Akun</label
                                    >
                                    <select
                                        v-model="form.status"
                                        class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                    >
                                        <option value="active">Aktif</option>
                                        <option value="inactive">
                                            Nonaktif
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="mb-1 block text-xs font-medium text-foreground"
                                >
                                    Ganti Sandi (Biarkan kosong jika tidak ingin
                                    mengubah)
                                </label>
                                <input
                                    v-model="form.password"
                                    type="password"
                                    placeholder="Masukkan sandi baru..."
                                    class="w-full rounded-xl border border-input bg-background px-3.5 py-2 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                />
                            </div>

                            <!-- Roles -->
                            <div class="pt-2">
                                <label
                                    class="mb-2 block text-xs font-medium text-foreground"
                                >
                                    Peran Akses (Roles)
                                </label>
                                <div
                                    class="grid gap-2 rounded-xl border border-border bg-muted/20 p-3 sm:grid-cols-2"
                                >
                                    <label
                                        v-for="r in roles"
                                        :key="r.id"
                                        class="flex cursor-pointer items-center gap-2 text-xs text-foreground select-none"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="r.id"
                                            v-model="form.role_ids"
                                            class="size-4 rounded border-input text-indigo-600 focus:ring-indigo-500"
                                        />
                                        <span class="font-medium">{{
                                            r.display_name
                                        }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Single Class Selection for Siswa -->
                            <div
                                v-if="isSiswaSelected"
                                class="rounded-xl border border-sky-200 bg-sky-50/60 p-4 dark:border-sky-900/60 dark:bg-sky-950/20"
                            >
                                <label
                                    class="mb-1.5 block flex items-center gap-1.5 text-xs font-bold text-sky-950 dark:text-sky-200"
                                >
                                    <GraduationCap
                                        class="size-4 text-sky-600"
                                    />
                                    Penempatan Kelas Siswa
                                </label>
                                <select
                                    v-model="form.student_class_id"
                                    class="w-full rounded-xl border border-input bg-card px-3.5 py-2 text-sm text-foreground focus:border-sky-500 focus:ring-1 focus:ring-sky-500 focus:outline-none"
                                >
                                    <option :value="null">
                                        -- Belum Ditentukan / Pilih Kelas --
                                    </option>
                                    <option
                                        v-for="cls in classes"
                                        :key="cls.id"
                                        :value="cls.id"
                                    >
                                        {{ cls.name }}
                                        {{
                                            cls.academic_year
                                                ? `(${cls.academic_year})`
                                                : ''
                                        }}
                                    </option>
                                </select>
                            </div>

                            <!-- Homeroom Class Selection for Wali Kelas -->
                            <div
                                v-if="isWaliKelasSelected"
                                class="rounded-xl border border-amber-300 bg-amber-50/60 p-4 dark:border-amber-900/60 dark:bg-amber-950/20"
                            >
                                <div
                                    class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <div>
                                        <label
                                            class="flex items-center gap-1.5 text-xs font-bold text-amber-950 dark:text-amber-200"
                                        >
                                            <School
                                                class="size-4 text-amber-600"
                                            />
                                            Pilih Kelas Perwalian (1 Guru dapat
                                            me-walikan banyak kelas)
                                        </label>
                                        <p
                                            class="mt-0.5 text-[11px] text-muted-foreground"
                                        >
                                            Wali kelas hanya dapat melihat data
                                            siswa pada kelas-kelas yang dipilih
                                            di bawah ini.
                                        </p>
                                    </div>
                                    <span
                                        class="inline-flex items-center gap-1 self-start rounded-full bg-amber-200/80 px-2.5 py-1 text-xs font-bold text-amber-900 sm:self-auto dark:bg-amber-900/70 dark:text-amber-200"
                                    >
                                        {{ form.class_ids.length }} kelas
                                        dipilih
                                    </span>
                                </div>

                                <!-- Class filters & search -->
                                <div
                                    class="mb-3 flex flex-col items-stretch gap-2 sm:flex-row sm:items-center"
                                >
                                    <div class="relative flex-1">
                                        <Search
                                            class="absolute top-2.5 left-3 size-3.5 text-muted-foreground"
                                        />
                                        <input
                                            v-model="editClassSearch"
                                            type="text"
                                            placeholder="Cari kelas..."
                                            class="w-full rounded-xl border border-border bg-card py-1.5 pr-3 pl-8 text-xs text-foreground focus:ring-1 focus:ring-amber-500 focus:outline-none"
                                        />
                                    </div>
                                    <div
                                        class="flex items-center gap-1 overflow-x-auto pb-1 sm:pb-0"
                                    >
                                        <button
                                            type="button"
                                            @click="editClassTier = 'all'"
                                            class="cursor-pointer rounded-lg px-2 py-1 text-[11px] font-bold transition-colors"
                                            :class="
                                                editClassTier === 'all'
                                                    ? 'bg-amber-800 text-white dark:bg-amber-300 dark:text-neutral-900'
                                                    : 'border border-border bg-card text-foreground hover:bg-muted'
                                            "
                                        >
                                            Semua
                                        </button>
                                        <button
                                            type="button"
                                            @click="editClassTier = 'sd'"
                                            class="cursor-pointer rounded-lg px-2 py-1 text-[11px] font-bold transition-colors"
                                            :class="
                                                editClassTier === 'sd'
                                                    ? 'bg-red-600 text-white'
                                                    : 'border border-red-200 bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300'
                                            "
                                        >
                                            SD
                                        </button>
                                        <button
                                            type="button"
                                            @click="editClassTier = 'smp'"
                                            class="cursor-pointer rounded-lg px-2 py-1 text-[11px] font-bold transition-colors"
                                            :class="
                                                editClassTier === 'smp'
                                                    ? 'bg-sky-600 text-white'
                                                    : 'border border-sky-200 bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300'
                                            "
                                        >
                                            SMP
                                        </button>
                                        <button
                                            type="button"
                                            @click="editClassTier = 'sma'"
                                            class="cursor-pointer rounded-lg px-2 py-1 text-[11px] font-bold transition-colors"
                                            :class="
                                                editClassTier === 'sma'
                                                    ? 'bg-emerald-600 text-white'
                                                    : 'border border-emerald-200 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300'
                                            "
                                        >
                                            SMA/SMK
                                        </button>
                                        <button
                                            type="button"
                                            @click="toggleSelectAllEditClasses"
                                            class="ml-auto cursor-pointer rounded-lg bg-amber-100 px-2 py-1 text-[11px] font-semibold text-amber-900 transition-colors hover:bg-amber-200 dark:bg-amber-900/60 dark:text-amber-200"
                                        >
                                            Pilih/Batal Semua
                                        </button>
                                    </div>
                                </div>

                                <div
                                    class="grid max-h-48 grid-cols-2 gap-2 overflow-y-auto p-1 sm:grid-cols-3"
                                >
                                    <label
                                        v-for="cls in filteredEditClasses"
                                        :key="cls.id"
                                        class="flex cursor-pointer items-center gap-2 rounded-lg border border-border bg-card p-2 text-xs transition-colors select-none hover:bg-muted/50"
                                        :class="{
                                            'border-amber-600 bg-amber-100/60 font-bold dark:bg-amber-950/50':
                                                form.class_ids.includes(cls.id),
                                        }"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="cls.id"
                                            v-model="form.class_ids"
                                            class="size-3.5 rounded border-input text-amber-600 focus:ring-amber-500"
                                        />
                                        <span
                                            class="truncate font-medium text-foreground"
                                            >{{ cls.name }}</span
                                        >
                                    </label>
                                    <div
                                        v-if="filteredEditClasses.length === 0"
                                        class="col-span-full py-4 text-center text-xs text-muted-foreground"
                                    >
                                        Tidak ada kelas yang cocok.
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Effective Permissions Preview -->
                <div class="space-y-6">
                    <div
                        class="rounded-2xl border border-border bg-card p-6 shadow-sm"
                    >
                        <div class="mb-3 flex items-center gap-2">
                            <Key class="size-5 text-indigo-600" />
                            <h2 class="text-base font-semibold text-foreground">
                                Hak Akses Efektif
                            </h2>
                        </div>
                        <p class="mb-4 text-xs text-muted-foreground">
                            Daftar hak akses yang diwarisi pengguna dari
                            peran-peran yang dimilikinya saat ini ({{
                                user.permissions.length
                            }}
                            izin aktif).
                        </p>

                        <div class="max-h-96 space-y-1.5 overflow-y-auto pr-1">
                            <div
                                v-for="perm in user.permissions"
                                :key="perm"
                                class="flex items-center gap-2 rounded-lg bg-muted/40 px-3 py-1.5 font-mono text-xs text-foreground"
                            >
                                <CheckCircle2
                                    class="size-3.5 shrink-0 text-emerald-500"
                                />
                                <span class="truncate">{{ perm }}</span>
                            </div>

                            <div
                                v-if="user.permissions.length === 0"
                                class="py-6 text-center text-xs text-muted-foreground"
                            >
                                Pengguna belum memiliki hak akses apapun.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
