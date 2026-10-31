<?php

namespace Database\Seeders;

use App\Models\Product;
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
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('password'),
        ]);

        Product::create([
            'name' => 'Isi Ulang Air Galon',
            'unit' => 'galon',
            'price' => 6000,
            'stock' => 0,
            'min_stock' => 0,
            'track_stock' => false,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Galon Kosong Baru',
            'unit' => 'pcs',
            'price' => 45000,
            'stock' => 20,
            'min_stock' => 5,
            'track_stock' => true,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Air Galon + Galon (Paket)',
            'unit' => 'pcs',
            'price' => 65000,
            'stock' => 10,
            'min_stock' => 3,
            'track_stock' => true,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Segel/Tutup Galon',
            'unit' => 'pcs',
            'price' => 2000,
            'stock' => 100,
            'min_stock' => 20,
            'track_stock' => true,
            'is_active' => true,
        ]);
    }
}