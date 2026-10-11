<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    Calendar,
    CheckCircle,
    CheckCircle2,
    GraduationCap,
    HeartHandshake,
    Lock,
    LogIn,
    MessageSquare,
    School,
    ShieldCheck,
    Sparkles,
    Star,
    TrendingUp,
    Users,
    ArrowRight,
    ArrowUpRight,
    Compass,
    Smile,
    Sun,
    Moon,
    Clock,
    FileText,
    MapPin,
    Phone,
    Mail,
    Globe,
    Building2,
} from '@lucide/vue';
import { dashboard, login } from '@/routes';
import { useAppearance } from '@/composables/useAppearance';

const page = usePage();
const { appearance, updateAppearance } = useAppearance();

// Dynamic school profile from props/share
const school = computed(() => (page.props as any).school || {});
const schoolName = computed(
    () => school.value.school_name || 'SMP Negeri 1 Cerdas Mandiri',
);
const portalName = computed(() => school.value.portal_name || 'Konseling-Yu');
const tagline = computed(
    () => school.value.tagline || 'Portal Bimbingan & Konseling Terpadu',
);
const logoUrl = computed(() => school.value.logo_url || null);
const welcomeMessage = computed(
    () =>
        school.value.welcome_message ||
        'Ajukan sesi bimbingan tatap muka mandiri, pantau rekam prestasi dan kedisiplinan, serta dapatkan pendampingan penuh dari Guru BK dengan jaminan kerahasiaan 100%.',
);
const address = computed(
    () =>
        school.value.address ||
        'Jl. Merdeka No. 45, Kebayoran Baru, Jakarta Selatan',
);
const phone = computed(() => school.value.phone || '(021) 7654321');
const whatsapp = computed(() => school.value.whatsapp || '0812-3456-7890');
const email = computed(
    () => school.value.email || 'info@smpn1cerdasmandiri.sch.id',
);
const website = computed(() => school.value.website || '#');

const services = [
    {
        icon: HeartHandshake,
        title: 'Bimbingan Pribadi',
        description:
            'Membantu memahami potensi diri, mengatasi kecemasan emosional, membangun kepercayaan diri, dan regulasi diri.',
        badge: 'Pribadi',
        badgeClass:
            'bg-white/90 text-rose-950 dark:bg-rose-900/60 dark:text-rose-200',
        cardClass:
            'bg-[#FDE5E5] dark:bg-rose-950/35 border-[#F8CDCD] dark:border-rose-900/40 text-rose-950 dark:text-rose-100',
        iconBg: 'bg-white dark:bg-rose-900/50 text-rose-700 dark:text-rose-300',
    },
    {
        icon: Users,
        title: 'Bimbingan Sosial',
        description:
            'Konsultasi hubungan pertemanan, adaptasi lingkungan sekolah, resolusi konflik, dan komunikasi asertif yang sehat.',
        badge: 'Sosial',
        badgeClass:
            'bg-white/90 text-emerald-950 dark:bg-emerald-900/60 dark:text-emerald-200',
        cardClass:
            'bg-[#DDF4E8] dark:bg-emerald-950/35 border-[#BEE5D0] dark:border-emerald-900/40 text-emerald-950 dark:text-emerald-100',
        iconBg: 'bg-white dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300',
    },
    {
        icon: BookOpen,
        title: 'Bimbingan Belajar',
        description:
            'Strategi belajar efektif, mengatasi kesulitan mata pelajaran, manajemen waktu ujian, dan motivasi berprestasi tinggi.',
        badge: 'Akademik',
        badgeClass:
            'bg-white/90 text-amber-950 dark:bg-amber-900/60 dark:text-amber-200',
        cardClass:
            'bg-[#FDECDA] dark:bg-amber-950/35 border-[#F7D2AB] dark:border-amber-900/40 text-amber-950 dark:text-amber-100',
        iconBg: 'bg-white dark:bg-amber-900/50 text-amber-700 dark:text-amber-300',
    },
    {
        icon: Compass,
        title: 'Bimbingan Karier',
        description:
            'Eksplorasi minat & bakat, persiapan penjurusan jenjang SMA/SMK, pengenalan perguruan tinggi, dan cita-cita masa depan.',
        badge: 'Masa Depan',
        badgeClass:
            'bg-white/90 text-purple-950 dark:bg-purple-900/60 dark:text-purple-200',
        cardClass:
            'bg-[#EFE7FA] dark:bg-purple-950/35 border-[#DDD0F4] dark:border-purple-900/40 text-purple-950 dark:text-purple-100',
        iconBg: 'bg-white dark:bg-purple-900/50 text-purple-700 dark:text-purple-300',
    },
];

