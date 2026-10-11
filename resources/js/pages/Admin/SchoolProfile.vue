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
    address: props.school.address || 'Jl. Merdeka No. 45, Kebayoran Baru, Jakarta Selatan',
    phone: props.school.phone || '(021) 7654321',
    whatsapp: props.school.whatsapp || '0812-3456-7890',
    email: props.school.email || 'info@smpn1cerdasmandiri.sch.id',
    website: props.school.website || 'https://smpn1cerdasmandiri.sch.id',
    principal_name: props.school.principal_name || 'Drs. Bambang Sutrisno, M.Pd',
    welcome_message: props.school.welcome_message || 'Ruang aman dan nyaman untuk konsultasi, bimbingan belajar, eksplorasi masa depan, dan pendampingan karakter.',
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

        <div class="flex flex-col gap-6 p-6 max-w-7xl mx-auto w-full">
            <!-- Header Section -->
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-border/70 pb-5">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-foreground flex items-center gap-2.5">
                        <div class="size-10 rounded-2xl bg-[#EAE2D5]/80 text-foreground dark:bg-neutral-800 flex items-center justify-center border border-[#DFD5C6] dark:border-neutral-700 shadow-2xs">
                            <School class="size-5" />
                        </div>
                        Profil &amp; Identitas Sekolah
                    </h1>
                    <p class="text-xs sm:text-sm text-muted-foreground mt-1">
                        Kelola nama instansi, logo resmi, alamat, kontak, dan pesan publik yang ditampilkan di halaman utama (Welcome).
                    </p>
                </div>

                <div class="flex items-center gap-2.5">
                    <button
                        @click="submit"
                        :disabled="form.processing"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 px-5 py-2.5 text-xs sm:text-sm font-bold shadow-xs hover:bg-neutral-800 dark:hover:bg-neutral-100 transition-colors cursor-pointer disabled:opacity-50"
                    >
                        <Spinner v-if="form.processing" class="size-4" />
                        <Save v-else class="size-4" />
                        Simpan Perubahan
                    </button>
                </div>
            </div>

            <!-- Main Content Form & Preview Grid -->
            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- LEFT COLUMN: Form Inputs (8 Cols) -->
                <div class="lg:col-span-8 flex flex-col gap-6">
                    <!-- SECTION 1: Logo & Media Sekolah -->
                    <div class="rounded-3xl border border-[#EBE2D4] dark:border-neutral-800 bg-card p-6 shadow-2xs">
                        <div class="flex items-center gap-2 mb-4 border-b border-border/70 pb-3">
                            <ImageIcon class="size-4 text-muted-foreground" />
                            <h2 class="text-base font-bold text-foreground">Logo Resmi Sekolah</h2>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            <!-- Logo Preview Avatar -->
                            <div class="relative group">
                                <div
                                    class="size-24 rounded-3xl border-2 border-dashed border-[#DFD5C6] dark:border-neutral-700 bg-background/80 flex items-center justify-center overflow-hidden shadow-2xs"
                                >
                                    <img
                                        v-if="logoPreview"
                                        :src="logoPreview"
                                        alt="Logo Sekolah"
                                        class="size-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="size-full rounded-2xl bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 flex items-center justify-center font-black text-2xl tracking-tighter"
                                    >
                                        BK
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Controls -->
                            <div class="flex-1 space-y-2 text-center sm:text-left">
                                <input
                                    ref="fileInput"
                                    type="file"
                                    accept="image/png, image/jpeg, image/webp, image/svg+xml"
                                    class="hidden"
                                    @change="onFileChange"
                                />
                                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                                    <button
                                        type="button"
                                        @click="triggerFileInput"
                                        class="inline-flex items-center gap-1.5 rounded-full border border-border bg-background px-4 py-2 text-xs font-bold text-foreground hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors shadow-2xs cursor-pointer"
                                    >
                                        <Upload class="size-3.5" />
                                        Unggah Logo Baru
                                    </button>
                                    <button
                                        v-if="logoPreview"
                                        type="button"
                                        @click="removeLogo"
                                        class="inline-flex items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50/70 text-rose-700 dark:bg-rose-950/40 dark:border-rose-900/60 dark:text-rose-300 px-3.5 py-2 text-xs font-bold hover:bg-rose-100 transition-colors cursor-pointer"
                                    >
                                        <Trash2 class="size-3.5" />
                                        Hapus Logo Kustom
                                    </button>
                                </div>
                                <p class="text-[11px] text-muted-foreground leading-relaxed">
                                    Format: <strong>PNG, JPG, WEBP, atau SVG</strong> (Maksimal 2 MB). Jika logo tidak diunggah, sistem akan menggunakan logo bawaan emblem <strong>BK</strong>.
                                </p>
                                <p v-if="form.errors.logo" class="text-xs text-rose-600 font-semibold mt-1">
                                    {{ form.errors.logo }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: Identitas Instansi -->
                    <div class="rounded-3xl border border-[#EBE2D4] dark:border-neutral-800 bg-card p-6 shadow-2xs">
                        <div class="flex items-center gap-2 mb-4 border-b border-border/70 pb-3">
                            <Building2 class="size-4 text-muted-foreground" />
                            <h2 class="text-base font-bold text-foreground">Identitas &amp; Legalitas Sekolah</h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nama Sekolah -->
                            <div class="sm:col-span-2 space-y-1.5">
                                <label class="text-xs font-bold text-foreground flex items-center justify-between">
                                    <span>Nama Resmi Sekolah <span class="text-rose-500">*</span></span>
                                    <span class="text-[10px] text-muted-foreground">Tampil di header &amp; laporan</span>
                                </label>
                                <input
                                    v-model="form.school_name"
                                    type="text"
                                    required
                                    placeholder="Contoh: SMP Negeri 1 Cerdas Mandiri"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs sm:text-sm text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none"
                                />
                                <p v-if="form.errors.school_name" class="text-xs text-rose-600 font-medium">
                                    {{ form.errors.school_name }}
                                </p>
                            </div>

                            <!-- Nama Portal Aplikasi -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-foreground flex items-center justify-between">
                                    <span>Nama Portal Aplikasi <span class="text-rose-500">*</span></span>
                                </label>
                                <input
                                    v-model="form.portal_name"
                                    type="text"
                                    required
                                    placeholder="Konseling-Yu"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs sm:text-sm text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none"
                                />
                                <p v-if="form.errors.portal_name" class="text-xs text-rose-600 font-medium">
                                    {{ form.errors.portal_name }}
                                </p>
                            </div>

                            <!-- NPSN -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-foreground">Nomor Pokok Sekolah Nasional (NPSN)</label>
                                <input
                                    v-model="form.npsn"
                                    type="text"
                                    placeholder="Contoh: 20104567"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs sm:text-sm text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none"
                                />
                            </div>

                            <!-- Tagline / Slogan -->
                            <div class="sm:col-span-2 space-y-1.5">
                                <label class="text-xs font-bold text-foreground">Slogan / Tagline Layanan</label>
                                <input
                                    v-model="form.tagline"
                                    type="text"
                                    placeholder="Portal Bimbingan & Konseling Terpadu"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs sm:text-sm text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none"
                                />
                            </div>

                            <!-- Kepala Sekolah -->
                            <div class="sm:col-span-2 space-y-1.5">
                                <label class="text-xs font-bold text-foreground flex items-center gap-1.5">
                                    <UserCheck class="size-3.5 text-muted-foreground" />
                                    Nama Kepala Sekolah
                                </label>
                                <input
                                    v-model="form.principal_name"
                                    type="text"
                                    placeholder="Drs. Bambang Sutrisno, M.Pd"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs sm:text-sm text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: Alamat & Kontak Resmi -->
                    <div class="rounded-3xl border border-[#EBE2D4] dark:border-neutral-800 bg-card p-6 shadow-2xs">
                        <div class="flex items-center gap-2 mb-4 border-b border-border/70 pb-3">
                            <MapPin class="size-4 text-muted-foreground" />
                            <h2 class="text-base font-bold text-foreground">Alamat &amp; Kontak Resmi</h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Alamat Lengkap -->
                            <div class="sm:col-span-2 space-y-1.5">
                                <label class="text-xs font-bold text-foreground flex items-center gap-1.5">
                                    <MapPin class="size-3.5 text-muted-foreground" />
                                    Alamat Lengkap Sekolah
                                </label>
                                <textarea
                                    v-model="form.address"
                                    rows="2"
                                    placeholder="Jl. Merdeka No. 45, Kebayoran Baru, Jakarta Selatan"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs sm:text-sm text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none"
                                ></textarea>
                            </div>

                            <!-- Telepon Kantor -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-foreground flex items-center gap-1.5">
                                    <Phone class="size-3.5 text-muted-foreground" />
                                    Nomor Telepon Kantor
                                </label>
                                <input
                                    v-model="form.phone"
                                    type="text"
                                    placeholder="(021) 7654321"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs sm:text-sm text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none"
                                />
                            </div>

                            <!-- WhatsApp Pelayanan -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-foreground flex items-center gap-1.5">
                                    <MessageSquare class="size-3.5 text-emerald-600" />
                                    WhatsApp Layanan BK
                                </label>
                                <input
                                    v-model="form.whatsapp"
                                    type="text"
                                    placeholder="0812-3456-7890"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs sm:text-sm text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none"
                                />
                            </div>

                            <!-- Email Resmi -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-foreground flex items-center gap-1.5">
                                    <Mail class="size-3.5 text-muted-foreground" />
                                    Email Resmi
                                </label>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="info@smpn1cerdasmandiri.sch.id"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs sm:text-sm text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none"
                                />
                                <p v-if="form.errors.email" class="text-xs text-rose-600 font-medium">
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Website -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-foreground flex items-center gap-1.5">
                                    <Globe class="size-3.5 text-muted-foreground" />
                                    Tautan Website Sekolah
                                </label>
                                <input
                                    v-model="form.website"
                                    type="text"
                                    placeholder="https://smpn1cerdasmandiri.sch.id"
                                    class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs sm:text-sm text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: Pesan Sambutan Publik -->
                    <div class="rounded-3xl border border-[#EBE2D4] dark:border-neutral-800 bg-card p-6 shadow-2xs">
                        <div class="flex items-center gap-2 mb-4 border-b border-border/70 pb-3">
                            <Sparkles class="size-4 text-amber-600" />
                            <h2 class="text-base font-bold text-foreground">Deskripsi &amp; Pesan Halaman Welcome</h2>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-foreground">
                                Pesan Pembuka untuk Siswa &amp; Pengunjung
                            </label>
                            <textarea
                                v-model="form.welcome_message"
                                rows="3"
                                placeholder="Tuliskan deskripsi sambutan hangat bimbingan konseling..."
                                class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-xs sm:text-sm text-foreground focus:ring-2 focus:ring-foreground/20 focus:outline-none"
                            ></textarea>
                            <p class="text-[11px] text-muted-foreground">
                                Paragraf ini akan langsung ditampilkan di bawah judul utama pada halaman depan Welcome.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Live Preview Card (4 Cols) -->
                <div class="lg:col-span-4 flex flex-col gap-6">
                    <div class="sticky top-24 space-y-6">
                        <!-- Live Preview Card -->
                        <div class="rounded-3xl border border-[#EBE2D4] dark:border-neutral-800 bg-card p-5 sm:p-6 shadow-md space-y-4">
                            <div class="flex items-center justify-between border-b border-border/70 pb-3">
                                <div class="flex items-center gap-2">
                                    <Eye class="size-4 text-muted-foreground" />
                                    <h3 class="text-xs font-bold text-foreground uppercase tracking-wider">
                                        Pratinjau Halaman Welcome
                                    </h3>
                                </div>
                                <span class="rounded-full bg-[#DDF4E8] dark:bg-emerald-950/70 border border-[#BEE5D0] dark:border-emerald-800 px-2 py-0.5 text-[10px] font-bold text-emerald-950 dark:text-emerald-200">
                                    Live
                                </span>
                            </div>

                            <!-- Mini Header Preview -->
                            <div class="rounded-2xl border border-border/80 bg-background/80 p-3.5 shadow-2xs">
                                <p class="text-[10px] uppercase font-bold text-muted-foreground mb-2">Tampilan Logo &amp; Navbar</p>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="size-9 rounded-2xl border border-border flex items-center justify-center overflow-hidden shrink-0 shadow-2xs bg-white"
                                    >
                                        <img
                                            v-if="logoPreview"
                                            :src="logoPreview"
                                            alt="Logo"
                                            class="size-full object-cover"
                                        />
                                        <div
                                            v-else
                                            class="size-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 flex items-center justify-center font-bold text-xs"
                                        >
                                            BK
                                        </div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-black text-foreground truncate">
                                            {{ form.portal_name || 'Konseling-Yu' }}
                                        </p>
                                        <p class="text-[10px] text-muted-foreground truncate font-medium">
                                            {{ form.school_name || 'SMP Negeri 1 Cerdas Mandiri' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Mini Hero Banner Preview -->
                            <div class="rounded-2xl bg-[#DDF4E8]/60 dark:bg-emerald-950/30 border border-[#BEE5D0] dark:border-emerald-900/40 p-3.5 space-y-2">
                                <div class="inline-flex items-center gap-1.5 rounded-full bg-white/90 dark:bg-emerald-900/60 px-2.5 py-0.5 text-[10px] font-bold text-emerald-950 dark:text-emerald-200 shadow-2xs">
                                    <Sparkles class="size-3" />
                                    {{ form.school_name || 'Nama Sekolah' }}
                                </div>
                                <p class="text-xs font-extrabold text-foreground leading-tight">
                                    Ruang Aman, Nyaman &amp; Terpercaya
                                </p>
                                <p class="text-[11px] text-muted-foreground line-clamp-3">
                                    {{ form.welcome_message || 'Ruang aman dan nyaman untuk bimbingan konseling...' }}
                                </p>
                            </div>

                            <!-- Mini Contact Preview -->
                            <div class="rounded-2xl border border-border/80 bg-background/80 p-3.5 space-y-2 text-xs">
                                <p class="text-[10px] uppercase font-bold text-muted-foreground">Kontak &amp; Lokasi</p>
                                <div class="flex items-start gap-2 text-muted-foreground">
                                    <MapPin class="size-3.5 text-foreground shrink-0 mt-0.5" />
                                    <span class="text-[11px] line-clamp-2">{{ form.address || 'Alamat sekolah' }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-muted-foreground">
                                    <Phone class="size-3.5 text-foreground shrink-0" />
                                    <span class="text-[11px]">{{ form.phone || '-' }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-muted-foreground">
                                    <MessageSquare class="size-3.5 text-emerald-600 shrink-0" />
                                    <span class="text-[11px] font-medium text-emerald-700 dark:text-emerald-400">
                                        WA: {{ form.whatsapp || '-' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 text-muted-foreground">
                                    <Mail class="size-3.5 text-foreground shrink-0" />
                                    <span class="text-[11px] truncate">{{ form.email || '-' }}</span>
                                </div>
                            </div>

                            <!-- Bottom Action Button -->
                            <button
                                @click="submit"
                                :disabled="form.processing"
                                type="button"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-full bg-[#18181B] text-white dark:bg-white dark:text-neutral-900 py-3 text-xs sm:text-sm font-bold shadow-xs hover:bg-neutral-800 dark:hover:bg-neutral-100 transition-colors cursor-pointer disabled:opacity-50"
                            >
                                <Spinner v-if="form.processing" class="size-4" />
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
