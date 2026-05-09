<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['code' => 'kg', 'name' => 'Kilogram', 'category' => 'WEIGHT'],
            ['code' => 'g', 'name' => 'Gram', 'category' => 'WEIGHT'],
            ['code' => 'mg', 'name' => 'Miligram', 'category' => 'WEIGHT'],
            ['code' => 'ons', 'name' => 'Ons', 'category' => 'WEIGHT'],
            ['code' => 'l', 'name' => 'Liter', 'category' => 'VOLUME'],
            ['code' => 'ml', 'name' => 'Mililiter', 'category' => 'VOLUME'],
            ['code' => 'sdt', 'name' => 'Sendok Teh', 'category' => 'SPOON'],
            ['code' => 'sdm', 'name' => 'Sendok Makan', 'category' => 'SPOON'],
            ['code' => 'pcs', 'name' => 'pcs', 'category' => 'QUANTITY'],
            ['code' => 'butir', 'name' => 'butir', 'category' => 'QUANTITY'],
            ['code' => 'buah', 'name' => 'buah', 'category' => 'QUANTITY'],
            ['code' => 'lembar', 'name' => 'lembar', 'category' => 'QUANTITY'],
            ['code' => 'batang', 'name' => 'batang', 'category' => 'QUANTITY'],
            ['code' => 'siung', 'name' => 'siung', 'category' => 'QUANTITY'],
            ['code' => 'ruas', 'name' => 'ruas', 'category' => 'QUANTITY'],
            ['code' => 'ikat', 'name' => 'ikat', 'category' => 'QUANTITY'],
            ['code' => 'tangkai', 'name' => 'tangkai', 'category' => 'QUANTITY'],
            ['code' => 'bungkus', 'name' => 'bungkus', 'category' => 'QUANTITY'],
            ['code' => 'sachet', 'name' => 'sachet', 'category' => 'QUANTITY'],
            ['code' => 'kaleng', 'name' => 'kaleng', 'category' => 'QUANTITY'],
            ['code' => 'botol', 'name' => 'botol', 'category' => 'QUANTITY'],
            ['code' => 'gelas', 'name' => 'gelas', 'category' => 'QUANTITY'],
            ['code' => 'cangkir', 'name' => 'cangkir', 'category' => 'QUANTITY'],
            ['code' => 'mangkuk', 'name' => 'mangkuk', 'category' => 'QUANTITY'],
            ['code' => 'potong', 'name' => 'potong', 'category' => 'QUANTITY'],
            ['code' => 'ekor', 'name' => 'ekor', 'category' => 'QUANTITY'],
            ['code' => 'secukupnya', 'name' => 'secukupnya', 'category' => 'SPECIAL'],
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
