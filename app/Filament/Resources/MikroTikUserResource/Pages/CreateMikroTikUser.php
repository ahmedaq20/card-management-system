<?php

namespace App\Filament\Resources\MikroTikUserResource\Pages;

use RouterOS\Query;
use RouterOS\Client;
use App\Models\MikroTikUser;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\MikroTikUserResource;
use Illuminate\Database\Eloquent\Model;

class CreateMikroTikUser extends CreateRecord
{
    protected static string $resource = MikroTikUserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    protected function handleRecordCreation(array $data): Model
    {
        try {
            // Connect to MikroTik API
            $client = new Client([
                'host' => env('MIKROTIK_HOST'),
                'user' => env('MIKROTIK_USERNAME'),
                'pass' => env('MIKROTIK_PASSWORD'),
                'port' => (int) env('MIKROTIK_PORT', 8728),
            ]);

            // Query active users
            $query = new Query('/ip/hotspot/active/print');
            $activeUsers = $client->query($query)->read();

            // Find the user in the API response
            $userData = collect($activeUsers)->firstWhere('user', $data['user_in_network']);
    //      dd($userData); // Debugging line to check the user data

            if ($userData) {
                // Update the data array with API information
                $data['last_ip_address'] = $userData['address'] ?? null;
                $data['last_mac'] = $userData['mac-address'] ?? null;
                $data['is_active'] = true;
                $data['comment'] = $userData['comment'] ?? 'لا يوجد ملاحظات';
            } else {
                // Mark the user as inactive if not found in the API
                $data['is_active'] = false;
                $data['comment'] = 'المستخدم غير نشط في الشبكة';
            }
        } catch (\Throwable $e) {
            // Handle API connection errors
            $data['is_active'] = false;
            $data['comment'] = 'فشل في الاتصال بـ MikroTik: ' . $e->getMessage();
        }

        // Create the user record in the database
        return MikroTikUser::create($data);
    }
}