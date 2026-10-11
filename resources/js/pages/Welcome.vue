<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    BookOpen,
    Calendar,
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
    Compass,
    Smile,
} from '@lucide/vue';
import { dashboard, login } from '@/routes';

const services = [
    {
        icon: HeartHandshake,
        title: 'Bimbingan Pribadi',
        description: 'Membantu memahami potensi diri, mengatasi kecemasan, mengelola emosi, dan membangun kepercayaan diri.',
        color: 'from-pink-500/10 to-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-200/60 dark:border-rose-900/40',
        badge: 'Pribadi',
    },
    {
        icon: Users,
        title: 'Bimbingan Sosial',
        description: 'Konsultasi hubungan pertemanan, adaptasi lingkungan sekolah, resolusi konflik, dan komunikasi asertif.',
        color: 'from-blue-500/10 to-cyan-500/10 text-blue-600 dark:text-blue-400 border-blue-200/60 dark:border-blue-900/40',
        badge: 'Sosial',
    },
    {
        icon: BookOpen,
        title: 'Bimbingan Belajar',
        description: 'Strategi belajar efektif, mengatasi kesulitan mata pelajaran, manajemen waktu ujian, dan motivasi berprestasi.',
        color: 'from-amber-500/10 to-yellow-500/10 text-amber-600 dark:text-amber-400 border-amber-200/60 dark:border-amber-900/40',
        badge: 'Akademik',
    },
    {
        icon: Compass,
        title: 'Bimbingan Karir',
        description: 'Eksplorasi minat bakat, persiapan penjurusan SMA/SMK, pengenalan perguruan tinggi, dan cita-cita masa depan.',
        color: 'from-indigo-500/10 to-purple-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-200/60 dark:border-indigo-900/40',
        badge: 'Masa Depan',
    },
];

const steps = [
    {
        step: '01',
        title: 'Ajukan Permohonan',
        desc: 'Pilih jenis bimbingan, tanggal luang yang diinginkan, dan sampaikan cerita awal secara aman.',
    },
    {
        step: '02',
        title: 'Verifikasi & Jadwal',
        desc: 'Guru BK menerima pengajuan dan menetapkan jadwal sesi tatap muka di ruang konseling.',
    },
    {
        step: '03',
        title: 'Sesi Konseling',
        desc: 'Konsultasi tatap muka hangat dan terpercaya dengan jaminan kerahasiaan 100%.',
    },
    {
        step: '04',
        title: 'Solusi & Pemantauan',
        desc: 'Dapatkan arahan, rekomendasi perkembangan, dan tindak lanjut berkelanjutan.',
    },
];

