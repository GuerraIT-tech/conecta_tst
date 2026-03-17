<?php

namespace App\Observers;

use App\Models\Company;
use App\Notifications\NewUserCreatedNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\CompanyUpdatedNotification;

class CompanyObserver
{
    /**
     * Handle the Company "created" event.
     */
    public function created(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "updated" event.
     */
    public function updated(Company $company): void
    {
        // Envia para todos os usuários (ou admin)
        $users = User::all(); // ou filtre por papel, permissão etc.
        foreach ($users as $user) {
            $user->notify(new CompanyUpdatedNotification($company));
        }
    }

    /**
     * Handle the Company "deleted" event.
     */
    public function deleted(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "restored" event.
     */
    public function restored(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "force deleted" event.
     */
    public function forceDeleted(Company $company): void
    {
        //
    }
}
