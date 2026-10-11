<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ScrollText, Plus, Search, Eye, Clock, CheckCircle2, XCircle, AlertCircle, Calendar } from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AppModal from '@/components/AppModal.vue';
import { usePermission } from '@/composables/usePermission';

interface CounselingTypeOption {
    id: number;
    name: string;
}

interface RequestItem {
    id: number;
    student_name: string;
    student_nis: string;
    class_name: string;
    counseling_type: string;
    preferred_date: string;
    preferred_time: string | null;
    scheduled_datetime: string | null;
    status: string;
    counselor_name: string | null;
    created_at: string;
}

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    requests: PaginatedData<RequestItem>;
    filters: { status?: string };
    types: CounselingTypeOption[];
}>();

const { can, hasRole } = usePermission();

const selectedStatus = ref(props.filters.status || '');

const handleFilter = () => {
    router.get(
        '/counseling/requests',
        { status: selectedStatus.value || undefined },
        { preserveState: true, replace: true }
    );
};

// Date & Time helpers
const formatDateStr = (date: Date) => {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
};

const getTomorrowDate = () => {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    return formatDateStr(d);
};

const minDate = computed(() => getTomorrowDate());

const datePresets = [
    { label: 'Besok', days: 1 },
    { label: 'Lusa', days: 2 },
    { label: '3 Hari Lagi', days: 3 },
    { label: 'Minggu Depan', days: 7 },
];

const setDatePreset = (days: number) => {
    const d = new Date();
    d.setDate(d.getDate() + days);
    submitForm.preferred_date = formatDateStr(d);
};

const timeOptions = [
    'Istirahat 1 (09:30 - 10:00 WIB)',
    'Istirahat 2 / Ishoma (12:00 - 12:45 WIB)',
    'Selesai KBM / Pulang Sekolah (14:30 - 15:30 WIB)',
    'Jam Kosong / Pelajaran BK',
    'Pagi Sebelum Bel Masuk (06:45 - 07:15 WIB)',
    'Fleksibel (Kapan saja Guru BK bersedia)',
    'Kustom (Tentukan Jam Tertentu)',
];

const selectedTimeOption = ref('Istirahat 1 (09:30 - 10:00 WIB)');
const customTimeValue = ref('10:00');

const onTimeOptionChange = () => {
    if (selectedTimeOption.value === 'Kustom (Tentukan Jam Tertentu)') {
        submitForm.preferred_time = `Pukul ${customTimeValue.value} WIB`;
    } else {
        submitForm.preferred_time = selectedTimeOption.value;
    }
};

const onCustomTimeChange = () => {
    if (selectedTimeOption.value === 'Kustom (Tentukan Jam Tertentu)' && customTimeValue.value) {
        submitForm.preferred_time = `Pukul ${customTimeValue.value} WIB`;
    }
};

// Student submit request modal
const showCreateModal = ref(false);
const submitForm = useForm({
    counseling_type_id: '' as string | number,
    preferred_date: getTomorrowDate(),
    preferred_time: 'Istirahat 1 (09:30 - 10:00 WIB)',
    complaint_summary: '',
});

const openCreateModal = () => {
    if (!submitForm.preferred_date) {
        submitForm.preferred_date = getTomorrowDate();
    }
    selectedTimeOption.value = 'Istirahat 1 (09:30 - 10:00 WIB)';
    submitForm.preferred_time = selectedTimeOption.value;
    showCreateModal.value = true;
};

const submitRequest = () => {
    submitForm.post('/counseling/requests', {
        onSuccess: () => {
            showCreateModal.value = false;
            submitForm.reset();
        },
    });
};

const statusBadgeClass: Record<string, string> = {
    pending: 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
    approved: 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300',
    scheduled: 'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300',
    completed: 'bg-green-100 text-green-800 dark:bg-green-950/60 dark:text-green-300',
    rescheduled: 'bg-orange-100 text-orange-800 dark:bg-orange-950/60 dark:text-orange-300',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300',
    cancelled: 'bg-gray-100 text-gray-800 dark:bg-gray-950 dark:text-gray-300',
};

