<?php

namespace App\Providers;

use App\Models\Child;
use App\Models\Appointment;
use App\Models\VaccinationRecord;
use App\Models\ParentRequest;
use App\Models\Notification;
use App\Models\Hospital;
use App\Models\User;
use App\Policies\ChildPolicy;
use App\Policies\AppointmentPolicy;
use App\Policies\VaccinationRecordPolicy;
use App\Policies\ParentRequestPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\HospitalPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Child::class => ChildPolicy::class,
        Appointment::class => AppointmentPolicy::class,
        VaccinationRecord::class => VaccinationRecordPolicy::class,
        ParentRequest::class => ParentRequestPolicy::class,
        Notification::class => NotificationPolicy::class,
        Hospital::class => HospitalPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Portal access gates
        Gate::define('view-admin-portal', fn(User $user) => $user->isAdmin());
        Gate::define('view-parent-portal', fn(User $user) => $user->isParent());
        Gate::define('view-hospital-portal', fn(User $user) => $user->isHospital());
    }
}
