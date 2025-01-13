<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Coke',
                'price' => 3.99,
                'quantity_available' => 50
            ],
            [
                'name' => 'Pepsi',
                'price' => 6.885,
                'quantity_available' => 50
            ],
            [
                'name' => 'Water',
                'price' => 0.50,
                'quantity_available' => 100
            ]
        ];
        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
