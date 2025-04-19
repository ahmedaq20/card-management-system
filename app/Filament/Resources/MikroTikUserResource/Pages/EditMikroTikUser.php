<?php

namespace App\Filament\Resources\MikroTikUserResource\Pages;

use App\Filament\Resources\MikroTikUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMikroTikUser extends EditRecord
{
    protected static string $resource = MikroTikUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
