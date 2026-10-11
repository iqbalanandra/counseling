<?php

namespace App\Presentation\Http\Controllers;

use App\Application\UseCases\User\CreateUserUseCase;
use App\Application\UseCases\User\UpdateUserUseCase;
use App\Domain\Role\Repositories\RoleRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly CreateUserUseCase $createUser,
        private readonly UpdateUserUseCase $updateUser,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('user.view');

        $users = $this->userRepository->paginate(
            perPage: 15,
            search: $request->query('search'),
            role: $request->query('role')
        );

        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get()->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'grade' => $c->grade,
            'academic_year' => $c->academicYear?->name ?? $c->academic_year,
        ]);

        return Inertia::render('Users/Index', [
            'users' => $users->through(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'identifier_number' => $user->identifier_number,
                'status' => $user->status,
                'is_system_user' => $user->is_system_user,
                'roles' => $user->roles->map(fn ($r) => ['id' => $r->id, 'name' => $r->name, 'display_name' => $r->display_name]),
                'class_name' => $user->student?->schoolClass?->name,
                'homeroom_classes' => $user->teacher ? $user->teacher->getAllHomeroomClasses()->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]) : [],
            ]),
            'filters' => $request->only('search', 'role'),
            'roles' => $this->roleRepository->all()->map(fn ($r) => ['id' => $r->id, 'name' => $r->name, 'display_name' => $r->display_name]),
            'classes' => $classes,
        ]);
    }

    public function show(User $user): Response
    {
        $this->authorize('user.view');

        $user = $this->userRepository->findById($user->id);

        $classes = SchoolClass::orderBy('grade')->orderBy('name')->get()->map(fn ($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'grade' => $c->grade,
            'academic_year' => $c->academicYear?->name ?? $c->academic_year,
        ]);

        return Inertia::render('Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'identifier_number' => $user->identifier_number,
                'gender' => $user->student?->gender ?? $user->teacher?->gender ?? 'L',
                'status' => $user->status,
                'is_system_user' => $user->is_system_user,
                'roles' => $user->roles->map(fn ($r) => ['id' => $r->id, 'name' => $r->name, 'display_name' => $r->display_name]),
                'permissions' => $user->getAllPermissions(),
                'student' => $user->student,
                'student_class_id' => $user->student?->class_id,
                'teacher' => $user->teacher,
                'homeroom_classes' => $user->teacher ? $user->teacher->getAllHomeroomClasses()->map(fn ($c) => ['id' => $c->id, 'name' => $c->name]) : [],
                'homeroom_class_ids' => $user->teacher ? $user->teacher->getAllHomeroomClassIds() : [],
            ],
            'roles' => $this->roleRepository->all()->map(fn ($r) => ['id' => $r->id, 'name' => $r->name, 'display_name' => $r->display_name]),
            'classes' => $classes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('user.create');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'identifier_number' => 'nullable|string|max:50',
            'gender' => 'nullable|in:L,P',
            'password' => 'required|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'integer|exists:roles,id',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'integer|exists:classes,id',
            'student_class_id' => 'nullable|integer|exists:classes,id',
        ]);

        $user = $this->createUser->execute($validated);

        // Role checks
        $roleIds = $validated['role_ids'] ?? [];
        $waliKelasRoleId = Role::where('name', 'Wali Kelas')->value('id');
        $siswaRoleId = Role::where('name', 'Siswa')->value('id');
        $guruBkRoleId = Role::where('name', 'Guru BK')->value('id');
        $kepalaRoleId = Role::where('name', 'Kepala Sekolah')->value('id');

        $isWaliKelas = in_array($waliKelasRoleId, $roleIds);
        $isSiswa = in_array($siswaRoleId, $roleIds);
        $isGuruBk = in_array($guruBkRoleId, $roleIds);
        $isKepala = in_array($kepalaRoleId, $roleIds);

        // Wali Kelas profile and classes sync
        if ($isWaliKelas || ! empty($validated['class_ids'])) {
            $teacher = Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'nip' => $user->identifier_number ?? null,
                    'phone' => $user->phone ?? null,
                    'gender' => $validated['gender'] ?? 'L',
                    'position' => 'Wali Kelas',
                ]
            );

            $classIds = $validated['class_ids'] ?? [];
            $teacher->homeroomClasses()->sync($classIds);

            if (! empty($classIds)) {
                SchoolClass::whereIn('id', $classIds)->update(['homeroom_teacher_id' => $teacher->id]);
            }
        } elseif ($isGuruBk || $isKepala) {
            Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'nip' => $user->identifier_number ?? null,
                    'phone' => $user->phone ?? null,
                    'gender' => $validated['gender'] ?? 'L',
                    'position' => $isGuruBk ? 'Guru BK' : 'Kepala Sekolah',
                ]
            );
        }

        // Siswa profile sync
        if ($isSiswa) {
            $student = null;
            if (! empty($validated['identifier_number'])) {
                $student = Student::where('nis', $validated['identifier_number'])->first();
            }

            if ($student) {
                $student->update([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'class_id' => $validated['student_class_id'] ?? $student->class_id,
                    'gender' => $validated['gender'] ?? $student->gender,
                ]);
            } else {
                Student::create([
                    'user_id' => $user->id,
                    'nis' => $validated['identifier_number'] ?? ('S'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT)),
                    'name' => $user->name,
                    'gender' => $validated['gender'] ?? 'L',
                    'class_id' => $validated['student_class_id'] ?? null,
                    'status' => 'active',
                ]);
            }
        }

        return back()->with('success', "Akun pengguna '{$user->name}' berhasil dibuat.");
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('user.update');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'nullable|string|max:20',
            'identifier_number' => 'nullable|string|max:50',
            'gender' => 'nullable|in:L,P',
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'integer|exists:roles,id',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'integer|exists:classes,id',
            'student_class_id' => 'nullable|integer|exists:classes,id',
        ]);

        $this->updateUser->execute($user, $validated);

        // Role checks
        $roleIds = $validated['role_ids'] ?? [];
        $waliKelasRoleId = Role::where('name', 'Wali Kelas')->value('id');
        $siswaRoleId = Role::where('name', 'Siswa')->value('id');
        $guruBkRoleId = Role::where('name', 'Guru BK')->value('id');
        $kepalaRoleId = Role::where('name', 'Kepala Sekolah')->value('id');

        $isWaliKelas = in_array($waliKelasRoleId, $roleIds);
        $isSiswa = in_array($siswaRoleId, $roleIds);
        $isGuruBk = in_array($guruBkRoleId, $roleIds);
        $isKepala = in_array($kepalaRoleId, $roleIds);

        // Wali Kelas / Teacher Sync
        if ($isWaliKelas || ! empty($validated['class_ids'])) {
            $teacher = Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'nip' => $user->identifier_number ?? null,
                    'phone' => $user->phone ?? null,
                    'gender' => $validated['gender'] ?? 'L',
                    'position' => 'Wali Kelas',
                ]
            );

            $classIds = $validated['class_ids'] ?? [];
            $teacher->homeroomClasses()->sync($classIds);

            if (! empty($classIds)) {
                SchoolClass::whereIn('id', $classIds)->update(['homeroom_teacher_id' => $teacher->id]);
            }
            SchoolClass::where('homeroom_teacher_id', $teacher->id)
                ->whereNotIn('id', $classIds)
                ->update(['homeroom_teacher_id' => null]);
        } elseif ($isGuruBk || $isKepala) {
            Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'nip' => $user->identifier_number ?? null,
                    'phone' => $user->phone ?? null,
                    'gender' => $validated['gender'] ?? 'L',
                    'position' => $isGuruBk ? 'Guru BK' : 'Kepala Sekolah',
                ]
            );
        } elseif (! $isWaliKelas && $user->teacher) {
            $user->teacher->homeroomClasses()->sync([]);
            SchoolClass::where('homeroom_teacher_id', $user->teacher->id)->update(['homeroom_teacher_id' => null]);
        }

        // Siswa sync
        if ($isSiswa) {
            if ($user->student) {
                $user->student->update([
                    'name' => $user->name,
                    'class_id' => $validated['student_class_id'] ?? $user->student->class_id,
                    'gender' => $validated['gender'] ?? $user->student->gender,
                    'nis' => $validated['identifier_number'] ?? $user->student->nis,
                ]);
            } else {
                Student::create([
                    'user_id' => $user->id,
                    'nis' => $validated['identifier_number'] ?? ('S'.str_pad((string) $user->id, 6, '0', STR_PAD_LEFT)),
                    'name' => $user->name,
                    'gender' => $validated['gender'] ?? 'L',
                    'class_id' => $validated['student_class_id'] ?? null,
                    'status' => 'active',
                ]);
            }
        }

        return back()->with('success', "Akun pengguna '{$user->name}' berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('user.delete');

        if ($user->is_system_user) {
            return back()->with('error', 'Akun system tidak dapat dihapus.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')->with('success', "Akun pengguna '{$name}' berhasil dihapus.");
    }
}
