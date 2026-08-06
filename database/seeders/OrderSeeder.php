<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        $products = \App\Models\Product::all();
        
        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        $cashier = $users->where('role_id', \App\Models\Role::where('name', 'Cashier')->first()->id)->first() ?? $users->first();

        // Create Customers
        $customers = [];
        for ($i = 1; $i <= 5; $i++) {
            $customers[] = \App\Models\Customer::create([
                'name' => 'Customer ' . $i,
                'phone' => '01234567' . $i,
            ]);
        }

        // Create Orders
        for ($i = 1; $i <= 10; $i++) {
            $customer = $customers[array_rand($customers)];
            
            $subtotal = 0;
            $items = [];
            $numItems = rand(1, 4);
            
            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $qty = rand(1, 3);
                $price = $product->selling_price ?? $product->price ?? 10;
                $itemSubtotal = $qty * $price;
                $subtotal += $itemSubtotal;
                
                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $itemSubtotal,
                ];
            }
            
            $order = \App\Models\Order::create([
                'customer_id' => $customer->id,
                'user_id' => $cashier->id,
                'invoice_no' => 'INV-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'order_date' => now()->subDays(rand(0, 30)),
                'subtotal' => $subtotal,
                'tax' => 0,
                'discount' => 0,
                'grand_total' => $subtotal,
                'status' => 'completed',
            ]);

            foreach ($items as $item) {
                $order->items()->create($item);
            }
        }
    }
}
