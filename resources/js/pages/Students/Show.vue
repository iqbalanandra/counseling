<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    GraduationCap,
    ArrowLeft,
    AlertTriangle,
    Star,
    User,
    Phone,
    MapPin,
    Calendar,
    Award,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { usePermission } from '@/composables/usePermission';

interface ViolationItem {
    id: number;
    category: string;
    violation_date: string;
    points: number;
    status: string;
    description: string | null;
}

interface AchievementItem {
    id: number;
    title: string;
    level: string;
    rank: string | null;
    event_date: string;
    verification_status: string;
}

interface StudentDetail {
    id: number;
    nis: string;
    nisn: string | null;
    name: string;
    gender: 'L' | 'P';
    birth_place: string | null;
    birth_date: string | null;
    address: string | null;
    parent_name: string | null;
    parent_phone: string | null;
    status: string;
    class_name: string | null;
    homeroom_teacher: string | null;
    total_violation_points: number;
    total_achievement_points: number;
    violations: ViolationItem[];
    achievements: AchievementItem[];
}

const props = defineProps<{
    student: StudentDetail;
}>();

const { can } = usePermission();
const activeTab = ref<'profil' | 'pelanggaran' | 'prestasi'>('profil');
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Data Siswa', href: '/students' },
            { title: student.name, href: `/students/${student.id}` },
        ]"
    >
        <Head :title="`Profil Siswa — ${student.name}`" />

        <div class="flex flex-col gap-6 p-6">
            <!-- Header bar -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link
                        href="/students"
                        class="rounded-xl border border-border p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                    >
                        <ArrowLeft class="size-5" />
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold text-foreground">
                            {{ student.name }}
                        </h1>
                        <p class="text-sm text-muted-foreground">
                            NIS: {{ student.nis }} · Kelas:
                            {{ student.class_name || '—' }} · Wali:
                            {{ student.homeroom_teacher || '—' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stats Header -->
            <div class="grid gap-4 sm:grid-cols-3">
                <div
                    class="flex items-center gap-4 rounded-2xl border border-border bg-card p-5 shadow-sm"
                >
                    <div
                        class="rounded-xl bg-blue-100 p-3 text-blue-600 dark:bg-blue-950 dark:text-blue-400"
                    >
                        <GraduationCap class="size-6" />
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">
                            Kelas Saat Ini
                        </p>
                        <p class="text-lg font-bold text-foreground">
                            {{ student.class_name || 'Belum Ada' }}
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center gap-4 rounded-2xl border border-border bg-card p-5 shadow-sm"
                >
                    <div
                        class="rounded-xl bg-red-100 p-3 text-red-600 dark:bg-red-950 dark:text-red-400"
                    >
                        <AlertTriangle class="size-6" />
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">
                            Total Poin Pelanggaran
                        </p>
                        <p
                            class="text-lg font-bold"
                            :class="
                                student.total_violation_points > 0
                                    ? 'text-red-600'
                                    : 'text-foreground'
                            "
                        >
                            {{ student.total_violation_points }} Poin
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center gap-4 rounded-2xl border border-border bg-card p-5 shadow-sm"
                >
                    <div
                        class="rounded-xl bg-amber-100 p-3 text-amber-600 dark:bg-amber-950 dark:text-amber-400"
                    >
                        <Star class="size-6 fill-amber-500 text-amber-500" />
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">
                            Total Poin Prestasi
                        </p>
                        <p class="text-lg font-bold text-amber-600">
                            {{ student.total_achievement_points }} Poin
                        </p>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex gap-6 border-b border-border">
                <button
                    @click="activeTab = 'profil'"
                    class="relative pb-3 text-sm font-semibold transition-colors"
                    :class="
                        activeTab === 'profil'
                            ? 'text-indigo-600 dark:text-indigo-400'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                >
                    Biodata Lengkap
                    <span
                        v-if="activeTab === 'profil'"
                        class="absolute right-0 bottom-0 left-0 h-0.5 bg-indigo-600 dark:bg-indigo-400"
                    ></span>
                </button>
                <button
                    @click="activeTab = 'pelanggaran'"
                    class="relative flex items-center gap-1.5 pb-3 text-sm font-semibold transition-colors"
                    :class="
                        activeTab === 'pelanggaran'
                            ? 'text-indigo-600 dark:text-indigo-400'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                >
                    Catatan Pelanggaran
                    <span
                        class="py-0.2 rounded-full bg-red-100 px-2 text-xs font-bold text-red-600 dark:bg-red-950 dark:text-red-400"
                    >
                        {{ student.violations.length }}
                    </span>
                    <span
                        v-if="activeTab === 'pelanggaran'"
                        class="absolute right-0 bottom-0 left-0 h-0.5 bg-indigo-600 dark:bg-indigo-400"
                    ></span>
                </button>
                <button
                    @click="activeTab = 'prestasi'"
                    class="relative flex items-center gap-1.5 pb-3 text-sm font-semibold transition-colors"
                    :class="
                        activeTab === 'prestasi'
                            ? 'text-indigo-600 dark:text-indigo-400'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                >
                    Catatan Prestasi
                    <span
                        class="py-0.2 rounded-full bg-amber-100 px-2 text-xs font-bold text-amber-600 dark:bg-amber-950 dark:text-amber-400"
                    >
                        {{ student.achievements.length }}
                    </span>
                    <span
                        v-if="activeTab === 'prestasi'"
                        class="absolute right-0 bottom-0 left-0 h-0.5 bg-indigo-600 dark:bg-indigo-400"
                    ></span>
                </button>
            </div>

            <!-- Tab: Profil -->
            <div
                v-if="activeTab === 'profil'"
                class="rounded-2xl border border-border bg-card p-6 shadow-sm"
            >
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-4">
                        <h3
                            class="border-b border-border pb-2 text-xs font-bold tracking-wider text-muted-foreground uppercase"
                        >
                            Data Pribadi
                        </h3>
                        <div class="text-sm">
                            <span class="block text-xs text-muted-foreground"
                                >Nama Lengkap</span
                            >
                            <span class="font-medium text-foreground">{{
                                student.name
                            }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="block text-xs text-muted-foreground"
                                >NIS / NISN</span
                            >
                            <span class="font-medium text-foreground"
                                >{{ student.nis }} /
                                {{ student.nisn || '—' }}</span
                            >
                        </div>
                        <div class="text-sm">
                            <span class="block text-xs text-muted-foreground"
                                >Jenis Kelamin</span
                            >
                            <span class="font-medium text-foreground">{{
                                student.gender === 'L'
                                    ? 'Laki-laki'
                                    : 'Perempuan'
                            }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="block text-xs text-muted-foreground"
                                >Tempat, Tanggal Lahir</span
                            >
                            <span class="font-medium text-foreground">
                                {{ student.birth_place || '—' }},
                                {{ student.birth_date || '—' }}
                            </span>
                        </div>
                        <div class="text-sm">
                            <span class="block text-xs text-muted-foreground"
                                >Alamat</span
                            >
                            <span class="font-medium text-foreground">{{
                                student.address || '—'
                            }}</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3
                            class="border-b border-border pb-2 text-xs font-bold tracking-wider text-muted-foreground uppercase"
                        >
                            Keluarga & Akademik
                        </h3>
                        <div class="text-sm">
                            <span class="block text-xs text-muted-foreground"
                                >Nama Orang Tua / Wali</span
                            >
                            <span class="font-medium text-foreground">{{
                                student.parent_name || '—'
                            }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="block text-xs text-muted-foreground"
                                >Kontak Orang Tua</span
                            >
                            <span class="font-medium text-foreground">{{
                                student.parent_phone || '—'
                            }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="block text-xs text-muted-foreground"
                                >Wali Kelas</span
                            >
                            <span class="font-medium text-foreground">{{
                                student.homeroom_teacher || '—'
                            }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="block text-xs text-muted-foreground"
                                >Status Kesiswaan</span
                            >
                            <span
                                class="font-medium text-foreground capitalize"
                                >{{ student.status }}</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Pelanggaran -->
            <div
                v-if="activeTab === 'pelanggaran'"
                class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
            >
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-border bg-muted/50">
                        <tr>
                            <th
                                class="px-5 py-3 text-xs font-semibold text-muted-foreground uppercase"
                            >
                                Tanggal
                            </th>
                            <th
                                class="px-5 py-3 text-xs font-semibold text-muted-foreground uppercase"
                            >
                                Kategori
                            </th>
                            <th
                                class="px-5 py-3 text-xs font-semibold text-muted-foreground uppercase"
                            >
                                Deskripsi
                            </th>
                            <th
                                class="px-5 py-3 text-center text-xs font-semibold text-muted-foreground uppercase"
                            >
                                Poin
                            </th>
                            <th
                                class="px-5 py-3 text-xs font-semibold text-muted-foreground uppercase"
                            >
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="v in student.violations" :key="v.id">
                            <td class="px-5 py-3 text-muted-foreground">
                                {{ v.violation_date }}
                            </td>
                            <td class="px-5 py-3 font-medium text-foreground">
                                {{ v.category }}
                            </td>
                            <td class="px-5 py-3 text-muted-foreground">
                                {{ v.description || '—' }}
                            </td>
                            <td
                                class="px-5 py-3 text-center font-bold text-red-600"
                            >
                                {{ v.points }}
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="rounded-full bg-muted px-2 py-0.5 text-xs font-medium"
                                    >{{ v.status }}</span
                                >
                            </td>
                        </tr>
                        <tr v-if="student.violations.length === 0">
                            <td
                                colspan="5"
                                class="py-8 text-center text-sm text-muted-foreground"
                            >
                                Tidak ada catatan pelanggaran untuk siswa ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tab: Prestasi -->
            <div
                v-if="activeTab === 'prestasi'"
                class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
            >
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-border bg-muted/50">
                        <tr>
                            <th
                                class="px-5 py-3 text-xs font-semibold text-muted-foreground uppercase"
                            >
                                Tanggal
                            </th>
                            <th
                                class="px-5 py-3 text-xs font-semibold text-muted-foreground uppercase"
                            >
                                Prestasi
                            </th>
                            <th
                                class="px-5 py-3 text-xs font-semibold text-muted-foreground uppercase"
                            >
                                Tingkat
                            </th>
                            <th
                                class="px-5 py-3 text-xs font-semibold text-muted-foreground uppercase"
                            >
                                Peringkat
                            </th>
                            <th
                                class="px-5 py-3 text-xs font-semibold text-muted-foreground uppercase"
                            >
                                Verifikasi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="a in student.achievements" :key="a.id">
                            <td class="px-5 py-3 text-muted-foreground">
                                {{ a.event_date }}
                            </td>
                            <td class="px-5 py-3 font-medium text-foreground">
                                {{ a.title }}
                            </td>
                            <td class="px-5 py-3 text-muted-foreground">
                                {{ a.level }}
                            </td>
                            <td class="px-5 py-3 text-muted-foreground">
                                {{ a.rank || '—' }}
                            </td>
                            <td class="px-5 py-3">
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs font-semibold"
                                    :class="
                                        a.verification_status === 'verified'
                                            ? 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-300'
                                            : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300'
                                    "
                                >
                                    {{
                                        a.verification_status === 'verified'
                                            ? 'Terverifikasi'
                                            : 'Menunggu'
                                    }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="student.achievements.length === 0">
                            <td
                                colspan="5"
                                class="py-8 text-center text-sm text-muted-foreground"
                            >
                                Tidak ada catatan prestasi untuk siswa ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
