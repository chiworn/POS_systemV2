<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Beverages',
                'description' => 'Drinks including soft drinks, bottled water, juice, coffee, tea, and energy drinks.',
            ],
            [
                'id' => 2,
                'name' => 'Snacks',
                'description' => 'Light snacks such as chips, biscuits, chocolate, candy, and cookies.',
            ],
            [
                'id' => 3,
                'name' => 'Instant Noodles',
                'description' => 'Instant noodles, cup noodles, and ready-to-cook noodle products.',
            ],
            [
                'id' => 4,
                'name' => 'Dairy',
                'description' => 'Milk, yogurt, cheese, butter, and other dairy products.',
            ],
            [
                'id' => 5,
                'name' => 'Frozen Food',
                'description' => 'Frozen meat, seafood, vegetables, and ready-to-eat frozen meals.',
            ],
            [
                'id' => 6,
                'name' => 'Personal Care',
                'description' => 'Personal hygiene products including shampoo, soap, toothpaste, toothbrushes, and skincare items.',
            ],
            [
                'id' => 7,
                'name' => 'Household',
                'description' => 'Household essentials such as tissue, detergent, cleaning supplies, garbage bags, and air fresheners.',
            ],
            [
                'id' => 8,
                'name' => 'Stationery',
                'description' => 'Office and school supplies including pens, notebooks, paper, markers, and pencils.',
            ],
            [
                'id' => 9,
                'name' => 'Baby Products',
                'description' => 'Baby care products including diapers, baby milk, baby food, wipes, and baby shampoo.',
            ],
            [
                'id' => 10,
                'name' => 'Canned Food',
                'description' => 'Canned meat, fish, vegetables, fruits, soup, and other preserved food products.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['id' => $category['id']],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                ]
            );
        }
    }
}
