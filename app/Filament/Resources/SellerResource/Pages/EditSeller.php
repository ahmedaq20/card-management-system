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

    public function getTabs():array{

        return [

            'All' =>Tab::make(),
            'with Cards' =>Tab::make()->modifyQueryUsing(function($query){
                $query->where('with_cards',1);
            }),
            'without Cards' =>Tab::make()->modifyQueryUsing(function($query){
                $query->where('with_cards',0);
            }),
        ];

    }
}