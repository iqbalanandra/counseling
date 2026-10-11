<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    BookOpen,
    CheckCircle,
    Clock,
    GraduationCap,
    Star,
    Users,
    AlertTriangle,
    Calendar,
    Sparkles,
    ShieldCheck,
    Plus,
    FileText,
    ArrowUpRight,
    ChevronLeft,
    ChevronRight,
    MapPin,
    UserCheck,
    CalendarDays,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { usePermission } from '@/composables/usePermission';
import { useInitials } from '@/composables/useInitials';

const { user } = usePermission();
const { getInitials } = useInitials();

interface StudentProfile {
    name: string;
    nis: string;
    class_name?: string;
    total_violation_points: number;
    total_achievement_points: number;
}

interface ClassInfo {
    id: number;
    name: string;
}

interface RecentRequest {
    id: number;
    type?: string;
    status: string;
    preferred_date?: string;
    scheduled_datetime?: string;
    student_name?: string;
    class_name?: string;
}

interface CounselingScheduleItem {
    id: number;
    request_id?: number;
    session_date?: string;
    date: string; // YYYY-MM-DD
    time: string; // HH:mm
    formatted_date: string;
    student_name: string;
    student_nis: string;
    class_name: string;
    counselor_name: string;
    counseling_type: string;
    venue: string;
    status: string;
}

const props = defineProps<{
    role?: string;
    scheduleScope?: string;
    stats?: Record<string, number>;
    student?: StudentProfile;
    class?: ClassInfo;
    students?: Array<{
        id: number;
        name: string;
        nis: string;
        total_violation_points: number;
        total_achievement_points: number;
    }>;
    recentRequests?: RecentRequest[];
    counselingSchedules?: CounselingScheduleItem[];
}>();

// Greetings based on current time
const greetingText = computed(() => {
    const hours = new Date().getHours();
    if (hours < 11) return 'Selamat Pagi';
    if (hours < 15) return 'Selamat Siang';
    if (hours < 18) return 'Selamat Sore';
    return 'Selamat Malam';
});

