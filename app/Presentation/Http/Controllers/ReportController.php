<?php

namespace App\Presentation\Http\Controllers;

use App\Domain\Achievement\Repositories\AchievementRepositoryInterface;
use App\Domain\Counseling\Repositories\CounselingRepositoryInterface;
use App\Domain\SchoolClass\Repositories\SchoolClassRepositoryInterface;
use App\Domain\Student\Repositories\StudentRepositoryInterface;
use App\Domain\Violation\Repositories\ViolationRepositoryInterface;
use App\Infrastructure\Services\AuditLogService;
use App\Models\Achievement;
use App\Models\CounselingRequest;
use App\Models\CounselingSession;
use App\Models\Student;
use App\Models\Violation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly SchoolClassRepositoryInterface $classRepository,
        private readonly StudentRepositoryInterface $studentRepository,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('report.view');

        $user = Auth::user();

        return Inertia::render('Reports/Index', [
            'classes' => $this->classRepository->all()->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]),
            'canExport' => $user->hasPermission('report.export'),
        ]);
    }

    public function exportPdf(Request $request): HttpResponse
    {
        $this->authorize('report.export');

        $validated = $request->validate([
            'type' => 'required|in:counseling,violations,achievements,students',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'class_id' => 'nullable|integer|exists:classes,id',
        ]);

        $type = $validated['type'];
        $startDate = $validated['start_date'] ?? now()->startOfMonth()->toDateString();
        $endDate = $validated['end_date'] ?? now()->toDateString();
        $classId = $validated['class_id'] ?? null;

        $period = "{$startDate} s/d {$endDate}";
        $headers = [];
        $rows = [];
        $title = '';

        if ($type === 'counseling') {
            $title = 'Laporan Layanan Konseling';
            $headers = ['Nama Siswa', 'Kelas', 'Bidang Konseling', 'Tanggal Sesi', 'Konselor', 'Status'];

            $query = CounselingSession::with(['student.schoolClass', 'counselingType', 'counselor'])
                ->whereBetween('session_date', ["{$startDate} 00:00:00", "{$endDate} 23:59:59"]);

            if ($classId) {
                $query->whereHas('student', fn ($q) => $q->where('class_id', $classId));
            }

            $rows = $query->get()->map(fn ($s) => [
                $s->student?->name ?? '—',
                $s->student?->schoolClass?->name ?? '—',
                $s->counselingType?->name ?? '—',
                $s->session_date?->format('d/m/Y H:i') ?? '—',
                $s->counselor?->name ?? '—',
                ucfirst($s->status),
            ])->toArray();
        } elseif ($type === 'violations') {
            $title = 'Laporan Pelanggaran Tata Tertib Siswa';
            $headers = ['Nama Siswa', 'Kelas', 'Kategori Pelanggaran', 'Tanggal', 'Poin', 'Sanksi'];

            $query = Violation::with(['student.schoolClass', 'category'])
                ->whereBetween('violation_date', [$startDate, $endDate]);

            if ($classId) {
                $query->whereHas('student', fn ($q) => $q->where('class_id', $classId));
            }

            $rows = $query->get()->map(fn ($v) => [
                $v->student?->name ?? '—',
                $v->student?->schoolClass?->name ?? '—',
                $v->category?->name ?? '—',
                $v->violation_date?->format('d/m/Y') ?? '—',
                $v->points,
                $v->sanction_given ?? '—',
            ])->toArray();
        } elseif ($type === 'achievements') {
            $title = 'Laporan Prestasi & Kejuaraan Siswa';
            $headers = ['Nama Siswa', 'Kelas', 'Nama Prestasi', 'Tingkat', 'Peringkat', 'Tanggal', 'Status'];

            $query = Achievement::with(['student.schoolClass'])
                ->whereBetween('event_date', [$startDate, $endDate]);

            if ($classId) {
                $query->whereHas('student', fn ($q) => $q->where('class_id', $classId));
            }

            $rows = $query->get()->map(fn ($a) => [
                $a->student?->name ?? '—',
                $a->student?->schoolClass?->name ?? '—',
                $a->title,
                $a->level,
                $a->rank ?? 'Peserta',
                $a->event_date?->format('d/m/Y') ?? '—',
                ucfirst($a->verification_status),
            ])->toArray();
        } elseif ($type === 'students') {
            $title = 'Laporan Rekapitulasi Data Siswa';
            $headers = ['NIS', 'Nama Siswa', 'Kelas', 'Jenis Kelamin', 'Poin Pelanggaran', 'Poin Prestasi', 'Status'];

            $query = Student::with('schoolClass');
            if ($classId) {
                $query->where('class_id', $classId);
            }

            $rows = $query->get()->map(fn ($s) => [
                $s->nis,
                $s->name,
                $s->schoolClass?->name ?? '—',
                $s->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                $s->total_violation_points,
                $s->total_achievement_points,
                ucfirst($s->status),
            ])->toArray();
        }

        AuditLogService::log(
            action: 'export',
            module: 'report',
            description: "Mengekspor laporan PDF: {$title} (Periode: {$period})"
        );

        $pdf = Pdf::loadView('reports.pdf', [
            'title' => $title,
            'period' => $period,
            'printed_by' => Auth::user()->name,
            'generated_at' => now()->translatedFormat('d F Y, H:i'),
            'headers' => $headers,
            'rows' => $rows,
        ]);

        $fileName = str_replace(' ', '_', strtolower($title)) . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($fileName);
    }
}
