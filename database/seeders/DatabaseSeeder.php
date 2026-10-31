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
     *
     * Uses firstOrCreate so the seeder is safe to run repeatedly: existing
     * users and products (including stock/price changes made by the owner)
     * are never overwritten.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );

        $this->firstOrCreateProduct('Isi Ulang Air Galon', 'galon', 6000, 0, 0, false);
        $this->firstOrCreateProduct('Galon Kosong Baru', 'pcs', 45000, 20, 5, true);
        $this->firstOrCreateProduct('Air Galon + Galon (Paket)', 'pcs', 65000, 10, 3, true);
        $this->firstOrCreateProduct('Segel/Tutup Galon', 'pcs', 2000, 100, 20, true);
    }

    private function firstOrCreateProduct(
        string $name,
        string $unit,
        int $price,
        int $stock,
        int $minStock,
        bool $trackStock
    ): void {
        Product::firstOrCreate(
            ['name' => $name],
            [
                'unit' => $unit,
                'price' => $price,
                'stock' => $stock,
                'min_stock' => $minStock,
                'track_stock' => $trackStock,
                'is_active' => true,
            ]
        );
    }
}
