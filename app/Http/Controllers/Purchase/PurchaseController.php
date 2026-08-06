<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    public function __construct(protected PurchaseService $purchaseService) {}

    public function index()
    {
        $purchases = Purchase::with(['supplier', 'user'])
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return Inertia::render('backend/Admin/Purchase/Index', [
            'purchases' => $purchases,
        ]);
    }

    public function create()
    {
        return Inertia::render('backend/Admin/Purchase/Create', [
            'suppliers' => Supplier::orderBy('company_name')->get(),
            'products'  => Product::with('category')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id'          => 'required|exists:suppliers,id',
            'purchase_date'        => 'required|date',
            'total'                => 'required|numeric|min:0',
            'note'                 => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.cost'         => 'required|numeric|min:0',
            'items.*.subtotal'     => 'required|numeric|min:0',
        ]);

        $this->purchaseService->store($validated);

        return redirect()->route('purchases.index')->with('success', 'Purchase saved! Stock updated.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'user', 'items.product']);

        return Inertia::render('backend/Admin/Purchase/Show', [
            'purchase' => $purchase,
        ]);
    }

    public function destroy(Purchase $purchase)
    {
        $this->purchaseService->destroy($purchase);

        return redirect()->route('purchases.index')->with('success', 'Purchase deleted and stock reversed.');
    }
}
