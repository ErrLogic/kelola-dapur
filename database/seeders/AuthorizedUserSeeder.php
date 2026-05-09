<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AuthorizedUser;

class AuthorizedUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'telegram_user_id' => 329481927,
                'telegram_username' => '@crackmyapple',
                'display_name' => 'Agik Bika Ristiawan',
                'role' => 'user'
            ],
            [
                'telegram_user_id' => 620075228,
                'telegram_username' => '@Fruviany',
                'display_name' => 'Febrina Ruviany',
                'role' => 'user'
            ],
        ];

        foreach ($users as $user) {
            AuthorizedUser::create($user);
        }
    }
}
