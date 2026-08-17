<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InvoiceReceiptModal from '@/Components/InvoiceReceiptModal.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, nextTick } from 'vue';
import { useI18n } from 'vue-i18n';
import QRCode from 'qrcode';
import axios from 'axios';
import { 
    Search, ShoppingCart, Plus, Minus, Trash2, CreditCard, 
    ImageOff, MonitorSmartphone, Banknote, QrCode, X, 
    CheckCircle2, DollarSign, Smartphone, Building2, Check, RefreshCw
} from '@lucide/vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const { t } = useI18n();
const page = usePage();

const settings = computed(() => page.props.settings || {});
const isTaxEnabled = computed(() => Boolean(settings.value?.enable_tax));
const taxRatePercent = computed(() => isTaxEnabled.value ? Number(settings.value?.tax_rate || 0) : 0);
const taxName = computed(() => settings.value?.tax_name || t('pos.tax'));
const storeName = computed(() => settings.value?.store_name || 'CHOUERNCHYWORN KONG');

const search = ref(props.filters?.search || '');
const category_id = ref(props.filters?.category_id || '');

let searchTimeout = null;
watch([search, category_id], ([newSearch, newCategory]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const query = {};
        if (newSearch) query.search = newSearch;
        if (newCategory) query.category_id = newCategory;

        router.get(route('pos.index'), query, {
            preserveState: true,
            replace: true,
            preserveScroll: true,
            onSuccess: () => {
                window.history.replaceState(window.history.state, '', window.location.pathname);
            }
        });
    }, 300);
});

// Cart State
const cart = ref([]);

const addToCart = (product) => {
    const existing = cart.value.find(item => item.id === product.id);
    if (existing) {
        if (existing.quantity < product.stock) {
            existing.quantity++;
        }
    } else {
        cart.value.push({ ...product, quantity: 1 });
    }
};

const increaseQuantity = (item) => {
    if (item.quantity < item.stock) {
        item.quantity++;
    }
};

const decreaseQuantity = (item) => {
    if (item.quantity > 1) {
        item.quantity--;
    } else {
        removeFromCart(item);
    }
};

const removeFromCart = (item) => {
    cart.value = cart.value.filter(i => i.id !== item.id);
};

// Totals Computation
const subtotal = computed(() => {
    return cart.value.reduce((total, item) => total + (item.selling_price * item.quantity), 0);
});

const tax = computed(() => {
    if (!isTaxEnabled.value) return 0;
    return subtotal.value * (taxRatePercent.value / 100);
});
const discount = ref(0);
const grandTotal = computed(() => subtotal.value + tax.value - discount.value);

// Currency Exchange & Change Calculation
const exchangeRate = 4100; // 1 USD = 4,100 KHR
const grandTotalKhr = computed(() => Math.round(grandTotal.value * exchangeRate));

// Payment & Invoice Modal State
const showPaymentModal = ref(false);
const showInvoiceModal = ref(false);
const currentInvoiceOrder = ref(null);

const paymentMethod = ref('cash'); // 'cash' | 'khqr'
const cashReceived = ref(null);
const cashReceivedKhr = ref(null);
const qrCodeDataUrl = ref('');
const qrMd5 = ref('');
const bakongMerchantName = ref('CHOUERNCHYWORN KONG');
const bakongAccountId = ref('chouernchyworn_kong@bkrt');
const isGeneratingQr = ref(false);
const isCheckingStatus = ref(false);
const checkPaymentStatusMessage = ref('');

// Auto-trigger invoice receipt modal when order checkout completes
watch(() => page.props.flash?.order, (newOrder) => {
    if (newOrder) {
        currentInvoiceOrder.value = newOrder;
        showInvoiceModal.value = true;
    }
}, { immediate: true });

const totalCashReceivedUsd = computed(() => {
    const usd = Number(cashReceived.value || 0);
    const khrInUsd = Number(cashReceivedKhr.value || 0) / exchangeRate;
    return usd + khrInUsd;
});

const changeAmount = computed(() => {
    if (totalCashReceivedUsd.value === 0) return 0;
    return Math.max(0, totalCashReceivedUsd.value - grandTotal.value);
});

const changeAmountKhr = computed(() => Math.round(changeAmount.value * exchangeRate));

const isCashValid = computed(() => {
    if (paymentMethod.value !== 'cash') return true;
    return totalCashReceivedUsd.value >= (grandTotal.value - 0.001);
});

