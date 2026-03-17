<?php

namespace App\Filament\Resources\BidsResource\Pages;

use App\Filament\Resources\BidsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditBids extends EditRecord
{
    protected static string $resource = BidsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),

            Actions\Action::make('toggleFavorite')
            ->label(fn ($record) => auth()->user()->favoriteBids->contains($record->id)
                ? 'Remover dos Favoritos'
                : 'Favoritar')
            ->icon(fn ($record) => auth()->user()->favoriteBids->contains($record->id)
                ? 'heroicon-o-star'
                : 'heroicon-o-star')
            ->color(fn ($record) => auth()->user()->favoriteBids->contains($record->id)
                ? 'warning'
                : 'gray')
            ->action(function ($record) {
                $user = auth()->user();

                if ($user->favoriteBids()->where('bid_id', $record->id)->exists()) {
                    $user->favoriteBids()->detach($record->id);
                    Notification::make()
                        ->title('Removido dos favoritos')
                        ->success()
                        ->send();
                } else {
                    $user->favoriteBids()->attach($record->id);
                    Notification::make()
                        ->title('Adicionado aos favoritos')
                        ->success()
                        ->send();
                }
            })
            ->requiresConfirmation(),
        ];
    }
}
