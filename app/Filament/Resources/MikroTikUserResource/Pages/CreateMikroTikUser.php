<?php

namespace App\Filament\Resources\MikroTikUserResource\Pages;

use RouterOS\Query;
use RouterOS\Client;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\MikroTikUserResource;
use Illuminate\Database\Eloquent\Model;
use App\Models\MikroTikUser;

class CreateMikroTikUser extends CreateRecord
{
    protected static string $resource = MikroTikUserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        // Create the user record in the database
        $mikrotikUser = MikroTikUser::create($data);

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

            if ($userData) {
                // Update the MikroTikUser record with API information
                $mikrotikUser->update([
                    'last_ip_address' => $userData['address'] ?? null,
                    'last_mac' => $userData['mac-address'] ?? null,
                    'is_active' => true,
                    'comment' => $userData['comment'] ?? 'لا يوجد ملاحظات',
                ]);
            } else {
                // Mark the user as inactive if not found in the API
                $mikrotikUser->update([
                    'is_active' => false,
                    'comment' => 'المستخدم غير نشط في الشبكة',
                ]);
            }
        } catch (\Throwable $e) {
            // Handle API connection errors and update the record
            $mikrotikUser->update([
                'is_active' => false,
                'comment' => 'فشل في الاتصال بـ MikroTik: ' . $e->getMessage(),
            ]);
        }

        return $mikrotikUser;
    }
}