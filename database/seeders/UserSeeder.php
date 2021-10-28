<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Not using factory because I want this exact credentials in this case
        $users = [
            [
                'name' => 'Test User',
                'email' => 'admin@example.com',
                'email_verified_at' => now(),
                'password' => bcrypt('password')
            ]
        ];

        foreach ($users as $user) {
            User::firstOrCreate($user);
        }
    }
}
