<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyStatus;

class CompanyStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Prospekt',
                'slug' => 'prospekt',
                'color' => '#10B981', // green-500
                'icon' => 'circle',
                'order' => 1
            ],
            [
                'name' => 'Müzakere',
                'slug' => 'muzakere',
                'color' => '#F59E0B', // amber-500
                'icon' => 'clock',
                'order' => 2
            ],
            [
                'name' => 'Müşteri',
                'slug' => 'musteri',
                'color' => '#3B82F6', // blue-500
                'icon' => 'check-circle',
                'order' => 3
            ],
            [
                'name' => 'Kayıp',
                'slug' => 'kayip',
                'color' => '#EF4444', // red-500
                'icon' => 'x-circle',
                'order' => 4
            ],
        ];

        foreach ($statuses as $status) {
            CompanyStatus::updateOrCreate(
                ['slug' => $status['slug']],
                $status
            );
        }
    }
}
