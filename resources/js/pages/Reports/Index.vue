<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { FileText, Download, Calendar, Filter, FileSpreadsheet, CheckCircle2 } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { usePermission } from '@/composables/usePermission';

interface ClassOption {
    id: number;
    name: string;
}

const props = defineProps<{
    classes: ClassOption[];
    canExport: boolean;
}>();

const selectedType = ref<'counseling' | 'violations' | 'achievements' | 'students'>('counseling');
const selectedClass = ref('');
const startDate = ref(new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0, 10));
const endDate = ref(new Date().toISOString().slice(0, 10));

const setDateRange = (range: 'this_month' | 'last_month' | 'last_30_days') => {
    const now = new Date();
    const pad = (n: number) => String(n).padStart(2, '0');
    const format = (d: Date) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

    if (range === 'this_month') {
        startDate.value = format(new Date(now.getFullYear(), now.getMonth(), 1));
        endDate.value = format(now);
    } else if (range === 'last_month') {
        startDate.value = format(new Date(now.getFullYear(), now.getMonth() - 1, 1));
        endDate.value = format(new Date(now.getFullYear(), now.getMonth(), 0));
    } else if (range === 'last_30_days') {
        const past = new Date();
        past.setDate(past.getDate() - 30);
        startDate.value = format(past);
        endDate.value = format(now);
    }
};

const downloadPdf = () => {
    const params = new URLSearchParams({
        type: selectedType.value,
        start_date: startDate.value,
        end_date: endDate.value,
    });

    if (selectedClass.value) {
        params.append('class_id', selectedClass.value);
    }

    window.open(`/reports/export-pdf?${params.toString()}`, '_blank');
};

const reportCards = [
    {
        id: 'counseling',
        title: 'Laporan Layanan Konseling',
        description: 'Rekapitulasi sesi tatap muka bimbingan konseling siswa, jenis bimbingan, konselor, dan status penyelesaian.',
        icon: FileText,
        color: 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/50',
    },
    {
        id: 'violations',
        title: 'Laporan Pelanggaran Siswa',
        description: 'Rekapitulasi catatan pelanggaran tata tertib sekolah, jumlah akumulasi poin pelanggaran, dan sanksi yang diberikan.',
        icon: FileText,
        color: 'text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-950/50',
    },
    {
        id: 'achievements',
        title: 'Laporan Prestasi & Kejuaraan',
        description: 'Daftar raihan juara, lomba, sertifikat kejuaraan dan capaian membanggakan siswa di semua tingkatan.',
        icon: FileText,
        color: 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/50',
    },
    {
        id: 'students',
        title: 'Rekapitulasi Induk Siswa',
        description: 'Daftar profil kesiswaan beserta total skor poin pelanggaran dan prestasi per kelas.',
        icon: FileSpreadsheet,
        color: 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Laporan', href: '/reports' }]">
        <Head title="Pusat Cetak Laporan" />

        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-foreground flex items-center gap-2.5">
                    <div class="size-10 rounded-2xl bg-[#D8F5E5] text-emerald-700 flex items-center justify-center">
                        <FileText class="size-6" />
                    </div>
                    Pusat Laporan & Ekspor PDF
                </h1>
                <p class="text-sm text-muted-foreground mt-1">
                    Hasilkan rekapitulasi berkala layanan konseling, pelanggaran, dan prestasi berformat dokumen PDF resmi.
                </p>
            </div>

            <!-- Report Type Selection Cards -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="card in reportCards"
                    :key="card.id"
                    @click="selectedType = card.id as any"
                    class="rounded-3xl border p-6 cursor-pointer transition-all duration-300 hover:-translate-y-1"
                    :class="selectedType === card.id ? 'border-[#18181B] bg-white dark:bg-card shadow-lg ring-2 ring-neutral-900/10 dark:border-white' : 'border-border/80 bg-white/70 dark:bg-card/60 hover:bg-white dark:hover:bg-card shadow-xs'"
                >
                    <div class="flex items-start justify-between">
                        <div class="rounded-2xl p-3" :class="card.color">
                            <component :is="card.icon" class="size-6" />
                        </div>
                        <CheckCircle2 v-if="selectedType === card.id" class="size-5 text-[#18181B] dark:text-white" />
                    </div>

                    <h3 class="font-bold text-foreground mt-4 text-base">{{ card.title }}</h3>
                    <p class="text-xs text-muted-foreground mt-1 line-clamp-3 leading-relaxed">
                        {{ card.description }}
                    </p>
                </div>
            </div>

            <!-- Filter and Export Panel -->
            <div class="rounded-3xl border border-border/80 bg-white dark:bg-card p-6 shadow-xs">
                <h2 class="text-base font-bold text-foreground mb-4 flex items-center gap-2">
                    <Filter class="size-4 text-emerald-600" />
                    Parameter Periode & Ruang Lingkup
                </h2>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-xs font-bold text-foreground uppercase tracking-wider mb-1.5">Dari Tanggal Mulai</label>
                        <input
                            v-model="startDate"
                            type="date"
                            @click="($event.target as HTMLInputElement).showPicker?.()"
                            class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-sm text-foreground focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 transition-all cursor-pointer"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-foreground uppercase tracking-wider mb-1.5">Sampai Tanggal Selesai</label>
                        <input
                            v-model="endDate"
                            type="date"
                            @click="($event.target as HTMLInputElement).showPicker?.()"
                            class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-sm text-foreground focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 transition-all cursor-pointer"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-foreground uppercase tracking-wider mb-1.5">Filter Kelas (Opsional)</label>
                        <select
                            v-model="selectedClass"
                            class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-sm text-foreground focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 transition-all cursor-pointer"
                        >
                            <option value="">Semua Kelas</option>
                            <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Quick Date Range Presets -->
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="text-xs text-muted-foreground mr-1">Preset Rentang Cepat:</span>
                    <button
                        type="button"
                        @click="setDateRange('this_month')"
                        class="rounded-xl border border-border bg-background px-3 py-1 text-xs font-medium text-foreground hover:bg-muted transition-colors cursor-pointer"
                    >
                        Bulan Ini
                    </button>
                    <button
                        type="button"
                        @click="setDateRange('last_30_days')"
                        class="rounded-xl border border-border bg-background px-3 py-1 text-xs font-medium text-foreground hover:bg-muted transition-colors cursor-pointer"
                    >
                        30 Hari Terakhir
                    </button>
                    <button
                        type="button"
                        @click="setDateRange('last_month')"
                        class="rounded-xl border border-border bg-background px-3 py-1 text-xs font-medium text-foreground hover:bg-muted transition-colors cursor-pointer"
                    >
                        Bulan Lalu
                    </button>
                </div>

                <div class="mt-6 pt-4 border-t border-border/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <p class="text-xs text-muted-foreground">
                        Dokumen PDF akan dilengkapi kop surat sekolah, rekap tabel, dan tanda tangan pejabat berwenang.
                    </p>

                    <button
                        v-if="canExport"
                        @click="downloadPdf"
                        class="inline-flex items-center justify-center gap-2 rounded-full bg-[#18181B] px-6 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-neutral-800 transition-all cursor-pointer dark:bg-white dark:text-neutral-900 shrink-0"
                    >
                        <Download class="size-4" />
                        Download Laporan PDF
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