const form = useForm({
    cart: [],
    subtotal: 0,
    tax: 0,
    discount: 0,
    grand_total: 0,
    customer_id: null,
    payment_method: 'cash',
    cash_received: null,
    change_amount: 0,
});

const openPaymentModal = () => {
    if (cart.value.length === 0) return;
    cashReceived.value = Number(grandTotal.value.toFixed(2));
    paymentMethod.value = 'cash';
    showPaymentModal.value = true;
    generateKhqr();
};

const closePaymentModal = () => {
    showPaymentModal.value = false;
};

const selectPaymentMethod = (method) => {
    paymentMethod.value = method;
    if (method === 'khqr' && !qrCodeDataUrl.value) {
        generateKhqr();
    }
};

const generateKhqr = async () => {
    isGeneratingQr.value = true;
    checkPaymentStatusMessage.value = '';
    try {
        const response = await axios.post(route('pos.khqr.generate'), {
            grand_total: grandTotal.value,
            currency: 'KHR',
        });

        if (response.data && response.data.qr) {
            qrMd5.value = response.data.md5;
            bakongMerchantName.value = response.data.merchant_name || 'CHOUERNCHYWORN KONG';
            bakongAccountId.value = response.data.account_id || 'chouernchyworn_kong@bkrt';

            const url = await QRCode.toDataURL(response.data.qr, {
                width: 260,
                margin: 1,
                color: {
                    dark: '#000000',
                    light: '#ffffff',
                },
                errorCorrectionLevel: 'M',
            });
            qrCodeDataUrl.value = url;
        }
    } catch (err) {
        console.error('Failed to generate official Bakong KHQR:', err);
    } finally {
        isGeneratingQr.value = false;
    }
};

const verifyBakongPayment = async () => {
    if (!qrMd5.value) return;
    isCheckingStatus.value = true;
    checkPaymentStatusMessage.value = '';
    try {
        const res = await axios.post(route('pos.khqr.check'), {
            md5: qrMd5.value,
        });

        if (res.data && (res.data.responseCode === 0 || res.data.data?.hash || res.data.errorCode === 0)) {
            checkPaymentStatusMessage.value = '✓ Payment Verified Successfully!';
            setTimeout(() => {
                confirmCheckout();
            }, 800);
        } else {
            checkPaymentStatusMessage.value = res.data?.responseMessage || res.data?.message || 'Transaction not found yet. Please ensure customer completes transfer in app.';
        }
    } catch (err) {
        checkPaymentStatusMessage.value = err.response?.data?.error || 'Checking Bakong API... Ensure transfer is completed.';
    } finally {
        isCheckingStatus.value = false;
    }
};

const setQuickCash = (val) => {
    cashReceivedKhr.value = null;
    if (val === 'exact') {
        cashReceived.value = Number(grandTotal.value.toFixed(2));
    } else {
        cashReceived.value = Number(val);
    }
};

const addQuickCash = (val) => {
    cashReceived.value = Number((Number(cashReceived.value || 0) + val).toFixed(2));
};

const setQuickCashKhr = (rielVal) => {
    cashReceived.value = null;
    cashReceivedKhr.value = rielVal;
};

const formatKhr = (val) => {
    return new Intl.NumberFormat('km-KH').format(val) + ' ៛';
};

const confirmCheckout = () => {
    if (cart.value.length === 0) return;
    if (paymentMethod.value === 'cash' && !isCashValid.value) return;

    form.cart = cart.value.map(item => ({
        id: item.id,
        quantity: item.quantity,
        price: item.selling_price,
    }));
    form.subtotal = subtotal.value;
    form.tax = tax.value;
    form.discount = discount.value;
    form.grand_total = grandTotal.value;
    form.payment_method = paymentMethod.value;
    form.cash_received = totalCashReceivedUsd.value;
    form.change_amount = changeAmount.value;

    form.post(route('pos.checkout'), {
        preserveScroll: true,
        onSuccess: () => {
            cart.value = [];
            discount.value = 0;
            cashReceived.value = null;
            cashReceivedKhr.value = null;
            showPaymentModal.value = false;
        }
    });
};
</script>

