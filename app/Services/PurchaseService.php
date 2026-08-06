<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    /**
     * Create a new purchase with items and update product stock.
     * Wrapped in a transaction for data integrity.
     */
    public function store(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            // Step 1: Create the Purchase header
            $purchase = Purchase::create([
                'supplier_id'   => $data['supplier_id'],
                'user_id'       => auth()->id(),
                'purchase_date' => $data['purchase_date'],
                'total'         => $data['total'],
                'note'          => $data['note'] ?? null,
            ]);

            // Step 2: Create Purchase Items & Step 3: Update Stock
            foreach ($data['items'] as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id'  => $item['product_id'],
                    'quantity'    => $item['quantity'],
                    'cost'        => $item['cost'],
                    'subtotal'    => $item['subtotal'],
                ]);

                // Increment product stock
                Product::where('id', $item['product_id'])
                    ->increment('stock', $item['quantity']);
            }

            return $purchase;
        });
    }

    /**
     * Delete a purchase and reverse stock changes.
     */
    public function destroy(Purchase $purchase): void
    {
        DB::transaction(function () use ($purchase) {
            // Reverse stock for each item
            foreach ($purchase->items as $item) {
                Product::where('id', $item->product_id)
                    ->decrement('stock', $item->quantity);
            }

            $purchase->delete(); // cascades to purchase_items
        });
    }
}
