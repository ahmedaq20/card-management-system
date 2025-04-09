<?php

namespace App\Filament\Resources\SellerResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Infolists\Components\Tabs;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SellerResource;

class EditSeller extends EditRecord
{
    protected static string $resource = SellerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    
}