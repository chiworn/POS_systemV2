<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { 
    ClipboardList, 
    TrendingUp, 
    DollarSign, 
    Calendar, 
    ShoppingBag, 
    Truck, 
    AlertTriangle, 
    Package,
    ChevronLeft,
    ChevronRight,
    ShoppingCart
} from '@lucide/vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    salesSummary: Object,
    recentSales: Array,
    purchasesBySupplier: Object,
    stockReport: Object,
    lowStockReport: Object,
    bestSellingProducts: Object,
});

const { t } = useI18n();

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
};

// goToPage removed as Pagination component handles routing
</script>

<template>
    <Head :title="t('reports.title')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                    <ClipboardList class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">{{ t('reports.title') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('reports.subtitle') }}</p>
                </div>
            </div>
        </template>

        <div class="px-[30px] py-6 space-y-6">
            
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Today's Sales -->
                <div class="bg-white dark:bg-gray-800 rounded-md p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4 transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-center w-12 h-12 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                        <DollarSign class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-600 dark:text-gray-300 leading-snug">{{ t('reports.sales_today') }}</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ formatCurrency(salesSummary.today) }}</h3>
                    </div>
                </div>

                <!-- This Week -->
                <div class="bg-white dark:bg-gray-800 rounded-md p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4 transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-center w-12 h-12 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                        <Calendar class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-600 dark:text-gray-300 leading-snug">{{ t('reports.this_week') }}</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ formatCurrency(salesSummary.week) }}</h3>
                    </div>
                </div>

                <!-- This Month -->
                <div class="bg-white dark:bg-gray-800 rounded-md p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4 transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-center w-12 h-12 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                        <TrendingUp class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-600 dark:text-gray-300 leading-snug">{{ t('reports.this_month') }}</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ formatCurrency(salesSummary.month) }}</h3>
                    </div>
                </div>

                <!-- Total Sales -->
                <div class="bg-emerald-700 dark:bg-emerald-900/50 rounded-md p-6 shadow-md border border-emerald-600 dark:border-emerald-800/50 flex items-center gap-4 text-white transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-center w-12 h-12 rounded-md bg-emerald-600/50 dark:bg-emerald-800/50 text-white dark:text-emerald-100">
                        <ShoppingBag class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-base font-semibold text-emerald-100 dark:text-emerald-200 leading-snug">{{ t('reports.total_sales') }}</p>
                        <h3 class="text-2xl font-bold text-white dark:text-emerald-50 mt-1">{{ formatCurrency(salesSummary.total) }}</h3>
                    </div>
                </div>
            </div>

            <!-- Main Charts / Tables Area -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Best Selling Products -->
                <div class="bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col">
                    <div class="px-6 py-5 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <TrendingUp class="w-5 h-5 text-emerald-700 dark:text-emerald-400" /> {{ t('reports.best_selling_products') }}
                        </h3>
                    </div>
                    <div class="p-6 flex-1 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                                    <th class="pb-3 pr-4 font-medium w-3/5">{{ t('product.product') }}</th>
                                    <th class="pb-3 pr-4 font-medium w-1/5">{{ t('product.category') }}</th>
                                    <th class="pb-3 pr-4 font-medium w-1/5">{{ t('product.price') }}</th>
                                    <th class="pb-3 font-medium text-right w-1/6">{{ t('reports.qty_sold') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                <tr v-for="item in bestSellingProducts.data" :key="item.product_id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-3 pr-4 text-gray-800 dark:text-gray-200 font-medium max-w-[60px]  sm:max-w-[150px] truncate" :title="item.product?.name ?? 'Unknown'">
                                        {{ item.product?.name?.substring(0, 20) ?? 'Unknown' }}
                                    </td>
                                    <td class="py-3 pr-4 text-gray-500 dark:text-gray-400 max-w-[80px] truncate" :title="item.product?.category?.name ?? '—'">
                                        {{ item.product?.category?.name ?? '—' }}
                                    </td>
                                    <td class="py-3 pr-4 text-gray-800 dark:text-gray-200 font-semibold">{{ formatCurrency(item.product?.selling_price ?? 0) }}</td>
                                    <td class="py-3 text-right">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 whitespace-nowrap">
                                            <ShoppingBag class="w-3.5 h-3.5" />
                                            {{ item.total_qty_sold }} {{ t('reports.items') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="bestSellingProducts.data.length === 0">
                                    <td colspan="4" class="py-4 text-center text-gray-400 dark:text-gray-500">{{ t('reports.no_data') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <Pagination :data="bestSellingProducts" />
                </div>

                <!-- Top Suppliers (Purchase Report) -->
                <div class="bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col">
                    <div class="px-6 py-5 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <Truck class="w-5 h-5 text-emerald-700 dark:text-emerald-400" /> {{ t('reports.top_suppliers') }}
                        </h3>
                    </div>
                    <div class="p-6 flex-1 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                                    <th class="pb-3 pr-5 font-medium w-1/5">{{ t('purchase.supplier') }}</th>
                                    <th class="pb-3 pr-4 font-medium w-2/5">{{ t('supplier.contact_person') }}</th>
                                    <th class="pb-3 pr-4 font-medium text-center w-1/5">{{ t('reports.orders') }}</th>
                                    <th class="pb-3 font-medium text-right w-1/5">{{ t('reports.total_purchased') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                <tr v-for="purchase in purchasesBySupplier.data" :key="purchase.supplier_id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-3 pr-5 text-gray-800 dark:text-gray-200 font-medium max-w-[130px] sm:max-w-[180px] truncate" :title="purchase.supplier?.company_name ?? 'Unknown'">
                                        {{ purchase.supplier?.company_name ?? 'Unknown' }}
                                    </td>
                                    <td class="py-3 pr-5 text-gray-500 dark:text-gray-400 max-w-[100px] truncate" :title="purchase.supplier?.contact_name ?? '—'">
                                        {{ purchase.supplier?.contact_name ?? '—' }}
                                    </td>
                                    <td class="py-3 pr-4 text-center">
                                        <span class="inline-flex items-center justify-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 whitespace-nowrap">
                                            <ShoppingCart class="w-3.5 h-3.5" />
                                            {{ purchase.purchase_count }} {{ t('reports.orders') }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right font-semibold text-gray-800 dark:text-gray-200">
                                        {{ formatCurrency(purchase.total_amount) }}
                                    </td>
                                </tr>
                                <tr v-if="purchasesBySupplier.data.length === 0">
                                    <td colspan="4" class="py-4 text-center text-gray-400 dark:text-gray-500">{{ t('reports.no_data') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <Pagination :data="purchasesBySupplier" />
                </div>

            </div>

            <!-- Bottom Tables Area -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                
                <!-- Stock Report -->
                <div class="bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col">
                    <div class="px-6 py-5 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <Package class="w-5 h-5 text-emerald-700 dark:text-emerald-400" /> {{ t('reports.current_stock_levels') }}
                        </h3>
                    </div>
                    <div class="p-6 flex-1 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-gray-400 dark:text-gray-500 border-b border-gray-100 dark:border-gray-700">
                                    <th class="pb-3 pr-4 font-medium w-3/4">{{ t('product.product') }}</th>
                                    <th class="pb-3 font-medium text-right w-1/4">{{ t('product.stock') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                <tr v-for="product in stockReport.data" :key="product.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="py-3 pr-4 text-gray-800 dark:text-gray-200 font-medium max-w-[150px] sm:max-w-[200px] truncate" :title="product.name">
                                        {{ product.name }}
                                    </td>
                                    <td class="py-3 text-right">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 whitespace-nowrap">
                                            <Package class="w-3.5 h-3.5" />
                                            {{ product.stock }} {{ t('reports.items') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="stockReport.data.length === 0">
                                    <td colspan="2" class="py-4 text-center text-gray-400 dark:text-gray-500">{{ t('reports.no_data') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <Pagination :data="stockReport" />
                </div>

                <!-- Low Stock Report -->
                <div class="bg-red-50/30 dark:bg-red-900/10 rounded-md shadow-sm border border-red-100 dark:border-red-900/50 flex flex-col">
                    <div class="px-6 py-5 border-b border-red-100/50 dark:border-red-900/50 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-red-700 dark:text-red-400 flex items-center gap-2">
                            <AlertTriangle class="w-5 h-5 text-red-600 dark:text-red-400" /> {{ t('reports.low_stock_alerts') }}
                        </h3>
                    </div>
                    <div class="p-6 flex-1 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="text-red-400 dark:text-red-500 border-b border-red-100/50 dark:border-red-900/50">
                                    <th class="pb-3 pr-4 font-medium text-red-800 dark:text-red-400 w-3/4">{{ t('product.product') }}</th>
                                    <th class="pb-3 font-medium text-right text-red-800 dark:text-red-400 w-1/4">{{ t('product.stock') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-red-100/30 dark:divide-red-900/30">
                                <tr v-for="product in lowStockReport.data" :key="product.id" class="hover:bg-red-50/50 dark:hover:bg-red-900/20">
                                    <td class="py-3 pr-4 text-gray-800 dark:text-gray-200 font-medium max-w-[150px] sm:max-w-[200px] truncate" :title="product.name">
                                        {{ product.name }}
                                    </td>
                                    <td class="py-3 text-right">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 whitespace-nowrap">
                                            <AlertTriangle class="w-3.5 h-3.5" />
                                            {{ product.stock }} {{ t('reports.items') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="lowStockReport.data.length === 0">
                                    <td colspan="2" class="py-4 text-center text-emerald-500 dark:text-emerald-400 font-medium flex items-center justify-center gap-2">
                                        <Package class="w-4 h-4"/> {{ t('reports.no_low_stock') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <Pagination :data="lowStockReport" />
                </div>

            </div>

        </div>
    </AuthenticatedLayout>
</template>
