<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'ho_ten' => 'Administrator',
            'email' => 'admin@example.com',
            'mat_khau' => Hash::make('password'), // Default password
            'vai_tro' => 'admin',
            'trang_thai' => 'hoat_dong',
        ]);
    }
}