const steps = [
    {
        step: '01',
        title: 'Ajukan Permohonan',
        desc: 'Pilih jenis bimbingan, tanggal luang yang diinginkan, dan sampaikan cerita awal secara aman & mandiri.',
        badgeColor:
            'bg-[#FDE5E5] text-rose-950 border border-[#F8CDCD] dark:bg-rose-950/60 dark:text-rose-200 dark:border-rose-900',
    },
    {
        step: '02',
        title: 'Verifikasi & Jadwal',
        desc: 'Guru BK menerima pengajuan dan menetapkan jadwal sesi tatap muka di ruang konseling sekolah.',
        badgeColor:
            'bg-[#FDECDA] text-amber-950 border border-[#F7D2AB] dark:bg-amber-950/60 dark:text-amber-200 dark:border-amber-900',
    },
    {
        step: '03',
        title: 'Sesi Konseling',
        desc: 'Konsultasi tatap muka hangat dan terpercaya dengan jaminan kerahasiaan catatan 100%.',
        badgeColor:
            'bg-[#EFE7FA] text-purple-950 border border-[#DDD0F4] dark:bg-purple-950/60 dark:text-purple-200 dark:border-purple-900',
    },
    {
        step: '04',
        title: 'Solusi & Tindak Lanjut',
        desc: 'Dapatkan pendampingan solutif, rekomendasi perkembangan, dan pemantauan berkala.',
        badgeColor:
            'bg-[#DDF4E8] text-emerald-950 border border-[#BEE5D0] dark:bg-emerald-950/60 dark:text-emerald-200 dark:border-emerald-900',
    },
];

const demoAccounts = [
    {
        role: 'Siswa',
        name: 'Ayu Lestari (Kelas 7A)',
        email: 'siswa@example.com',
        badge: 'bg-[#DDF4E8] text-emerald-950 border border-[#BEE5D0] dark:bg-emerald-950/60 dark:text-emerald-200 dark:border-emerald-800',
    },
    {
        role: 'Guru BK',
        name: 'Dra. Hj. Nurjanah, M.Pd',
        email: 'bk@example.com',
        badge: 'bg-[#EFE7FA] text-purple-950 border border-[#DDD0F4] dark:bg-purple-950/60 dark:text-purple-200 dark:border-purple-800',
    },
    {
        role: 'Wali Kelas',
        name: 'Ahmad Fauzi, S.Pd',
        email: 'walikelas@example.com',
        badge: 'bg-[#FDECDA] text-amber-950 border border-[#F7D2AB] dark:bg-amber-950/60 dark:text-amber-200 dark:border-amber-800',
    },
    {
        role: 'Kepala Sekolah',
        name: 'Drs. Bambang Sutrisno, M.Pd',
        email: 'kepala@example.com',
        badge: 'bg-[#FDE5E5] text-rose-950 border border-[#F8CDCD] dark:bg-rose-950/60 dark:text-rose-200 dark:border-rose-800',
    },
    {
        role: 'Superadmin',
        name: 'Administrator Utama',
        email: 'superadmin@example.com',
        badge: 'bg-[#18181B] text-white dark:bg-white dark:text-neutral-900',
    },
];
</script>

