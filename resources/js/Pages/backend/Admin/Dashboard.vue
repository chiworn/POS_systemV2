<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { LayoutDashboard, DollarSign, ShoppingCart, Package, Users, AlertTriangle, Truck, TrendingUp, TrendingDown, ArrowUpRight, Clock, UserCheck, UserX } from '@lucide/vue';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import VueApexCharts from 'vue3-apexcharts';

import Pagination from '@/Components/Pagination.vue';

const { t } = useI18n();

const props = defineProps({
    summary: Object,
    salesChart: Array,
    purchaseChart: Array,
    topProducts: Object,
    lowStockProducts: Object,
    recentOrders: Object,
    recentPurchases: Object,
    revenueVsPurchase: Object,
    pendingUsers: {
        type: Array,
        default: () => [],
    },
});

const approveUser = (user) => {
    if (confirm(`Approve cashier account for "${user.name}"?`)) {
        router.patch(route('users.toggle-status', user.id), {}, { preserveScroll: true });
    }
};

const rejectUser = (user) => {
    if (confirm(`Reject and delete registration request for "${user.name}"?`)) {
        router.delete(route('users.destroy', user.id), { preserveScroll: true });
    }
};

const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

const fmt = (v) => {
    if (v >= 1000) return '$' + (v/1000).toFixed(1) + 'k';
    return '$' + Number(v).toFixed(2);
};

const fmtDate = (d) => {
    if (!d) return '—';
    const dt = new Date(d);
    const now = new Date();
    const diff = Math.floor((now - dt) / 86400000);
    if (diff === 0) return 'Today';
    if (diff === 1) return 'Yesterday';
    return dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
};

const cards = computed(() => [
    { label: t('dashboard.todays_sales'), value: fmt(props.summary.todaysSales), icon: DollarSign, color: 'emerald', bg: 'bg-emerald-50 dark:bg-emerald-900/30', text: 'text-emerald-700 dark:text-emerald-400', ring: 'ring-emerald-200 dark:ring-emerald-800/50' },
    { label: t('dashboard.todays_purchase'), value: fmt(props.summary.todaysPurchase), icon: ShoppingCart, color: 'blue', bg: 'bg-blue-50 dark:bg-blue-900/30', text: 'text-blue-700 dark:text-blue-400', ring: 'ring-blue-200 dark:ring-blue-800/50' },
    { label: t('dashboard.total_products'), value: props.summary.totalProducts, icon: Package, color: 'violet', bg: 'bg-violet-50 dark:bg-violet-900/30', text: 'text-violet-700 dark:text-violet-400', ring: 'ring-violet-200 dark:ring-violet-800/50' },
    { label: t('dashboard.total_customers'), value: props.summary.totalCustomers, icon: Users, color: 'amber', bg: 'bg-amber-50 dark:bg-amber-900/30', text: 'text-amber-700 dark:text-amber-400', ring: 'ring-amber-200 dark:ring-amber-800/50' },
    { label: t('dashboard.low_stock'), value: props.summary.lowStockCount, icon: AlertTriangle, color: 'red', bg: 'bg-red-50 dark:bg-red-900/30', text: 'text-red-700 dark:text-red-400', ring: 'ring-red-200 dark:ring-red-800/50' },
    { label: t('dashboard.total_suppliers'), value: props.summary.totalSuppliers, icon: Truck, color: 'cyan', bg: 'bg-cyan-50 dark:bg-cyan-900/30', text: 'text-cyan-700 dark:text-cyan-400', ring: 'ring-cyan-200 dark:ring-cyan-800/50' },
]);

const salesChartOptions = computed(() => ({
    chart: { type: 'area', height: 320, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
    colors: ['#059669'],
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [0, 100] } },
    stroke: { curve: 'smooth', width: 3 },
    xaxis: { categories: months, labels: { style: { colors: '#9ca3af', fontSize: '12px' } } },
    yaxis: { labels: { style: { colors: '#9ca3af', fontSize: '12px' }, formatter: v => '$' + v } },
    grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
    dataLabels: { enabled: false },
    tooltip: { y: { formatter: v => '$' + v.toFixed(2) } },
}));
const salesSeries = computed(() => [{ name: t('dashboard.monthly_sales'), data: props.salesChart }]);

