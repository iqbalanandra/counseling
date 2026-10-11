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
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
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
            sessionCount: schedules.filter(s => s.date === dateStr).length,
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
            sessionCount: schedules.filter(s => s.date === dateStr).length,
        });
    }

    // Next month padding to complete 35 or 42 grid cells
    const remainingCells = 42 - days.length > 7 ? 35 - days.length : 42 - days.length;
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
            sessionCount: schedules.filter(s => s.date === dateStr).length,
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
    return list.filter(item => item.date === selectedDate.value);
});

// Status helpers
const statusStyle: Record<string, string> = {
    scheduled: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80',
    completed: 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200/80',
    pending: 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80',
    approved: 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200/80',
    rescheduled: 'bg-orange-100 text-orange-800 dark:bg-orange-950/60 dark:text-orange-300 border border-orange-200/80',
    cancelled: 'bg-neutral-100 text-neutral-800 dark:bg-neutral-800 dark:text-neutral-300 border border-neutral-200/80',
    rejected: 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80',
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
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-3xl bg-card border border-[#EBE2D4] dark:border-neutral-800 p-5 sm:p-6 shadow-2xs">
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100/90 dark:bg-emerald-950/60 px-3 py-0.5 text-xs font-bold text-emerald-800 dark:text-emerald-300 border border-emerald-200/70">
                            <Sparkles class="size-3.5" />
                            Portal BK SMPN 1
                        </span>
                        <span class="text-xs font-medium text-muted-foreground">
                            {{ todayFormatted }}
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-foreground tracking-tight">
                        {{ greetingText }}, {{ user?.name }} 👋
                    </h1>
                    <p class="text-xs sm:text-sm text-muted-foreground mt-0.5 font-normal">
                        Ringkasan aktivitas bimbingan konseling dan agenda sesi tatap muka siswa.
                    </p>
                </div>

                <!-- Quick Action Button according to Role -->
                <div class="flex items-center gap-2.5 shrink-0">
                    <Link
                        v-if="role === 'siswa'"
                        href="/counseling/requests"
                        class="inline-flex items-center gap-2 rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 px-4.5 py-2 text-xs sm:text-sm font-bold shadow-xs hover:bg-neutral-800 transition-colors"
                    >
                        <Plus class="size-4" />
                        Ajukan Konseling
                    </Link>
                    <Link
                        v-else
                        href="/counseling/sessions"
                        class="inline-flex items-center gap-2 rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 px-4.5 py-2 text-xs sm:text-sm font-bold shadow-xs hover:bg-neutral-800 transition-colors"
                    >
                        <CalendarDays class="size-4" />
                        Kelola Sesi Konseling
                    </Link>
                </div>
            </div>

            <!-- 2. STREAMLINED METRICS CARDS (Visual Hierarchy: 4 Pastel KPI Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                <!-- CARD 1: Permohonan Menunggu (Rose) -->
                <Link
                    href="/counseling/requests"
                    class="group rounded-3xl bg-[#FDE5E5] dark:bg-rose-950/35 border border-[#F8CDCD] dark:border-rose-900/40 p-5 shadow-2xs hover:shadow-md transition-all duration-200 hover:-translate-y-0.5"
                >
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 dark:bg-rose-900/60 px-2.5 py-0.5 text-xs font-bold text-rose-950 dark:text-rose-200 shadow-2xs">
                            <Clock class="size-3.5 text-rose-600" />
                            Permohonan
                        </span>
                        <ArrowUpRight class="size-4 text-rose-700/60 group-hover:text-rose-800 group-hover:translate-x-0.5 transition-all" />
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-black text-rose-950 dark:text-rose-100 tracking-tight">
                            {{ stats?.pending_requests ?? stats?.pending_counseling ?? 0 }}
                        </div>
                        <p class="text-xs font-semibold text-rose-900/80 dark:text-rose-300 mt-1">
                            Permohonan Menunggu Tindakan
                        </p>
                    </div>
                </Link>

                <!-- CARD 2: Sesi Terjadwal (Peach) -->
                <Link
                    href="/counseling/sessions"
                    class="group rounded-3xl bg-[#FDECDA] dark:bg-amber-950/35 border border-[#F7D2AB] dark:border-amber-900/40 p-5 shadow-2xs hover:shadow-md transition-all duration-200 hover:-translate-y-0.5"
                >
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 dark:bg-amber-900/60 px-2.5 py-0.5 text-xs font-bold text-amber-950 dark:text-amber-200 shadow-2xs">
                            <Calendar class="size-3.5 text-amber-600" />
                            Sesi Terjadwal
                        </span>
                        <ArrowUpRight class="size-4 text-amber-700/60 group-hover:text-amber-800 group-hover:translate-x-0.5 transition-all" />
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-black text-amber-950 dark:text-amber-100 tracking-tight">
                            {{ stats?.today_sessions ?? counselingSchedules?.filter(s => s.status === 'scheduled').length ?? 0 }}
                        </div>
                        <p class="text-xs font-semibold text-amber-900/80 dark:text-amber-300 mt-1">
                            Sesi Tatap Muka Terjadwal
                        </p>
                    </div>
                </Link>

                <!-- CARD 3: Siswa / Binaan (Lavender) -->
                <Link
                    :href="role === 'siswa' ? '/violations' : '/students'"
                    class="group rounded-3xl bg-[#EFE7FA] dark:bg-purple-950/35 border border-[#DDD0F4] dark:border-purple-900/40 p-5 shadow-2xs hover:shadow-md transition-all duration-200 hover:-translate-y-0.5"
                >
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 dark:bg-purple-900/60 px-2.5 py-0.5 text-xs font-bold text-purple-950 dark:text-purple-200 shadow-2xs">
                            <Users class="size-3.5 text-purple-600" />
                            {{ role === 'siswa' ? 'Poin Pelanggaran' : 'Siswa Aktif' }}
                        </span>
                        <ArrowUpRight class="size-4 text-purple-700/60 group-hover:text-purple-800 group-hover:translate-x-0.5 transition-all" />
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-black text-purple-950 dark:text-purple-100 tracking-tight">
                            <template v-if="role === 'siswa'">
                                {{ student?.total_violation_points ?? 0 }} <span class="text-sm font-semibold opacity-75">Poin</span>
                            </template>
                            <template v-else>
                                {{ stats?.total_students ?? 0 }} <span class="text-sm font-semibold opacity-75">Siswa</span>
                            </template>
                        </div>
                        <p class="text-xs font-semibold text-purple-900/80 dark:text-purple-300 mt-1">
                            {{ role === 'wali_kelas' ? (props.class?.name ? `Kelas ${props.class.name}` : 'Siswa Kelas') : (role === 'siswa' ? 'Kedisiplinan Siswa' : 'Terdaftar di Sekolah') }}
                        </p>
                    </div>
                </Link>

                <!-- CARD 4: Selesai / Prestasi (Mint) -->
                <Link
                    :href="role === 'siswa' ? '/achievements' : '/reports'"
                    class="group rounded-3xl bg-[#DDF4E8] dark:bg-emerald-950/35 border border-[#BEE5D0] dark:border-emerald-900/40 p-5 shadow-2xs hover:shadow-md transition-all duration-200 hover:-translate-y-0.5"
                >
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 dark:bg-emerald-900/60 px-2.5 py-0.5 text-xs font-bold text-emerald-950 dark:text-emerald-200 shadow-2xs">
                            <CheckCircle class="size-3.5 text-emerald-600" />
                            {{ role === 'siswa' ? 'Poin Prestasi' : 'Layanan Tuntas' }}
                        </span>
                        <ArrowUpRight class="size-4 text-emerald-700/60 group-hover:text-emerald-800 group-hover:translate-x-0.5 transition-all" />
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-black text-emerald-950 dark:text-emerald-100 tracking-tight">
                            <template v-if="role === 'siswa'">
                                {{ student?.total_achievement_points ?? 0 }} <span class="text-sm font-semibold opacity-75">Poin</span>
                            </template>
                            <template v-else>
                                {{ stats?.total_counseling_this_month ?? stats?.total_counseling ?? stats?.total_achievements ?? 0 }}
                            </template>
                        </div>
                        <p class="text-xs font-semibold text-emerald-900/80 dark:text-emerald-300 mt-1">
                            {{ role === 'siswa' ? 'Prestasi Membanggakan' : 'Konseling Selesai Optimal' }}
                        </p>
                    </div>
                </Link>
            </div>

            <!-- 3. MAIN SECTION: 2-COLUMN BALANCED LAYOUT -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- LEFT COLUMN: KALENDER & AGENDA JADWAL KONSELING (7 Cols) -->
                <div class="lg:col-span-7 flex flex-col gap-6">
                    <div class="rounded-3xl border border-[#EBE2D4] dark:border-neutral-800 bg-card p-5 sm:p-6 shadow-2xs">
                        <!-- Header Calendar Section with Role-Scoped Badge -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-border/70">
                            <div>
                                <div class="flex items-center gap-2">
                                    <div class="size-8 rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 flex items-center justify-center">
                                        <Calendar class="size-4" />
                                    </div>
                                    <div>
                                        <h2 class="text-base sm:text-lg font-bold text-foreground leading-tight">
                                            Kalender & Jadwal Konseling
                                        </h2>
                                        <p class="text-xs text-muted-foreground">
                                            {{ scheduleScope || 'Jadwal Sesi Konseling' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Month Switcher & Reset Button -->
                            <div class="flex items-center gap-2">
                                <button
                                    @click="resetToToday"
                                    type="button"
                                    class="rounded-full px-3 py-1 text-xs font-bold bg-[#EAE2D5]/80 hover:bg-[#DDD2C2] dark:bg-neutral-800 dark:hover:bg-neutral-700 text-foreground transition-colors cursor-pointer"
                                >
                                    Hari Ini
                                </button>
                                <div class="inline-flex items-center rounded-full border border-border bg-background p-0.5">
                                    <button
                                        @click="prevMonth"
                                        type="button"
                                        class="size-7 rounded-full flex items-center justify-center hover:bg-neutral-100 dark:hover:bg-neutral-800 text-foreground transition-colors cursor-pointer"
                                        title="Bulan Sebelumnya"
                                    >
                                        <ChevronLeft class="size-4" />
                                    </button>
                                    <span class="px-2 text-xs font-bold text-foreground select-none">
                                        {{ currentMonthLabel }}
                                    </span>
                                    <button
                                        @click="nextMonth"
                                        type="button"
                                        class="size-7 rounded-full flex items-center justify-center hover:bg-neutral-100 dark:hover:bg-neutral-800 text-foreground transition-colors cursor-pointer"
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
                            <div class="grid grid-cols-7 gap-1 text-center mb-1">
                                <div
                                    v-for="dName in dayNames"
                                    :key="dName"
                                    class="text-[11px] font-bold text-muted-foreground uppercase tracking-wider py-1"
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
                                    class="relative h-11 sm:h-12 rounded-2xl flex flex-col items-center justify-center transition-all cursor-pointer select-none"
                                    :class="[
                                        day.isSelected
                                            ? 'bg-[#18181B] text-white shadow-md dark:bg-white dark:text-neutral-900 font-black scale-102'
                                            : day.isToday
                                                ? 'bg-emerald-100/70 text-emerald-950 font-bold border border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-200 dark:border-emerald-800'
                                                : day.isCurrentMonth
                                                    ? 'hover:bg-[#EAE4DC] dark:hover:bg-neutral-800 text-foreground font-semibold'
                                                    : 'text-muted-foreground/40 hover:bg-[#F2ECE3] dark:hover:bg-neutral-800/40 font-normal',
                                    ]"
                                >
                                    <span class="text-xs sm:text-sm leading-none">{{ day.dayNumber }}</span>

                                    <!-- Indicator dots if date has counseling sessions -->
                                    <div
                                        v-if="day.sessionCount > 0"
                                        class="flex items-center gap-0.5 mt-1"
                                    >
                                        <span
                                            class="size-1.5 rounded-full"
                                            :class="day.isSelected ? 'bg-amber-300 dark:bg-amber-600' : 'bg-emerald-600 dark:bg-emerald-400'"
                                        ></span>
                                        <span
                                            v-if="day.sessionCount > 1"
                                            class="text-[9px] font-bold leading-none"
                                            :class="day.isSelected ? 'text-amber-200' : 'text-emerald-700 dark:text-emerald-300'"
                                        >
                                            {{ day.sessionCount }}
                                        </span>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Schedule Agenda List on Selected/Upcoming Dates -->
                        <div class="mt-6 pt-5 border-t border-border/70">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-foreground">
                                        {{ selectedDate ? `Jadwal Tanggal ${selectedDate}` : 'Daftar Agenda Konseling' }}
                                    </h3>
                                    <span class="rounded-full bg-neutral-200/80 dark:bg-neutral-800 px-2 py-0.5 text-[11px] font-bold text-foreground">
                                        {{ filteredSchedules.length }} Sesi
                                    </span>
                                </div>

                                <button
                                    v-if="selectedDate"
                                    @click="selectedDate = ''"
                                    type="button"
                                    class="text-xs font-semibold text-muted-foreground hover:text-foreground transition-colors"
                                >
                                    Tampilkan Semua
                                </button>
                            </div>

                            <!-- List of Session Cards -->
                            <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                                <div
                                    v-for="session in filteredSchedules"
                                    :key="session.id"
                                    class="rounded-2xl border border-border/70 bg-background/70 hover:bg-background p-3.5 transition-all shadow-2xs hover:shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                                >
                                    <div class="flex items-start gap-3 min-w-0">
                                        <!-- Time Badge -->
                                        <div class="size-11 rounded-2xl bg-[#EAE2D5] dark:bg-neutral-800 flex flex-col items-center justify-center shrink-0 border border-border/40">
                                            <span class="text-[11px] font-black text-foreground leading-none">{{ session.time }}</span>
                                            <span class="text-[9px] text-muted-foreground uppercase font-bold mt-0.5">WIB</span>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <h4 class="text-sm font-bold text-foreground truncate">
                                                    {{ session.student_name }}
                                                </h4>
                                                <span class="rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 px-2 py-0.5 text-[10px] font-bold">
                                                    Kelas {{ session.class_name }}
                                                </span>
                                                <span
                                                    class="rounded-full px-2 py-0.5 text-[10px] font-bold"
                                                    :class="statusStyle[session.status] || 'bg-neutral-100 text-neutral-800'"
                                                >
                                                    {{ statusName[session.status] || session.status }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-3 text-xs text-muted-foreground mt-1 flex-wrap">
                                                <span class="inline-flex items-center gap-1">
                                                    <BookOpen class="size-3 text-purple-600" />
                                                    {{ session.counseling_type }}
                                                </span>
                                                <span class="inline-flex items-center gap-1">
                                                    <MapPin class="size-3 text-rose-500" />
                                                    {{ session.venue }}
                                                </span>
                                                <span class="inline-flex items-center gap-1">
                                                    <UserCheck class="size-3 text-blue-600" />
                                                    Konselor: {{ session.counselor_name }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Link to Session Detail / Action -->
                                    <Link
                                        :href="`/counseling/sessions`"
                                        class="shrink-0 inline-flex items-center gap-1 rounded-full border border-border px-3 py-1.5 text-xs font-semibold text-foreground hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors self-end sm:self-center"
                                    >
                                        Buka Sesi
                                        <ArrowUpRight class="size-3" />
                                    </Link>
                                </div>

                                <!-- Empty State -->
                                <div
                                    v-if="filteredSchedules.length === 0"
                                    class="py-8 text-center rounded-2xl border border-dashed border-border p-6"
                                >
                                    <Calendar class="size-8 text-muted-foreground/60 mx-auto mb-2" />
                                    <p class="text-xs font-semibold text-foreground">
                                        {{ selectedDate ? 'Tidak ada sesi konseling pada tanggal ini.' : 'Belum ada jadwal sesi konseling aktif.' }}
                                    </p>
                                    <p class="text-[11px] text-muted-foreground mt-0.5">
                                        {{ role === 'siswa' ? 'Gunakan tombol di atas untuk mengajukan konseling baru.' : 'Sesi konseling baru dapat dijadwalkan dari menu Permohonan Konseling.' }}
                                    </p>
                                    <button
                                        v-if="selectedDate"
                                        @click="selectedDate = ''"
                                        type="button"
                                        class="mt-3 inline-block rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 px-3 py-1 text-xs font-bold"
                                    >
                                        Lihat Semua Jadwal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: PERMOHONAN TERKINI & PROFIL AKSI CEPAT (5 Cols) -->
                <div class="lg:col-span-5 flex flex-col gap-6">
                    <!-- CARD 1: PERMOHONAN TERKINI (Recent Requests) -->
                    <div class="rounded-3xl border border-[#EBE2D4] dark:border-neutral-800 bg-card p-5 sm:p-6 shadow-2xs">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-border/70">
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-foreground">
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
                                v-for="req in (recentRequests || []).slice(0, 4)"
                                :key="req.id"
                                class="rounded-2xl border border-border/60 bg-background/60 p-3 hover:bg-background transition-colors flex items-center justify-between gap-3"
                            >
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <p class="text-xs font-bold text-foreground truncate">
                                            {{ req.student_name || user?.name }}
                                        </p>
                                        <span v-if="req.class_name" class="rounded-full bg-muted px-1.5 py-0.2 text-[10px] font-semibold text-muted-foreground">
                                            {{ req.class_name }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-muted-foreground mt-0.5">
                                        Jenis: {{ req.type || 'Konseling' }} • {{ req.preferred_date || 'Segera' }}
                                    </p>
                                </div>

                                <span
                                    class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-bold"
                                    :class="statusStyle[req.status] || 'bg-neutral-100 text-neutral-800'"
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
                    <div class="rounded-3xl border border-[#EBE2D4] dark:border-neutral-800 bg-[#F5EFE6] dark:bg-[#1A1F1D] p-5 sm:p-6 shadow-2xs">
                        <div class="flex items-center gap-4">
                            <div class="size-14 rounded-full bg-white dark:bg-neutral-800 ring-3 ring-[#E2D6C0] dark:ring-neutral-700 flex items-center justify-center font-black text-lg text-foreground shrink-0 shadow-2xs">
                                {{ getInitials(user?.name || 'BK') }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-base font-extrabold text-foreground truncate">
                                    {{ user?.name }}
                                </h4>
                                <p class="text-xs text-muted-foreground truncate">
                                    {{ user?.email }}
                                </p>
                                <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                    <span class="inline-block rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 px-2.5 py-0.5 text-[11px] font-bold">
                                        {{ user?.roles?.[0] || 'User' }}
                                    </span>
                                    <span v-if="student?.class_name" class="inline-block rounded-full bg-white/80 dark:bg-neutral-800 px-2 py-0.5 text-[11px] font-semibold text-foreground">
                                        Kelas {{ student.class_name }}
                                    </span>
                                    <span v-else-if="props.class?.name" class="inline-block rounded-full bg-white/80 dark:bg-neutral-800 px-2 py-0.5 text-[11px] font-semibold text-foreground">
                                        Wali Kelas {{ props.class.name }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Shortcuts Grid -->
                        <div class="mt-5 pt-4 border-t border-[#E5DAC7] dark:border-neutral-800">
                            <h5 class="text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2.5">
                                Akses Cepat Menu
                            </h5>
                            <div class="grid grid-cols-2 gap-2 text-xs font-semibold">
                                <Link
                                    href="/counseling/requests"
                                    class="rounded-xl bg-white dark:bg-card border border-border/80 p-2.5 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors flex items-center gap-2"
                                >
                                    <FileText class="size-3.5 text-rose-600" />
                                    <span>Permohonan</span>
                                </Link>
                                <Link
                                    href="/counseling/sessions"
                                    class="rounded-xl bg-white dark:bg-card border border-border/80 p-2.5 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors flex items-center gap-2"
                                >
                                    <CalendarDays class="size-3.5 text-amber-600" />
                                    <span>Sesi Tatap Muka</span>
                                </Link>
                                <Link
                                    href="/students"
                                    class="rounded-xl bg-white dark:bg-card border border-border/80 p-2.5 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors flex items-center gap-2"
                                >
                                    <GraduationCap class="size-3.5 text-purple-600" />
                                    <span>Data Siswa</span>
                                </Link>
                                <Link
                                    href="/reports"
                                    class="rounded-xl bg-white dark:bg-card border border-border/80 p-2.5 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors flex items-center gap-2"
                                >
                                    <CheckCircle class="size-3.5 text-emerald-600" />
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