<template>
    <Head :title="t('sidebar.pos_screen')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                    <MonitorSmartphone class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">{{ t('sidebar.pos_screen') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('pos.subtitle') }}</p>
                </div>
            </div>
        </template>

        <div class="flex flex-col lg:flex-row h-[calc(100vh-14rem)] gap-6 px-[30px] py-4 overflow-hidden">
            
            <!-- Left Side: Products Grid -->
            <div class="flex flex-col flex-1 bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Toolbar / Search -->
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-4 bg-gray-50/50 dark:bg-gray-800/50 shrink-0">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <Search class="h-4 w-4 text-gray-400 dark:text-gray-500" />
                        </div>
                        <input type="text" v-model="search" :placeholder="t('pos.search_product')"
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100" />
                    </div>
                    <select v-model="category_id"
                        class="block w-48 py-2.5 px-3 border border-gray-300 dark:border-gray-700 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100">
                        <option value="">{{ t('common.all_categories') }}</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>

                <!-- Product Grid -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                        <div v-for="product in products.data" :key="product.id" @click="addToCart(product)"
                            class="group relative flex flex-col bg-white dark:bg-gray-900/30 border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden cursor-pointer hover:border-emerald-500 dark:hover:border-emerald-500 hover:shadow-md transition-all active:scale-95">
                            <div class="h-[158px] bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden">
                                <img v-if="product.image" :src="'/storage/' + product.image" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                <ImageOff v-else class="w-8 h-8 text-gray-300 dark:text-gray-600" />
                            </div>
                            <div class="p-3 flex flex-col flex-1">
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate mb-1">{{ product.category?.name || 'Uncategorized' }}</p>
                                <h3 class="font-bold text-gray-800 dark:text-gray-200 text-sm leading-tight line-clamp-2 mb-2 flex-1">{{ product.name }}</h3>
                                <div class="flex items-center justify-between mt-auto pt-2 border-t border-gray-50 dark:border-gray-700/50">
                                    <span class="font-extrabold text-emerald-700 dark:text-emerald-400">${{ Number(product.selling_price).toFixed(2) }}</span>
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ product.stock }} {{ t('common.units') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="products.data.length === 0" class="flex flex-col items-center justify-center h-64 text-gray-400 dark:text-gray-500">
                        <Search class="w-12 h-12 mb-3 text-gray-300 dark:text-gray-600" />
                        <p class="text-lg font-medium text-gray-500 dark:text-gray-400">{{ t('product.no_products') }}</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="p-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex justify-center gap-2 shrink-0">
                    <button v-for="(link, i) in products.links" :key="i"
                        @click="link.url ? router.visit(link.url, { preserveState: true }) : null"
                        v-html="link.label"
                        :disabled="!link.url"
                        :class="[
                            'px-3 py-1 rounded-md text-sm transition-colors',
                            link.active ? 'bg-emerald-700 text-white font-bold shadow-sm' : 'bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600',
                            !link.url && 'opacity-50 cursor-not-allowed'
                        ]"
                    ></button>
                </div>
            </div>

            <!-- Right Side: Cart Panel -->
            <div class="w-full lg:w-[400px] flex flex-col bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden shrink-0">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-md bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                        <ShoppingCart class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 text-lg">{{ t('pos.cart') }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ cart.length }} {{ t('reports.items') }}</p>
                    </div>
                    <button v-if="cart.length > 0" @click="cart = []" class="ml-auto text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 px-3 py-1.5 rounded-md transition-colors">
                        {{ t('pos.clear_cart') }}
                    </button>
                </div>

                <!-- Cart Items List -->
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    <div v-for="item in cart" :key="item.id" class="flex gap-3 bg-white dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 p-3 rounded-md shadow-sm group">
                        <div class="w-16 h-16 rounded-md bg-gray-50 dark:bg-gray-800 flex items-center justify-center overflow-hidden shrink-0">
                            <img v-if="item.image" :src="'/storage/' + item.image" class="w-full h-full object-cover" />
                            <ImageOff v-else class="w-5 h-5 text-gray-300 dark:text-gray-600" />
                        </div>
                        
                        <div class="flex-1 flex flex-col justify-between">
                            <div class="flex justify-between items-start gap-2">
                                <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-200 line-clamp-2 leading-tight">{{ item.name }}</h4>
                                <span class="font-bold text-emerald-700 dark:text-emerald-400 text-sm whitespace-nowrap">${{ (item.selling_price * item.quantity).toFixed(2) }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">${{ Number(item.selling_price).toFixed(2) }}</span>
                                <div class="flex items-center bg-gray-100 dark:bg-gray-800 rounded-md border border-gray-200 dark:border-gray-700">
                                    <button @click="decreaseQuantity(item)" class="p-1 text-gray-600 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-400 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-l-md transition-colors">
                                        <Minus class="w-3.5 h-3.5" />
                                    </button>
                                    <span class="w-8 text-center text-xs font-bold text-gray-800 dark:text-gray-200 select-none">{{ item.quantity }}</span>
                                    <button @click="increaseQuantity(item)" :disabled="item.quantity >= item.stock" class="p-1 text-gray-600 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-400 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-r-md transition-colors disabled:opacity-30">
                                        <Plus class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="cart.length === 0" class="flex flex-col items-center justify-center h-full text-gray-400 dark:text-gray-500 py-12">
                        <ShoppingCart class="w-12 h-12 mb-3 text-gray-200 dark:text-gray-700" />
                        <p class="text-sm font-medium">{{ t('pos.empty_cart') }}</p>
                    </div>
                </div>

                <!-- Totals & Pay Trigger -->
                <div class="p-5 border-t border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-800/80 space-y-3 shrink-0">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400 font-medium">{{ t('pos.subtotal') }}</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">${{ subtotal.toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400 font-medium">
                            {{ taxName }} ({{ isTaxEnabled ? taxRatePercent + '%' : '0%' }})
                        </span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">${{ tax.toFixed(2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400 font-medium">{{ t('pos.discount') }}</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">-${{ discount.toFixed(2) }}</span>
                    </div>
                    
                    <div class="pt-3 pb-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-end mt-2">
                        <div>
                            <span class="text-base font-bold text-gray-800 dark:text-gray-100 block">{{ t('pos.total') }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ formatKhr(grandTotalKhr) }}</span>
                        </div>
                        <span class="text-3xl font-extrabold text-emerald-700 dark:text-emerald-400">${{ grandTotal.toFixed(2) }}</span>
                    </div>

                    <button @click="openPaymentModal" :disabled="cart.length === 0 || form.processing"
                        class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-md text-base font-bold text-white transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
                        :class="cart.length > 0 ? 'bg-emerald-700 hover:bg-emerald-800 shadow-emerald-500/30 shadow-lg hover:shadow-emerald-500/40 hover:-translate-y-0.5' : 'bg-gray-400 dark:bg-gray-600'">
                        <CreditCard class="w-5 h-5" />
                        {{ t('pos.pay') }}
                    </button>
                    <InputError class="mt-2 text-center" :message="form.errors.error" />
                </div>
            </div>
        </div>

        <!-- PAYMENT METHOD MODAL (CASH & BAKONG KHQR) -->
        <div v-if="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in">
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-800/80 flex items-center justify-between shrink-0">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ t('pos.select_payment') }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total: <span class="font-bold text-emerald-600 dark:text-emerald-400">${{ grandTotal.toFixed(2) }}</span> ({{ formatKhr(grandTotalKhr) }})</p>
                    </div>
                    <button @click="closePaymentModal" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- Payment Method Selector Tabs -->
                <div class="p-4 bg-gray-100/60 dark:bg-gray-900/40 border-b border-gray-100 dark:border-gray-700 grid grid-cols-2 gap-3 shrink-0">
                    <!-- Cash Button Option -->
                    <button @click="selectPaymentMethod('cash')"
                        :class="[
                            'flex items-center justify-center gap-2 py-3 px-4 rounded-lg font-bold text-sm transition-all border shadow-sm',
                            paymentMethod === 'cash' 
                                ? 'bg-emerald-700 text-white border-emerald-700 shadow-emerald-700/20 ring-2 ring-emerald-500/30' 
                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'
                        ]">
                        <Banknote class="w-5 h-5" />
                        {{ t('pos.cash') }}
                    </button>

                    <!-- Bank / KHQR Button Option -->
                    <button @click="selectPaymentMethod('khqr')"
                        :class="[
                            'flex items-center justify-center gap-2 py-3 px-4 rounded-lg font-bold text-sm transition-all border shadow-sm',
                            paymentMethod === 'khqr' 
                                ? 'bg-red-600 text-white border-red-600 shadow-red-600/20 ring-2 ring-red-500/30' 
                                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'
                        ]">
                        <QrCode class="w-5 h-5 text-red-500 group-hover:text-white" :class="paymentMethod === 'khqr' ? '!text-white' : ''" />
                        {{ t('pos.khqr') }}
                    </button>
                </div>

                <!-- Modal Body Content -->
                <div class="p-6 overflow-y-auto space-y-5">
                    
                    <!-- OPTION 1: CASH PAYMENT -->
                    <div v-if="paymentMethod === 'cash'" class="space-y-4">
                        <!-- Total Due Box -->
                        <div class="bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <span class="text-xs font-semibold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider block">Total Amount Due</span>
                                <span class="text-2xl font-extrabold text-emerald-700 dark:text-emerald-400">${{ grandTotal.toFixed(2) }}</span>
                            </div>
                            <span class="text-sm font-bold text-emerald-800 dark:text-emerald-300 bg-emerald-100 dark:bg-emerald-900/60 px-3 py-1 rounded-md">{{ formatKhr(grandTotalKhr) }}</span>
                        </div>

                        <!-- Dual Currency Cash Received Inputs ($ USD & ៛ KHR) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- USD Input -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    {{ t('pos.cash_received') }} ($ USD)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 font-bold">$</div>
                                    <input type="number" step="0.01" min="0" v-model="cashReceived" placeholder="0.00"
                                        class="block w-full pl-7 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-base font-extrabold focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100" />
                                </div>
                            </div>

                            <!-- KHR Input -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    ប្រាក់ទទួលបាន (៛ KHR)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 font-bold text-xs">៛</div>
                                    <input type="number" step="100" min="0" v-model="cashReceivedKhr" placeholder="0"
                                        class="block w-full pl-7 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-base font-extrabold focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100" />
                                </div>
                            </div>
                        </div>

                        <!-- Quick Presets ($ USD & ៛ KHR) -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Quick Presets:</span>
                                <button type="button" @click="setQuickCash('exact')" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                                    Reset Exact (${{ grandTotal.toFixed(2) }})
                                </button>
                            </div>

                            <!-- Smart Bills ($ USD) -->
                            <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                                <button type="button" @click="setQuickCash('exact')"
                                    :class="[
                                        'py-1.5 px-2 text-xs font-bold rounded-md border transition-colors',
                                        cashReceived === Number(grandTotal.toFixed(2)) 
                                            ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' 
                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-200 dark:border-gray-600 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 hover:text-emerald-700'
                                    ]">
                                    {{ t('pos.exact_amount') }}
                                </button>
                                <button type="button" v-for="amount in smartCashPresets" :key="'usd-'+amount" @click="setQuickCash(amount)"
                                    :class="[
                                        'py-1.5 px-2 text-xs font-bold rounded-md border transition-colors',
                                        cashReceived === amount 
                                            ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' 
                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 border-gray-200 dark:border-gray-600 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 hover:text-emerald-700'
                                    ]">
                                    ${{ amount }}
                                </button>
                            </div>

                            <!-- Common Riel Bills (៛ KHR) -->
                            <div class="grid grid-cols-4 gap-1.5 pt-1">
                                <button type="button" v-for="khrVal in [10000, 20000, 50000, 100000]" :key="'khr-'+khrVal" @click="setQuickCashKhr(khrVal)"
                                    :class="[
                                        'py-1.5 px-2 text-[11px] font-bold rounded border transition-colors',
                                        cashReceivedKhr === khrVal
                                            ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm'
                                            : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/60'
                                    ]">
                                    {{ formatKhr(khrVal) }}
                                </button>
                            </div>
                        </div>

                        <!-- Change Amount Display -->
                        <div class="bg-gray-50 dark:bg-gray-900/60 border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">{{ t('pos.change') }}</span>
                                <span :class="['text-2xl font-extrabold', isCashValid ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500']">
                                    ${{ changeAmount.toFixed(2) }}
                                </span>
                            </div>
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-3 py-1 rounded-md">
                                {{ formatKhr(changeAmountKhr) }}
                            </span>
                        </div>

                        <p v-if="!isCashValid" class="text-xs text-red-500 font-semibold text-center">
                            ⚠️ {{ t('pos.insufficient_cash') }}
                        </p>
                    </div>

                    <!-- OPTION 2: BAKONG KHQR PAYMENT -->
                    <div v-else-if="paymentMethod === 'khqr'" class="flex flex-col items-center space-y-4">
                        
                        <!-- Official Bakong KHQR Styled Container -->
                        <div class="w-full max-w-[320px] bg-white dark:bg-gray-900 rounded-xl overflow-hidden shadow-xl border-2 border-red-600/30 flex flex-col items-center">
                            
                            <!-- Red Header Header -->
                            <div class="w-full bg-[#E11927] text-white py-3 px-4 flex items-center justify-between shadow-md">
                                <div class="flex items-center gap-2">
                                    <span class="font-extrabold text-lg tracking-wider">KHQR</span>
                                </div>
                                <span class="text-[10px] font-bold bg-white/20 px-2 py-0.5 rounded uppercase">Bakong Payment</span>
                            </div>

                            <!-- Merchant Store Info -->
                            <div class="w-full px-4 pt-3 pb-2 text-center bg-gray-50 dark:bg-gray-800/80 border-b border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ t('pos.merchant') }}</p>
                                <h4 class="font-bold text-gray-900 dark:text-gray-100 text-sm truncate">{{ bakongMerchantName }}</h4>
                                <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400">{{ bakongAccountId }}</p>
                            </div>

                            <!-- Dynamic QR Code -->
                            <div class="p-5 flex flex-col items-center justify-center relative bg-white dark:bg-white w-full">
                                <div v-if="isGeneratingQr" class="w-56 h-56 flex items-center justify-center bg-gray-100 rounded-lg">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-red-600"></div>
                                </div>
                                <div v-else-if="qrCodeDataUrl" class="relative group">
                                    <img :src="qrCodeDataUrl" alt="KHQR Code" class="w-56 h-56 object-contain rounded-md" />
                                    <!-- Center KHQR logo badge -->
                                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                        <div class="w-10 h-10 bg-red-600 text-white rounded-md shadow-md flex items-center justify-center font-extrabold text-xs border-2 border-white">
                                            KHQR
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Dual Amount Header (USD & KHR) -->
                            <div class="w-full px-4 py-3 bg-red-50 dark:bg-red-950/30 border-t border-red-100 dark:border-red-900/40 text-center">
                                <div class="text-2xl font-extrabold text-red-600 dark:text-red-400">${{ grandTotal.toFixed(2) }}</div>
                                <div class="text-xs font-bold text-red-700 dark:text-red-300 mt-0.5">{{ formatKhr(grandTotalKhr) }}</div>
                            </div>
                        </div>

                        <!-- Scan Helper Note -->
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-600">
                            <Smartphone class="w-4 h-4 text-red-500 shrink-0" />
                            <span>{{ t('pos.scan_to_pay') }}</span>
                        </div>

                        <!-- Verify Payment Status Button -->
                        <button type="button" @click="verifyBakongPayment" :disabled="isCheckingStatus || !qrMd5"
                            class="w-full max-w-[320px] flex items-center justify-center gap-2 py-2.5 px-4 bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800 rounded-lg text-xs font-bold hover:bg-red-100 dark:hover:bg-red-900/60 transition-colors shadow-sm disabled:opacity-50">
                            <RefreshCw class="w-4 h-4" :class="isCheckingStatus ? 'animate-spin' : ''" />
                            <span>{{ isCheckingStatus ? 'Checking Bakong API...' : 'Verify Transfer Status (Bakong API)' }}</span>
                        </button>

                        <p v-if="checkPaymentStatusMessage" class="text-xs font-semibold text-center max-w-[320px] px-2" :class="checkPaymentStatusMessage.includes('✓') ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'">
                            {{ checkPaymentStatusMessage }}
                        </p>
                    </div>

                </div>

                <!-- Modal Footer Controls -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-800/80 flex items-center justify-end gap-3 shrink-0">
                    <button type="button" @click="closePaymentModal"
                        class="py-2.5 px-4 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        {{ t('common.cancel') }}
                    </button>
                    
                    <button type="button" @click="confirmCheckout" :disabled="form.processing || (paymentMethod === 'cash' && !isCashValid)"
                        :class="[
                            'flex items-center gap-2 py-2.5 px-6 rounded-lg text-sm font-bold text-white transition-all shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed',
                            paymentMethod === 'khqr' 
                                ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500 shadow-red-600/30' 
                                : 'bg-emerald-700 hover:bg-emerald-800 focus:ring-emerald-600 shadow-emerald-700/30'
                        ]">
                        <Check class="w-4 h-4" />
                        <span>{{ t('pos.confirm_payment') }}</span>
                    </button>
                </div>

            </div>
        </div>

        <!-- PRINTABLE INVOICE RECEIPT MODAL -->
        <InvoiceReceiptModal :show="showInvoiceModal" :order="currentInvoiceOrder" @close="showInvoiceModal = false" />

    </AuthenticatedLayout>
</template>

<style scoped>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.97); }
    to { opacity: 1; transform: scale(1); }
}
.animate-fade-in {
    animation: fadeIn 0.18s ease-out forwards;
}
</style>
