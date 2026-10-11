<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ScrollText,
    ArrowLeft,
    CheckCircle2,
    Clock,
    Calendar,
    Lock,
    AlertCircle,
    MessageSquare,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AppModal from '@/components/AppModal.vue';
import SearchSelectBox from '@/components/SearchSelectBox.vue';
import { usePermission } from '@/composables/usePermission';

interface RequestDetail {
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
    complaint_summary?: string;
    notes?: string | null;
    rescheduled_reason?: string | null;
    rejection_reason?: string | null;
}

interface CounselorOption {
    id: number;
    name: string;
    identifier: string;
    badge: string;
}

const props = defineProps<{
    request: RequestDetail;
    canApprove: boolean;
    canReschedule: boolean;
    canViewPrivate: boolean;
    counselors?: CounselorOption[];
}>();

// Helpers for DateTime Local formatting
const formatToDateTimeLocal = (
    dateStr?: string | null,
    defaultHour = '09:00',
) => {
    if (!dateStr) {
        const d = new Date();
        d.setDate(d.getDate() + 1);
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}T${defaultHour}`;
    }
    if (dateStr.includes(' ')) {
        const [d, t] = dateStr.split(' ');
        const timePart = t ? t.slice(0, 5) : defaultHour;
        return `${d}T${timePart}`;
    }
    if (dateStr.includes('T')) {
        return dateStr.slice(0, 16);
    }
    return `${dateStr}T${defaultHour}`;
};

const getTomorrowDate = () => {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
};

const minDateTime = computed(() => {
    const now = new Date();
    const y = now.getFullYear();
    const m = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}T07:00`;
});

const timePresets = [
    { time: '08:00', label: 'Pagi' },
    { time: '09:30', label: 'Istirahat 1' },
    { time: '11:00', label: 'Siang' },
    { time: '12:30', label: 'Istirahat 2' },
    { time: '14:15', label: 'Selesai KBM' },
];

const setTimePresetForApprove = (timeStr: string) => {
    const currentDate = approveForm.scheduled_datetime?.includes('T')
        ? approveForm.scheduled_datetime.split('T')[0]
        : props.request.preferred_date || getTomorrowDate();
    approveForm.scheduled_datetime = `${currentDate}T${timeStr}`;
};

const setTimePresetForReschedule = (timeStr: string) => {
    const currentDate = rescheduleForm.scheduled_datetime?.includes('T')
        ? rescheduleForm.scheduled_datetime.split('T')[0]
        : props.request.preferred_date || getTomorrowDate();
    rescheduleForm.scheduled_datetime = `${currentDate}T${timeStr}`;
};

// Approve Form
const showApproveModal = ref(false);
const approveForm = useForm({
    counselor_id: '' as string | number,
    scheduled_datetime: formatToDateTimeLocal(
        props.request.scheduled_datetime || props.request.preferred_date,
        '09:00',
    ),
    venue: 'Ruang Konseling BK',
    notes: '',
});

const counselorSelectOptions = computed(() =>
    (props.counselors || []).map((c) => ({
        id: c.id,
        name: c.name,
        identifier: c.identifier,
        subtitle: `NIP/ID: ${c.identifier} · ${c.badge}`,
        badge: c.badge,
    })),
);

const openApproveModal = () => {
    approveForm.scheduled_datetime = formatToDateTimeLocal(
        props.request.scheduled_datetime || props.request.preferred_date,
        '09:00',
    );
    showApproveModal.value = true;
};

const submitApprove = () => {
    const formatted = approveForm.scheduled_datetime.replace('T', ' ');
    const normalized = formatted.length === 16 ? `${formatted}:00` : formatted;
    approveForm
        .transform((data) => ({
            ...data,
            scheduled_datetime: normalized,
        }))
        .patch(`/counseling/requests/${props.request.id}/approve`, {
            onSuccess: () => {
                showApproveModal.value = false;
            },
        });
};

// Reschedule Form
const showRescheduleModal = ref(false);
const rescheduleForm = useForm({
    scheduled_datetime: formatToDateTimeLocal(
        props.request.scheduled_datetime || props.request.preferred_date,
        '10:00',
    ),
    rescheduled_reason: '',
});

