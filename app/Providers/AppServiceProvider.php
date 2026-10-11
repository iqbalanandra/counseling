<?php

namespace App\Providers;

use App\Domain\Achievement\Repositories\AchievementRepositoryInterface;
use App\Domain\Counseling\Repositories\CounselingRepositoryInterface;
use App\Domain\Permission\Repositories\PermissionRepositoryInterface;
use App\Domain\Role\Repositories\RoleRepositoryInterface;
use App\Domain\SchoolClass\Repositories\SchoolClassRepositoryInterface;
use App\Domain\Student\Repositories\StudentRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Violation\Repositories\ViolationRepositoryInterface;
use App\Infrastructure\Persistence\Repositories\AchievementRepository;
use App\Infrastructure\Persistence\Repositories\CounselingRepository;
use App\Infrastructure\Persistence\Repositories\PermissionRepository;
use App\Infrastructure\Persistence\Repositories\RoleRepository;
use App\Infrastructure\Persistence\Repositories\SchoolClassRepository;
use App\Infrastructure\Persistence\Repositories\StudentRepository;
use App\Infrastructure\Persistence\Repositories\UserRepository;
use App\Infrastructure\Persistence\Repositories\ViolationRepository;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);
        $this->app->bind(SchoolClassRepositoryInterface::class, SchoolClassRepository::class);
        $this->app->bind(CounselingRepositoryInterface::class, CounselingRepository::class);
        $this->app->bind(ViolationRepositoryInterface::class, ViolationRepository::class);
        $this->app->bind(AchievementRepositoryInterface::class, AchievementRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        Gate::before(function ($user, string $ability) {
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return true;
            }

            if (method_exists($user, 'hasPermission') && $user->hasPermission($ability)) {
                return true;
            }

            return null;
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
