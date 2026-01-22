<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Siti Cahyani',
                'email' => 'siti@example.com',
                'password' => Hash::make('password'),
                'phone' => '081234567892',
                'role' => 'regular',
                'is_guest' => false,
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Agus Arifin',
                'email' => 'agus@example.com',
                'password' => Hash::make('password'),
                'phone' => '081234567893',
                'role' => 'regular',
                'is_guest' => false,
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Dewi Putri',
                'email' => 'dewi@example.com',
                'password' => Hash::make('password'),
                'phone' => '081234567894',
                'role' => 'regular',
                'is_guest' => false,
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Rudi Aditya',
                'email' => 'rudi@example.com',
                'password' => Hash::make('password'),
                'phone' => '081234567895',
                'role' => 'regular',
                'is_guest' => false,
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Lisa Suryana',
                'email' => 'lisa@example.com',
                'password' => Hash::make('password'),
                'phone' => '081234567896',
                'role' => 'regular',
                'is_guest' => false,
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'David Pratama',
                'email' => 'david@example.com',
                'password' => Hash::make('password'),
                'phone' => '081234567897',
                'role' => 'regular',
                'is_guest' => false,
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Maria Amanda',
                'email' => 'maria@example.com',
                'password' => Hash::make('password'),
                'phone' => '081234567898',
                'role' => 'regular',
                'is_guest' => false,
                'provider' => 'google',
                'provider_id' => '123456789',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ahmad Reza',
                'email' => 'ahmad@example.com',
                'password' => Hash::make('password'),
                'phone' => '081234567899',
                'role' => 'regular',
                'is_guest' => true,
                'provider' => 'whatsapp',
                'provider_id' => '987654321',
                'status' => 'active',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
