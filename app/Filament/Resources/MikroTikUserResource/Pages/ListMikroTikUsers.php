<?php

namespace App\Filament\Resources\MikroTikUserResource\Pages;

use App\Filament\Resources\MikroTikUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMikroTikUsers extends ListRecords
{
    protected static string $resource = MikroTikUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