<template>
    <Head
        :title="`${portalName} — Layanan Bimbingan & Konseling ${schoolName}`"
    />

    <div
        class="min-h-screen bg-background text-foreground antialiased selection:bg-[#18181B] selection:text-white dark:selection:bg-white dark:selection:text-neutral-900"
    >
        <!-- Soft Organic Background Accent (Aligned with sage/cream theme) -->
        <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
            <div
                class="absolute -top-32 left-1/2 h-[380px] w-[650px] -translate-x-1/2 rounded-full bg-gradient-to-tr from-emerald-500/10 via-amber-500/10 to-teal-500/5 blur-[120px]"
            ></div>
            <div
                class="absolute top-[38rem] -left-32 h-[320px] w-[420px] rounded-full bg-emerald-500/10 blur-[110px]"
            ></div>
        </div>

        <!-- Sticky Header Navbar -->
        <header
            class="sticky top-0 z-50 border-b border-[#EBE2D4] bg-[#FCFAF7]/85 backdrop-blur-md transition-colors dark:border-neutral-800 dark:bg-[#141816]/85"
        >
            <div
                class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8"
            >
                <!-- Brand Logo (Dynamic School Logo or Iconic BK badge) -->
                <Link href="/" class="group flex items-center gap-3">
                    <div
                        v-if="logoUrl"
                        class="flex aspect-square size-10 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-border bg-white shadow-sm transition-transform duration-200 group-hover:scale-105"
                    >
                        <img
                            :src="logoUrl"
                            :alt="portalName"
                            class="size-full object-cover"
                        />
                    </div>
                    <div
                        v-else
                        class="flex aspect-square size-10 shrink-0 items-center justify-center rounded-2xl bg-[#18181B] text-white shadow-sm transition-transform duration-200 group-hover:scale-105 dark:bg-white dark:text-neutral-900"
                    >
                        <span class="text-base font-extrabold tracking-tighter"
                            >BK</span
                        >
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span
                                class="text-lg font-extrabold tracking-tight text-foreground sm:text-xl"
                            >
                                {{ portalName }}
                            </span>
                        </div>
                        <p
                            class="max-w-[220px] truncate text-[11px] font-medium text-muted-foreground sm:max-w-none"
                        >
                            {{ schoolName }}
                        </p>
                    </div>
                </Link>

                <!-- Navigation Links -->
                <nav
                    class="hidden items-center gap-7 text-sm font-semibold text-muted-foreground md:flex"
                >
                    <a
                        href="#layanan"
                        class="transition-colors hover:text-foreground"
                        >4 Bidang Layanan</a
                    >
                    <a
                        href="#alur"
                        class="transition-colors hover:text-foreground"
                        >Alur Konseling</a
                    >
                    <a
                        href="#keunggulan"
                        class="transition-colors hover:text-foreground"
                        >Keunggulan</a
                    >
                    <a
                        href="#kontak"
                        class="transition-colors hover:text-foreground"
                        >Kontak &amp; Lokasi</a
                    >
                    <a
                        href="#akun-demo"
                        class="transition-colors hover:text-foreground"
                        >Akun Demo</a
                    >
                </nav>

                <!-- Right Side Actions: Theme Switcher & Login CTA -->
                <div class="flex items-center gap-3">
                    <!-- Theme Switcher Pill -->
                    <div
                        class="inline-flex items-center rounded-full border border-[#DFD5C6] bg-[#EAE2D5]/70 p-0.5 shadow-2xs dark:border-neutral-700/60 dark:bg-neutral-800/80"
                    >
                        <button
                            @click="updateAppearance('light')"
                            type="button"
                            class="flex size-7 cursor-pointer items-center justify-center rounded-full transition-all duration-200"
                            :class="
                                appearance === 'light'
                                    ? 'bg-white text-amber-600 shadow-xs ring-1 ring-black/5 dark:bg-neutral-700 dark:text-amber-400'
                                    : 'text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100'
                            "
                            title="Mode Terang"
                        >
                            <Sun class="size-3.5" />
                        </button>
                        <button
                            @click="updateAppearance('dark')"
                            type="button"
                            class="flex size-7 cursor-pointer items-center justify-center rounded-full transition-all duration-200"
                            :class="
                                appearance === 'dark'
                                    ? 'bg-neutral-900 text-neutral-100 shadow-xs ring-1 ring-white/10 dark:bg-neutral-700 dark:text-neutral-100'
                                    : 'text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100'
                            "
                            title="Mode Gelap"
                        >
                            <Moon class="size-3.5" />
                        </button>
                    </div>

                    <!-- Auth Action Button (Matching App's Dark Charcoal Pill Style) -->
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="inline-flex items-center gap-2 rounded-full bg-[#18181B] px-5 py-2 text-xs font-bold text-white shadow-xs transition-colors hover:bg-neutral-800 sm:text-sm dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100"
                    >
                        <GraduationCap class="size-4" />
                        Buka Dashboard
                    </Link>
                    <Link
                        v-else
                        :href="login()"
                        class="inline-flex items-center gap-2 rounded-full bg-[#18181B] px-5 py-2 text-xs font-bold text-white shadow-xs transition-colors hover:bg-neutral-800 sm:text-sm dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100"
                    >
                        <LogIn class="size-4" />
                        Masuk / Log In
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10">
            <!-- Hero Section -->
            <section
                class="mx-auto max-w-7xl px-4 pt-14 pb-18 text-center sm:px-6 lg:px-8 lg:pt-20 lg:pb-24"
            >
                <!-- Tagline Pill with School Name -->
                <div
                    class="mb-6 inline-flex items-center gap-2 rounded-full border border-[#DFD5C6] bg-card px-4 py-1.5 text-xs font-bold text-foreground shadow-2xs dark:border-neutral-700"
                >
                    <Sparkles
                        class="size-3.5 text-amber-600 dark:text-amber-400"
                    />
                    {{ schoolName }} • {{ tagline }}
                </div>

                <h1
                    class="mx-auto max-w-4xl text-4xl leading-[1.15] font-black tracking-tight text-foreground sm:text-5xl sm:leading-[1.15] lg:text-6xl"
                >
                    Ruang Aman &amp; Nyaman untuk Tumbuh, Belajar, dan Meraih
                    <span
                        class="mt-1 inline-block rounded-2xl border border-[#BEE5D0] bg-[#DDF4E8] px-3 py-1 text-emerald-950 dark:border-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-200"
                    >
                        Masa Depan.
                    </span>
                </h1>

                <p
                    class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-muted-foreground sm:text-lg"
                >
                    {{ welcomeMessage }}
                </p>

                <!-- Hero CTA Buttons -->
                <div
                    class="mt-9 flex flex-wrap items-center justify-center gap-3.5"
                >
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="login()"
                        class="inline-flex items-center gap-2 rounded-full bg-[#18181B] px-7 py-3.5 text-sm font-bold text-white shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:bg-neutral-800 sm:text-base dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100"
                    >
                        <LogIn class="size-4.5" />
                        Masuk ke Portal BK
                        <ArrowRight class="ml-1 size-4" />
                    </Link>
                    <Link
                        v-else
                        :href="dashboard()"
                        class="inline-flex items-center gap-2 rounded-full bg-[#18181B] px-7 py-3.5 text-sm font-bold text-white shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:bg-neutral-800 sm:text-base dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100"
                    >
                        <GraduationCap class="size-4.5" />
                        Buka Dashboard Anda
                        <ArrowRight class="ml-1 size-4" />
                    </Link>
                    <a
                        href="#alur"
                        class="inline-flex items-center gap-2 rounded-full border border-[#DFD5C6] bg-card px-6 py-3.5 text-sm font-bold text-foreground shadow-2xs transition-all duration-200 hover:bg-neutral-100 sm:text-base dark:border-neutral-700 dark:hover:bg-neutral-800"
                    >
                        <BookOpen class="size-4 text-muted-foreground" />
                        Pelajari Alur Konseling
                    </a>
                </div>

                <!-- 4 KPI Summary Cards (Identical Palette to Dashboard) -->
                <div
                    class="mx-auto mt-14 grid max-w-5xl grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <!-- KPI 1: Rose (Permohonan & Rahasia) -->
                    <div
                        class="rounded-3xl border border-[#F8CDCD] bg-[#FDE5E5] p-5 text-left shadow-2xs dark:border-rose-900/40 dark:bg-rose-950/35"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-white/90 px-2.5 py-0.5 text-xs font-bold text-rose-950 shadow-2xs dark:bg-rose-900/60 dark:text-rose-200"
                            >
                                <Lock class="size-3.5 text-rose-600" />
                                Privasi Penuh
                            </span>
                            <ArrowUpRight class="size-4 text-rose-700/60" />
                        </div>
                        <div class="mt-4">
                            <div
                                class="text-3xl font-black tracking-tight text-rose-950 dark:text-rose-100"
                            >
                                100%
                            </div>
                            <p
                                class="mt-1 text-xs font-semibold text-rose-900/80 dark:text-rose-300"
                            >
                                Catatan Konseling Rahasia &amp; Aman
                            </p>
                        </div>
                    </div>

                    <!-- KPI 2: Peach (Sesi Terjadwal) -->
                    <div
                        class="rounded-3xl border border-[#F7D2AB] bg-[#FDECDA] p-5 text-left shadow-2xs dark:border-amber-900/40 dark:bg-amber-950/35"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-white/90 px-2.5 py-0.5 text-xs font-bold text-amber-950 shadow-2xs dark:bg-amber-900/60 dark:text-amber-200"
                            >
                                <Calendar class="size-3.5 text-amber-600" />
                                Tatap Muka
                            </span>
                            <ArrowUpRight class="size-4 text-amber-700/60" />
                        </div>
                        <div class="mt-4">
                            <div
                                class="text-3xl font-black tracking-tight text-amber-950 dark:text-amber-100"
                            >
                                Fleksibel
                            </div>
                            <p
                                class="mt-1 text-xs font-semibold text-amber-900/80 dark:text-amber-300"
                            >
                                Pilih Tanggal &amp; Jam Luang Sesuai Kebutuhan
                            </p>
                        </div>
                    </div>

                    <!-- KPI 3: Lavender (Bidang Layanan) -->
                    <div
                        class="rounded-3xl border border-[#DDD0F4] bg-[#EFE7FA] p-5 text-left shadow-2xs dark:border-purple-900/40 dark:bg-purple-950/35"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-white/90 px-2.5 py-0.5 text-xs font-bold text-purple-950 shadow-2xs dark:bg-purple-900/60 dark:text-purple-200"
                            >
                                <HeartHandshake
                                    class="size-3.5 text-purple-600"
                                />
                                4 Bidang
                            </span>
                            <ArrowUpRight class="size-4 text-purple-700/60" />
                        </div>
                        <div class="mt-4">
                            <div
                                class="text-3xl font-black tracking-tight text-purple-950 dark:text-purple-100"
                            >
                                Holistik
                            </div>
                            <p
                                class="mt-1 text-xs font-semibold text-purple-900/80 dark:text-purple-300"
                            >
                                Pribadi, Sosial, Belajar, &amp; Karier
                            </p>
                        </div>
                    </div>

                    <!-- KPI 4: Mint (Karakter & Prestasi) -->
                    <div
                        class="rounded-3xl border border-[#BEE5D0] bg-[#DDF4E8] p-5 text-left shadow-2xs dark:border-emerald-900/40 dark:bg-emerald-950/35"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-white/90 px-2.5 py-0.5 text-xs font-bold text-emerald-950 shadow-2xs dark:bg-emerald-900/60 dark:text-emerald-200"
                            >
                                <CheckCircle
                                    class="size-3.5 text-emerald-600"
                                />
                                Terpadu
                            </span>
                            <ArrowUpRight class="size-4 text-emerald-700/60" />
                        </div>
                        <div class="mt-4">
                            <div
                                class="text-3xl font-black tracking-tight text-emerald-950 dark:text-emerald-100"
                            >
                                RBAC
                            </div>
                            <p
                                class="mt-1 text-xs font-semibold text-emerald-900/80 dark:text-emerald-300"
                            >
                                Hak Akses Berlapis: Siswa, Guru, Kepala
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4 Services Pillars (Pastel Cards Matching Theme) -->
            <section
                id="layanan"
                class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8"
            >
                <div class="mx-auto mb-12 max-w-2xl text-center">
                    <span
                        class="text-xs font-bold tracking-wider text-muted-foreground uppercase"
                        >Bidang Konseling</span
                    >
                    <h2
                        class="mt-1 text-3xl font-extrabold tracking-tight text-foreground"
                    >
                        4 Pilar Layanan Bimbingan Siswa
                    </h2>
                    <p class="mt-2 text-sm text-muted-foreground">
                        Fasilitas pendampingan holistik untuk mendukung
                        perkembangan karakter, kesehatan mental, dan prestasi
                        belajar.
                    </p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="service in services"
                        :key="service.title"
                        class="flex flex-col justify-between rounded-3xl border p-6 shadow-2xs transition-all duration-200 hover:-translate-y-1 hover:shadow-md"
                        :class="service.cardClass"
                    >
                        <div>
                            <div
                                class="mb-5 flex size-12 items-center justify-center rounded-2xl shadow-2xs"
                                :class="service.iconBg"
                            >
                                <component :is="service.icon" class="size-6" />
                            </div>
                            <span
                                class="rounded-full px-2.5 py-0.5 text-[10px] font-bold shadow-2xs"
                                :class="service.badgeClass"
                            >
                                {{ service.badge }}
                            </span>
                            <h3 class="mt-3 text-lg leading-snug font-bold">
                                {{ service.title }}
                            </h3>
                            <p class="mt-2 text-xs leading-relaxed opacity-85">
                                {{ service.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- How It Works Flow -->
            <section
                id="alur"
                class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8"
            >
                <div
                    class="rounded-3xl border border-[#EBE2D4] bg-card p-8 shadow-2xs sm:p-12 dark:border-neutral-800"
                >
                    <div class="mx-auto mb-12 max-w-2xl text-center">
                        <span
                            class="text-xs font-bold tracking-wider text-muted-foreground uppercase"
                            >Langkah Mudah</span
                        >
                        <h2
                            class="mt-1 text-3xl font-extrabold tracking-tight text-foreground"
                        >
                            Alur Layanan Konseling Sekolah
                        </h2>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Mulai dari pengajuan mandiri hingga sesi tatap muka
                            dan tindak lanjut yang terstruktur.
                        </p>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div
                            v-for="item in steps"
                            :key="item.step"
                            class="rounded-2xl border border-border/80 bg-background/50 p-5 shadow-2xs"
                        >
                            <div class="mb-3 flex items-center gap-2.5">
                                <span
                                    class="rounded-full px-2.5 py-0.5 text-xs font-black shadow-2xs"
                                    :class="item.badgeColor"
                                >
                                    {{ item.step }}
                                </span>
                                <h3 class="text-sm font-bold text-foreground">
                                    {{ item.title }}
                                </h3>
                            </div>
                            <p
                                class="text-xs leading-relaxed text-muted-foreground"
                            >
                                {{ item.desc }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Key Features Showcase & Realistic Dashboard Mockup -->
            <section
                id="keunggulan"
                class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8"
            >
                <div class="grid items-center gap-10 lg:grid-cols-12">
                    <!-- Left: Feature Bullet Points (7 cols) -->
                    <div class="space-y-6 lg:col-span-7">
                        <div>
                            <span
                                class="text-xs font-bold tracking-wider text-muted-foreground uppercase"
                                >Keunggulan Sistem</span
                            >
                            <h2
                                class="mt-1 text-3xl font-extrabold tracking-tight text-foreground sm:text-4xl"
                            >
                                Dirancang Khusus untuk Sekolah Modern yang
                                Peduli Karakter
                            </h2>
                            <p
                                class="mt-3 text-sm leading-relaxed text-muted-foreground"
                            >
                                Menggabungkan kemudahan interaksi siswa,
                                penjadwalan efisien untuk Guru BK, dan
                                transparansi laporan bagi Wali Kelas serta
                                Kepala Sekolah.
                            </p>
                        </div>

                        <div class="space-y-4 pt-2">
                            <div
                                class="flex items-start gap-3.5 rounded-2xl border border-[#EBE2D4] bg-card p-4 shadow-2xs dark:border-neutral-800"
                            >
                                <div
                                    class="shrink-0 rounded-xl border border-[#F8CDCD] bg-[#FDE5E5] p-2 text-rose-800 dark:border-rose-900 dark:bg-rose-950/60 dark:text-rose-200"
                                >
                                    <Lock class="size-5" />
                                </div>
                                <div>
                                    <h4
                                        class="text-sm font-bold text-foreground"
                                    >
                                        Jaminan Kerahasiaan Catatan Konseling
                                    </h4>
                                    <p
                                        class="mt-0.5 text-xs text-muted-foreground"
                                    >
                                        Catatan medis permasalahan dan diagnosis
                                        hanya dapat dibuka oleh Guru BK yang
                                        memiliki izin rahasia (<code
                                            class="font-mono text-[11px]"
                                            >counseling.view_private</code
                                        >).
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex items-start gap-3.5 rounded-2xl border border-[#EBE2D4] bg-card p-4 shadow-2xs dark:border-neutral-800"
                            >
                                <div
                                    class="shrink-0 rounded-xl border border-[#DDD0F4] bg-[#EFE7FA] p-2 text-purple-800 dark:border-purple-900 dark:bg-purple-950/60 dark:text-purple-200"
                                >
                                    <ShieldCheck class="size-5" />
                                </div>
                                <div>
                                    <h4
                                        class="text-sm font-bold text-foreground"
                                    >
                                        Hak Akses Fleksibel &amp; Audit Trail
                                    </h4>
                                    <p
                                        class="mt-0.5 text-xs text-muted-foreground"
                                    >
                                        Matriks izin granular terintegrasi log
                                        aktivitas yang mencatat seluruh aksi
                                        penting secara akuntabel.
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex items-start gap-3.5 rounded-2xl border border-[#EBE2D4] bg-card p-4 shadow-2xs dark:border-neutral-800"
                            >
                                <div
                                    class="shrink-0 rounded-xl border border-[#BEE5D0] bg-[#DDF4E8] p-2 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-200"
                                >
                                    <FileText class="size-5" />
                                </div>
                                <div>
                                    <h4
                                        class="text-sm font-bold text-foreground"
                                    >
                                        Ekspor Laporan PDF Resmi Siap Cetak
                                    </h4>
                                    <p
                                        class="mt-0.5 text-xs text-muted-foreground"
                                    >
                                        Format laporan komprehensif lengkap
                                        dengan kop resmi sekolah untuk rapat
                                        evaluasi dan arsip berkas dinas.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: UI Dashboard Live Preview Card (5 cols) -->
                    <div class="lg:col-span-5">
                        <div
                            class="space-y-4 rounded-3xl border border-[#EBE2D4] bg-card p-5 shadow-md sm:p-6 dark:border-neutral-800"
                        >
                            <!-- Mini Header -->
                            <div
                                class="flex items-center justify-between border-b border-border/70 pb-3"
                            >
                                <div class="flex items-center gap-2">
                                    <div
                                        v-if="logoUrl"
                                        class="flex size-8 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-border bg-white shadow-2xs"
                                    >
                                        <img
                                            :src="logoUrl"
                                            :alt="portalName"
                                            class="size-full object-cover"
                                        />
                                    </div>
                                    <div
                                        v-else
                                        class="flex size-8 shrink-0 items-center justify-center rounded-xl bg-[#18181B] text-xs font-bold text-white dark:bg-white dark:text-neutral-900"
                                    >
                                        BK
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="truncate text-xs font-bold text-foreground"
                                        >
                                            {{ portalName }}
                                        </p>
                                        <p
                                            class="truncate text-[10px] text-muted-foreground"
                                        >
                                            {{ schoolName }}
                                        </p>
                                    </div>
                                </div>
                                <span
                                    class="rounded-full border border-[#BEE5D0] bg-[#DDF4E8] px-2 py-0.5 text-[10px] font-bold text-emerald-950 dark:border-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-200"
                                >
                                    Aktif
                                </span>
                            </div>

                            <!-- Mini Request Card -->
                            <div
                                class="rounded-2xl border border-[#F7D2AB] bg-[#FDECDA]/80 p-3.5 dark:border-amber-900/40 dark:bg-amber-950/30"
                            >
                                <div class="flex items-center justify-between">
                                    <span
                                        class="rounded-full bg-white/80 px-2 py-0.5 text-[10px] font-bold text-amber-950 dark:bg-amber-900/60 dark:text-amber-200"
                                    >
                                        Bimbingan Belajar
                                    </span>
                                    <span
                                        class="text-[10px] font-semibold text-amber-900 dark:text-amber-300"
                                    >
                                        Besok, 09:30 WIB
                                    </span>
                                </div>
                                <p
                                    class="mt-2 text-xs font-bold text-foreground"
                                >
                                    Persiapan Ujian &amp; Konsentrasi Belajar
                                </p>
                                <p
                                    class="mt-0.5 text-[11px] text-muted-foreground"
                                >
                                    Ruang Konseling BK 1 • Dra. Hj. Nurjanah,
                                    M.Pd
                                </p>
                            </div>

                            <!-- Mini Points Grid -->
                            <div class="grid grid-cols-2 gap-2.5">
                                <div
                                    class="rounded-2xl border border-[#BEE5D0] bg-[#DDF4E8]/80 p-3 dark:border-emerald-900/40 dark:bg-emerald-950/30"
                                >
                                    <p
                                        class="text-[10px] font-bold text-emerald-900 dark:text-emerald-300"
                                    >
                                        Poin Prestasi
                                    </p>
                                    <p
                                        class="mt-1 text-xl font-black text-emerald-950 dark:text-emerald-100"
                                    >
                                        +35 Pts
                                    </p>
                                    <p
                                        class="mt-0.5 text-[10px] text-emerald-800 dark:text-emerald-400"
                                    >
                                        Juara Lomba Tari
                                    </p>
                                </div>

                                <div
                                    class="rounded-2xl border border-[#F8CDCD] bg-[#FDE5E5]/80 p-3 dark:border-rose-900/40 dark:bg-rose-950/30"
                                >
                                    <p
                                        class="text-[10px] font-bold text-rose-900 dark:text-rose-300"
                                    >
                                        Poin Tata Tertib
                                    </p>
                                    <p
                                        class="mt-1 text-xl font-black text-rose-950 dark:text-rose-100"
                                    >
                                        0 Pts
                                    </p>
                                    <p
                                        class="mt-0.5 text-[10px] text-rose-800 dark:text-rose-400"
                                    >
                                        Disiplin Sangat Baik
                                    </p>
                                </div>
                            </div>

                            <!-- Action button inside mockup -->
                            <div class="pt-1">
                                <Link
                                    :href="login()"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-[#18181B] py-2.5 text-xs font-bold text-white shadow-2xs transition-colors hover:bg-neutral-800 dark:bg-white dark:text-neutral-900"
                                >
                                    Mulai Bimbingan Sekarang
                                    <ArrowRight class="size-3.5" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION: Kontak & Lokasi Resmi Sekolah -->
            <section
                id="kontak"
                class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8"
            >
                <div
                    class="rounded-3xl border border-[#EBE2D4] bg-card p-6 shadow-2xs sm:p-10 dark:border-neutral-800"
                >
                    <div class="mb-8 max-w-2xl">
                        <span
                            class="text-xs font-bold tracking-wider text-muted-foreground uppercase"
                            >Hubungi Kami</span
                        >
                        <h2
                            class="mt-1 text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl"
                        >
                            Kontak &amp; Informasi Layanan {{ schoolName }}
                        </h2>
                        <p
                            class="mt-2 text-xs text-muted-foreground sm:text-sm"
                        >
                            Punya pertanyaan seputar layanan bimbingan konseling
                            atau kunjungan konsultasi orang tua? Hubungi kami
                            melalui kanal resmi berikut.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Card 1: Alamat -->
                        <div
                            class="flex flex-col justify-between rounded-2xl border border-border/80 bg-background/60 p-4.5 shadow-2xs"
                        >
                            <div>
                                <div
                                    class="mb-3 flex size-9 items-center justify-center rounded-xl bg-[#EAE2D5]/70 text-foreground shadow-2xs dark:bg-neutral-800"
                                >
                                    <MapPin class="size-4.5" />
                                </div>
                                <h4 class="text-xs font-bold text-foreground">
                                    Alamat Sekolah
                                </h4>
                                <p
                                    class="mt-1.5 text-xs leading-relaxed text-muted-foreground"
                                >
                                    {{ address }}
                                </p>
                            </div>
                        </div>

                        <!-- Card 2: Telepon & Jam Kerja -->
                        <div
                            class="flex flex-col justify-between rounded-2xl border border-border/80 bg-background/60 p-4.5 shadow-2xs"
                        >
                            <div>
                                <div
                                    class="mb-3 flex size-9 items-center justify-center rounded-xl bg-[#FDECDA] text-amber-800 shadow-2xs dark:bg-amber-950/60 dark:text-amber-200"
                                >
                                    <Phone class="size-4.5" />
                                </div>
                                <h4 class="text-xs font-bold text-foreground">
                                    Telepon Kantor
                                </h4>
                                <p
                                    class="mt-1.5 font-mono text-xs font-bold text-foreground"
                                >
                                    {{ phone }}
                                </p>
                                <p
                                    class="mt-1 text-[11px] text-muted-foreground"
                                >
                                    Senin – Jumat: 07.30 – 15.00 WIB
                                </p>
                            </div>
                        </div>

                        <!-- Card 3: WhatsApp Layanan BK -->
                        <div
                            class="flex flex-col justify-between rounded-2xl border border-border/80 bg-background/60 p-4.5 shadow-2xs"
                        >
                            <div>
                                <div
                                    class="mb-3 flex size-9 items-center justify-center rounded-xl bg-[#DDF4E8] text-emerald-800 shadow-2xs dark:bg-emerald-950/60 dark:text-emerald-200"
                                >
                                    <MessageSquare class="size-4.5" />
                                </div>
                                <h4 class="text-xs font-bold text-foreground">
                                    WhatsApp Konseling
                                </h4>
                                <p
                                    class="mt-1.5 font-mono text-xs font-bold text-emerald-700 dark:text-emerald-400"
                                >
                                    {{ whatsapp }}
                                </p>
                                <p
                                    class="mt-1 text-[11px] text-muted-foreground"
                                >
                                    Layanan respon cepat seputar jadwal
                                    bimbingan
                                </p>
                            </div>
                        </div>

                        <!-- Card 4: Email & Website -->
                        <div
                            class="flex flex-col justify-between rounded-2xl border border-border/80 bg-background/60 p-4.5 shadow-2xs"
                        >
                            <div>
                                <div
                                    class="mb-3 flex size-9 items-center justify-center rounded-xl bg-[#EFE7FA] text-purple-800 shadow-2xs dark:bg-purple-950/60 dark:text-purple-200"
                                >
                                    <Mail class="size-4.5" />
                                </div>
                                <h4 class="text-xs font-bold text-foreground">
                                    Email &amp; Website
                                </h4>
                                <p
                                    class="mt-1.5 truncate font-mono text-xs text-muted-foreground"
                                >
                                    {{ email }}
                                </p>
                                <a
                                    v-if="website && website !== '#'"
                                    :href="website"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="mt-2 inline-flex items-center gap-1 text-[11px] font-bold text-foreground hover:underline"
                                >
                                    <Globe class="size-3" />
                                    Buka Website Sekolah
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Demo Accounts Panel -->
            <section
                id="akun-demo"
                class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8"
            >
                <div
                    class="rounded-3xl border border-[#EBE2D4] bg-card p-6 shadow-2xs sm:p-8 dark:border-neutral-800"
                >
                    <div
                        class="mb-6 flex flex-col justify-between gap-4 border-b border-border/70 pb-5 md:flex-row md:items-center"
                    >
                        <div>
                            <div class="flex items-center gap-2">
                                <span
                                    class="rounded-full bg-[#18181B] px-2.5 py-0.5 text-[10px] font-black text-white dark:bg-white dark:text-neutral-900"
                                >
                                    AKUN UJI COBA
                                </span>
                                <h3 class="text-lg font-bold text-foreground">
                                    Kredensial Demo Bawaan
                                </h3>
                            </div>
                            <p class="mt-1 text-xs text-muted-foreground">
                                Seluruh akun demo dapat digunakan langsung untuk
                                menguji sistem. Kata sandi default adalah:
                                <code
                                    class="rounded-md bg-[#EAE2D5]/70 px-2 py-0.5 font-mono font-bold text-foreground dark:bg-neutral-800"
                                    >password</code
                                >
                            </p>
                        </div>
                        <Link
                            :href="login()"
                            class="inline-flex shrink-0 items-center gap-2 rounded-full bg-[#18181B] px-4.5 py-2 text-xs font-bold text-white shadow-2xs transition-colors hover:bg-neutral-800 dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100"
                        >
                            <LogIn class="size-3.5" />
                            Buka Halaman Login
                        </Link>
                    </div>

                    <div class="grid gap-3.5 sm:grid-cols-2 lg:grid-cols-5">
                        <div
                            v-for="acc in demoAccounts"
                            :key="acc.role"
                            class="flex flex-col justify-between rounded-2xl border border-border/80 bg-background/60 p-4 shadow-2xs"
                        >
                            <div>
                                <div
                                    class="mb-2 flex items-center justify-between"
                                >
                                    <span
                                        class="inline-block rounded-full px-2.5 py-0.5 text-[10px] font-black tracking-wide"
                                        :class="acc.badge"
                                    >
                                        {{ acc.role }}
                                    </span>
                                </div>
                                <p
                                    class="truncate text-xs font-bold text-foreground"
                                >
                                    {{ acc.name }}
                                </p>
                                <p
                                    class="mt-1 font-mono text-xs break-all text-muted-foreground select-all"
                                >
                                    {{ acc.email }}
                                </p>
                            </div>
                            <div
                                class="mt-3 flex items-center justify-between border-t border-border/60 pt-2.5 text-[11px] text-muted-foreground"
                            >
                                <span>Kata Sandi:</span>
                                <span
                                    class="font-mono font-bold text-foreground"
                                    >password</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer
            class="mt-16 border-t border-[#EBE2D4] bg-[#FCFAF7] py-12 transition-colors dark:border-neutral-800 dark:bg-[#141816]"
        >
            <div
                class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 px-4 text-xs text-muted-foreground sm:px-6 md:flex-row lg:px-8"
            >
                <div class="flex items-center gap-3">
                    <div
                        v-if="logoUrl"
                        class="flex aspect-square size-9 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-border bg-white shadow-sm"
                    >
                        <img
                            :src="logoUrl"
                            :alt="portalName"
                            class="size-full object-cover"
                        />
                    </div>
                    <div
                        v-else
                        class="flex aspect-square size-9 shrink-0 items-center justify-center rounded-2xl bg-[#18181B] text-sm font-extrabold tracking-tighter text-white shadow-sm dark:bg-white dark:text-neutral-900"
                    >
                        BK
                    </div>
                    <div>
                        <p class="text-sm font-bold text-foreground">
                            {{ portalName }} • {{ schoolName }}
                        </p>
                        <p class="text-[11px] text-muted-foreground">
                            {{ address }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-6 font-semibold">
                    <Link
                        :href="login()"
                        class="transition-colors hover:text-foreground"
                        >Masuk / Login</Link
                    >
                    <a
                        href="#layanan"
                        class="transition-colors hover:text-foreground"
                        >4 Layanan</a
                    >
                    <a
                        href="#alur"
                        class="transition-colors hover:text-foreground"
                        >Alur Bimbingan</a
                    >
                    <a
                        href="#kontak"
                        class="transition-colors hover:text-foreground"
                        >Kontak</a
                    >
                    <a
                        href="#akun-demo"
                        class="transition-colors hover:text-foreground"
                        >Akun Demo</a
                    >
                </div>

                <p class="text-center md:text-right">
                    &copy; {{ new Date().getFullYear() }} {{ schoolName }}.
                </p>
            </div>
        </footer>
    </div>
</template>
