<?php

namespace App\Filament\Resources\PremiumMembershipResource\Pages;

use App\Filament\Resources\PremiumMembershipResource;
use App\Models\PremiumMembership;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPremiumMembership extends EditRecord
{
    protected static string $resource = PremiumMembershipResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->load('user');
        
        if ($this->record->user) {
            $data['user']['name'] = $this->record->user->name;
            $data['user']['email'] = $this->record->user->email;
            $data['user']['phone'] = $this->record->user->phone;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If changing status to active, set started_at and expires_at
        if ($data['status'] === 'active' && $this->record->status !== 'active') {
            $data['started_at'] = now();
            $data['expires_at'] = now()->addYear();

            // Update user's premium_expires_at
            $this->record->user->update(['premium_expires_at' => now()->addYear()]);
        }

        return $data;
    }
}
