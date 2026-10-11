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
const schoolName = computed(() => school.value.school_name || 'SMP Negeri 1 Cerdas Mandiri');
const portalName = computed(() => school.value.portal_name || 'Konseling-Yu');
const tagline = computed(() => school.value.tagline || 'Portal Bimbingan & Konseling Terpadu');
const logoUrl = computed(() => school.value.logo_url || null);
const welcomeMessage = computed(
    () =>
        school.value.welcome_message ||
        'Ajukan sesi bimbingan tatap muka mandiri, pantau rekam prestasi dan kedisiplinan, serta dapatkan pendampingan penuh dari Guru BK dengan jaminan kerahasiaan 100%.'
);
const address = computed(() => school.value.address || 'Jl. Merdeka No. 45, Kebayoran Baru, Jakarta Selatan');
const phone = computed(() => school.value.phone || '(021) 7654321');
const whatsapp = computed(() => school.value.whatsapp || '0812-3456-7890');
const email = computed(() => school.value.email || 'info@smpn1cerdasmandiri.sch.id');
const website = computed(() => school.value.website || '#');

const services = [
    {
        icon: HeartHandshake,
        title: 'Bimbingan Pribadi',
        description: 'Membantu memahami potensi diri, mengatasi kecemasan emosional, membangun kepercayaan diri, dan regulasi diri.',
        badge: 'Pribadi',
        badgeClass: 'bg-white/90 text-rose-950 dark:bg-rose-900/60 dark:text-rose-200',
        cardClass: 'bg-[#FDE5E5] dark:bg-rose-950/35 border-[#F8CDCD] dark:border-rose-900/40 text-rose-950 dark:text-rose-100',
        iconBg: 'bg-white dark:bg-rose-900/50 text-rose-700 dark:text-rose-300',
    },
    {
        icon: Users,
        title: 'Bimbingan Sosial',
        description: 'Konsultasi hubungan pertemanan, adaptasi lingkungan sekolah, resolusi konflik, dan komunikasi asertif yang sehat.',
        badge: 'Sosial',
        badgeClass: 'bg-white/90 text-emerald-950 dark:bg-emerald-900/60 dark:text-emerald-200',
        cardClass: 'bg-[#DDF4E8] dark:bg-emerald-950/35 border-[#BEE5D0] dark:border-emerald-900/40 text-emerald-950 dark:text-emerald-100',
        iconBg: 'bg-white dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300',
    },
    {
        icon: BookOpen,
        title: 'Bimbingan Belajar',
        description: 'Strategi belajar efektif, mengatasi kesulitan mata pelajaran, manajemen waktu ujian, dan motivasi berprestasi tinggi.',
        badge: 'Akademik',
        badgeClass: 'bg-white/90 text-amber-950 dark:bg-amber-900/60 dark:text-amber-200',
        cardClass: 'bg-[#FDECDA] dark:bg-amber-950/35 border-[#F7D2AB] dark:border-amber-900/40 text-amber-950 dark:text-amber-100',
        iconBg: 'bg-white dark:bg-amber-900/50 text-amber-700 dark:text-amber-300',
    },
    {
        icon: Compass,
        title: 'Bimbingan Karier',
        description: 'Eksplorasi minat & bakat, persiapan penjurusan jenjang SMA/SMK, pengenalan perguruan tinggi, dan cita-cita masa depan.',
        badge: 'Masa Depan',
        badgeClass: 'bg-white/90 text-purple-950 dark:bg-purple-900/60 dark:text-purple-200',
        cardClass: 'bg-[#EFE7FA] dark:bg-purple-950/35 border-[#DDD0F4] dark:border-purple-900/40 text-purple-950 dark:text-purple-100',
        iconBg: 'bg-white dark:bg-purple-900/50 text-purple-700 dark:text-purple-300',
    },
];

