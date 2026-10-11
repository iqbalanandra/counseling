<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import {
    School,
    Upload,
    Trash2,
    Save,
    Phone,
    Mail,
    Globe,
    MapPin,
    MessageSquare,
    Eye,
    CheckCircle2,
    Sparkles,
    ShieldCheck,
    Building2,
    UserCheck,
    Image as ImageIcon,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Spinner } from '@/components/ui/spinner';

interface SchoolData {
    id?: number;
    school_name: string;
    portal_name: string;
    tagline?: string | null;
    npsn?: string | null;
    logo_url?: string | null;
    address?: string | null;
    phone?: string | null;
    whatsapp?: string | null;
    email?: string | null;
    website?: string | null;
    principal_name?: string | null;
    welcome_message?: string | null;
}

const props = defineProps<{
    school: SchoolData;
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const logoPreview = ref<string | null>(props.school.logo_url || null);

const form = useForm({
    school_name: props.school.school_name || 'SMP Negeri 1 Cerdas Mandiri',
    portal_name: props.school.portal_name || 'Konseling-Yu',
    tagline: props.school.tagline || 'Portal Bimbingan & Konseling Terpadu',
    npsn: props.school.npsn || '20104567',
    address:
        props.school.address ||
        'Jl. Merdeka No. 45, Kebayoran Baru, Jakarta Selatan',
    phone: props.school.phone || '(021) 7654321',
    whatsapp: props.school.whatsapp || '0812-3456-7890',
    email: props.school.email || 'info@smpn1cerdasmandiri.sch.id',
    website: props.school.website || 'https://smpn1cerdasmandiri.sch.id',
    principal_name:
        props.school.principal_name || 'Drs. Bambang Sutrisno, M.Pd',
    welcome_message:
        props.school.welcome_message ||
        'Ruang aman dan nyaman untuk konsultasi, bimbingan belajar, eksplorasi masa depan, dan pendampingan karakter.',
    logo: null as File | null,
    remove_logo: false,
});

const onFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.logo = file;
        form.remove_logo = false;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const triggerFileInput = () => {
    fileInput.value?.click();
};

const removeLogo = () => {
    form.logo = null;
    form.remove_logo = true;
    logoPreview.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const submit = () => {
    form.post('/admin/school-profile', {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.logo = null;
        },
    });
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Administrasi', href: '/users' },
            { title: 'Profil Sekolah', href: '/admin/school-profile' },
        ]"
    >
        <Head title="Profil & Identitas Sekolah" />

        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 p-6">
            <!-- Header Section -->
            <div
                class="flex flex-col gap-2 border-b border-border/70 pb-5 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1
                        class="flex items-center gap-2.5 text-2xl font-extrabold tracking-tight text-foreground sm:text-3xl"
                    >
                        <div
                            class="flex size-10 items-center justify-center rounded-2xl border border-[#DFD5C6] bg-[#EAE2D5]/80 text-foreground shadow-2xs dark:border-neutral-700 dark:bg-neutral-800"
                        >
                            <School class="size-5" />
                        </div>
                        Profil &amp; Identitas Sekolah
                    </h1>
                    <p class="mt-1 text-xs text-muted-foreground sm:text-sm">
                        Kelola nama instansi, logo resmi, alamat, kontak, dan
                        pesan publik yang ditampilkan di halaman utama
                        (Welcome).
                    </p>
                </div>

                <div class="flex items-center gap-2.5">
                    <button
                        @click="submit"
                        :disabled="form.processing"
                        type="button"
                        class="inline-flex cursor-pointer items-center gap-2 rounded-full bg-[#18181B] px-5 py-2.5 text-xs font-bold text-white shadow-xs transition-colors hover:bg-neutral-800 disabled:opacity-50 sm:text-sm dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100"
                    >
                        <Spinner v-if="form.processing" class="size-4" />
                        <Save v-else class="size-4" />
                        Simpan Perubahan
                    </button>
                </div>
            </div>

            <!-- Main Content Form & Preview Grid -->
            <form
                @submit.prevent="submit"
                class="grid grid-cols-1 gap-6 lg:grid-cols-12"
            >
                <!-- LEFT COLUMN: Form Inputs (8 Cols) -->
                <div class="flex flex-col gap-6 lg:col-span-8">
                    <!-- SECTION 1: Logo & Media Sekolah -->
                    <div
                        class="rounded-3xl border border-[#EBE2D4] bg-card p-6 shadow-2xs dark:border-neutral-800"
                    >
                        <div
                            class="mb-4 flex items-center gap-2 border-b border-border/70 pb-3"
                        >
                            <ImageIcon class="size-4 text-muted-foreground" />
                            <h2 class="text-base font-bold text-foreground">
                                Logo Resmi Sekolah
                            </h2>
                        </div>

                        <div
                            class="flex flex-col items-center gap-6 sm:flex-row"
                        >
                            <!-- Logo Preview Avatar -->
                            <div class="group relative">
                                <div
                                    class="flex size-24 items-center justify-center overflow-hidden rounded-3xl border-2 border-dashed border-[#DFD5C6] bg-background/80 shadow-2xs dark:border-neutral-700"
                                >
                                    <img
                                        v-if="logoPreview"
                                        :src="logoPreview"
                                        alt="Logo Sekolah"
                                        class="size-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="flex size-full items-center justify-center rounded-2xl bg-[#18181B] text-2xl font-black tracking-tighter text-white dark:bg-white dark:text-neutral-900"
                                    >
                                        BK
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Controls -->
                            <div
                                class="flex-1 space-y-2 text-center sm:text-left"
                            >
                                <input
                                    ref="fileInput"
                                    type="file"
                                    accept="image/png, image/jpeg, image/webp, image/svg+xml"
                                    class="hidden"
                                    @change="onFileChange"
                                />
                                <div
                                    class="flex flex-wrap items-center justify-center gap-2.5 sm:justify-start"
                                >
                                    <button
                                        type="button"
                                        @click="triggerFileInput"
                                        class="inline-flex cursor-pointer items-center gap-1.5 rounded-full border border-border bg-background px-4 py-2 text-xs font-bold text-foreground shadow-2xs transition-colors hover:bg-neutral-100 dark:hover:bg-neutral-800"
                                    >
                                        <Upload class="size-3.5" />
                                        Unggah Logo Baru
                                    </button>
                                    <button
                                        v-if="logoPreview"
                                        type="button"
                                        @click="removeLogo"
                                        class="inline-flex cursor-pointer items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50/70 px-3.5 py-2 text-xs font-bold text-rose-700 transition-colors hover:bg-rose-100 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300"
                                    >
                                        <Trash2 class="size-3.5" />
                                        Hapus Logo Kustom
                                    </button>
                                </div>
                                <p
                                    class="text-[11px] leading-relaxed text-muted-foreground"
                                >
                                    Format:
                                    <strong>PNG, JPG, WEBP, atau SVG</strong>
                                    (Maksimal 2 MB). Jika logo tidak diunggah,
                                    sistem akan menggunakan logo bawaan emblem
                                    <strong>BK</strong>.
                                </p>
                                <p
                                    v-if="form.errors.logo"
                                    class="mt-1 text-xs font-semibold text-rose-600"
                                >
                                    {{ form.errors.logo }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: Identitas Instansi -->
                    <div
                        class="rounded-3xl border border-[#EBE2D4] bg-card p-6 shadow-2xs dark:border-neutral-800"
                    >
                        <div
                            class="mb-4 flex items-center gap-2 border-b border-border/70 pb-3"
                        >
                            <Building2 class="size-4 text-muted-foreground" />
                            <h2 class="text-base font-bold text-foreground">
                                Identitas &amp; Legalitas Sekolah
                            </h2>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Nama Sekolah -->
                            <div class="space-y-1.5 sm:col-span-2">
                                <label
                                    class="flex items-center justify-between text-xs font-bold text-foreground"
                                >
                                    <span
                                        >Nama Resmi Sekolah
                                        <span class="text-rose-500"
                                            >*</span
                                        ></span
                                    >
                                    <span
                                        class="text-[10px] text-muted-foreground"
                                        >Tampil di header &amp; laporan</span
                                    >
                                </label>
                                <input
                                    v-model="form.school_name"
                                    type="text"
                                    required
                                    placeholder="Contoh: SMP Negeri 1 Cerdas Mandiri"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none sm:text-sm"
                                />
                                <p
                                    v-if="form.errors.school_name"
                                    class="text-xs font-medium text-rose-600"
                                >
                                    {{ form.errors.school_name }}
                                </p>
                            </div>

                            <!-- Nama Portal Aplikasi -->
                            <div class="space-y-1.5">
                                <label
                                    class="flex items-center justify-between text-xs font-bold text-foreground"
                                >
                                    <span
                                        >Nama Portal Aplikasi
                                        <span class="text-rose-500"
                                            >*</span
                                        ></span
                                    >
                                </label>
                                <input
                                    v-model="form.portal_name"
                                    type="text"
                                    required
                                    placeholder="Konseling-Yu"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none sm:text-sm"
                                />
                                <p
                                    v-if="form.errors.portal_name"
                                    class="text-xs font-medium text-rose-600"
                                >
                                    {{ form.errors.portal_name }}
                                </p>
                            </div>

                            <!-- NPSN -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-foreground"
                                    >Nomor Pokok Sekolah Nasional (NPSN)</label
                                >
                                <input
                                    v-model="form.npsn"
                                    type="text"
                                    placeholder="Contoh: 20104567"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none sm:text-sm"
                                />
                            </div>

                            <!-- Tagline / Slogan -->
                            <div class="space-y-1.5 sm:col-span-2">
                                <label class="text-xs font-bold text-foreground"
                                    >Slogan / Tagline Layanan</label
                                >
                                <input
                                    v-model="form.tagline"
                                    type="text"
                                    placeholder="Portal Bimbingan & Konseling Terpadu"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none sm:text-sm"
                                />
                            </div>

                            <!-- Kepala Sekolah -->
                            <div class="space-y-1.5 sm:col-span-2">
                                <label
                                    class="flex items-center gap-1.5 text-xs font-bold text-foreground"
                                >
                                    <UserCheck
                                        class="size-3.5 text-muted-foreground"
                                    />
                                    Nama Kepala Sekolah
                                </label>
                                <input
                                    v-model="form.principal_name"
                                    type="text"
                                    placeholder="Drs. Bambang Sutrisno, M.Pd"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none sm:text-sm"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: Alamat & Kontak Resmi -->
                    <div
                        class="rounded-3xl border border-[#EBE2D4] bg-card p-6 shadow-2xs dark:border-neutral-800"
                    >
                        <div
                            class="mb-4 flex items-center gap-2 border-b border-border/70 pb-3"
                        >
                            <MapPin class="size-4 text-muted-foreground" />
                            <h2 class="text-base font-bold text-foreground">
                                Alamat &amp; Kontak Resmi
                            </h2>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Alamat Lengkap -->
                            <div class="space-y-1.5 sm:col-span-2">
                                <label
                                    class="flex items-center gap-1.5 text-xs font-bold text-foreground"
                                >
                                    <MapPin
                                        class="size-3.5 text-muted-foreground"
                                    />
                                    Alamat Lengkap Sekolah
                                </label>
                                <textarea
                                    v-model="form.address"
                                    rows="2"
                                    placeholder="Jl. Merdeka No. 45, Kebayoran Baru, Jakarta Selatan"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none sm:text-sm"
                                ></textarea>
                            </div>

                            <!-- Telepon Kantor -->
                            <div class="space-y-1.5">
                                <label
                                    class="flex items-center gap-1.5 text-xs font-bold text-foreground"
                                >
                                    <Phone
                                        class="size-3.5 text-muted-foreground"
                                    />
                                    Nomor Telepon Kantor
                                </label>
                                <input
                                    v-model="form.phone"
                                    type="text"
                                    placeholder="(021) 7654321"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none sm:text-sm"
                                />
                            </div>

                            <!-- WhatsApp Pelayanan -->
                            <div class="space-y-1.5">
                                <label
                                    class="flex items-center gap-1.5 text-xs font-bold text-foreground"
                                >
                                    <MessageSquare
                                        class="size-3.5 text-emerald-600"
                                    />
                                    WhatsApp Layanan BK
                                </label>
                                <input
                                    v-model="form.whatsapp"
                                    type="text"
                                    placeholder="0812-3456-7890"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none sm:text-sm"
                                />
                            </div>

                            <!-- Email Resmi -->
                            <div class="space-y-1.5">
                                <label
                                    class="flex items-center gap-1.5 text-xs font-bold text-foreground"
                                >
                                    <Mail
                                        class="size-3.5 text-muted-foreground"
                                    />
                                    Email Resmi
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="info@smpn1cerdasmandiri.sch.id"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none sm:text-sm"
                                />
                                <p
                                    v-if="form.errors.email"
                                    class="text-xs font-medium text-rose-600"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Website -->
                            <div class="space-y-1.5">
                                <label
                                    class="flex items-center gap-1.5 text-xs font-bold text-foreground"
                                >
                                    <Globe
                                        class="size-3.5 text-muted-foreground"
                                    />
                                    Tautan Website Sekolah
                                </label>
                                <input
                                    v-model="form.website"
                                    type="text"
                                    placeholder="https://smpn1cerdasmandiri.sch.id"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none sm:text-sm"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: Pesan Sambutan Publik -->
                    <div
                        class="rounded-3xl border border-[#EBE2D4] bg-card p-6 shadow-2xs dark:border-neutral-800"
                    >
                        <div
                            class="mb-4 flex items-center gap-2 border-b border-border/70 pb-3"
                        >
                            <Sparkles class="size-4 text-amber-600" />
                            <h2 class="text-base font-bold text-foreground">
                                Deskripsi &amp; Pesan Halaman Welcome
                            </h2>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-foreground">
                                Pesan Pembuka untuk Siswa &amp; Pengunjung
                            </label>
                            <textarea
                                v-model="form.welcome_message"
                                rows="3"
                                placeholder="Tuliskan deskripsi sambutan hangat bimbingan konseling..."
                                class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none sm:text-sm"
                            ></textarea>
                            <p class="text-[11px] text-muted-foreground">
                                Paragraf ini akan langsung ditampilkan di bawah
                                judul utama pada halaman depan Welcome.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Live Preview Card (4 Cols) -->
                <div class="flex flex-col gap-6 lg:col-span-4">
                    <div class="sticky top-24 space-y-6">
                        <!-- Live Preview Card -->
                        <div
                            class="space-y-4 rounded-3xl border border-[#EBE2D4] bg-card p-5 shadow-md sm:p-6 dark:border-neutral-800"
                        >
                            <div
                                class="flex items-center justify-between border-b border-border/70 pb-3"
                            >
                                <div class="flex items-center gap-2">
                                    <Eye class="size-4 text-muted-foreground" />
                                    <h3
                                        class="text-xs font-bold tracking-wider text-foreground uppercase"
                                    >
                                        Pratinjau Halaman Welcome
                                    </h3>
                                </div>
                                <span
                                    class="rounded-full border border-[#BEE5D0] bg-[#DDF4E8] px-2 py-0.5 text-[10px] font-bold text-emerald-950 dark:border-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-200"
                                >
                                    Live
                                </span>
                            </div>

                            <!-- Mini Header Preview -->
                            <div
                                class="rounded-2xl border border-border/80 bg-background/80 p-3.5 shadow-2xs"
                            >
                                <p
                                    class="mb-2 text-[10px] font-bold text-muted-foreground uppercase"
                                >
                                    Tampilan Logo &amp; Navbar
                                </p>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex size-9 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-border bg-white shadow-2xs"
                                    >
                                        <img
                                            v-if="logoPreview"
                                            :src="logoPreview"
                                            alt="Logo"
                                            class="size-full object-cover"
                                        />
                                        <div
                                            v-else
                                            class="flex size-full items-center justify-center bg-[#18181B] text-xs font-bold text-white dark:bg-white dark:text-neutral-900"
                                        >
                                            BK
                                        </div>
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="truncate text-xs font-black text-foreground"
                                        >
                                            {{
                                                form.portal_name ||
                                                'Konseling-Yu'
                                            }}
                                        </p>
                                        <p
                                            class="truncate text-[10px] font-medium text-muted-foreground"
                                        >
                                            {{
                                                form.school_name ||
                                                'SMP Negeri 1 Cerdas Mandiri'
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Mini Hero Banner Preview -->
                            <div
                                class="space-y-2 rounded-2xl border border-[#BEE5D0] bg-[#DDF4E8]/60 p-3.5 dark:border-emerald-900/40 dark:bg-emerald-950/30"
                            >
                                <div
                                    class="inline-flex items-center gap-1.5 rounded-full bg-white/90 px-2.5 py-0.5 text-[10px] font-bold text-emerald-950 shadow-2xs dark:bg-emerald-900/60 dark:text-emerald-200"
                                >
                                    <Sparkles class="size-3" />
                                    {{ form.school_name || 'Nama Sekolah' }}
                                </div>
                                <p
                                    class="text-xs leading-tight font-extrabold text-foreground"
                                >
                                    Ruang Aman, Nyaman &amp; Terpercaya
                                </p>
                                <p
                                    class="line-clamp-3 text-[11px] text-muted-foreground"
                                >
                                    {{
                                        form.welcome_message ||
                                        'Ruang aman dan nyaman untuk bimbingan konseling...'
                                    }}
                                </p>
                            </div>

                            <!-- Mini Contact Preview -->
                            <div
                                class="space-y-2 rounded-2xl border border-border/80 bg-background/80 p-3.5 text-xs"
                            >
                                <p
                                    class="text-[10px] font-bold text-muted-foreground uppercase"
                                >
                                    Kontak &amp; Lokasi
                                </p>
                                <div
                                    class="flex items-start gap-2 text-muted-foreground"
                                >
                                    <MapPin
                                        class="mt-0.5 size-3.5 shrink-0 text-foreground"
                                    />
                                    <span class="line-clamp-2 text-[11px]">{{
                                        form.address || 'Alamat sekolah'
                                    }}</span>
                                </div>
                                <div
                                    class="flex items-center gap-2 text-muted-foreground"
                                >
                                    <Phone
                                        class="size-3.5 shrink-0 text-foreground"
                                    />
                                    <span class="text-[11px]">{{
                                        form.phone || '-'
                                    }}</span>
                                </div>
                                <div
                                    class="flex items-center gap-2 text-muted-foreground"
                                >
                                    <MessageSquare
                                        class="size-3.5 shrink-0 text-emerald-600"
                                    />
                                    <span
                                        class="text-[11px] font-medium text-emerald-700 dark:text-emerald-400"
                                    >
                                        WA: {{ form.whatsapp || '-' }}
                                    </span>
                                </div>
                                <div
                                    class="flex items-center gap-2 text-muted-foreground"
                                >
                                    <Mail
                                        class="size-3.5 shrink-0 text-foreground"
                                    />
                                    <span class="truncate text-[11px]">{{
                                        form.email || '-'
                                    }}</span>
                                </div>
                            </div>

                            <!-- Bottom Action Button -->
                            <button
                                @click="submit"
                                :disabled="form.processing"
                                type="button"
                                class="inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-full bg-[#18181B] py-3 text-xs font-bold text-white shadow-xs transition-colors hover:bg-neutral-800 disabled:opacity-50 sm:text-sm dark:bg-white dark:text-neutral-900 dark:hover:bg-neutral-100"
                            >
                                <Spinner
                                    v-if="form.processing"
                                    class="size-4"
                                />
                                <Save v-else class="size-4" />
                                Simpan Profil Sekolah
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
