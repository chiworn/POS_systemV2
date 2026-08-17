<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InvoiceReceiptModal from '@/Components/InvoiceReceiptModal.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Clock, Receipt, User, CheckCircle2, ChevronLeft, ChevronRight, Search, Users, Banknote, QrCode, Printer } from '@lucide/vue';
import Pagination from '@/Components/Pagination.vue';

const { t } = useI18n();

const props = defineProps({
    orders: Object,
    cashiers: Array,
    filters: Object,
});

const isAdmin = computed(() => props.cashiers && props.cashiers.length > 0);

const search = ref(props.filters?.search || '');
const cashier_id = ref(props.filters?.cashier_id || '');
const date = ref(props.filters?.date || '');

const selectedOrderForInvoice = ref(null);
const showInvoiceModal = ref(false);

const openInvoiceModal = (order) => {
    selectedOrderForInvoice.value = order;
    showInvoiceModal.value = true;
};

let searchTimeout = null;
watch([search, cashier_id, date], ([newSearch, newCashier, newDate]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const query = {};
        if (newSearch) query.search = newSearch;
        if (newCashier) query.cashier_id = newCashier;
        if (newDate) query.date = newDate;

        router.get(route('pos.history'), query, {
            preserveState: true,
            replace: true,
            preserveScroll: true,
            onSuccess: () => {
                window.history.replaceState(window.history.state, '', window.location.pathname);
            }
        });
    }, 300);
});

// goToPage removed as Pagination component handles routing
const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: 'numeric', minute: '2-digit'
    });
};
</script>

<template>
    <Head :title="t('sidebar.sales_history')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                    <Clock class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">{{ t('sidebar.sales_history') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('history.subtitle') }}</p>
                </div>
            </div>
        </template>

        <div class="px-[30px] py-6 min-h-[calc(100vh-14rem)] flex flex-col">
            <!-- Toolbar -->
            <div class="flex flex-col sm:flex-row items-center gap-4 mb-6">
                <!-- Search -->
                <div class="relative w-full sm:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Search class="h-4 w-4 text-gray-400" />
                    </div>
                    <input type="text" v-model="search" :placeholder="t('history.search_placeholder')"
                        class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-emerald-700 focus:border-emerald-700 sm:text-sm transition duration-150 ease-in-out" />
                </div>

                <!-- Date Filter -->
                <div class="w-full sm:w-48">
                    <select v-model="date"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-emerald-700 focus:border-emerald-700 sm:text-sm rounded-md shadow-sm">
                        <option value="">{{ t('history.all_time') }}</option>
                        <option value="today">{{ t('history.today') }}</option>
                        <option value="week">{{ t('history.this_week') }}</option>
                        <option value="month">{{ t('history.this_month') }}</option>
                    </select>
                </div>

                <!-- Cashier Filter (Admin/Manager only) -->
                <div v-if="cashiers && cashiers.length > 0" class="w-full sm:w-48">
                    <select v-model="cashier_id"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-emerald-700 focus:border-emerald-700 sm:text-sm rounded-md shadow-sm">
                        <option value="">{{ t('history.all_cashiers') }}</option>
                        <option v-for="c in cashiers" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-col flex-1 rounded-md bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between shrink-0">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-200">{{ t('history.recent_transactions') }}</h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ t('history.showing') }} {{ orders.from ?? 0 }} {{ t('history.to') }} {{ orders.to ?? 0 }} {{ t('history.of') }} {{ orders.total }}</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left whitespace-nowrap">
                        <thead class="bg-emerald-700 text-white">
                            <tr>
                                <th class="px-4 py-3 font-semibold">{{ t('history.invoice_no') }}</th>
                                <th class="px-4 py-3 font-semibold">{{ t('history.date_time') }}</th>
                                <th class="px-4 py-3 font-semibold">{{ t('history.cashier') }}</th>
                                <th v-if="isAdmin" class="px-4 py-3 font-semibold">{{ t('history.customer') }}</th>
                                <th class="px-4 py-3 font-semibold">{{ t('history.payment_method') }}</th>
                                <th v-if="isAdmin" class="px-4 py-3 font-semibold">{{ t('history.subtotal') }}</th>
                                <th v-if="isAdmin" class="px-4 py-3 font-semibold">{{ t('history.discount') }}</th>
                                <th class="px-4 py-3 font-semibold">{{ t('history.grand_total') }}</th>
                                <th class="px-4 py-3 font-semibold text-center">{{ t('history.status') }}</th>
                                <th class="px-4 py-3 font-semibold text-center">Print</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="order in orders.data" :key="order.id" class="hover:bg-emerald-50/50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 font-mono text-emerald-700 dark:text-emerald-400 font-semibold">
                                        <Receipt class="w-4 h-4" />
                                        {{ order.invoice_no }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ formatDate(order.order_date) }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                        <User class="w-4 h-4 text-gray-400 dark:text-gray-500" />
                                        {{ order.user?.name || 'Unknown' }}
                                    </div>
                                </td>
                                <td v-if="isAdmin" class="px-4 py-3">
                                    <div class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                        <Users class="w-4 h-4 text-gray-400 dark:text-gray-500" />
                                        {{ order.customer?.name || 'Walk-in' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span v-if="order.payment_method === 'khqr' || order.payment_method === 'bank'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800/50">
                                        <QrCode class="w-3.5 h-3.5" />
                                        {{ t('pos.khqr') }}
                                    </span>
                                    <span v-else class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                        <Banknote class="w-3.5 h-3.5" />
                                        {{ t('pos.cash') }}
                                    </span>
                                </td>
                                <td v-if="isAdmin" class="px-4 py-3 text-gray-600 dark:text-gray-300">${{ Number(order.subtotal).toFixed(2) }}</td>
                                <td v-if="isAdmin" class="px-4 py-3 text-red-500 dark:text-red-400">-${{ Number(order.discount).toFixed(2) }}</td>
                                <td class="px-4 py-3 font-bold text-gray-800 dark:text-gray-100">${{ Number(order.grand_total).toFixed(2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                                        <CheckCircle2 class="w-3.5 h-3.5" />
                                        {{ order.status === 'completed' ? t('history.completed') : order.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button @click="openInvoiceModal(order)" title="Print Invoice Receipt" class="p-1.5 rounded-md text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors">
                                        <Printer class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="orders.data.length === 0">
                                <td :colspan="isAdmin ? 10 : 7" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                    <Receipt class="w-10 h-10 mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                                    <p>{{ t('history.no_transactions') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination :data="orders" />
            </div>
        </div>

        <!-- PRINTABLE INVOICE RECEIPT MODAL -->
        <InvoiceReceiptModal :show="showInvoiceModal" :order="selectedOrderForInvoice" @close="showInvoiceModal = false" />
    </AuthenticatedLayout>
</template>
