<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ShoppingCart, ArrowLeft, Truck, User, CalendarDays, FileText, Package } from '@lucide/vue';

const props = defineProps({
    purchase: Object,
});

const formatDate     = (d) => d ? new Date(d).toLocaleDateString('en-GB', { day:'2-digit', month:'long', year:'numeric' }) : '—';
const formatCurrency = (n) => '$' + parseFloat(n ?? 0).toFixed(2);
</script>

<template>
    <Head :title="`Purchase #${purchase.id}`" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <ShoppingCart class="w-5 h-5 text-emerald-700 dark:text-emerald-400" />
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Purchase #{{ purchase.id }}
                </h2>
            </div>
        </template>

        <div class="px-[30px] py-5">
            <div class="space-y-5">

                <!-- Back -->
                <Link :href="route('purchases.index')"
                    class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">
                    <ArrowLeft class="w-4 h-4" /> Back to Purchases
                </Link>

                <!-- Invoice Card -->
                <div class="bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

                    <!-- Invoice header band -->
                    <div class="bg-emerald-700 dark:bg-emerald-800 px-6 py-5 text-white">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-widest opacity-70 mb-1">Purchase Invoice</p>
                                <h1 class="text-2xl font-bold">#{{ String(purchase.id).padStart(5, '0') }}</h1>
                            </div>
                            <div class="text-right">
                                <p class="text-xs opacity-70 mb-1">Grand Total</p>
                                <p class="text-2xl font-bold">{{ formatCurrency(purchase.total) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Meta info grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-gray-100 dark:divide-gray-700 divide-y dark:divide-y-0">
                        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 sm:border-t-0">
                            <p class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500 mb-1"><Truck class="w-3.5 h-3.5" /> Supplier</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ purchase.supplier?.company_name ?? '—' }}</p>
                        </div>
                        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 sm:border-t-0">
                            <p class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500 mb-1"><CalendarDays class="w-3.5 h-3.5" /> Date</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ formatDate(purchase.purchase_date) }}</p>
                        </div>
                        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 sm:border-t-0">
                            <p class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500 mb-1"><User class="w-3.5 h-3.5" /> Created By</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ purchase.user?.name ?? '—' }}</p>
                        </div>
                        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700 sm:border-t-0">
                            <p class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500 mb-1"><FileText class="w-3.5 h-3.5" /> Note</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ purchase.note || '—' }}</p>
                        </div>
                    </div>

                    <!-- Items table -->
                    <div class="border-t border-gray-100 dark:border-gray-700">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                                    <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">#</th>
                                    <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Product</th>
                                    <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300 text-right">Qty</th>
                                    <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300 text-right">Cost</th>
                                    <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="(item, idx) in purchase.items" :key="item.id"
                                    class="hover:bg-emerald-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-3 text-gray-400 dark:text-gray-500 font-mono text-xs">{{ idx + 1 }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="flex items-center justify-center w-7 h-7 rounded bg-emerald-100 dark:bg-emerald-900/30 shrink-0">
                                                <Package class="w-3.5 h-3.5 text-emerald-700 dark:text-emerald-400" />
                                            </span>
                                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ item.product?.name ?? '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-right text-gray-700 dark:text-gray-300">{{ item.quantity }}</td>
                                    <td class="px-6 py-3 text-right text-gray-700 dark:text-gray-300">{{ formatCurrency(item.cost) }}</td>
                                    <td class="px-6 py-3 text-right font-semibold text-emerald-700 dark:text-emerald-400">{{ formatCurrency(item.subtotal) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Total footer -->
                        <div class="flex items-center justify-end gap-8 px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Items</span>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ purchase.items.length }}</span>
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 ml-6">Grand Total</span>
                            <span class="text-lg font-bold text-emerald-700 dark:text-emerald-400">{{ formatCurrency(purchase.total) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