// Format today's date in Indonesian
const todayFormatted = computed(() => {
    return new Intl.DateTimeFormat('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    }).format(new Date());
});

// Calendar State
const currentDate = new Date();
const currentMonth = ref(currentDate.getMonth());
const currentYear = ref(currentDate.getFullYear());
const selectedDate = ref<string>(''); // YYYY-MM-DD format, empty means show all/upcoming

const monthNames = [
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember',
];

const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

const currentMonthLabel = computed(() => {
    return `${monthNames[currentMonth.value]} ${currentYear.value}`;
});

const prevMonth = () => {
    if (currentMonth.value === 0) {
        currentMonth.value = 11;
        currentYear.value--;
    } else {
        currentMonth.value--;
    }
};

const nextMonth = () => {
    if (currentMonth.value === 11) {
        currentMonth.value = 0;
        currentYear.value++;
    } else {
        currentMonth.value++;
    }
};

const resetToToday = () => {
    const now = new Date();
    currentMonth.value = now.getMonth();
    currentYear.value = now.getFullYear();
    const y = now.getFullYear();
    const m = String(now.getMonth() + 1).padStart(2, '0');
    const d = String(now.getDate()).padStart(2, '0');
    selectedDate.value = `${y}-${m}-${d}`;
};

// Generate calendar days for the current month
interface CalendarDay {
    dayNumber: number;
    dateString: string;
    isCurrentMonth: boolean;
    isToday: boolean;
    isSelected: boolean;
    sessionCount: number;
}

const calendarDays = computed<CalendarDay[]>(() => {
    const days: CalendarDay[] = [];
    const year = currentYear.value;
    const month = currentMonth.value;

    const firstDayIndex = new Date(year, month, 1).getDay(); // 0 is Sunday
    const totalDaysInMonth = new Date(year, month + 1, 0).getDate();
    const totalDaysInPrevMonth = new Date(year, month, 0).getDate();

    const todayObj = new Date();
    const todayStr = `${todayObj.getFullYear()}-${String(todayObj.getMonth() + 1).padStart(2, '0')}-${String(todayObj.getDate()).padStart(2, '0')}`;

    const schedules = props.counselingSchedules || [];

    // Previous month padding
    for (let i = firstDayIndex - 1; i >= 0; i--) {
        const d = totalDaysInPrevMonth - i;
        const prevMonthNum = month === 0 ? 12 : month;
        const prevYearNum = month === 0 ? year - 1 : year;
        const dateStr = `${prevYearNum}-${String(prevMonthNum).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        days.push({
            dayNumber: d,
            dateString: dateStr,
            isCurrentMonth: false,
            isToday: dateStr === todayStr,
            isSelected: dateStr === selectedDate.value,
            sessionCount: schedules.filter((s) => s.date === dateStr).length,
        });
    }

    // Current month days
    for (let d = 1; d <= totalDaysInMonth; d++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        days.push({
            dayNumber: d,
            dateString: dateStr,
            isCurrentMonth: true,
            isToday: dateStr === todayStr,
            isSelected: dateStr === selectedDate.value,
            sessionCount: schedules.filter((s) => s.date === dateStr).length,
        });
    }

    // Next month padding to complete 35 or 42 grid cells
    const remainingCells =
        42 - days.length > 7 ? 35 - days.length : 42 - days.length;
    for (let d = 1; d <= remainingCells; d++) {
        const nextMonthNum = month === 11 ? 1 : month + 2;
        const nextYearNum = month === 11 ? year + 1 : year;
        const dateStr = `${nextYearNum}-${String(nextMonthNum).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        days.push({
            dayNumber: d,
            dateString: dateStr,
            isCurrentMonth: false,
            isToday: dateStr === todayStr,
            isSelected: dateStr === selectedDate.value,
            sessionCount: schedules.filter((s) => s.date === dateStr).length,
        });
    }

    return days;
});

const toggleSelectDate = (dateStr: string) => {
    if (selectedDate.value === dateStr) {
        selectedDate.value = ''; // clear filter to show all
    } else {
        selectedDate.value = dateStr;
    }
};

// Filtered schedules for agenda view
const filteredSchedules = computed(() => {
    const list = props.counselingSchedules || [];
    if (!selectedDate.value) {
        return list;
    }
    return list.filter((item) => item.date === selectedDate.value);
});

// Status helpers
const statusStyle: Record<string, string> = {
    scheduled:
        'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80',
    completed:
        'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200/80',
    pending:
        'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80',
    approved:
        'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/80',
    rescheduled:
        'bg-orange-100 text-orange-800 dark:bg-orange-950/60 dark:text-orange-300 border border-orange-200/80',
    cancelled:
        'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300 border border-neutral-200/80',
    rejected:
        'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80',
};

const statusName: Record<string, string> = {
    scheduled: 'Terjadwal',
    completed: 'Selesai',
    pending: 'Menunggu',
    approved: 'Disetujui',
    rescheduled: 'Jadwal Ulang',
    cancelled: 'Dibatalkan',
    rejected: 'Ditolak',
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Dashboard', href: '/dashboard' }]">
        <Head title="Dashboard — Sistem Informasi BK" />

        <div class="space-y-6 p-4 sm:p-6 lg:p-8">
            <!-- 1. COMPACT HERO BAR (Visual Hierarchy: Primary Welcome & Scope) -->
            <div
                class="flex flex-col justify-between gap-4 rounded-3xl border border-[#EBE2D4] bg-card p-5 shadow-2xs sm:flex-row sm:items-center sm:p-6 dark:border-neutral-800"
            >
                <div>
                    <div class="mb-1.5 flex items-center gap-2">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200/70 bg-emerald-100/90 px-3 py-0.5 text-xs font-bold text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300"
                        >
                            <Sparkles class="size-3.5" />
                            Portal BK SMPN 1
                        </span>
                        <span class="text-xs font-medium text-muted-foreground">
                            {{ todayFormatted }}
                        </span>
                    </div>
                    <h1
                        class="text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl"
                    >
                        {{ greetingText }}, {{ user?.name }} 👋
                    </h1>
                    <p
                        class="mt-0.5 text-xs font-normal text-muted-foreground sm:text-sm"
                    >
                        Ringkasan aktivitas bimbingan konseling dan agenda sesi
                        tatap muka siswa.
                    </p>
                </div>

                <!-- Quick Action Button according to Role -->
                <div class="flex shrink-0 items-center gap-2.5">
                    <Link
                        v-if="role === 'siswa'"
                        href="/counseling/requests"
                        class="inline-flex items-center gap-2 rounded-full bg-[#18181B] px-4.5 py-2 text-xs font-bold text-white shadow-xs transition-colors hover:bg-neutral-800 sm:text-sm dark:bg-white dark:text-neutral-900"
                    >
                        <Plus class="size-4" />
                        Ajukan Konseling
                    </Link>
                    <Link
                        v-else
                        href="/counseling/sessions"
                        class="inline-flex items-center gap-2 rounded-full bg-[#18181B] px-4.5 py-2 text-xs font-bold text-white shadow-xs transition-colors hover:bg-neutral-800 sm:text-sm dark:bg-white dark:text-neutral-900"
                    >
                        <CalendarDays class="size-4" />
                        Kelola Sesi Konseling
                    </Link>
                </div>
            </div>

            <!-- 2. STREAMLINED METRICS CARDS (Visual Hierarchy: 4 Pastel KPI Cards) -->
            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-4"
            >
                <!-- CARD 1: Permohonan Menunggu (Rose) -->
                <Link
                    href="/counseling/requests"
                    class="group rounded-3xl border border-[#F8CDCD] bg-[#FDE5E5] p-5 shadow-2xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-rose-900/40 dark:bg-rose-950/35"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-white/90 px-2.5 py-0.5 text-xs font-bold text-rose-950 shadow-2xs dark:bg-rose-900/60 dark:text-rose-200"
                        >
                            <Clock class="size-3.5 text-rose-600" />
                            Permohonan
                        </span>
                        <ArrowUpRight
                            class="size-4 text-rose-700/60 transition-all group-hover:translate-x-0.5 group-hover:text-rose-800"
                        />
                    </div>
                    <div class="mt-4">
                        <div
                            class="text-3xl font-black tracking-tight text-rose-950 dark:text-rose-100"
                        >
                            {{
                                stats?.pending_requests ??
                                stats?.pending_counseling ??
                                0
                            }}
                        </div>
                        <p
                            class="mt-1 text-xs font-semibold text-rose-900/80 dark:text-rose-300"
                        >
                            Permohonan Menunggu Tindakan
                        </p>
                    </div>
                </Link>

                <!-- CARD 2: Sesi Terjadwal (Peach) -->
                <Link
                    href="/counseling/sessions"
                    class="group rounded-3xl border border-[#F7D2AB] bg-[#FDECDA] p-5 shadow-2xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-amber-900/40 dark:bg-amber-950/35"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-white/90 px-2.5 py-0.5 text-xs font-bold text-amber-950 shadow-2xs dark:bg-amber-900/60 dark:text-amber-200"
                        >
                            <Calendar class="size-3.5 text-amber-600" />
                            Sesi Terjadwal
                        </span>
                        <ArrowUpRight
                            class="size-4 text-amber-700/60 transition-all group-hover:translate-x-0.5 group-hover:text-amber-800"
                        />
                    </div>
                    <div class="mt-4">
                        <div
                            class="text-3xl font-black tracking-tight text-amber-950 dark:text-amber-100"
                        >
                            {{
                                stats?.today_sessions ??
                                counselingSchedules?.filter(
                                    (s) => s.status === 'scheduled',
                                ).length ??
                                0
                            }}
                        </div>
                        <p
                            class="mt-1 text-xs font-semibold text-amber-900/80 dark:text-amber-300"
                        >
                            Sesi Tatap Muka Terjadwal
                        </p>
                    </div>
                </Link>

                <!-- CARD 3: Siswa / Binaan (Lavender) -->
                <Link
                    :href="role === 'siswa' ? '/violations' : '/students'"
                    class="group rounded-3xl border border-[#DDD0F4] bg-[#EFE7FA] p-5 shadow-2xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-purple-900/40 dark:bg-purple-950/35"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-white/90 px-2.5 py-0.5 text-xs font-bold text-purple-950 shadow-2xs dark:bg-purple-900/60 dark:text-purple-200"
                        >
                            <Users class="size-3.5 text-purple-600" />
                            {{
                                role === 'siswa'
                                    ? 'Poin Pelanggaran'
                                    : 'Siswa Aktif'
                            }}
                        </span>
                        <ArrowUpRight
                            class="size-4 text-purple-700/60 transition-all group-hover:translate-x-0.5 group-hover:text-purple-800"
                        />
                    </div>
                    <div class="mt-4">
                        <div
                            class="text-3xl font-black tracking-tight text-purple-950 dark:text-purple-100"
                        >
                            <template v-if="role === 'siswa'">
                                {{ student?.total_violation_points ?? 0 }}
                                <span class="text-sm font-semibold opacity-75"
                                    >Poin</span
                                >
                            </template>
                            <template v-else>
                                {{ stats?.total_students ?? 0 }}
                                <span class="text-sm font-semibold opacity-75"
                                    >Siswa</span
                                >
                            </template>
                        </div>
                        <p
                            class="mt-1 text-xs font-semibold text-purple-900/80 dark:text-purple-300"
                        >
                            {{
                                role === 'wali_kelas'
                                    ? props.class?.name
                                        ? `Kelas ${props.class.name}`
                                        : 'Siswa Kelas'
                                    : role === 'siswa'
                                      ? 'Kedisiplinan Siswa'
                                      : 'Terdaftar di Sekolah'
                            }}
                        </p>
                    </div>
                </Link>

                <!-- CARD 4: Selesai / Prestasi (Mint) -->
                <Link
                    :href="role === 'siswa' ? '/achievements' : '/reports'"
                    class="group rounded-3xl border border-[#BEE5D0] bg-[#DDF4E8] p-5 shadow-2xs transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-emerald-900/40 dark:bg-emerald-950/35"
                >
                    <div class="flex items-center justify-between">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-white/90 px-2.5 py-0.5 text-xs font-bold text-emerald-950 shadow-2xs dark:bg-emerald-900/60 dark:text-emerald-200"
                        >
                            <CheckCircle class="size-3.5 text-emerald-600" />
                            {{
                                role === 'siswa'
                                    ? 'Poin Prestasi'
                                    : 'Layanan Tuntas'
                            }}
                        </span>
                        <ArrowUpRight
                            class="size-4 text-emerald-700/60 transition-all group-hover:translate-x-0.5 group-hover:text-emerald-800"
                        />
                    </div>
                    <div class="mt-4">
                        <div
                            class="text-3xl font-black tracking-tight text-emerald-950 dark:text-emerald-100"
                        >
                            <template v-if="role === 'siswa'">
                                {{ student?.total_achievement_points ?? 0 }}
                                <span class="text-sm font-semibold opacity-75"
                                    >Poin</span
                                >
                            </template>
                            <template v-else>
                                {{
                                    stats?.total_counseling_this_month ??
                                    stats?.total_counseling ??
                                    stats?.total_achievements ??
                                    0
                                }}
                            </template>
                        </div>
                        <p
                            class="mt-1 text-xs font-semibold text-emerald-900/80 dark:text-emerald-300"
                        >
                            {{
                                role === 'siswa'
                                    ? 'Prestasi Membanggakan'
                                    : 'Konseling Selesai Optimal'
                            }}
                        </p>
                    </div>
                </Link>
            </div>

            <!-- 3. MAIN SECTION: 2-COLUMN BALANCED LAYOUT -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                <!-- LEFT COLUMN: KALENDER & AGENDA JADWAL KONSELING (7 Cols) -->
                <div class="flex flex-col gap-6 lg:col-span-7">
                    <div
                        class="rounded-3xl border border-[#EBE2D4] bg-card p-5 shadow-2xs sm:p-6 dark:border-neutral-800"
                    >
                        <!-- Header Calendar Section with Role-Scoped Badge -->
                        <div
                            class="flex flex-col justify-between gap-3 border-b border-border/70 pb-4 sm:flex-row sm:items-center"
                        >
                            <div>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="flex size-8 items-center justify-center rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900"
                                    >
                                        <Calendar class="size-4" />
                                    </div>
                                    <div>
                                        <h2
                                            class="text-base leading-tight font-bold text-foreground sm:text-lg"
                                        >
                                            Kalender & Jadwal Konseling
                                        </h2>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{
                                                scheduleScope ||
                                                'Jadwal Sesi Konseling'
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Month Switcher & Reset Button -->
                            <div class="flex items-center gap-2">
                                <button
                                    @click="resetToToday"
                                    type="button"
                                    class="cursor-pointer rounded-full bg-[#EAE2D5]/80 px-3 py-1 text-xs font-bold text-foreground transition-colors hover:bg-[#DDD2C2] dark:bg-neutral-800 dark:hover:bg-neutral-700"
                                >
                                    Hari Ini
                                </button>
                                <div
                                    class="inline-flex items-center rounded-full border border-border bg-background p-0.5"
                                >
                                    <button
                                        @click="prevMonth"
                                        type="button"
                                        class="flex size-7 cursor-pointer items-center justify-center rounded-full text-foreground transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-800"
                                        title="Bulan Sebelumnya"
                                    >
                                        <ChevronLeft class="size-4" />
                                    </button>
                                    <span
                                        class="px-2 text-xs font-bold text-foreground select-none"
                                    >
                                        {{ currentMonthLabel }}
                                    </span>
                                    <button
                                        @click="nextMonth"
                                        type="button"
                                        class="flex size-7 cursor-pointer items-center justify-center rounded-full text-foreground transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-800"
                                        title="Bulan Berikutnya"
                                    >
                                        <ChevronRight class="size-4" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="mt-4">
                            <!-- Day of week headers -->
                            <div
                                class="mb-1 grid grid-cols-7 gap-1 text-center"
                            >
                                <div
                                    v-for="dName in dayNames"
                                    :key="dName"
                                    class="py-1 text-[11px] font-bold tracking-wider text-muted-foreground uppercase"
                                >
                                    {{ dName }}
                                </div>
                            </div>

                            <!-- Calendar Day Cells -->
                            <div class="grid grid-cols-7 gap-1">
                                <button
                                    v-for="(day, idx) in calendarDays"
                                    :key="idx"
                                    @click="toggleSelectDate(day.dateString)"
                                    type="button"
                                    class="relative flex h-11 cursor-pointer flex-col items-center justify-center rounded-2xl transition-all select-none sm:h-12"
                                    :class="[
                                        day.isSelected
                                            ? 'scale-102 bg-[#18181B] font-black text-white shadow-md dark:bg-white dark:text-neutral-900'
                                            : day.isToday
                                              ? 'border border-emerald-300 bg-emerald-100/70 font-bold text-emerald-950 dark:border-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200'
                                              : day.isCurrentMonth
                                                ? 'font-semibold text-foreground hover:bg-[#EAE4DC] dark:hover:bg-neutral-800'
                                                : 'font-normal text-muted-foreground/40 hover:bg-[#F2ECE3] dark:hover:bg-neutral-800/40',
                                    ]"
                                >
                                    <span
                                        class="text-xs leading-none sm:text-sm"
                                        >{{ day.dayNumber }}</span
                                    >

                                    <!-- Indicator dots if date has counseling sessions -->
                                    <div
                                        v-if="day.sessionCount > 0"
                                        class="mt-1 flex items-center gap-0.5"
                                    >
                                        <span
                                            class="size-1.5 rounded-full"
                                            :class="
                                                day.isSelected
                                                    ? 'bg-amber-300 dark:bg-amber-600'
                                                    : 'bg-emerald-600 dark:bg-emerald-400'
                                            "
                                        ></span>
                                        <span
                                            v-if="day.sessionCount > 1"
                                            class="text-[9px] leading-none font-bold"
                                            :class="
                                                day.isSelected
                                                    ? 'text-amber-200'
                                                    : 'text-emerald-700 dark:text-emerald-300'
                                            "
                                        >
                                            {{ day.sessionCount }}
                                        </span>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Schedule Agenda List on Selected/Upcoming Dates -->
                        <div class="mt-6 border-t border-border/70 pt-5">
                            <div class="mb-3 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <h3
                                        class="text-sm font-bold text-foreground"
                                    >
                                        {{
                                            selectedDate
                                                ? `Jadwal Tanggal ${selectedDate}`
                                                : 'Daftar Agenda Konseling'
                                        }}
                                    </h3>
                                    <span
                                        class="rounded-full bg-neutral-200/80 px-2 py-0.5 text-[11px] font-bold text-foreground dark:bg-neutral-800"
                                    >
                                        {{ filteredSchedules.length }} Sesi
                                    </span>
                                </div>

                                <button
                                    v-if="selectedDate"
                                    @click="selectedDate = ''"
                                    type="button"
                                    class="text-xs font-semibold text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    Tampilkan Semua
                                </button>
                            </div>

                            <!-- List of Session Cards -->
                            <div
                                class="max-h-96 space-y-3 overflow-y-auto pr-1"
                            >
                                <div
                                    v-for="session in filteredSchedules"
                                    :key="session.id"
                                    class="flex flex-col justify-between gap-3 rounded-2xl border border-border/70 bg-background/70 p-3.5 shadow-2xs transition-all hover:bg-background hover:shadow-xs sm:flex-row sm:items-center"
                                >
                                    <div class="flex min-w-0 items-start gap-3">
                                        <!-- Time Badge -->
                                        <div
                                            class="flex size-11 shrink-0 flex-col items-center justify-center rounded-2xl border border-border/40 bg-[#EAE2D5] dark:bg-neutral-800"
                                        >
                                            <span
                                                class="text-[11px] leading-none font-black text-foreground"
                                                >{{ session.time }}</span
                                            >
                                            <span
                                                class="mt-0.5 text-[9px] font-bold text-muted-foreground uppercase"
                                                >WIB</span
                                            >
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div
                                                class="flex flex-wrap items-center gap-2"
                                            >
                                                <h4
                                                    class="truncate text-sm font-bold text-foreground"
                                                >
                                                    {{ session.student_name }}
                                                </h4>
                                                <span
                                                    class="rounded-full bg-[#18181B] px-2 py-0.5 text-[10px] font-bold text-white dark:bg-white dark:text-neutral-900"
                                                >
                                                    Kelas
                                                    {{ session.class_name }}
                                                </span>
                                                <span
                                                    class="rounded-full px-2 py-0.5 text-[10px] font-bold"
                                                    :class="
                                                        statusStyle[
                                                            session.status
                                                        ] ||
                                                        'bg-neutral-100 text-neutral-800'
                                                    "
                                                >
                                                    {{
                                                        statusName[
                                                            session.status
                                                        ] || session.status
                                                    }}
                                                </span>
                                            </div>

                                            <div
                                                class="mt-1 flex flex-wrap items-center gap-3 text-xs text-muted-foreground"
                                            >
                                                <span
                                                    class="inline-flex items-center gap-1"
                                                >
                                                    <BookOpen
                                                        class="size-3 text-purple-600"
                                                    />
                                                    {{
                                                        session.counseling_type
                                                    }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center gap-1"
                                                >
                                                    <MapPin
                                                        class="size-3 text-rose-500"
                                                    />
                                                    {{ session.venue }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center gap-1"
                                                >
                                                    <UserCheck
                                                        class="size-3 text-blue-600"
                                                    />
                                                    Konselor:
                                                    {{ session.counselor_name }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Link to Session Detail / Action -->
                                    <Link
                                        :href="`/counseling/sessions`"
                                        class="inline-flex shrink-0 items-center gap-1 self-end rounded-full border border-border px-3 py-1.5 text-xs font-semibold text-foreground transition-colors hover:bg-neutral-100 sm:self-center dark:hover:bg-neutral-800"
                                    >
                                        Buka Sesi
                                        <ArrowUpRight class="size-3" />
                                    </Link>
                                </div>

                                <!-- Empty State -->
                                <div
                                    v-if="filteredSchedules.length === 0"
                                    class="rounded-2xl border border-dashed border-border p-6 py-8 text-center"
                                >
                                    <Calendar
                                        class="mx-auto mb-2 size-8 text-muted-foreground/60"
                                    />
                                    <p
                                        class="text-xs font-semibold text-foreground"
                                    >
                                        {{
                                            selectedDate
                                                ? 'Tidak ada sesi konseling pada tanggal ini.'
                                                : 'Belum ada jadwal sesi konseling aktif.'
                                        }}
                                    </p>
                                    <p
                                        class="mt-0.5 text-[11px] text-muted-foreground"
                                    >
                                        {{
                                            role === 'siswa'
                                                ? 'Gunakan tombol di atas untuk mengajukan konseling baru.'
                                                : 'Sesi konseling baru dapat dijadwalkan dari menu Permohonan Konseling.'
                                        }}
                                    </p>
                                    <button
                                        v-if="selectedDate"
                                        @click="selectedDate = ''"
                                        type="button"
                                        class="mt-3 inline-block rounded-full bg-[#18181B] px-3 py-1 text-xs font-bold text-white dark:bg-white dark:text-neutral-900"
                                    >
                                        Lihat Semua Jadwal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: PERMOHONAN TERKINI & PROFIL AKSI CEPAT (5 Cols) -->
                <div class="flex flex-col gap-6 lg:col-span-5">
                    <!-- CARD 1: PERMOHONAN TERKINI (Recent Requests) -->
                    <div
                        class="rounded-3xl border border-[#EBE2D4] bg-card p-5 shadow-2xs sm:p-6 dark:border-neutral-800"
                    >
                        <div
                            class="mb-4 flex items-center justify-between border-b border-border/70 pb-3"
                        >
                            <div>
                                <h3
                                    class="text-sm font-bold text-foreground sm:text-base"
                                >
                                    Permohonan Konseling Masuk
                                </h3>
                                <p class="text-xs text-muted-foreground">
                                    Permohonan yang memerlukan konfirmasi
                                </p>
                            </div>
                            <Link
                                href="/counseling/requests"
                                class="text-xs font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400"
                            >
                                Lihat Semua
                            </Link>
                        </div>

                        <div class="space-y-3">
                            <div
                                v-for="req in (recentRequests || []).slice(
                                    0,
                                    4,
                                )"
                                :key="req.id"
                                class="flex items-center justify-between gap-3 rounded-2xl border border-border/60 bg-background/60 p-3 transition-colors hover:bg-background"
                            >
                                <div class="min-w-0">
                                    <div
                                        class="flex flex-wrap items-center gap-1.5"
                                    >
                                        <p
                                            class="truncate text-xs font-bold text-foreground"
                                        >
                                            {{ req.student_name || user?.name }}
                                        </p>
                                        <span
                                            v-if="req.class_name"
                                            class="py-0.2 rounded-full bg-muted px-1.5 text-[10px] font-semibold text-muted-foreground"
                                        >
                                            {{ req.class_name }}
                                        </span>
                                    </div>
                                    <p
                                        class="mt-0.5 text-[11px] text-muted-foreground"
                                    >
                                        Jenis: {{ req.type || 'Konseling' }} •
                                        {{ req.preferred_date || 'Segera' }}
                                    </p>
                                </div>

                                <span
                                    class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold"
                                    :class="
                                        statusStyle[req.status] ||
                                        'bg-neutral-100 text-neutral-800'
                                    "
                                >
                                    {{ statusName[req.status] || req.status }}
                                </span>
                            </div>

                            <div
                                v-if="!(recentRequests || []).length"
                                class="py-6 text-center text-xs text-muted-foreground"
                            >
                                Tidak ada permohonan baru yang menunggu.
                            </div>
                        </div>
                    </div>

                    <!-- CARD 2: PROFIL RINGKAS & CAKUPAN AKSES -->
                    <div
                        class="rounded-3xl border border-[#EBE2D4] bg-[#F5EFE6] p-5 shadow-2xs sm:p-6 dark:border-neutral-800 dark:bg-[#1A1F1D]"
                    >
                        <div class="flex items-center gap-4">
                            <div
                                class="flex size-14 shrink-0 items-center justify-center rounded-full bg-white text-lg font-black text-foreground shadow-2xs ring-3 ring-[#E2D6C0] dark:bg-neutral-800 dark:ring-neutral-700"
                            >
                                {{ getInitials(user?.name || 'BK') }}
                            </div>
                            <div class="min-w-0">
                                <h4
                                    class="truncate text-base font-extrabold text-foreground"
                                >
                                    {{ user?.name }}
                                </h4>
                                <p
                                    class="truncate text-xs text-muted-foreground"
                                >
                                    {{ user?.email }}
                                </p>
                                <div
                                    class="mt-1.5 flex flex-wrap items-center gap-2"
                                >
                                    <span
                                        class="inline-block rounded-full bg-[#18181B] px-2.5 py-0.5 text-[11px] font-bold text-white dark:bg-white dark:text-neutral-900"
                                    >
                                        {{ user?.roles?.[0] || 'User' }}
                                    </span>
                                    <span
                                        v-if="student?.class_name"
                                        class="inline-block rounded-full bg-white/80 px-2 py-0.5 text-[11px] font-semibold text-foreground dark:bg-neutral-800"
                                    >
                                        Kelas {{ student.class_name }}
                                    </span>
                                    <span
                                        v-else-if="props.class?.name"
                                        class="inline-block rounded-full bg-white/80 px-2 py-0.5 text-[11px] font-semibold text-foreground dark:bg-neutral-800"
                                    >
                                        Wali Kelas {{ props.class.name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Shortcuts Grid -->
                        <div
                            class="mt-5 border-t border-[#E5DAC7] pt-4 dark:border-neutral-800"
                        >
                            <h5
                                class="mb-2.5 text-xs font-bold tracking-wider text-muted-foreground uppercase"
                            >
                                Akses Cepat Menu
                            </h5>
                            <div
                                class="grid grid-cols-2 gap-2 text-xs font-semibold"
                            >
                                <Link
                                    href="/counseling/requests"
                                    class="flex items-center gap-2 rounded-xl border border-border/80 bg-white p-2.5 transition-colors hover:bg-neutral-100 dark:bg-card dark:hover:bg-neutral-800"
                                >
                                    <FileText class="size-3.5 text-rose-600" />
                                    <span>Permohonan</span>
                                </Link>
                                <Link
                                    href="/counseling/sessions"
                                    class="flex items-center gap-2 rounded-xl border border-border/80 bg-white p-2.5 transition-colors hover:bg-neutral-100 dark:bg-card dark:hover:bg-neutral-800"
                                >
                                    <CalendarDays
                                        class="size-3.5 text-amber-600"
                                    />
                                    <span>Sesi Tatap Muka</span>
                                </Link>
                                <Link
                                    href="/students"
                                    class="flex items-center gap-2 rounded-xl border border-border/80 bg-white p-2.5 transition-colors hover:bg-neutral-100 dark:bg-card dark:hover:bg-neutral-800"
                                >
                                    <GraduationCap
                                        class="size-3.5 text-purple-600"
                                    />
                                    <span>Data Siswa</span>
                                </Link>
                                <Link
                                    href="/reports"
                                    class="flex items-center gap-2 rounded-xl border border-border/80 bg-white p-2.5 transition-colors hover:bg-neutral-100 dark:bg-card dark:hover:bg-neutral-800"
                                >
                                    <CheckCircle
                                        class="size-3.5 text-emerald-600"
                                    />
                                    <span>Laporan BK</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
