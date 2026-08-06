<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Category 1: Beverages
            [
                'category_id' => 1,
                'name' => 'Coca Cola 330ml',
                'barcode' => '885195913001',
                'cost_price' => 0.40,
                'selling_price' => 0.75,
                'stock' => 100,
            ],
            [
                'category_id' => 1,
                'name' => 'Coca Cola Zero',
                'barcode' => '885195913002',
                'cost_price' => 0.40,
                'selling_price' => 0.75,
                'stock' => 80,
            ],
            [
                'category_id' => 1,
                'name' => 'Pepsi 330ml',
                'barcode' => '885195913003',
                'cost_price' => 0.40,
                'selling_price' => 0.75,
                'stock' => 80,
            ],
            [
                'category_id' => 1,
                'name' => 'Sprite',
                'barcode' => '885195913004',
                'cost_price' => 0.40,
                'selling_price' => 0.75,
                'stock' => 60,
            ],
            [
                'category_id' => 1,
                'name' => 'Fanta Orange',
                'barcode' => '885195913005',
                'cost_price' => 0.40,
                'selling_price' => 0.75,
                'stock' => 50,
            ],
            [
                'category_id' => 1,
                'name' => '7UP',
                'barcode' => '885195913006',
                'cost_price' => 0.40,
                'selling_price' => 0.75,
                'stock' => 50,
            ],
            [
                'category_id' => 1,
                'name' => 'Red Bull',
                'barcode' => '885195913007',
                'cost_price' => 0.80,
                'selling_price' => 1.50,
                'stock' => 120,
            ],
            [
                'category_id' => 1,
                'name' => 'Pocari Sweat',
                'barcode' => '885195913008',
                'cost_price' => 0.60,
                'selling_price' => 1.10,
                'stock' => 70,
            ],
            [
                'category_id' => 1,
                'name' => 'Angkor Beer',
                'barcode' => '885195913009',
                'cost_price' => 0.65,
                'selling_price' => 1.20,
                'stock' => 200,
            ],
            [
                'category_id' => 1,
                'name' => 'ABC Beer',
                'barcode' => '885195913010',
                'cost_price' => 1.10,
                'selling_price' => 2.00,
                'stock' => 150,
            ],

            // Category 2: Snacks
            [
                'category_id' => 2,
                'name' => 'Oreo',
                'barcode' => '885195913011',
                'cost_price' => 0.50,
                'selling_price' => 1.00,
                'stock' => 90,
            ],
            [
                'category_id' => 2,
                'name' => 'Lays Original',
                'barcode' => '885195913012',
                'cost_price' => 0.80,
                'selling_price' => 1.50,
                'stock' => 60,
            ],
            [
                'category_id' => 2,
                'name' => 'Lays BBQ',
                'barcode' => '885195913013',
                'cost_price' => 0.80,
                'selling_price' => 1.50,
                'stock' => 60,
            ],
            [
                'category_id' => 2,
                'name' => 'Pringles',
                'barcode' => '885195913014',
                'cost_price' => 1.50,
                'selling_price' => 2.50,
                'stock' => 45,
            ],
            [
                'category_id' => 2,
                'name' => 'Doritos',
                'barcode' => '885195913015',
                'cost_price' => 1.20,
                'selling_price' => 2.20,
                'stock' => 40,
            ],
            [
                'category_id' => 2,
                'name' => 'KitKat',
                'barcode' => '885195913016',
                'cost_price' => 0.60,
                'selling_price' => 1.20,
                'stock' => 100,
            ],
            [
                'category_id' => 2,
                'name' => 'M&M',
                'barcode' => '885195913017',
                'cost_price' => 0.70,
                'selling_price' => 1.35,
                'stock' => 85,
            ],
            [
                'category_id' => 2,
                'name' => 'Potato Chips',
                'barcode' => '885195913018',
                'cost_price' => 0.50,
                'selling_price' => 1.00,
                'stock' => 75,
            ],

            // Category 3: Instant Noodles
            [
                'category_id' => 3,
                'name' => 'Indomie Chicken',
                'barcode' => '885195913019',
                'cost_price' => 0.25,
                'selling_price' => 0.50,
                'stock' => 150,
            ],
            [
                'category_id' => 3,
                'name' => 'Mama Pork',
                'barcode' => '885195913020',
                'cost_price' => 0.25,
                'selling_price' => 0.50,
                'stock' => 140,
            ],
            [
                'category_id' => 3,
                'name' => 'Mama Seafood',
                'barcode' => '885195913021',
                'cost_price' => 0.30,
                'selling_price' => 0.55,
                'stock' => 130,
            ],
            [
                'category_id' => 3,
                'name' => 'Samyang Hot Chicken',
                'barcode' => '885195913022',
                'cost_price' => 1.00,
                'selling_price' => 1.80,
                'stock' => 80,
            ],
            [
                'category_id' => 3,
                'name' => 'Mi Sedaap',
                'barcode' => '885195913023',
                'cost_price' => 0.25,
                'selling_price' => 0.50,
                'stock' => 120,
            ],

            // Category 4: Dairy
            [
                'category_id' => 4,
                'name' => 'Fresh Milk 1L',
                'barcode' => '885195913024',
                'cost_price' => 1.80,
                'selling_price' => 2.80,
                'stock' => 40,
            ],
            [
                'category_id' => 4,
                'name' => 'Chocolate Milk',
                'barcode' => '885195913025',
                'cost_price' => 0.60,
                'selling_price' => 1.20,
                'stock' => 65,
            ],
            [
                'category_id' => 4,
                'name' => 'Yogurt',
                'barcode' => '885195913026',
                'cost_price' => 0.50,
                'selling_price' => 1.00,
                'stock' => 50,
            ],
            [
                'category_id' => 4,
                'name' => 'Butter',
                'barcode' => '885195913027',
                'cost_price' => 2.20,
                'selling_price' => 3.50,
                'stock' => 30,
            ],
            [
                'category_id' => 4,
                'name' => 'Cheese Slice',
                'barcode' => '885195913028',
                'cost_price' => 1.50,
                'selling_price' => 2.60,
                'stock' => 35,
            ],

            // Category 5: Frozen Food
            [
                'category_id' => 5,
                'name' => 'Chicken Nuggets 500g',
                'barcode' => '885195913029',
                'cost_price' => 2.50,
                'selling_price' => 4.00,
                'stock' => 25,
            ],
            [
                'category_id' => 5,
                'name' => 'French Fries 1kg',
                'barcode' => '885195913030',
                'cost_price' => 2.00,
                'selling_price' => 3.50,
                'stock' => 30,
            ],

            // Category 6: Personal Care
            [
                'category_id' => 6,
                'name' => 'Pantene Shampoo',
                'barcode' => '885195913031',
                'cost_price' => 2.50,
                'selling_price' => 4.20,
                'stock' => 30,
            ],
            [
                'category_id' => 6,
                'name' => 'Head & Shoulders',
                'barcode' => '885195913032',
                'cost_price' => 2.80,
                'selling_price' => 4.50,
                'stock' => 30,
            ],
            [
                'category_id' => 6,
                'name' => 'Lux Soap',
                'barcode' => '885195913033',
                'cost_price' => 0.50,
                'selling_price' => 0.90,
                'stock' => 80,
            ],
            [
                'category_id' => 6,
                'name' => 'Colgate Toothpaste',
                'barcode' => '885195913034',
                'cost_price' => 1.20,
                'selling_price' => 2.00,
                'stock' => 50,
            ],
            [
                'category_id' => 6,
                'name' => 'Oral-B Toothbrush',
                'barcode' => '885195913035',
                'cost_price' => 0.90,
                'selling_price' => 1.70,
                'stock' => 60,
            ],

            // Category 7: Household
            [
                'category_id' => 7,
                'name' => 'Tissue',
                'barcode' => '885195913036',
                'cost_price' => 0.40,
                'selling_price' => 0.80,
                'stock' => 100,
            ],
            [
                'category_id' => 7,
                'name' => 'Laundry Detergent',
                'barcode' => '885195913037',
                'cost_price' => 3.50,
                'selling_price' => 5.80,
                'stock' => 25,
            ],
            [
                'category_id' => 7,
                'name' => 'Dishwashing Liquid',
                'barcode' => '885195913038',
                'cost_price' => 1.10,
                'selling_price' => 1.90,
                'stock' => 45,
            ],
            [
                'category_id' => 7,
                'name' => 'Garbage Bag',
                'barcode' => '885195913039',
                'cost_price' => 0.80,
                'selling_price' => 1.50,
                'stock' => 60,
            ],
            [
                'category_id' => 7,
                'name' => 'Air Freshener',
                'barcode' => '885195913040',
                'cost_price' => 1.60,
                'selling_price' => 2.80,
                'stock' => 35,
            ],

            // Category 8: Stationery
            [
                'category_id' => 8,
                'name' => 'A4 Paper Pack',
                'barcode' => '885195913041',
                'cost_price' => 2.80,
                'selling_price' => 4.50,
                'stock' => 40,
            ],
            [
                'category_id' => 8,
                'name' => 'Ballpoint Pen Set',
                'barcode' => '885195913042',
                'cost_price' => 0.70,
                'selling_price' => 1.20,
                'stock' => 70,
            ],

            // Category 9: Baby Products
            [
                'category_id' => 9,
                'name' => 'Baby Wipes 80s',
                'barcode' => '885195913043',
                'cost_price' => 1.00,
                'selling_price' => 1.80,
                'stock' => 50,
            ],
            [
                'category_id' => 9,
                'name' => 'Baby Diapers L Size',
                'barcode' => '885195913044',
                'cost_price' => 6.50,
                'selling_price' => 9.90,
                'stock' => 20,
            ],

            // Category 10: Canned Food
            [
                'category_id' => 10,
                'name' => 'Canned Tuna 185g',
                'barcode' => '885195913045',
                'cost_price' => 1.10,
                'selling_price' => 1.90,
                'stock' => 60,
            ],
            [
                'category_id' => 10,
                'name' => 'Canned Sweet Corn',
                'barcode' => '885195913046',
                'cost_price' => 0.60,
                'selling_price' => 1.10,
                'stock' => 50,
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['name' => $productData['name']],
                $productData
            );
        }
    }
}