const demoAccounts = [
    { role: 'Siswa', email: 'siswa@example.com', badge: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' },
    { role: 'Guru BK', email: 'bk@example.com', badge: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300' },
    { role: 'Wali Kelas', email: 'walikelas@example.com', badge: 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' },
    { role: 'Kepala Sekolah', email: 'kepala@example.com', badge: 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' },
    { role: 'Superadmin', email: 'superadmin@example.com', badge: 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' },
];
</script>

<template>
    <Head title="Konseling-Yu — Layanan Bimbingan & Konseling Sekolah" />

    <div class="min-h-screen bg-background text-foreground antialiased selection:bg-indigo-500 selection:text-white">
        <!-- Background Glow Accent -->
        <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[700px] h-[450px] bg-gradient-to-tr from-indigo-500/20 via-purple-500/20 to-blue-500/10 blur-[130px] rounded-full"></div>
            <div class="absolute top-[40rem] -left-40 w-[500px] h-[350px] bg-blue-500/15 blur-[120px] rounded-full"></div>
        </div>

        <!-- Sticky Header Navbar -->
        <header class="sticky top-0 z-50 backdrop-blur-md bg-background/80 border-b border-border/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <!-- Brand Logo -->
                <Link href="/" class="flex items-center gap-3 group">
                    <div class="size-11 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition-transform duration-200">
                        <HeartHandshake class="size-6" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-xl tracking-tight bg-gradient-to-r from-indigo-600 via-purple-600 to-blue-600 bg-clip-text text-transparent">
                                Konseling-Yu
                            </span>
                            <span class="rounded-full bg-indigo-100 dark:bg-indigo-950/70 px-2 py-0.5 text-[10px] font-bold text-indigo-700 dark:text-indigo-300">
                                BK SMP
                            </span>
                        </div>
                        <p class="text-[11px] text-muted-foreground font-medium">SMP Negeri 1 Cerdas Mandiri</p>
                    </div>
                </Link>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-muted-foreground">
                    <a href="#layanan" class="hover:text-foreground transition-colors">Layanan BK</a>
                    <a href="#alur" class="hover:text-foreground transition-colors">Alur Konseling</a>
                    <a href="#keunggulan" class="hover:text-foreground transition-colors">Keunggulan</a>
                    <a href="#akun-demo" class="hover:text-foreground transition-colors">Akun Demo</a>
                </nav>

                <!-- Auth Action Button -->
                <div class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-500/20 hover:bg-indigo-700 hover:shadow-indigo-500/30 transition-all duration-200"
                    >
                        <GraduationCap class="size-4" />
                        Buka Dashboard
                    </Link>
                    <Link
                        v-else
                        :href="login()"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-500/25 hover:from-indigo-700 hover:to-purple-700 hover:shadow-indigo-500/35 transition-all duration-200"
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
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-20 lg:pt-24 lg:pb-28 text-center">
                <div class="inline-flex items-center gap-2 rounded-full border border-indigo-200/80 bg-indigo-50/70 dark:border-indigo-900/50 dark:bg-indigo-950/40 px-4 py-1.5 text-xs font-semibold text-indigo-700 dark:text-indigo-300 shadow-sm mb-6">
                    <Sparkles class="size-3.5" />
                    Sistem Informasi Layanan Bimbingan & Konseling Terintegrasi
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-foreground max-w-4xl mx-auto leading-tight sm:leading-tight">
                    Ruang Aman, Nyaman, &amp; Terpercaya untuk
                    <span class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                        Masa Depanmu.
                    </span>
                </h1>

                <p class="mt-6 text-base sm:text-lg text-muted-foreground max-w-2xl mx-auto leading-relaxed">
                    Pengajuan konseling online mandiri, penjadwalan tatap muka bersama Guru BK, pemantauan prestasi, serta penanganan kedisiplinan yang terjamin kerahasiaannya.
                </p>

                <!-- Hero CTA Buttons -->
                <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                    <Link
                        v-if="!$page.props.auth.user"
                        :href="login()"
                        class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-8 py-3.5 text-base font-bold text-white shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all duration-200"
                    >
                        <LogIn class="size-5" />
                        Masuk ke Portal BK
                        <ArrowRight class="size-4 ml-1" />
                    </Link>
                    <Link
                        v-else
                        :href="dashboard()"
                        class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-8 py-3.5 text-base font-bold text-white shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all duration-200"
                    >
                        <GraduationCap class="size-5" />
                        Buka Dashboard Anda
                        <ArrowRight class="size-4 ml-1" />
                    </Link>
                    <a
                        href="#alur"
                        class="inline-flex items-center gap-2 rounded-2xl border border-border bg-card/60 backdrop-blur-sm px-6 py-3.5 text-base font-semibold text-foreground hover:bg-muted hover:border-border transition-all duration-200"
                    >
                        <BookOpen class="size-4 text-indigo-600" />
                        Pelajari Alur Konseling
                    </a>
                </div>

                <!-- Trust Metrics / Badges -->
                <div class="mt-14 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
                    <div class="rounded-2xl border border-border/80 bg-card/60 backdrop-blur-sm p-4 text-center">
                        <div class="flex items-center justify-center gap-1.5 text-indigo-600 dark:text-indigo-400 font-bold text-2xl">
                            <Lock class="size-5" />
                            100%
                        </div>
                        <p class="text-xs text-muted-foreground mt-1 font-medium">Kerahasiaan Terjamin</p>
                    </div>
                    <div class="rounded-2xl border border-border/80 bg-card/60 backdrop-blur-sm p-4 text-center">
                        <div class="flex items-center justify-center gap-1.5 text-purple-600 dark:text-purple-400 font-bold text-2xl">
                            <HeartHandshake class="size-5" />
                            4 Bidang
                        </div>
                        <p class="text-xs text-muted-foreground mt-1 font-medium">Layanan Bimbingan</p>
                    </div>
                    <div class="rounded-2xl border border-border/80 bg-card/60 backdrop-blur-sm p-4 text-center">
                        <div class="flex items-center justify-center gap-1.5 text-blue-600 dark:text-blue-400 font-bold text-2xl">
                            <Calendar class="size-5" />
                            Fleksibel
                        </div>
                        <p class="text-xs text-muted-foreground mt-1 font-medium">Jadwal Sesi Tatap Muka</p>
                    </div>
                    <div class="rounded-2xl border border-border/80 bg-card/60 backdrop-blur-sm p-4 text-center">
                        <div class="flex items-center justify-center gap-1.5 text-green-600 dark:text-green-400 font-bold text-2xl">
                            <ShieldCheck class="size-5" />
                            RBAC
                        </div>
                        <p class="text-xs text-muted-foreground mt-1 font-medium">Akses Aman Terproteksi</p>
                    </div>
                </div>
            </section>

            <!-- Demo Accounts Panel -->
            <section id="akun-demo" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <div class="rounded-3xl border border-indigo-200/60 dark:border-indigo-900/50 bg-gradient-to-r from-indigo-50/50 via-purple-50/30 to-background dark:from-indigo-950/20 dark:via-purple-950/10 dark:to-card p-6 sm:p-8 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-border/60 pb-5 mb-5">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="rounded-md bg-indigo-600 text-white text-[10px] font-bold px-2 py-0.5">DEV ACCOUNTS</span>
                                <h3 class="text-lg font-bold text-foreground">Akun Uji Coba Cepat (Login Credentials)</h3>
                            </div>
                            <p class="text-xs text-muted-foreground mt-1">
                                Klik tombol <strong>Masuk / Log In</strong> di atas dan gunakan salah satu akun di bawah ini. Semua kata sandi bawaan adalah: <code class="font-mono bg-muted px-1.5 py-0.5 rounded text-foreground font-semibold">password</code>
                            </p>
                        </div>
                        <Link
                            :href="login()"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white shadow hover:bg-indigo-700 transition-colors shrink-0"
                        >
                            <LogIn class="size-3.5" />
                            Halaman Login
                        </Link>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                        <div
                            v-for="acc in demoAccounts"
                            :key="acc.role"
                            class="rounded-xl border border-border bg-card p-3.5 shadow-xs flex flex-col justify-between"
                        >
                            <div>
                                <span class="inline-block rounded-md px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider mb-2" :class="acc.badge">
                                    {{ acc.role }}
                                </span>
                                <p class="text-xs font-mono font-medium text-foreground select-all break-all">
                                    {{ acc.email }}
                                </p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-border/50 text-[10px] text-muted-foreground flex justify-between items-center">
                                <span>Sandi:</span>
                                <span class="font-mono font-bold text-foreground">password</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4 Services Pillars -->
            <section id="layanan" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Bidang Pelayanan</span>
                    <h2 class="text-3xl font-bold text-foreground tracking-tight mt-1">4 Pilar Bimbingan & Konseling</h2>
                    <p class="text-sm text-muted-foreground mt-2">
                        Fasilitas pendampingan holistik untuk mendukung perkembangan psikologis dan akademis siswa.
                    </p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="service in services"
                        :key="service.title"
                        class="rounded-3xl border bg-card p-6 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-200 flex flex-col justify-between"
                        :class="service.color"
                    >
                        <div>
                            <div class="size-12 rounded-2xl bg-card border border-border flex items-center justify-center mb-5 shadow-xs">
                                <component :is="service.icon" class="size-6" />
                            </div>
                            <span class="rounded-full bg-background px-2.5 py-0.5 text-[10px] font-bold border border-border/80">
                                {{ service.badge }}
                            </span>
                            <h3 class="text-lg font-bold text-foreground mt-3">{{ service.title }}</h3>
                            <p class="text-xs text-muted-foreground mt-2 leading-relaxed">
                                {{ service.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- How It Works Flow -->
            <section id="alur" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="rounded-3xl border border-border bg-card p-8 sm:p-12 shadow-sm">
                    <div class="text-center max-w-2xl mx-auto mb-12">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Langkah Mudah</span>
                        <h2 class="text-3xl font-bold text-foreground tracking-tight mt-1">Alur Layanan Konseling</h2>
                        <p class="text-sm text-muted-foreground mt-2">
                            Proses pengajuan hingga tindak lanjut dirancang mudah, cepat, dan terjaga kerahasiaannya.
                        </p>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        <div v-for="(item, idx) in steps" :key="item.step" class="relative">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="font-mono text-2xl font-black text-indigo-600/30 dark:text-indigo-400/30">
                                    {{ item.step }}
                                </span>
                                <h3 class="font-bold text-foreground text-base">{{ item.title }}</h3>
                            </div>
                            <p class="text-xs text-muted-foreground leading-relaxed">
                                {{ item.desc }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Key Features Showcase -->
            <section id="keunggulan" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid gap-12 lg:grid-cols-2 items-center">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Fitur Sistem</span>
                        <h2 class="text-3xl sm:text-4xl font-black text-foreground tracking-tight mt-1">
                            Sistem Terpadu dengan Keamanan Hak Akses Tertinggi
                        </h2>
                        <p class="text-sm text-muted-foreground mt-4 leading-relaxed">
                            Dibangun khusus untuk SMP Negeri 1 dengan standar keamanan data yang ketat. Catatan rahasia konseling terlindungi dari pihak yang tidak berwenang.
                        </p>

                        <div class="mt-8 space-y-4">
                            <div class="flex items-start gap-3.5">
                                <div class="rounded-xl bg-green-100 dark:bg-green-950/60 p-2 text-green-600 dark:text-green-400 shrink-0">
                                    <CheckCircle2 class="size-5" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-foreground text-sm">Privasi Catatan Konseling (Confidential)</h4>
                                    <p class="text-xs text-muted-foreground mt-0.5">Catatan rekam medis dan isi curhat hanya dapat diakses oleh Guru BK yang menangani.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5">
                                <div class="rounded-xl bg-indigo-100 dark:bg-indigo-950/60 p-2 text-indigo-600 dark:text-indigo-400 shrink-0">
                                    <CheckCircle2 class="size-5" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-foreground text-sm">Role-Based Access Control Dinamis (RBAC)</h4>
                                    <p class="text-xs text-muted-foreground mt-0.5">Pengaturan hak akses ala Discord yang fleksibel untuk berbagai jabatan di sekolah.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5">
                                <div class="rounded-xl bg-purple-100 dark:bg-purple-950/60 p-2 text-purple-600 dark:text-purple-400 shrink-0">
                                    <CheckCircle2 class="size-5" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-foreground text-sm">Laporan Berkala &amp; Ekspor PDF Resmi</h4>
                                    <p class="text-xs text-muted-foreground mt-0.5">Hasilkan laporan konseling, rekap pelanggaran, dan prestasi siswa siap cetak lengkap dengan kop sekolah.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Card Mockup -->
                    <div class="rounded-3xl border border-border bg-gradient-to-tr from-indigo-500/5 via-purple-500/5 to-transparent p-6 sm:p-8 shadow-xl">
                        <div class="rounded-2xl border border-border bg-card p-5 shadow-sm space-y-4">
                            <div class="flex items-center justify-between border-b border-border pb-3">
                                <div class="flex items-center gap-2">
                                    <div class="size-3 rounded-full bg-red-400"></div>
                                    <div class="size-3 rounded-full bg-amber-400"></div>
                                    <div class="size-3 rounded-full bg-green-400"></div>
                                    <span class="text-xs font-mono text-muted-foreground ml-2">Portal Konseling-Yu</span>
                                </div>
                                <span class="rounded-full bg-green-100 dark:bg-green-950 px-2 py-0.5 text-[10px] font-bold text-green-700 dark:text-green-300">
                                    Aktif
                                </span>
                            </div>

                            <div class="rounded-xl bg-muted/40 p-4">
                                <p class="text-xs font-semibold text-foreground">Permohonan Konseling Siswa</p>
                                <p class="text-xs text-muted-foreground mt-1">Siswa mengajukan permohonan bimbingan belajar menghadapi ujian sekolah.</p>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-[11px] font-mono text-indigo-600 dark:text-indigo-400">Jadwal: Besok, 09:30 WIB</span>
                                    <span class="rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 text-[10px] px-2 py-0.5 font-bold">
                                        Disetujui
                                    </span>
                                </div>
                            </div>

                            <div class="rounded-xl bg-muted/40 p-4">
                                <p class="text-xs font-semibold text-foreground">Pencatatan Poin Prestasi</p>
                                <p class="text-xs text-muted-foreground mt-1">Juara 1 Olimpiade Sains Nasional (OSN) Tingkat Kabupaten.</p>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-[11px] font-mono text-amber-600 dark:text-amber-400">+25 Poin Penghargaan</span>
                                    <span class="rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 text-[10px] px-2 py-0.5 font-bold">
                                        Terverifikasi
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-border/80 bg-card py-12 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6 text-xs text-muted-foreground">
                <div class="flex items-center gap-3">
                    <div class="size-8 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold">
                        BK
                    </div>
                    <div>
                        <p class="font-bold text-foreground text-sm">Konseling-Yu SMP Negeri 1 Cerdas Mandiri</p>
                        <p class="text-[11px] text-muted-foreground">Sistem Informasi Layanan Bimbingan dan Konseling Terpadu</p>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <Link :href="login()" class="hover:text-foreground transition-colors font-medium">Halaman Login</Link>
                    <a href="#layanan" class="hover:text-foreground transition-colors font-medium">Layanan</a>
                    <a href="#alur" class="hover:text-foreground transition-colors font-medium">Alur Konseling</a>
                </div>

                <p class="text-center md:text-right">
                    &copy; {{ new Date().getFullYear() }} SMP Negeri 1. Hak Cipta Dilindungi Undang-Undang.
                </p>
            </div>
        </footer>
    </div>
</template>
