<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        
        // ── Cashier Dashboard ────────────────────────────────────────
        if ($user->role && $user->role->name === 'Cashier') {
            $todaySales = Order::whereDate('order_date', $today)->sum('grand_total');
            $todayOrders = Order::whereDate('order_date', $today)->count();
            
            $mySalesToday = Order::whereDate('order_date', $today)
                                ->where('user_id', $user->id)
                                ->sum('grand_total');
            $myOrdersToday = Order::whereDate('order_date', $today)
                                ->where('user_id', $user->id)
                                ->count();
            
            $recentOrders = Order::with('customer:id,name')
                                ->where('user_id', $user->id)
                                ->orderByDesc('order_date')
                                ->take(5)
                                ->get(['id', 'invoice_no', 'customer_id', 'grand_total', 'status', 'order_date']);

            $currentYear = Carbon::now()->year;
            $monthlySales = Order::selectRaw('EXTRACT(MONTH FROM order_date)::int as month, SUM(grand_total) as total')
                ->where('user_id', $user->id)
                ->whereYear('order_date', $currentYear)
                ->groupByRaw('EXTRACT(MONTH FROM order_date)')
                ->orderByRaw('EXTRACT(MONTH FROM order_date)')
                ->pluck('total', 'month')
                ->toArray();

            $salesData = [];
            for ($m = 1; $m <= 12; $m++) {
                $salesData[] = round((float) ($monthlySales[$m] ?? 0), 2);
            }

            return Inertia::render('backend/Admin/CashierDashboard', [
                'summary' => [
                    'todaySales' => round($todaySales, 2),
                    'todayOrders' => $todayOrders,
                    'mySalesToday' => round($mySalesToday, 2),
                    'myOrdersToday' => $myOrdersToday,
                ],
                'recentOrders' => $recentOrders,
                'salesChart' => $salesData,
            ]);
        }

        $currentYear = Carbon::now()->year;

        // ── Summary Cards ────────────────────────────────────────
        $todaysSales = Order::whereDate('order_date', $today)->sum('grand_total');
        $todaysPurchase = Purchase::whereDate('purchase_date', $today)->sum('total');
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $lowStockCount = Product::where('stock', '<=', 5)->count();
        $totalSuppliers = Supplier::count();

        // ── Monthly Sales Chart (current year) ───────────────────
        $monthlySales = Order::selectRaw('EXTRACT(MONTH FROM order_date)::int as month, SUM(grand_total) as total')
            ->whereYear('order_date', $currentYear)
            ->groupByRaw('EXTRACT(MONTH FROM order_date)')
            ->orderByRaw('EXTRACT(MONTH FROM order_date)')
            ->pluck('total', 'month')
            ->toArray();

        // Fill all 12 months
        $salesData = [];
        for ($m = 1; $m <= 12; $m++) {
            $salesData[] = round((float) ($monthlySales[$m] ?? 0), 2);
        }

        // ── Monthly Purchase Chart (current year) ────────────────
        $monthlyPurchases = Purchase::selectRaw('EXTRACT(MONTH FROM purchase_date)::int as month, SUM(total) as total')
            ->whereYear('purchase_date', $currentYear)
            ->groupByRaw('EXTRACT(MONTH FROM purchase_date)')
            ->orderByRaw('EXTRACT(MONTH FROM purchase_date)')
            ->pluck('total', 'month')
            ->toArray();

        $purchaseData = [];
        for ($m = 1; $m <= 12; $m++) {
            $purchaseData[] = round((float) ($monthlyPurchases[$m] ?? 0), 2);
        }

        // ── Top Selling Products ─────────────────────────────────
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product:id,name,image')
            ->paginate(5, ['*'], 'top_page');
            
        $topProducts->getCollection()->transform(function ($item) {
            return [
                'name' => $item->product->name ?? 'Unknown',
                'image' => $item->product->image ?? null,
                'total_sold' => (int) $item->total_sold,
            ];
        });

        // ── Low Stock Products ───────────────────────────────────
        $lowStockProducts = Product::select('id', 'name', 'stock', 'image')
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->paginate(5, ['*'], 'low_stock_page');

        // ── Recent Orders ────────────────────────────────────────
        $recentOrders = Order::with('customer:id,name')
            ->select('id', 'invoice_no', 'customer_id', 'grand_total', 'status', 'order_date')
            ->orderByDesc('order_date')
            ->paginate(5, ['*'], 'orders_page');

        // ── Recent Purchases ─────────────────────────────────────
        $recentPurchases = Purchase::with('supplier:id,company_name')
            ->select('id', 'supplier_id', 'total', 'purchase_date')
            ->orderByDesc('purchase_date')
            ->paginate(5, ['*'], 'purchases_page');

        // ── Revenue vs Purchase ──────────────────────────────────
        $totalRevenue = Order::whereYear('order_date', $currentYear)->sum('grand_total');
        $totalPurchaseAmount = Purchase::whereYear('purchase_date', $currentYear)->sum('total');

        // ── Pending Users ────────────────────────────────────────
        $pendingUsers = User::with('role')
            ->where('status', false)
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('backend/Admin/Dashboard', [
            'summary' => [
                'todaysSales' => round($todaysSales, 2),
                'todaysPurchase' => round($todaysPurchase, 2),
                'totalProducts' => $totalProducts,
                'totalCustomers' => $totalCustomers,
                'lowStockCount' => $lowStockCount,
                'totalSuppliers' => $totalSuppliers,
            ],
            'salesChart' => $salesData,
            'purchaseChart' => $purchaseData,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
            'recentOrders' => $recentOrders,
            'recentPurchases' => $recentPurchases,
            'pendingUsers' => $pendingUsers,
            'revenueVsPurchase' => [
                'revenue' => round($totalRevenue, 2),
                'purchase' => round($totalPurchaseAmount, 2),
            ],
        ]);
    }
}