const steps = [
    {
        step: '01',
        title: 'Ajukan Permohonan',
        desc: 'Pilih jenis bimbingan, tanggal luang yang diinginkan, dan sampaikan cerita awal secara aman & mandiri.',
        badgeColor: 'bg-[#FDE5E5] text-rose-950 border border-[#F8CDCD] dark:bg-rose-950/60 dark:text-rose-200 dark:border-rose-900',
    },
    {
        step: '02',
        title: 'Verifikasi & Jadwal',
        desc: 'Guru BK menerima pengajuan dan menetapkan jadwal sesi tatap muka di ruang konseling sekolah.',
        badgeColor: 'bg-[#FDECDA] text-amber-950 border border-[#F7D2AB] dark:bg-amber-950/60 dark:text-amber-200 dark:border-amber-900',
    },
    {
        step: '03',
        title: 'Sesi Konseling',
        desc: 'Konsultasi tatap muka hangat dan terpercaya dengan jaminan kerahasiaan catatan 100%.',
        badgeColor: 'bg-[#EFE7FA] text-purple-950 border border-[#DDD0F4] dark:bg-purple-950/60 dark:text-purple-200 dark:border-purple-900',
    },
    {
        step: '04',
        title: 'Solusi & Tindak Lanjut',
        desc: 'Dapatkan pendampingan solutif, rekomendasi perkembangan, dan pemantauan berkala.',
        badgeColor: 'bg-[#DDF4E8] text-emerald-950 border border-[#BEE5D0] dark:bg-emerald-950/60 dark:text-emerald-200 dark:border-emerald-900',
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
    <Head :title="`${portalName} — Layanan Bimbingan & Konseling ${schoolName}`" />

    <div class="min-h-screen bg-background text-foreground antialiased selection:bg-[#18181B] selection:text-white dark:selection:bg-white dark:selection:text-neutral-900">
        <!-- Soft Organic Background Accent (Aligned with sage/cream theme) -->
        <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
            <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[650px] h-[380px] bg-gradient-to-tr from-emerald-500/10 via-amber-500/10 to-teal-500/5 blur-[120px] rounded-full"></div>
            <div class="absolute top-[38rem] -left-32 w-[420px] h-[320px] bg-emerald-500/10 blur-[110px] rounded-full"></div>
        </div>

        <!-- Sticky Header Navbar -->
        <header class="sticky top-0 z-50 backdrop-blur-md bg-[#FCFAF7]/85 dark:bg-[#141816]/85 border-b border-[#EBE2D4] dark:border-neutral-800 transition-colors">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <!-- Brand Logo (Dynamic School Logo or Iconic BK badge) -->
                <Link href="/" class="flex items-center gap-3 group">
                    <div
                        v-if="logoUrl"
                        class="flex aspect-square size-10 items-center justify-center rounded-2xl border border-border bg-white shadow-sm overflow-hidden shrink-0 group-hover:scale-105 transition-transform duration-200"
                    >
                        <img :src="logoUrl" :alt="portalName" class="size-full object-cover" />
                    </div>
                    <div
                        v-else
                        class="flex aspect-square size-10 items-center justify-center rounded-2xl bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 shadow-sm group-hover:scale-105 transition-transform duration-200 shrink-0"
                    >
                        <span class="text-base font-extrabold tracking-tighter">BK</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-lg sm:text-xl tracking-tight text-foreground">
                                {{ portalName }}
                            </span>
                        </div>
                        <p class="text-[11px] text-muted-foreground font-medium truncate max-w-[220px] sm:max-w-none">
                            {{ schoolName }}
                        </p>
                    </div>
                </Link>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-muted-foreground">
                    <a href="#layanan" class="hover:text-foreground transition-colors">4 Bidang Layanan</a>
                    <a href="#alur" class="hover:text-foreground transition-colors">Alur Konseling</a>
                    <a href="#keunggulan" class="hover:text-foreground transition-colors">Keunggulan</a>
                    <a href="#kontak" class="hover:text-foreground transition-colors">Kontak &amp; Lokasi</a>
                    <a href="#akun-demo" class="hover:text-foreground transition-colors">Akun Demo</a>
                </nav>

                <!-- Right Side Actions: Theme Switcher & Login CTA -->
                <div class="flex items-center gap-3">
                    <!-- Theme Switcher Pill -->
                    <div class="inline-flex items-center rounded-full bg-[#EAE2D5]/70 dark:bg-neutral-800/80 p-0.5 border border-[#DFD5C6] dark:border-neutral-700/60 shadow-2xs">
                        <button
                            @click="updateAppearance('light')"
                            type="button"
                            class="flex size-7 items-center justify-center rounded-full transition-all duration-200 cursor-pointer"
                            :class="appearance === 'light'
                                ? 'bg-white text-amber-600 shadow-xs ring-1 ring-black/5 dark:bg-neutral-700 dark:text-amber-400'
                                : 'text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100'"
                            title="Mode Terang"
                        >
                            <Sun class="size-3.5" />
                        </button>
                        <button
                            @click="updateAppearance('dark')"
                            type="button"
                            class="flex size-7 items-center justify-center rounded-full transition-all duration-200 cursor-pointer"
                            :class="appearance === 'dark'
                                ? 'bg-neutral-900 text-neutral-100 shadow-xs ring-1 ring-white/10 dark:bg-neutral-700 dark:text-neutral-100'
                                : 'text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-100'"
                            title="Mode Gelap"
                        >
                            <Moon class="size-3.5" />
                        </button>
                    </div>

                    <!-- Auth Action Button (Matching App's Dark Charcoal Pill Style) -->
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="inline-flex items-center gap-2 rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 px-5 py-2 text-xs sm:text-sm font-bold shadow-xs hover:bg-neutral-800 dark:hover:bg-neutral-100 transition-colors"
                    >
                        <GraduationCap class="size-4" />
                        Buka Dashboard
                    </Link>
                    <Link
                        v-else
                        :href="login()"
                        class="inline-flex items-center gap-2 rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 px-5 py-2 text-xs sm:text-sm font-bold shadow-xs hover:bg-neutral-800 dark:hover:bg-neutral-100 transition-colors"
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
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-18 lg:pt-20 lg:pb-24 text-center">
                <!-- Tagline Pill with School Name -->
                <div class="inline-flex items-center gap-2 rounded-full border border-[#DFD5C6] dark:border-neutral-700 bg-card px-4 py-1.5 text-xs font-bold text-foreground shadow-2xs mb-6">
                    <Sparkles class="size-3.5 text-amber-600 dark:text-amber-400" />
                    {{ schoolName }} • {{ tagline }}
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-foreground max-w-4xl mx-auto leading-[1.15] sm:leading-[1.15]">
                    Ruang Aman &amp; Nyaman untuk Tumbuh, Belajar, dan Meraih
                    <span class="inline-block px-3 py-1 mt-1 rounded-2xl bg-[#DDF4E8] dark:bg-emerald-950/60 text-emerald-950 dark:text-emerald-200 border border-[#BEE5D0] dark:border-emerald-800">
                        Masa Depan.
                    </span>
                </h1>

                <p class="mt-6 text-base sm:text-lg text-muted-foreground max-w-2xl mx-auto leading-relaxed">
                    {{ welcomeMessage }}
                </p>

                <!-- Hero CTA Buttons -->
                <div class="mt-9 flex flex-wrap items-center justify-center gap-3.5">
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="login()"
                        class="inline-flex items-center gap-2 rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 px-7 py-3.5 text-sm sm:text-base font-bold shadow-xs hover:bg-neutral-800 dark:hover:bg-neutral-100 hover:-translate-y-0.5 transition-all duration-200"
                    >
                        <LogIn class="size-4.5" />
                        Masuk ke Portal BK
                        <ArrowRight class="size-4 ml-1" />
                    </Link>
                    <Link
                        v-else
                        :href="dashboard()"
                        class="inline-flex items-center gap-2 rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 px-7 py-3.5 text-sm sm:text-base font-bold shadow-xs hover:bg-neutral-800 dark:hover:bg-neutral-100 hover:-translate-y-0.5 transition-all duration-200"
                    >
                        <GraduationCap class="size-4.5" />
                        Buka Dashboard Anda
                        <ArrowRight class="size-4 ml-1" />
                    </Link>
                    <a
                        href="#alur"
                        class="inline-flex items-center gap-2 rounded-full border border-[#DFD5C6] dark:border-neutral-700 bg-card px-6 py-3.5 text-sm sm:text-base font-bold text-foreground hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-all duration-200 shadow-2xs"
                    >
                        <BookOpen class="size-4 text-muted-foreground" />
                        Pelajari Alur Konseling
                    </a>
                </div>

                <!-- 4 KPI Summary Cards (Identical Palette to Dashboard) -->
                <div class="mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 max-w-5xl mx-auto">
                    <!-- KPI 1: Rose (Permohonan & Rahasia) -->
                    <div class="rounded-3xl bg-[#FDE5E5] dark:bg-rose-950/35 border border-[#F8CDCD] dark:border-rose-900/40 p-5 text-left shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 dark:bg-rose-900/60 px-2.5 py-0.5 text-xs font-bold text-rose-950 dark:text-rose-200 shadow-2xs">
                                <Lock class="size-3.5 text-rose-600" />
                                Privasi Penuh
                            </span>
                            <ArrowUpRight class="size-4 text-rose-700/60" />
                        </div>
                        <div class="mt-4">
                            <div class="text-3xl font-black text-rose-950 dark:text-rose-100 tracking-tight">100%</div>
                            <p class="text-xs font-semibold text-rose-900/80 dark:text-rose-300 mt-1">
                                Catatan Konseling Rahasia &amp; Aman
                            </p>
                        </div>
                    </div>

                    <!-- KPI 2: Peach (Sesi Terjadwal) -->
                    <div class="rounded-3xl bg-[#FDECDA] dark:bg-amber-950/35 border border-[#F7D2AB] dark:border-amber-900/40 p-5 text-left shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 dark:bg-amber-900/60 px-2.5 py-0.5 text-xs font-bold text-amber-950 dark:text-amber-200 shadow-2xs">
                                <Calendar class="size-3.5 text-amber-600" />
                                Tatap Muka
                            </span>
                            <ArrowUpRight class="size-4 text-amber-700/60" />
                        </div>
                        <div class="mt-4">
                            <div class="text-3xl font-black text-amber-950 dark:text-amber-100 tracking-tight">Fleksibel</div>
                            <p class="text-xs font-semibold text-amber-900/80 dark:text-amber-300 mt-1">
                                Pilih Tanggal &amp; Jam Luang Sesuai Kebutuhan
                            </p>
                        </div>
                    </div>

                    <!-- KPI 3: Lavender (Bidang Layanan) -->
                    <div class="rounded-3xl bg-[#EFE7FA] dark:bg-purple-950/35 border border-[#DDD0F4] dark:border-purple-900/40 p-5 text-left shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 dark:bg-purple-900/60 px-2.5 py-0.5 text-xs font-bold text-purple-950 dark:text-purple-200 shadow-2xs">
                                <HeartHandshake class="size-3.5 text-purple-600" />
                                4 Bidang
                            </span>
                            <ArrowUpRight class="size-4 text-purple-700/60" />
                        </div>
                        <div class="mt-4">
                            <div class="text-3xl font-black text-purple-950 dark:text-purple-100 tracking-tight">Holistik</div>
                            <p class="text-xs font-semibold text-purple-900/80 dark:text-purple-300 mt-1">
                                Pribadi, Sosial, Belajar, &amp; Karier
                            </p>
                        </div>
                    </div>

                    <!-- KPI 4: Mint (Karakter & Prestasi) -->
                    <div class="rounded-3xl bg-[#DDF4E8] dark:bg-emerald-950/35 border border-[#BEE5D0] dark:border-emerald-900/40 p-5 text-left shadow-2xs">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 dark:bg-emerald-900/60 px-2.5 py-0.5 text-xs font-bold text-emerald-950 dark:text-emerald-200 shadow-2xs">
                                <CheckCircle class="size-3.5 text-emerald-600" />
                                Terpadu
                            </span>
                            <ArrowUpRight class="size-4 text-emerald-700/60" />
                        </div>
                        <div class="mt-4">
                            <div class="text-3xl font-black text-emerald-950 dark:text-emerald-100 tracking-tight">RBAC</div>
                            <p class="text-xs font-semibold text-emerald-900/80 dark:text-emerald-300 mt-1">
                                Hak Akses Berlapis: Siswa, Guru, Kepala
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4 Services Pillars (Pastel Cards Matching Theme) -->
            <section id="layanan" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Bidang Konseling</span>
                    <h2 class="text-3xl font-extrabold text-foreground tracking-tight mt-1">4 Pilar Layanan Bimbingan Siswa</h2>
                    <p class="text-sm text-muted-foreground mt-2">
                        Fasilitas pendampingan holistik untuk mendukung perkembangan karakter, kesehatan mental, dan prestasi belajar.
                    </p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="service in services"
                        :key="service.title"
                        class="rounded-3xl border p-6 shadow-2xs hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between"
                        :class="service.cardClass"
                    >
                        <div>
                            <div class="size-12 rounded-2xl flex items-center justify-center mb-5 shadow-2xs" :class="service.iconBg">
                                <component :is="service.icon" class="size-6" />
                            </div>
                            <span class="rounded-full px-2.5 py-0.5 text-[10px] font-bold shadow-2xs" :class="service.badgeClass">
                                {{ service.badge }}
                            </span>
                            <h3 class="text-lg font-bold mt-3 leading-snug">{{ service.title }}</h3>
                            <p class="text-xs opacity-85 mt-2 leading-relaxed">
                                {{ service.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- How It Works Flow -->
            <section id="alur" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                <div class="rounded-3xl border border-[#EBE2D4] dark:border-neutral-800 bg-card p-8 sm:p-12 shadow-2xs">
                    <div class="text-center max-w-2xl mx-auto mb-12">
                        <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Langkah Mudah</span>
                        <h2 class="text-3xl font-extrabold text-foreground tracking-tight mt-1">Alur Layanan Konseling Sekolah</h2>
                        <p class="text-sm text-muted-foreground mt-2">
                            Mulai dari pengajuan mandiri hingga sesi tatap muka dan tindak lanjut yang terstruktur.
                        </p>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div v-for="item in steps" :key="item.step" class="rounded-2xl border border-border/80 bg-background/50 p-5 shadow-2xs">
                            <div class="flex items-center gap-2.5 mb-3">
                                <span class="rounded-full px-2.5 py-0.5 text-xs font-black shadow-2xs" :class="item.badgeColor">
                                    {{ item.step }}
                                </span>
                                <h3 class="font-bold text-foreground text-sm">{{ item.title }}</h3>
                            </div>
                            <p class="text-xs text-muted-foreground leading-relaxed">
                                {{ item.desc }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Key Features Showcase & Realistic Dashboard Mockup -->
            <section id="keunggulan" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                <div class="grid gap-10 lg:grid-cols-12 items-center">
                    <!-- Left: Feature Bullet Points (7 cols) -->
                    <div class="lg:col-span-7 space-y-6">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Keunggulan Sistem</span>
                            <h2 class="text-3xl sm:text-4xl font-extrabold text-foreground tracking-tight mt-1">
                                Dirancang Khusus untuk Sekolah Modern yang Peduli Karakter
                            </h2>
                            <p class="text-sm text-muted-foreground mt-3 leading-relaxed">
                                Menggabungkan kemudahan interaksi siswa, penjadwalan efisien untuk Guru BK, dan transparansi laporan bagi Wali Kelas serta Kepala Sekolah.
                            </p>
                        </div>

                        <div class="space-y-4 pt-2">
                            <div class="flex items-start gap-3.5 rounded-2xl border border-[#EBE2D4] dark:border-neutral-800 bg-card p-4 shadow-2xs">
                                <div class="rounded-xl bg-[#FDE5E5] dark:bg-rose-950/60 p-2 text-rose-800 dark:text-rose-200 border border-[#F8CDCD] dark:border-rose-900 shrink-0">
                                    <Lock class="size-5" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-foreground text-sm">Jaminan Kerahasiaan Catatan Konseling</h4>
                                    <p class="text-xs text-muted-foreground mt-0.5">
                                        Catatan medis permasalahan dan diagnosis hanya dapat dibuka oleh Guru BK yang memiliki izin rahasia (<code class="text-[11px] font-mono">counseling.view_private</code>).
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5 rounded-2xl border border-[#EBE2D4] dark:border-neutral-800 bg-card p-4 shadow-2xs">
                                <div class="rounded-xl bg-[#EFE7FA] dark:bg-purple-950/60 p-2 text-purple-800 dark:text-purple-200 border border-[#DDD0F4] dark:border-purple-900 shrink-0">
                                    <ShieldCheck class="size-5" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-foreground text-sm">Hak Akses Fleksibel &amp; Audit Trail</h4>
                                    <p class="text-xs text-muted-foreground mt-0.5">
                                        Matriks izin granular terintegrasi log aktivitas yang mencatat seluruh aksi penting secara akuntabel.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5 rounded-2xl border border-[#EBE2D4] dark:border-neutral-800 bg-card p-4 shadow-2xs">
                                <div class="rounded-xl bg-[#DDF4E8] dark:bg-emerald-950/60 p-2 text-emerald-800 dark:text-emerald-200 border border-[#BEE5D0] dark:border-emerald-900 shrink-0">
                                    <FileText class="size-5" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-foreground text-sm">Ekspor Laporan PDF Resmi Siap Cetak</h4>
                                    <p class="text-xs text-muted-foreground mt-0.5">
                                        Format laporan komprehensif lengkap dengan kop resmi sekolah untuk rapat evaluasi dan arsip berkas dinas.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: UI Dashboard Live Preview Card (5 cols) -->
                    <div class="lg:col-span-5">
                        <div class="rounded-3xl border border-[#EBE2D4] dark:border-neutral-800 bg-card p-5 sm:p-6 shadow-md space-y-4">
                            <!-- Mini Header -->
                            <div class="flex items-center justify-between border-b border-border/70 pb-3">
                                <div class="flex items-center gap-2">
                                    <div
                                        v-if="logoUrl"
                                        class="size-8 rounded-xl border border-border bg-white flex items-center justify-center overflow-hidden shrink-0 shadow-2xs"
                                    >
                                        <img :src="logoUrl" :alt="portalName" class="size-full object-cover" />
                                    </div>
                                    <div
                                        v-else
                                        class="size-8 rounded-xl bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 flex items-center justify-center font-bold text-xs shrink-0"
                                    >
                                        BK
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-foreground truncate">{{ portalName }}</p>
                                        <p class="text-[10px] text-muted-foreground truncate">{{ schoolName }}</p>
                                    </div>
                                </div>
                                <span class="rounded-full bg-[#DDF4E8] dark:bg-emerald-950/70 border border-[#BEE5D0] dark:border-emerald-800 px-2 py-0.5 text-[10px] font-bold text-emerald-950 dark:text-emerald-200">
                                    Aktif
                                </span>
                            </div>

                            <!-- Mini Request Card -->
                            <div class="rounded-2xl bg-[#FDECDA]/80 dark:bg-amber-950/30 border border-[#F7D2AB] dark:border-amber-900/40 p-3.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-amber-950 dark:text-amber-200 rounded-full bg-white/80 dark:bg-amber-900/60 px-2 py-0.5">
                                        Bimbingan Belajar
                                    </span>
                                    <span class="text-[10px] font-semibold text-amber-900 dark:text-amber-300">
                                        Besok, 09:30 WIB
                                    </span>
                                </div>
                                <p class="text-xs font-bold text-foreground mt-2">Persiapan Ujian &amp; Konsentrasi Belajar</p>
                                <p class="text-[11px] text-muted-foreground mt-0.5">Ruang Konseling BK 1 • Dra. Hj. Nurjanah, M.Pd</p>
                            </div>

                            <!-- Mini Points Grid -->
                            <div class="grid grid-cols-2 gap-2.5">
                                <div class="rounded-2xl bg-[#DDF4E8]/80 dark:bg-emerald-950/30 border border-[#BEE5D0] dark:border-emerald-900/40 p-3">
                                    <p class="text-[10px] font-bold text-emerald-900 dark:text-emerald-300">Poin Prestasi</p>
                                    <p class="text-xl font-black text-emerald-950 dark:text-emerald-100 mt-1">+35 Pts</p>
                                    <p class="text-[10px] text-emerald-800 dark:text-emerald-400 mt-0.5">Juara Lomba Tari</p>
                                </div>

                                <div class="rounded-2xl bg-[#FDE5E5]/80 dark:bg-rose-950/30 border border-[#F8CDCD] dark:border-rose-900/40 p-3">
                                    <p class="text-[10px] font-bold text-rose-900 dark:text-rose-300">Poin Tata Tertib</p>
                                    <p class="text-xl font-black text-rose-950 dark:text-rose-100 mt-1">0 Pts</p>
                                    <p class="text-[10px] text-rose-800 dark:text-rose-400 mt-0.5">Disiplin Sangat Baik</p>
                                </div>
                            </div>

                            <!-- Action button inside mockup -->
                            <div class="pt-1">
                                <Link
                                    :href="login()"
                                    class="w-full inline-flex items-center justify-center gap-2 rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 py-2.5 text-xs font-bold hover:bg-neutral-800 transition-colors shadow-2xs"
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
            <section id="kontak" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                <div class="rounded-3xl border border-[#EBE2D4] dark:border-neutral-800 bg-card p-6 sm:p-10 shadow-2xs">
                    <div class="max-w-2xl mb-8">
                        <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Hubungi Kami</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-foreground tracking-tight mt-1">
                            Kontak &amp; Informasi Layanan {{ schoolName }}
                        </h2>
                        <p class="text-xs sm:text-sm text-muted-foreground mt-2">
                            Punya pertanyaan seputar layanan bimbingan konseling atau kunjungan konsultasi orang tua? Hubungi kami melalui kanal resmi berikut.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Card 1: Alamat -->
                        <div class="rounded-2xl border border-border/80 bg-background/60 p-4.5 shadow-2xs flex flex-col justify-between">
                            <div>
                                <div class="size-9 rounded-xl bg-[#EAE2D5]/70 dark:bg-neutral-800 flex items-center justify-center text-foreground mb-3 shadow-2xs">
                                    <MapPin class="size-4.5" />
                                </div>
                                <h4 class="text-xs font-bold text-foreground">Alamat Sekolah</h4>
                                <p class="text-xs text-muted-foreground mt-1.5 leading-relaxed">
                                    {{ address }}
                                </p>
                            </div>
                        </div>

                        <!-- Card 2: Telepon & Jam Kerja -->
                        <div class="rounded-2xl border border-border/80 bg-background/60 p-4.5 shadow-2xs flex flex-col justify-between">
                            <div>
                                <div class="size-9 rounded-xl bg-[#FDECDA] text-amber-800 dark:bg-amber-950/60 dark:text-amber-200 flex items-center justify-center mb-3 shadow-2xs">
                                    <Phone class="size-4.5" />
                                </div>
                                <h4 class="text-xs font-bold text-foreground">Telepon Kantor</h4>
                                <p class="text-xs font-mono font-bold text-foreground mt-1.5">
                                    {{ phone }}
                                </p>
                                <p class="text-[11px] text-muted-foreground mt-1">
                                    Senin – Jumat: 07.30 – 15.00 WIB
                                </p>
                            </div>
                        </div>

                        <!-- Card 3: WhatsApp Layanan BK -->
                        <div class="rounded-2xl border border-border/80 bg-background/60 p-4.5 shadow-2xs flex flex-col justify-between">
                            <div>
                                <div class="size-9 rounded-xl bg-[#DDF4E8] text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-200 flex items-center justify-center mb-3 shadow-2xs">
                                    <MessageSquare class="size-4.5" />
                                </div>
                                <h4 class="text-xs font-bold text-foreground">WhatsApp Konseling</h4>
                                <p class="text-xs font-mono font-bold text-emerald-700 dark:text-emerald-400 mt-1.5">
                                    {{ whatsapp }}
                                </p>
                                <p class="text-[11px] text-muted-foreground mt-1">
                                    Layanan respon cepat seputar jadwal bimbingan
                                </p>
                            </div>
                        </div>

                        <!-- Card 4: Email & Website -->
                        <div class="rounded-2xl border border-border/80 bg-background/60 p-4.5 shadow-2xs flex flex-col justify-between">
                            <div>
                                <div class="size-9 rounded-xl bg-[#EFE7FA] text-purple-800 dark:bg-purple-950/60 dark:text-purple-200 flex items-center justify-center mb-3 shadow-2xs">
                                    <Mail class="size-4.5" />
                                </div>
                                <h4 class="text-xs font-bold text-foreground">Email &amp; Website</h4>
                                <p class="text-xs font-mono text-muted-foreground mt-1.5 truncate">
                                    {{ email }}
                                </p>
                                <a
                                    v-if="website && website !== '#'"
                                    :href="website"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1 text-[11px] font-bold text-foreground hover:underline mt-2"
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
            <section id="akun-demo" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="rounded-3xl border border-[#EBE2D4] dark:border-neutral-800 bg-card p-6 sm:p-8 shadow-2xs">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border/70 pb-5 mb-6">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 text-[10px] font-black px-2.5 py-0.5">
                                    AKUN UJI COBA
                                </span>
                                <h3 class="text-lg font-bold text-foreground">Kredensial Demo Bawaan</h3>
                            </div>
                            <p class="text-xs text-muted-foreground mt-1">
                                Seluruh akun demo dapat digunakan langsung untuk menguji sistem. Kata sandi default adalah:
                                <code class="font-mono bg-[#EAE2D5]/70 dark:bg-neutral-800 px-2 py-0.5 rounded-md text-foreground font-bold">password</code>
                            </p>
                        </div>
                        <Link
                            :href="login()"
                            class="inline-flex items-center gap-2 rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 px-4.5 py-2 text-xs font-bold hover:bg-neutral-800 dark:hover:bg-neutral-100 transition-colors shrink-0 shadow-2xs"
                        >
                            <LogIn class="size-3.5" />
                            Buka Halaman Login
                        </Link>
                    </div>

                    <div class="grid gap-3.5 sm:grid-cols-2 lg:grid-cols-5">
                        <div
                            v-for="acc in demoAccounts"
                            :key="acc.role"
                            class="rounded-2xl border border-border/80 bg-background/60 p-4 shadow-2xs flex flex-col justify-between"
                        >
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="inline-block rounded-full px-2.5 py-0.5 text-[10px] font-black tracking-wide" :class="acc.badge">
                                        {{ acc.role }}
                                    </span>
                                </div>
                                <p class="text-xs font-bold text-foreground truncate">{{ acc.name }}</p>
                                <p class="text-xs font-mono text-muted-foreground mt-1 select-all break-all">
                                    {{ acc.email }}
                                </p>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-border/60 text-[11px] text-muted-foreground flex justify-between items-center">
                                <span>Kata Sandi:</span>
                                <span class="font-mono font-bold text-foreground">password</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-[#EBE2D4] dark:border-neutral-800 bg-[#FCFAF7] dark:bg-[#141816] py-12 mt-16 transition-colors">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6 text-xs text-muted-foreground">
                <div class="flex items-center gap-3">
                    <div
                        v-if="logoUrl"
                        class="flex aspect-square size-9 items-center justify-center rounded-2xl border border-border bg-white shadow-sm overflow-hidden shrink-0"
                    >
                        <img :src="logoUrl" :alt="portalName" class="size-full object-cover" />
                    </div>
                    <div
                        v-else
                        class="flex aspect-square size-9 items-center justify-center rounded-2xl bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 font-extrabold text-sm tracking-tighter shadow-sm shrink-0"
                    >
                        BK
                    </div>
                    <div>
                        <p class="font-bold text-foreground text-sm">{{ portalName }} • {{ schoolName }}</p>
                        <p class="text-[11px] text-muted-foreground">{{ address }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-6 font-semibold">
                    <Link :href="login()" class="hover:text-foreground transition-colors">Masuk / Login</Link>
                    <a href="#layanan" class="hover:text-foreground transition-colors">4 Layanan</a>
                    <a href="#alur" class="hover:text-foreground transition-colors">Alur Bimbingan</a>
                    <a href="#kontak" class="hover:text-foreground transition-colors">Kontak</a>
                    <a href="#akun-demo" class="hover:text-foreground transition-colors">Akun Demo</a>
                </div>

                <p class="text-center md:text-right">
                    &copy; {{ new Date().getFullYear() }} {{ schoolName }}.
                </p>
            </div>
        </footer>
    </div>
</template>