const openRescheduleModal = () => {
    rescheduleForm.scheduled_datetime = formatToDateTimeLocal(
        props.request.scheduled_datetime || props.request.preferred_date,
        '10:00',
    );
    showRescheduleModal.value = true;
};

const submitReschedule = () => {
    const formatted = rescheduleForm.scheduled_datetime.replace('T', ' ');
    const normalized = formatted.length === 16 ? `${formatted}:00` : formatted;
    rescheduleForm
        .transform((data) => ({
            ...data,
            scheduled_datetime: normalized,
        }))
        .patch(`/counseling/requests/${props.request.id}/reschedule`, {
            onSuccess: () => {
                showRescheduleModal.value = false;
            },
        });
};

const statusBadgeClass: Record<string, string> = {
    pending:
        'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300',
    approved:
        'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300',
    scheduled:
        'bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300',
    completed:
        'bg-green-100 text-green-800 dark:bg-green-950/60 dark:text-green-300',
    rescheduled:
        'bg-orange-100 text-orange-800 dark:bg-orange-950/60 dark:text-orange-300',
    rejected: 'bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300',
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Konseling', href: '#' },
            { title: 'Permohonan', href: '/counseling/requests' },
            {
                title: `#${request.id} - ${request.student_name}`,
                href: `/counseling/requests/${request.id}`,
            },
        ]"
    >
        <Head :title="`Permohonan Konseling #${request.id}`" />

        <div class="flex flex-col gap-6 p-6">
            <!-- Header bar -->
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-center gap-3">
                    <Link
                        href="/counseling/requests"
                        class="rounded-xl border border-border p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
                    >
                        <ArrowLeft class="size-5" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-2xl font-bold text-foreground">
                                Permohonan #{{ request.id }}
                            </h1>
                            <span
                                class="rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize"
                                :class="statusBadgeClass[request.status]"
                            >
                                {{ request.status }}
                            </span>
                        </div>
                        <p class="text-sm text-muted-foreground">
                            Pemohon:
                            <span class="font-medium text-foreground">{{
                                request.student_name
                            }}</span>
                            ({{ request.class_name }})
                        </p>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center gap-3">
                    <button
                        v-if="canReschedule && request.status !== 'completed'"
                        @click="openRescheduleModal"
                        class="cursor-pointer rounded-xl border border-border bg-background px-4 py-2 text-sm font-medium text-foreground transition-colors hover:bg-muted"
                    >
                        Jadwal Ulang
                    </button>
                    <button
                        v-if="
                            canApprove &&
                            (request.status === 'pending' ||
                                request.status === 'rescheduled')
                        "
                        @click="openApproveModal"
                        class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white transition-colors hover:bg-indigo-700"
                    >
                        <CheckCircle2 class="size-4" />
                        Setujui & Jadwalkan
                    </button>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <!-- Confidential Complaint Card -->
                    <div
                        class="rounded-2xl border border-border bg-card p-6 shadow-sm"
                    >
                        <div
                            class="mb-4 flex items-center justify-between border-b border-border pb-3"
                        >
                            <h2
                                class="flex items-center gap-2 text-base font-semibold text-foreground"
                            >
                                <MessageSquare class="size-5 text-indigo-600" />
                                Keluhan / Topik yang Ingin Dibicarakan
                            </h2>
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800 dark:bg-amber-950 dark:text-amber-300"
                            >
                                <Lock class="size-3" />
                                Rahasia Konseling
                            </span>
                        </div>

                        <div
                            v-if="canViewPrivate && request.complaint_summary"
                            class="rounded-xl bg-muted/40 p-4 text-sm leading-relaxed whitespace-pre-wrap text-foreground"
                        >
                            {{ request.complaint_summary }}
                        </div>
                        <div
                            v-else
                            class="flex flex-col items-center gap-2 rounded-xl bg-muted/20 p-6 text-center text-xs text-muted-foreground"
                        >
                            <Lock class="size-6 text-muted-foreground/60" />
                            <span
                                >Konten keluhan bersifat rahasia dan hanya dapat
                                diakses oleh Guru BK yang berwenang.</span
                            >
                        </div>
                    </div>

                    <!-- Scheduling status -->
                    <div
                        v-if="request.notes || request.rescheduled_reason"
                        class="rounded-2xl border border-border bg-card p-6 shadow-sm"
                    >
                        <h2
                            class="mb-3 text-base font-semibold text-foreground"
                        >
                            Catatan Tambahan Guru BK
                        </h2>
                        <p
                            v-if="request.notes"
                            class="mb-3 rounded-xl bg-muted/30 p-3 text-sm text-foreground"
                        >
                            {{ request.notes }}
                        </p>
                        <p
                            v-if="request.rescheduled_reason"
                            class="rounded-xl bg-orange-50 p-3 text-sm text-orange-700 dark:bg-orange-950/40 dark:text-orange-300"
                        >
                            <strong>Alasan Perubahan Jadwal:</strong>
                            {{ request.rescheduled_reason }}
                        </p>
                    </div>
                </div>

                <!-- Request Summary Sidebar -->
                <div class="space-y-6">
                    <div
                        class="space-y-4 rounded-2xl border border-border bg-card p-6 shadow-sm"
                    >
                        <h2
                            class="border-b border-border pb-3 text-base font-semibold text-foreground"
                        >
                            Ringkasan Permohonan
                        </h2>

                        <div>
                            <span class="block text-xs text-muted-foreground"
                                >Jenis Konseling</span
                            >
                            <span class="font-semibold text-foreground">{{
                                request.counseling_type
                            }}</span>
                        </div>

                        <div>
                            <span class="block text-xs text-muted-foreground"
                                >Pilihan Tanggal Pemohon</span
                            >
                            <span class="font-semibold text-foreground">{{
                                request.preferred_date
                            }}</span>
                        </div>

                        <div>
                            <span class="block text-xs text-muted-foreground"
                                >Pilihan Jam Pemohon</span
                            >
                            <span class="font-semibold text-foreground">{{
                                request.preferred_time || 'Fleksibel'
                            }}</span>
                        </div>

                        <div>
                            <span class="block text-xs text-muted-foreground"
                                >Jadwal Sesi Disetujui</span
                            >
                            <span
                                class="font-semibold text-indigo-600 dark:text-indigo-400"
                            >
                                {{
                                    request.scheduled_datetime ||
                                    'Belum dijadwalkan'
                                }}
                            </span>
                        </div>

                        <div>
                            <span class="block text-xs text-muted-foreground"
                                >Guru BK Pembimbing</span
                            >
                            <span class="font-semibold text-foreground">{{
                                request.counselor_name || 'Menunggu penugasan'
                            }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approve Modal -->
            <AppModal
                :show="showApproveModal"
                @close="showApproveModal = false"
                title="Setujui & Jadwalkan Konseling"
                description="Tentukan waktu tatap muka, ruangan pertemuan, dan guru BK pendamping."
                max-width="md"
            >
                <form @submit.prevent="submitApprove" class="space-y-4">
                    <div>
                        <SearchSelectBox
                            v-model="approveForm.counselor_id"
                            label="Tugaskan Guru BK / Konselor"
                            placeholder="Pilih Guru BK pendamping (opsional)..."
                            :options="counselorSelectOptions"
                            icon-type="teacher"
                        />
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label
                                class="block text-xs font-semibold text-foreground"
                            >
                                Waktu & Tanggal Sesi
                                <span class="text-rose-500">*</span>
                            </label>
                            <span
                                v-if="request.preferred_date"
                                class="text-[11px] text-muted-foreground"
                            >
                                Permintaan siswa:
                                <strong class="text-foreground">{{
                                    request.preferred_date
                                }}</strong>
                                {{
                                    request.preferred_time
                                        ? `(${request.preferred_time})`
                                        : ''
                                }}
                            </span>
                        </div>
                        <input
                            v-model="approveForm.scheduled_datetime"
                            type="datetime-local"
                            :min="minDateTime"
                            required
                            @click="
                                (
                                    $event.target as HTMLInputElement
                                ).showPicker?.()
                            "
                            class="w-full cursor-pointer rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        />
                        <!-- Preset Hour Chips -->
                        <div class="mt-2 flex flex-wrap items-center gap-1.5">
                            <span class="mr-1 text-[11px] text-muted-foreground"
                                >Pilih Jam Cepat:</span
                            >
                            <button
                                v-for="slot in timePresets"
                                :key="slot.time"
                                type="button"
                                @click="setTimePresetForApprove(slot.time)"
                                class="cursor-pointer rounded-lg border border-border bg-muted/40 px-2.5 py-1 text-[11px] font-medium text-foreground transition-colors hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-950/40 dark:hover:text-indigo-400"
                            >
                                {{ slot.time }}
                                <span class="text-[10px] text-muted-foreground"
                                    >({{ slot.label }})</span
                                >
                            </button>
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-foreground"
                            >Ruangan / Tempat Pertemuan</label
                        >
                        <input
                            v-model="approveForm.venue"
                            type="text"
                            placeholder="Ruang Konseling BK"
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-foreground"
                            >Pesan / Instruksi untuk Siswa</label
                        >
                        <textarea
                            v-model="approveForm.notes"
                            rows="2"
                            placeholder="Pesan pengingat atau instruksi saat menghadiri konseling..."
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 border-t border-border/80 pt-4"
                    >
                        <button
                            type="button"
                            @click="showApproveModal = false"
                            class="cursor-pointer rounded-xl border border-border px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="approveForm.processing"
                            class="cursor-pointer rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-xs transition-colors hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{
                                approveForm.processing
                                    ? 'Menyimpan...'
                                    : 'Konfirmasi Jadwal'
                            }}
                        </button>
                    </div>
                </form>
            </AppModal>

            <!-- Reschedule Modal -->
            <AppModal
                :show="showRescheduleModal"
                @close="showRescheduleModal = false"
                title="Jadwalkan Ulang Konseling"
                description="Berikan jadwal baru beserta alasan perubahan yang jelas untuk siswa."
                max-width="md"
            >
                <form @submit.prevent="submitReschedule" class="space-y-4">
                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label
                                class="block text-xs font-semibold text-foreground"
                            >
                                Jadwal Baru <span class="text-rose-500">*</span>
                            </label>
                            <span
                                v-if="request.scheduled_datetime"
                                class="text-[11px] text-muted-foreground"
                            >
                                Jadwal lama:
                                <strong class="text-foreground">{{
                                    request.scheduled_datetime
                                }}</strong>
                            </span>
                        </div>
                        <input
                            v-model="rescheduleForm.scheduled_datetime"
                            type="datetime-local"
                            :min="minDateTime"
                            required
                            @click="
                                (
                                    $event.target as HTMLInputElement
                                ).showPicker?.()
                            "
                            class="w-full cursor-pointer rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        />
                        <!-- Preset Hour Chips -->
                        <div class="mt-2 flex flex-wrap items-center gap-1.5">
                            <span class="mr-1 text-[11px] text-muted-foreground"
                                >Pilih Jam Cepat:</span
                            >
                            <button
                                v-for="slot in timePresets"
                                :key="slot.time"
                                type="button"
                                @click="setTimePresetForReschedule(slot.time)"
                                class="cursor-pointer rounded-lg border border-border bg-muted/40 px-2.5 py-1 text-[11px] font-medium text-foreground transition-colors hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-950/40 dark:hover:text-indigo-400"
                            >
                                {{ slot.time }}
                                <span class="text-[10px] text-muted-foreground"
                                    >({{ slot.label }})</span
                                >
                            </button>
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-foreground"
                        >
                            Alasan Perubahan Jadwal
                            <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            v-model="rescheduleForm.rescheduled_reason"
                            required
                            rows="3"
                            placeholder="Contoh: Ada rapat dewan guru pada jam tersebut, dimajukan ke..."
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 border-t border-border/80 pt-4"
                    >
                        <button
                            type="button"
                            @click="showRescheduleModal = false"
                            class="cursor-pointer rounded-xl border border-border px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="rescheduleForm.processing"
                            class="cursor-pointer rounded-xl bg-orange-600 px-5 py-2 text-sm font-semibold text-white shadow-xs transition-colors hover:bg-orange-700 disabled:opacity-50"
                        >
                            {{
                                rescheduleForm.processing
                                    ? 'Menyimpan...'
                                    : 'Ubah Jadwal'
                            }}
                        </button>
                    </div>
                </form>
            </AppModal>
        </div>
    </AppLayout>
</template>
