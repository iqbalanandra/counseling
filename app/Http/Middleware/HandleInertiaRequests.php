<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        if ($user) {
            $user->loadMissing(['roles.permissions', 'student.schoolClass', 'teacher.homeroomClass']);
        }

        return [
            ...parent::share($request),
            'name' => config('app.name', 'Sistem Informasi BK'),
            'school' => \App\Models\SchoolSetting::getSettings(),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'identifier_number' => $user->identifier_number,
                    'status' => $user->status,
                    'is_system_user' => (bool) $user->is_system_user,
                    'avatar_url' => $user->avatar_url,
                    'roles' => $user->roles->pluck('name')->toArray(),
                    'permissions' => $user->getAllPermissions(),
                    'student' => $user->student ? [
                        'id' => $user->student->id,
                        'nis' => $user->student->nis,
                        'class_id' => $user->student->class_id,
                        'class_name' => $user->student->schoolClass?->name,
                        'total_violation_points' => $user->student->total_violation_points,
                        'total_achievement_points' => $user->student->total_achievement_points,
                    ] : null,
                    'teacher' => $user->teacher ? [
                        'id' => $user->teacher->id,
                        'nip' => $user->teacher->nip,
                        'position' => $user->teacher->position,
                        'homeroom_class' => $user->teacher->homeroomClass ? [
                            'id' => $user->teacher->homeroomClass->id,
                            'name' => $user->teacher->homeroomClass->name,
                        ] : null,
                    ] : null,
                ] : null,
                'notifications' => $user ? $user->appNotifications()->latest()->take(6)->get()->map(fn ($n) => [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'type' => $n->type,
                    'link' => $n->link,
                    'is_read' => $n->read_at !== null,
                    'created_at' => $n->created_at?->diffForHumans(),
                ]) : [],
                'unread_notifications_count' => $user ? $user->appNotifications()->whereNull('read_at')->count() : 0,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'message' => $request->session()->get('message'),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
