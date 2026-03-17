<?php

namespace App\Observers;

use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Enviar notificação a todos os administradores, por exemplo:
        $admins = User::where('email', 'admin@admin.com')->get();

        FilamentNotification::make()
            ->title('Novo usuário criado')
            ->body("O usuário {$user->name} foi cadastrado.")
            ->success()
            ->sendToDatabase($admins);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