const purchaseChartOptions = computed(() => ({
    chart: { type: 'bar', height: 320, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
    colors: ['#3b82f6'],
    plotOptions: { bar: { borderRadius: 6, columnWidth: '50%' } },
    xaxis: { categories: months, labels: { style: { colors: '#9ca3af', fontSize: '12px' } } },
    yaxis: { labels: { style: { colors: '#9ca3af', fontSize: '12px' }, formatter: v => '$' + v } },
    grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
    dataLabels: { enabled: false },
    tooltip: { y: { formatter: v => '$' + v.toFixed(2) } },
}));
const purchaseSeries = computed(() => [{ name: t('dashboard.monthly_purchases'), data: props.purchaseChart }]);

const revMax = computed(() => Math.max(props.revenueVsPurchase.revenue, props.revenueVsPurchase.purchase, 1));
const revPct = computed(() => (props.revenueVsPurchase.revenue / revMax.value * 100).toFixed(0));
const purPct = computed(() => (props.revenueVsPurchase.purchase / revMax.value * 100).toFixed(0));
const profit = computed(() => props.revenueVsPurchase.revenue - props.revenueVsPurchase.purchase);
</script>

<template>
    <Head :title="t('dashboard.title')" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                    <LayoutDashboard class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">{{ t('dashboard.title') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('dashboard.subtitle') }}</p>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <div v-for="(card, i) in cards" :key="i"
                    class="group relative overflow-hidden rounded-md bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm p-5 hover:shadow-md transition-all duration-300 hover:-translate-y-0.5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-base font-semibold text-gray-600 dark:text-gray-300 leading-snug">{{ card.label }}</p>
                            <p class="mt-2 text-2xl font-bold text-gray-800 dark:text-gray-100">{{ card.value }}</p>
                        </div>
                        <div :class="[card.bg, card.text]" class="flex items-center justify-center w-10 h-10 rounded-md ring-1" :style="{ '--tw-ring-color': 'transparent' }">
                            <component :is="card.icon" class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 h-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        :class="{
                            'bg-emerald-500': card.color === 'emerald',
                            'bg-blue-500': card.color === 'blue',
                            'bg-violet-500': card.color === 'violet',
                            'bg-amber-500': card.color === 'amber',
                            'bg-red-500': card.color === 'red',
                            'bg-cyan-500': card.color === 'cyan',
                        }"></div>
                </div>
            </div>

            <!-- Revenue vs Purchase -->
            <div class="rounded-md bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider mb-5">{{ t('dashboard.revenue_vs_purchase') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <TrendingUp class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ t('dashboard.total_revenue') }}</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ fmt(revenueVsPurchase.revenue) }}</p>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3">
                            <div class="bg-emerald-500 h-3 rounded-full transition-all duration-700" :style="{ width: revPct + '%' }"></div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <TrendingDown class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ t('dashboard.total_purchase') }}</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-800 dark:text-gray-100">{{ fmt(revenueVsPurchase.purchase) }}</p>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-3">
                            <div class="bg-blue-500 h-3 rounded-full transition-all duration-700" :style="{ width: purPct + '%' }"></div>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900 rounded-md p-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ t('dashboard.net_profit') }}</p>
                        <p class="text-3xl font-bold" :class="profit >= 0 ? 'text-emerald-600' : 'text-red-600'">
                            {{ fmt(profit) }}
                        </p>
                        <div class="flex items-center gap-1 mt-1" :class="profit >= 0 ? 'text-emerald-500' : 'text-red-500'">
                            <ArrowUpRight v-if="profit >= 0" class="w-3.5 h-3.5" />
                            <TrendingDown v-else class="w-3.5 h-3.5" />
                            <span class="text-xs font-medium">{{ revenueVsPurchase.revenue > 0 ? ((profit / revenueVsPurchase.revenue) * 100).toFixed(1) : 0 }}% {{ t('dashboard.margin') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Sales Chart -->
                <div class="rounded-md bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider mb-4">📈 {{ t('dashboard.monthly_sales') }}</h3>
                    <VueApexCharts type="area" height="320" :options="salesChartOptions" :series="salesSeries" />
                </div>
                <!-- Purchase Chart -->
                <div class="rounded-md bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider mb-4">📊 {{ t('dashboard.monthly_purchases') }}</h3>
                    <VueApexCharts type="bar" height="320" :options="purchaseChartOptions" :series="purchaseSeries" />
                </div>
            </div>

            <!-- Top Selling + Low Stock -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Selling -->
                <div class="rounded-md bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">🏆 {{ t('dashboard.top_selling_products') }}</h3>
                    </div>
                    <div class="divide-y divide-gray-50 dark:divide-gray-700">
                        <div v-for="(p, i) in topProducts.data" :key="i" class="flex items-center gap-4 px-6 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <span class="flex items-center justify-center w-7 h-7 rounded-md text-xs font-bold"
                                :class="i === 0 ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400' : i === 1 ? 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300' : i === 2 ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400'">
                                {{ i + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ p.name }}</p>
                            </div>
                            <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-0.5 rounded-full">{{ p.total_sold }} {{ t('dashboard.sold') }}</span>
                        </div>
                        <div v-if="!topProducts.data.length" class="px-6 py-12 text-center text-gray-400">
                            <Package class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                            <p class="text-sm">{{ t('dashboard.no_sales_data') }}</p>
                        </div>
                    </div>
                    <Pagination :data="topProducts" />
                </div>

                <!-- Low Stock -->
                <div class="rounded-md bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">⚠️ {{ t('dashboard.low_stock_alert') }}</h3>
                    </div>
                    <div class="divide-y divide-gray-50 dark:divide-gray-700">
                        <div v-for="p in lowStockProducts.data" :key="p.id" class="flex items-center justify-between px-6 py-3.5 hover:bg-red-50/50 dark:hover:bg-red-900/10 transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-md bg-red-100 dark:bg-red-900/30">
                                    <AlertTriangle class="w-4 h-4 text-red-600 dark:text-red-400" />
                                </span>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ p.name }}</p>
                            </div>
                            <span class="text-sm font-bold px-3 py-1 rounded-full"
                                :class="p.stock <= 2 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'">
                                {{ p.stock }} {{ t('dashboard.left') }}
                            </span>
                        </div>
                        <div v-if="!lowStockProducts.data.length" class="px-6 py-12 text-center text-gray-400">
                            <Package class="w-8 h-8 mx-auto mb-2 text-gray-300" />
                            <p class="text-sm">{{ t('dashboard.well_stocked') }}</p>
                        </div>
                    </div>
                    <Pagination :data="lowStockProducts" />
                </div>
            </div>

            <!-- Recent Orders + Recent Purchases -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Orders -->
                <div class="rounded-md bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">🧾 {{ t('dashboard.recent_orders') }}</h3>
                        <Link :href="route('pos.history')" class="text-sm font-medium text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                            {{ t('dashboard.view_history') }} <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-emerald-700 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">{{ t('dashboard.invoice') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">{{ t('dashboard.customer') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase">{{ t('dashboard.total') }}</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase">{{ t('dashboard.status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                <tr v-for="o in recentOrders.data" :key="o.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-200">{{ o.invoice_no }}</td>
                                    <td class="px-6 py-3 text-gray-600 dark:text-gray-400">{{ o.customer?.name || 'Walk-in' }}</td>
                                    <td class="px-6 py-3 text-right font-semibold text-gray-800 dark:text-gray-200">${{ Number(o.grand_total).toFixed(2) }}</td>
                                    <td class="px-6 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            :class="o.status === 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400'">
                                            {{ o.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!recentOrders.data.length">
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">{{ t('dashboard.no_orders') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <Pagination :data="recentOrders" />
                </div>

                <!-- Recent Purchases -->
                <div class="rounded-md bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">📦 {{ t('dashboard.recent_purchases') }}</h3>
                        <Link :href="route('purchases.index')" class="text-sm font-medium text-blue-700 hover:text-blue-800 flex items-center gap-1">
                            {{ t('dashboard.view_all') }} <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                        </Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-emerald-700 text-white">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">{{ t('dashboard.supplier') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase">{{ t('dashboard.total') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase">{{ t('dashboard.date') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                <tr v-for="p in recentPurchases.data" :key="p.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="flex items-center justify-center w-7 h-7 rounded-md bg-blue-100 dark:bg-blue-900/30">
                                                <Truck class="w-3.5 h-3.5 text-blue-700 dark:text-blue-400" />
                                            </span>
                                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ p.supplier?.company_name || '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-right font-semibold text-gray-800 dark:text-gray-200">${{ Number(p.total).toFixed(2) }}</td>
                                    <td class="px-6 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1 text-gray-500 dark:text-gray-400">
                                            <Clock class="w-3.5 h-3.5" />
                                            <span>{{ fmtDate(p.purchase_date) }}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!recentPurchases.data.length">
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-400">{{ t('dashboard.no_purchases') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <Pagination :data="recentPurchases" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
