<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use RouterOS\Client;
use RouterOS\Query;

class ActiveUsersList extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wifi';
    protected static ?string $navigationGroup = 'شبكة الميكروتك';

    // protected static ?string $title = 'قائمة المستخدمين النشطين';
    //  protected static ?string $title='s';
    protected static string $view = 'filament.pages.active-users-list';

    public $activeUsers = [];
    public string $search = '';

    public function mount(): void
    {
        try {
            $client = new Client([
                'host' => env('MIKROTIK_HOST'),
                'user' => env('MIKROTIK_USERNAME'),
                'pass' => env('MIKROTIK_PASSWORD'),
                'port' => (int) env('MIKROTIK_PORT', 8728),
            ]);

            $query = new Query('/ip/hotspot/active/print');
            $response = $client->query($query)->read();

            $this->activeUsers = $response;
        } catch (\Throwable $th) {
            $this->activeUsers = [];
        }

        // dd($response);

    }

    public function getTitle(): string 
    {
        return static::$title ?? '';
    }

    
  
}