<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    BookOpen,
    Calendar,
    MapPin,
    CheckCircle2,
    Clock,
    FilePlus2,
    Lock,
    ShieldAlert,
} from '@lucide/vue';
import AppLayout from '@/layouts/AppLayout.vue';
import AppModal from '@/components/AppModal.vue';
import { usePermission } from '@/composables/usePermission';

interface SessionItem {
    id: number;
    student_name: string;
    student_nis: string;
    class_name: string;
    counseling_type: string;
    session_date: string;
    venue: string;
    status: string;
    counselor_name: string;
    has_record: boolean;
}

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
}

const props = defineProps<{
    sessions: PaginatedData<SessionItem>;
}>();

const { can } = usePermission();

// Record Session Modal
const selectedSession = ref<SessionItem | null>(null);
const showRecordModal = ref(false);

const recordForm = useForm({
    topic: '',
    problem_description: '',
    counseling_notes: '',
    assessment_diagnosis: '',
    recommendations: '',
    follow_up_plan: '',
});

const openRecordModal = (session: SessionItem) => {
    selectedSession.value = session;
    recordForm.reset();
    showRecordModal.value = true;
};

const submitRecord = () => {
    if (!selectedSession.value) return;

    recordForm.post(`/counseling/sessions/${selectedSession.value.id}/record`, {
        onSuccess: () => {
            showRecordModal.value = false;
            selectedSession.value = null;
        },
    });
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Konseling', href: '#' },
            { title: 'Sesi Tatap Muka', href: '/counseling/sessions' },
        ]"
    >
        <Head title="Sesi Konseling Tatap Muka" />

        <div class="flex flex-col gap-6 p-6">
            <!-- Header -->
            <div>
                <h1
                    class="flex items-center gap-2 text-2xl font-bold tracking-tight text-foreground"
                >
                    <BookOpen
                        class="size-7 text-indigo-600 dark:text-indigo-400"
                    />
                    Sesi Konseling Tatap Muka
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Jadwal pertemuan tatap muka dan pengisian rekam hasil
                    konseling yang bersifat rahasia.
                </p>
            </div>

            <!-- Sessions Table -->
            <div
                class="overflow-hidden rounded-2xl border border-border bg-card shadow-sm"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-border bg-muted/50">
                            <tr>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    Siswa & Kelas
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    Bidang
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    Waktu & Tempat
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    Pembimbing BK
                                </th>
                                <th
                                    class="px-5 py-3.5 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    Status Rekam
                                </th>
                                <th
                                    class="px-5 py-3.5 text-right text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr
                                v-for="session in sessions.data"
                                :key="session.id"
                                class="transition-colors hover:bg-muted/30"
                            >
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-foreground">
                                        {{ session.student_name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        NIS: {{ session.student_nis }} ·
                                        {{ session.class_name }}
                                    </p>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="inline-flex rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300"
                                    >
                                        {{ session.counseling_type }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    <p
                                        class="flex items-center gap-1.5 font-semibold text-foreground"
                                    >
                                        <Calendar
                                            class="size-3.5 text-indigo-600"
                                        />
                                        {{ session.session_date }}
                                    </p>
                                    <p
                                        class="mt-0.5 flex items-center gap-1.5 text-muted-foreground"
                                    >
                                        <MapPin class="size-3.5" />
                                        {{ session.venue }}
                                    </p>
                                </td>
                                <td
                                    class="px-5 py-4 text-xs font-medium text-foreground"
                                >
                                    {{ session.counselor_name }}
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        v-if="session.has_record"
                                        class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700 dark:bg-green-950 dark:text-green-300"
                                    >
                                        <CheckCircle2 class="size-3" />
                                        Hasil Tercatat
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-300"
                                    >
                                        <Clock class="size-3" />
                                        Belum Direkam
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <button
                                        v-if="
                                            !session.has_record &&
                                            can('counseling.record')
                                        "
                                        @click="openRecordModal(session)"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700"
                                    >
                                        <FilePlus2 class="size-3.5" />
                                        Rekam Hasil BK
                                    </button>
                                    <span
                                        v-else-if="session.has_record"
                                        class="text-xs text-muted-foreground italic"
                                    >
                                        Sesi Selesai
                                    </span>
                                </td>
                            </tr>

                            <tr v-if="sessions.data.length === 0">
                                <td
                                    colspan="6"
                                    class="py-12 text-center text-sm text-muted-foreground"
                                >
                                    Belum ada sesi konseling yang dijadwalkan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="sessions.total > 15"
                    class="flex items-center justify-between border-t border-border px-5 py-3 text-xs text-muted-foreground"
                >
                    <span
                        >Menampilkan {{ sessions.data.length }} dari total
                        {{ sessions.total }} sesi</span
                    >
                    <div class="flex gap-1">
                        <Link
                            v-for="(link, i) in sessions.links"
                            :key="i"
                            :href="link.url || '#'"
                            :class="[
                                'rounded-lg px-2.5 py-1 transition-colors',
                                link.active
                                    ? 'bg-indigo-600 font-bold text-white'
                                    : 'text-foreground hover:bg-muted',
                                !link.url && 'pointer-events-none opacity-50',
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>

            <!-- Record Modal -->
            <AppModal
                :show="showRecordModal && !!selectedSession"
                @close="showRecordModal = false"
                max-width="2xl"
            >
                <template #header>
                    <div class="flex items-start justify-between">
                        <div>
                            <h2
                                class="text-xl font-extrabold tracking-tight text-foreground"
                            >
                                Rekam Hasil Sesi Konseling
                            </h2>
                            <p class="mt-1 text-xs text-muted-foreground">
                                Siswa:
                                <span class="font-bold text-foreground">{{
                                    selectedSession?.student_name
                                }}</span>
                                · {{ selectedSession?.counseling_type }}
                            </p>
                        </div>
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-700 dark:bg-rose-950 dark:text-rose-300"
                        >
                            <Lock class="size-3" />
                            Rahasia BK
                        </span>
                    </div>
                </template>

                <form
                    v-if="selectedSession"
                    @submit.prevent="submitRecord"
                    class="space-y-4"
                >
                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-foreground"
                        >
                            Topik Pembahasan
                            <span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="recordForm.topic"
                            type="text"
                            placeholder="Contoh: Kesulitan belajar matematika dan manajemen waktu"
                            required
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>

                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-foreground"
                        >
                            Uraian Masalah <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            v-model="recordForm.problem_description"
                            required
                            minlength="20"
                            rows="3"
                            placeholder="Uraikan gambaran masalah yang diutarakan siswa selama sesi konseling..."
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-foreground"
                        >
                            Catatan Hasil Konseling / Solusi yang Disepakati
                            <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            v-model="recordForm.counseling_notes"
                            required
                            minlength="20"
                            rows="3"
                            placeholder="Catat respons siswa, pemahaman baru yang dicapai, dan komitmen perubahan..."
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold text-foreground"
                                >Diagnosis / Asesmen BK</label
                            >
                            <textarea
                                v-model="recordForm.assessment_diagnosis"
                                rows="2"
                                placeholder="Analisis profesional konselor..."
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                            ></textarea>
                        </div>
                        <div>
                            <label
                                class="mb-1.5 block text-xs font-semibold text-foreground"
                                >Rekomendasi</label
                            >
                            <textarea
                                v-model="recordForm.recommendations"
                                rows="2"
                                placeholder="Rekomendasi tindakan..."
                                class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                            ></textarea>
                        </div>
                    </div>

                    <div>
                        <label
                            class="mb-1.5 block text-xs font-semibold text-foreground"
                            >Rencana Tindak Lanjut (Follow-Up)</label
                        >
                        <textarea
                            v-model="recordForm.follow_up_plan"
                            rows="2"
                            placeholder="Jadwal follow up berikutnya atau koordinasi dengan orang tua/wali kelas jika diperlukan..."
                            class="w-full rounded-2xl border border-input bg-background px-3.5 py-2.5 text-sm text-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div
                        class="flex items-center justify-end gap-3 border-t border-border/80 pt-4"
                    >
                        <button
                            type="button"
                            @click="showRecordModal = false"
                            class="cursor-pointer rounded-xl border border-border px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-muted"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="recordForm.processing"
                            class="cursor-pointer rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-xs transition-colors hover:bg-indigo-700 disabled:opacity-50"
                        >
                            {{
                                recordForm.processing
                                    ? 'Menyimpan...'
                                    : 'Simpan Rekam Konseling'
                            }}
                        </button>
                    </div>
                </form>
            </AppModal>
        </div>
    </AppLayout>
</template>