const statusLabel: Record<string, string> = {
    pending: 'Menunggu BK',
    approved: 'Disetujui',
    scheduled: 'Terjadwal',
    completed: 'Selesai',
    rescheduled: 'Dijadwal Ulang',
    rejected: 'Ditolak',
    cancelled: 'Dibatalkan',
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Konseling', href: '#' }, { title: 'Permohonan Konseling', href: '/counseling/requests' }]">
        <Head title="Permohonan Konseling" />

        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-foreground flex items-center gap-2.5">
                        <div class="size-10 rounded-2xl bg-[#FDE5E5] text-rose-700 flex items-center justify-center">
                            <ScrollText class="size-6" />
                        </div>
                        Permohonan Konseling
                    </h1>
                    <p class="text-sm text-muted-foreground mt-1">
                        Daftar permohonan bimbingan konseling dan penjadwalan sesi tatap muka.
                    </p>
                </div>
                <button
                    v-if="can('counseling.create')"
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 rounded-full bg-[#18181B] px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-neutral-800 transition-all cursor-pointer dark:bg-white dark:text-neutral-900"
                >
                    <Plus class="size-4" />
                    Ajukan Konseling Baru
                </button>
            </div>

            <!-- Filters Bar -->
            <div class="flex items-center gap-3 rounded-3xl border border-border/80 bg-white dark:bg-card p-4 shadow-xs">
                <div class="w-full sm:w-64">
                    <select
                        v-model="selectedStatus"
                        @change="handleFilter"
                        class="w-full rounded-2xl border border-border bg-background px-4 py-2.5 text-sm text-foreground focus:border-neutral-900 focus:outline-none focus:ring-1 focus:ring-neutral-900 transition-all cursor-pointer"
                    >
                        <option value="">Semua Status Permohonan</option>
                        <option value="pending">Menunggu BK</option>
                        <option value="approved">Disetujui</option>
                        <option value="rescheduled">Dijadwal Ulang</option>
                        <option value="completed">Selesai</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <div class="rounded-3xl border border-border/80 bg-white dark:bg-card shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#F5EFE6]/70 dark:bg-neutral-900/80 border-b border-border/60">
                            <tr>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Pemohon</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Jenis Konseling</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Waktu Diinginkan</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Jadwal Fix</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider">Status</th>
                                <th class="px-5 py-4 font-bold text-muted-foreground text-xs uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <tr v-for="req in requests.data" :key="req.id" class="hover:bg-muted/30 transition-colors">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-foreground">{{ req.student_name }}</p>
                                    <p class="text-xs text-muted-foreground">NIS: {{ req.student_nis }} · Kelas {{ req.class_name }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                        {{ req.counseling_type }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-muted-foreground">
                                    <p class="font-medium text-foreground">{{ req.preferred_date }}</p>
                                    <p>{{ req.preferred_time || 'Fleksibel' }}</p>
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    <span v-if="req.scheduled_datetime" class="font-semibold text-indigo-600 dark:text-indigo-400">
                                        {{ req.scheduled_datetime }}
                                    </span>
                                    <span v-else class="text-muted-foreground italic">Belum dijadwalkan</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold" :class="statusBadgeClass[req.status] || 'bg-muted'">
                                        {{ statusLabel[req.status] || req.status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <Link
                                        :href="`/counseling/requests/${req.id}`"
                                        class="inline-flex items-center gap-1 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-foreground hover:bg-muted transition-colors"
                                    >
                                        <Eye class="size-3.5" />
                                        Detail & Tanggapi
                                    </Link>
                                </td>
                            </tr>

                            <tr v-if="requests.data.length === 0">
                                <td colspan="6" class="py-12 text-center text-muted-foreground text-sm">
                                    Belum ada permohonan konseling.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="requests.total > 15" class="flex items-center justify-between border-t border-border px-5 py-3 text-xs text-muted-foreground">
                    <span>Menampilkan {{ requests.data.length }} dari total {{ requests.total }} permohonan</span>
                    <div class="flex gap-1">
                        <Link
                            v-for="(link, i) in requests.links"
                            :key="i"
                            :href="link.url || '#'"
                            :class="[
                                'rounded-lg px-2.5 py-1 transition-colors',
                                link.active ? 'bg-indigo-600 text-white font-bold' : 'hover:bg-muted text-foreground',
                                !link.url && 'opacity-50 pointer-events-none'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>

            <!-- Student Submit Modal -->
            <AppModal
                :show="showCreateModal"
                @close="showCreateModal = false"
                title="Ajukan Permohonan Konseling"
                description="Curhat dan bimbingan bersama Guru BK secara tatap muka dan terjaga kerahasiaannya."
                max-width="lg"
            >
                <form @submit.prevent="submitRequest" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1.5">
                            Bidang Layanan Konseling <span class="text-rose-500">*</span>
                        </label>
                        <select
                            v-model="submitForm.counseling_type_id"
                            required
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        >
                            <option value="">Pilih Jenis Masalah / Konseling...</option>
                            <option v-for="t in types" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </select>
                    </div>

                    <div class="space-y-4">
                        <!-- Date Selection -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-semibold text-foreground">
                                    Pilihan Tanggal Tatap Muka <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-muted-foreground">Minimal mulai besok</span>
                            </div>
                            <input
                                v-model="submitForm.preferred_date"
                                type="date"
                                :min="minDate"
                                required
                                @click="($event.target as HTMLInputElement).showPicker?.()"
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 cursor-pointer"
                            />
                            <!-- Date quick chips -->
                            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                <span class="text-[11px] text-muted-foreground mr-1">Pilih Cepat:</span>
                                <button
                                    v-for="preset in datePresets"
                                    :key="preset.days"
                                    type="button"
                                    @click="setDatePreset(preset.days)"
                                    class="rounded-lg border border-border bg-muted/30 px-2 py-1 text-[11px] font-medium text-foreground hover:bg-indigo-50 hover:border-indigo-300 hover:text-indigo-600 dark:hover:bg-indigo-950/40 dark:hover:text-indigo-400 transition-colors cursor-pointer"
                                >
                                    {{ preset.label }}
                                </button>
                            </div>
                        </div>

                        <!-- Time Selection -->
                        <div>
                            <label class="block text-xs font-semibold text-foreground mb-1.5">
                                Pilihan Waktu / Jam Tatap Muka <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="selectedTimeOption"
                                @change="onTimeOptionChange"
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 cursor-pointer"
                            >
                                <option v-for="opt in timeOptions" :key="opt" :value="opt">
                                    {{ opt }}
                                </option>
                            </select>

                            <!-- Custom Time Picker if selected -->
                            <div v-if="selectedTimeOption === 'Kustom (Tentukan Jam Tertentu)'" class="mt-2 flex items-center gap-2">
                                <label class="text-xs text-muted-foreground shrink-0">Tentukan Jam:</label>
                                <input
                                    v-model="customTimeValue"
                                    type="time"
                                    @input="onCustomTimeChange"
                                    @click="($event.target as HTMLInputElement).showPicker?.()"
                                    class="rounded-xl border border-input bg-background px-3 py-1.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 cursor-pointer"
                                />
                                <span class="text-xs text-muted-foreground">WIB</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-foreground mb-1.5">
                            Ceritakan Masalah / Hal yang Ingin Dibicarakan <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            v-model="submitForm.complaint_summary"
                            required
                            minlength="20"
                            rows="4"
                            placeholder="Jelaskan secara singkat apa yang sedang kamu hadapi atau rasakan. Catatan ini bersifat rahasia dan hanya dibaca oleh Guru BK..."
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        ></textarea>
                        <p class="text-xs text-muted-foreground mt-1">Minimal 20 karakter.</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-border/80">
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="rounded-xl border border-border px-4 py-2 text-sm font-medium text-muted-foreground hover:bg-muted transition-colors cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="submitForm.processing"
                            class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-50 transition-colors cursor-pointer shadow-xs"
                        >
                            {{ submitForm.processing ? 'Mengirim...' : 'Kirim Permohonan' }}
                        </button>
                    </div>
                </form>
            </AppModal>
        </div>
    </AppLayout>
</template>
