<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Sales Report Data
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        $salesToday = Order::whereDate('order_date', $today)->sum('grand_total');
        $salesWeek = Order::whereDate('order_date', '>=', $startOfWeek)->sum('grand_total');
        $salesMonth = Order::whereDate('order_date', '>=', $startOfMonth)->sum('grand_total');
        $salesTotal = Order::sum('grand_total');
        
        $recentSales = Order::with('customer')->orderBy('order_date', 'desc')->take(5)->get();

        // 2. Purchase Report (By Supplier)
        $purchasesBySupplier = Purchase::with('supplier')
            ->select('supplier_id', DB::raw('SUM(total) as total_amount'), DB::raw('COUNT(id) as purchase_count'))
            ->groupBy('supplier_id')
            ->orderByDesc('total_amount')
            ->paginate(5, ['*'], 'purchases_page');

        // 3. Stock Report
        $stockReport = Product::select('id', 'name', 'stock')
            ->orderBy('stock', 'desc')
            ->paginate(5, ['*'], 'stock_page');

        // 4. Low Stock Report
        $lowStockReport = Product::select('id', 'name', 'stock')
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->paginate(5, ['*'], 'low_stock_page');

        // 5. Best Selling Product
        $bestSellingProducts = OrderItem::with(['product', 'product.category'])
            ->select('product_id', DB::raw('SUM(quantity) as total_qty_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty_sold')
            ->paginate(5, ['*'], 'best_selling_page');

        return Inertia::render('backend/Admin/Reports/Index', [
            'salesSummary' => [
                'today' => $salesToday,
                'week' => $salesWeek,
                'month' => $salesMonth,
                'total' => $salesTotal,
            ],
            'recentSales' => $recentSales,
            'purchasesBySupplier' => $purchasesBySupplier,
            'stockReport' => $stockReport,
            'lowStockReport' => $lowStockReport,
            'bestSellingProducts' => $bestSellingProducts,
        ]);
    }
}
