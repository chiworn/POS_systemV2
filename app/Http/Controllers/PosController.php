<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    /**
     * Display the POS screen.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->where('stock', '>', 0)->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::all();
        
        return Inertia::render('backend/Pos/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category_id']),
        ]);
    }

    /**
     * Process checkout and create order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'nullable|string|in:cash,khqr,bank',
            'cash_received' => 'nullable|numeric|min:0',
            'change_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Check stock again to prevent overselling
            foreach ($validated['cart'] as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Not enough stock for {$product->name}. Only {$product->stock} available.");
                }
            }

            // Create Order
            $order = Order::create([
                'user_id' => $request->user()->id,
                'customer_id' => $validated['customer_id'] ?? null,
                'invoice_no' => 'INV-' . strtoupper(Str::random(8)),
                'order_date' => now(),
                'subtotal' => $validated['subtotal'],
                'tax' => $validated['tax'],
                'discount' => $validated['discount'],
                'grand_total' => $validated['grand_total'],
                'payment_method' => $validated['payment_method'] ?? 'cash',
                'status' => 'completed',
            ]);

            // Create Order Items and update stock
            foreach ($validated['cart'] as $item) {
                $subtotal = $item['price'] * $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                ]);

                // Reduce stock
                Product::where('id', $item['id'])->decrement('stock', $item['quantity']);
            }

            DB::commit();

            $loadedOrder = $order->load(['items.product', 'user', 'customer']);
            if (isset($validated['cash_received'])) {
                $loadedOrder->cash_received = $validated['cash_received'];
                $loadedOrder->change_amount = $validated['change_amount'] ?? 0;
            }

            return redirect()->back()->with([
                'success' => 'Order completed successfully! Invoice: ' . $order->invoice_no,
                'order' => $loadedOrder,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display sales history for the user.
     */
    public function history(Request $request)
    {
        $query = Order::with(['items.product', 'user', 'customer'])->latest();
        $user = $request->user();

        // If Cashier, only show their own orders
        if ($user->role && $user->role->name === 'Cashier') {
            $query->where('user_id', $user->id);
        } else {
            // Admin/Manager can filter by Cashier
            if ($request->filled('cashier_id')) {
                $query->where('user_id', $request->cashier_id);
            }
        }

        // Search by Invoice, Customer Name, or Cashier Name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Date Filter
        if ($request->filled('date')) {
            if ($request->date === 'today') {
                $query->whereDate('order_date', today());
            } elseif ($request->date === 'week') {
                $query->whereBetween('order_date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($request->date === 'month') {
                $query->whereMonth('order_date', now()->month)
                      ->whereYear('order_date', now()->year);
            }
        }

        $orders = $query->paginate(10)->withQueryString();

        // Pass cashiers list for the filter dropdown if user is not a cashier
        $cashiers = [];
        if (!$user->role || $user->role->name !== 'Cashier') {
            $cashiers = \App\Models\User::whereHas('role', function($q) {
                $q->where('name', 'Cashier');
            })->get(['id', 'name']);
        }

        return Inertia::render('backend/Pos/History', [
            'orders' => $orders,
            'cashiers' => $cashiers,
            'filters' => $request->only(['search', 'cashier_id', 'date']),
        ]);
    }

    /**
     * Generate Bakong KHQR code payload and MD5.
     */
    public function generateKhqr(Request $request)
    {
        $validated = $request->validate([
            'grand_total' => 'required|numeric|min:0.01',
            'currency' => 'nullable|string|in:USD,KHR',
        ]);

        try {
            $currencyStr = strtoupper($request->currency ?? 'KHR');
            $grandTotalUsd = (float) $validated['grand_total'];
            $exchangeRate = 4100;

            if ($currencyStr === 'KHR') {
                $amount = (float) round($grandTotalUsd * $exchangeRate);
                $khqrCurrency = \KHQR\Helpers\KHQRData::CURRENCY_KHR;
            } else {
                $amount = (float) number_format($grandTotalUsd, 2, '.', '');
                $khqrCurrency = \KHQR\Helpers\KHQRData::CURRENCY_USD;
            }

            $individualInfo = new \KHQR\Models\IndividualInfo(
                bakongAccountID: config('services.bakong.account_id', 'chouernchyworn_kong@bkrt'),
                merchantName: config('services.bakong.merchant_name', 'CHOUERNCHYWORN KONG'),
                merchantCity: config('services.bakong.merchant_city', 'Phnom Penh'),
                currency: $khqrCurrency,
                amount: $amount,
                billNumber: 'INV-' . strtoupper(Str::random(6))
            );

            $response = \KHQR\BakongKHQR::generateIndividual($individualInfo);

            return response()->json([
                'status' => 'success',
                'qr' => $response->data['qr'],
                'md5' => $response->data['md5'],
                'merchant_name' => config('services.bakong.merchant_name', 'CHOUERNCHYWORN KONG'),
                'account_id' => config('services.bakong.account_id', 'chouernchyworn_kong@bkrt'),
                'amount_usd' => $grandTotalUsd,
                'amount_khr' => round($grandTotalUsd * $exchangeRate),
                'currency' => $currencyStr,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Check transaction status by MD5 with Bakong API.
     */
    public function checkBakongTransaction(Request $request)
    {
        $request->validate([
            'md5' => 'required|string',
        ]);

        try {
            $token = config('services.bakong.token');
            if (!$token) {
                return response()->json(['error' => 'BAKONG_TOKEN is not configured in .env'], 400);
            }

            $bakong = new \KHQR\BakongKHQR($token);
            $result = $bakong->checkTransactionByMD5($request->md5);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
