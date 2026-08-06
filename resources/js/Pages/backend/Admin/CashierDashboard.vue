<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { LayoutDashboard, DollarSign, ShoppingBag, Receipt, ChevronRight, Store, TrendingUp } from '@lucide/vue';
import VueApexCharts from 'vue3-apexcharts';

const props = defineProps({
    summary: Object,
    recentOrders: Array,
    salesChart: Array,
});

const { t } = useI18n();

const chartOptions = {
    chart: { type: 'area', height: 350, toolbar: { show: false }, zoom: { enabled: false } },
    colors: ['#047857'],
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0, stops: [0, 90, 100] } },
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    xaxis: { categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'] },
    yaxis: { labels: { formatter: (value) => '$' + value } },
};

const chartSeries = computed(() => [{ name: t('cashier_dashboard.my_monthly_sales'), data: props.salesChart }]);

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: 'numeric', minute: '2-digit'
    });
};
</script>

<template>
    <Head :title="t('cashier_dashboard.title')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                    <LayoutDashboard class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">{{ t('cashier_dashboard.title') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('cashier_dashboard.subtitle') }}</p>
                </div>
            </div>
        </template>

        <div class="px-[30px] py-6 space-y-6">
            
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Today's Sales -->
                <div class="bg-white dark:bg-gray-800 rounded-md p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4 transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-center w-12 h-12 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                        <Store class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-600 dark:text-gray-300 leading-snug">{{ t('cashier_dashboard.shop_sales_today') }}</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ formatCurrency(summary.todaySales) }}</h3>
                    </div>
                </div>

                <!-- Today's Orders -->
                <div class="bg-white dark:bg-gray-800 rounded-md p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4 transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-center w-12 h-12 rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                        <Receipt class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-600 dark:text-gray-300 leading-snug">{{ t('cashier_dashboard.shop_orders_today') }}</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ summary.todayOrders }}</h3>
                    </div>
                </div>

                <!-- My Sales Today -->
                <div class="bg-emerald-700 dark:bg-emerald-800 rounded-md p-6 shadow-md border border-emerald-600 dark:border-emerald-700 flex items-center gap-4 text-white transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-center w-12 h-12 rounded-md bg-emerald-600/50 dark:bg-emerald-900/50 text-white">
                        <DollarSign class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-base font-semibold text-emerald-100 leading-snug">{{ t('cashier_dashboard.my_sales_today') }}</p>
                        <h3 class="text-2xl font-bold text-white mt-1">{{ formatCurrency(summary.mySalesToday) }}</h3>
                    </div>
                </div>

                <!-- My Orders Today -->
                <div class="bg-white dark:bg-gray-800 rounded-md p-6 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4 transition-transform hover:-translate-y-1">
                    <div class="flex items-center justify-center w-12 h-12 rounded-md bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">
                        <ShoppingBag class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-600 dark:text-gray-300 leading-snug">{{ t('cashier_dashboard.my_orders_today') }}</p>
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ summary.myOrdersToday }}</h3>
                    </div>
                </div>
            </div>

            <!-- Quick Action Banner -->
            <div class="bg-emerald-700 dark:bg-emerald-800 rounded-md shadow-sm border border-emerald-600 dark:border-emerald-700 p-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-600/50 dark:bg-emerald-900/50 rounded-full flex items-center justify-center">
                        <Store class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold mb-1">{{ t('cashier_dashboard.ready_for_sale') }}</h3>
                        <p class="text-sm text-emerald-100">{{ t('cashier_dashboard.ready_desc') }}</p>
                    </div>
                </div>
                <Link :href="route('pos.index')" class="inline-flex justify-center items-center gap-2 px-6 py-3 text-sm font-bold text-emerald-700 dark:text-emerald-800 bg-white rounded-md hover:bg-gray-50 transition-colors shadow-sm whitespace-nowrap">
                    {{ t('cashier_dashboard.open_pos') }} <ChevronRight class="w-4 h-4" />
                </Link>
            </div>

            <!-- Main Content (50% Chart, 50% Table) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                    <div class="px-6 py-5 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <TrendingUp class="w-5 h-5 text-emerald-700 dark:text-emerald-400" /> {{ t('cashier_dashboard.my_monthly_sales') }}
                        </h3>
                    </div>
                    <div class="p-4 flex-1">
                        <VueApexCharts type="area" height="300" :options="chartOptions" :series="chartSeries" />
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col h-full">
                    <div class="px-6 py-5 border-b border-gray-50 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                            <Receipt class="w-5 h-5 text-emerald-700 dark:text-emerald-400" /> {{ t('cashier_dashboard.my_recent_orders') }}
                        </h3>
                        <Link :href="route('pos.history')" class="text-sm font-medium text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300 flex items-center gap-1">
                            {{ t('dashboard.view_all') }} <ChevronRight class="w-4 h-4" />
                        </Link>
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50/50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-3 font-medium">{{ t('cashier_dashboard.invoice_no') }}</th>
                                    <th class="px-6 py-3 font-medium">{{ t('dashboard.customer') }}</th>
                                    <th class="px-6 py-3 font-medium">{{ t('cashier_dashboard.date_time') }}</th>
                                    <th class="px-6 py-3 font-medium text-right">{{ t('dashboard.total') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                                <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 font-mono font-semibold text-emerald-700 dark:text-emerald-400">{{ order.invoice_no }}</td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ order.customer?.name || 'Walk-in' }}</td>
                                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ formatDate(order.order_date) }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-800 dark:text-gray-100">{{ formatCurrency(order.grand_total) }}</td>
                                </tr>
                                <tr v-if="recentOrders.length === 0">
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500">
                                        <Receipt class="w-8 h-8 mx-auto mb-2 text-gray-300 dark:text-gray-600" />
                                        <p>{{ t('cashier_dashboard.no_orders_processed') }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>
    </AuthenticatedLayout>
</template>
