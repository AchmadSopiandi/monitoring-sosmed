<?php

namespace Database\Seeders;

use App\Models\Sentiment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        collect([
            [Sentiment::POSITIVE, 'Positif', '#15803d'],
            [Sentiment::NEUTRAL, 'Netral', '#a16207'],
            [Sentiment::NEGATIVE, 'Negatif', '#b91c1c'],
        ])->each(fn (array $sentiment) => Sentiment::firstOrCreate(
            ['name' => $sentiment[0]],
            ['label' => $sentiment[1], 'color' => $sentiment[2]]
        ));

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );
    }
}
