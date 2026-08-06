<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { Search, ShoppingCart, Plus, Minus, Trash2, CreditCard, ImageOff, MonitorSmartphone } from '@lucide/vue';

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
const discount = ref(0); // Optional discount field
const grandTotal = computed(() => subtotal.value + tax.value - discount.value);

const form = useForm({
    cart: [],
    subtotal: 0,
    tax: 0,
    discount: 0,
    grand_total: 0,
    customer_id: null,
});

const checkout = () => {
    if (cart.value.length === 0) return;

    form.cart = cart.value.map(item => ({
        id: item.id,
        quantity: item.quantity,
        price: item.selling_price,
    }));
    form.subtotal = subtotal.value;
    form.tax = tax.value;
    form.discount = discount.value;
    form.grand_total = grandTotal.value;

    form.post(route('pos.checkout'), {
        preserveScroll: true,
        onSuccess: () => {
            cart.value = []; // Clear cart on success
            discount.value = 0;
            // The backend returns a success message which will be displayed globally or we can handle it here
            alert('Payment Successful!');
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
                    <p class="text-sm text-gray-500 dark:text-gray-400">Fast checkout system</p>
                </div>
            </div>
        </template>

        <div class="flex flex-col lg:flex-row h-[calc(100vh-14rem)] gap-6 px-[30px] py-4 overflow-hidden">
            
            <!-- Left Side: Products Grid (Takes remaining width) -->
            <div class="flex flex-col flex-1 bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Toolbar / Search -->
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-4 bg-gray-50/50 dark:bg-gray-800/50 shrink-0">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <Search class="h-4 w-4 text-gray-400 dark:text-gray-500" />
                        </div>
                        <input type="text" v-model="search" placeholder="Search product name or barcode..."
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100" />
                    </div>
                    <select v-model="category_id"
                        class="block w-48 py-2.5 px-3 border border-gray-300 dark:border-gray-700 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100">
                        <option value="">All Categories</option>
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
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded-full">{{ product.stock }} left</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="products.data.length === 0" class="flex flex-col items-center justify-center h-64 text-gray-400 dark:text-gray-500">
                        <Search class="w-12 h-12 mb-3 text-gray-300 dark:text-gray-600" />
                        <p class="text-lg font-medium text-gray-500 dark:text-gray-400">No products found.</p>
                        <p class="text-sm">Try adjusting your search or category filter.</p>
                    </div>
                </div>

                <!-- Pagination (Simple) -->
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

            <!-- Right Side: Cart (Fixed width) -->
            <div class="w-full lg:w-[400px] flex flex-col bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden shrink-0">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-md bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                        <ShoppingCart class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-100 text-lg">Current Order</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ cart.length }} items selected</p>
                    </div>
                    <button v-if="cart.length > 0" @click="cart = []" class="ml-auto text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 px-3 py-1.5 rounded-md transition-colors">
                        Clear All
                    </button>
                </div>

                <!-- Cart Items List -->
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    <div v-for="item in cart" :key="item.id" class="flex gap-3 bg-white dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 p-3 rounded-md shadow-sm group">
                        <!-- Image -->
                        <div class="w-16 h-16 rounded-md bg-gray-50 dark:bg-gray-800 flex items-center justify-center overflow-hidden shrink-0">
                            <img v-if="item.image" :src="'/storage/' + item.image" class="w-full h-full object-cover" />
                            <ImageOff v-else class="w-5 h-5 text-gray-300 dark:text-gray-600" />
                        </div>
                        
                        <!-- Details -->
                        <div class="flex-1 flex flex-col justify-between">
                            <div class="flex justify-between items-start gap-2">
                                <h4 class="font-semibold text-sm text-gray-800 dark:text-gray-200 line-clamp-2 leading-tight">{{ item.name }}</h4>
                                <span class="font-bold text-emerald-700 dark:text-emerald-400 text-sm whitespace-nowrap">${{ (item.selling_price * item.quantity).toFixed(2) }}</span>
                            </div>
                            
                            <!-- Quantity Controls -->
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">${{ Number(item.selling_price).toFixed(2) }} / ea</span>
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
                        <p class="text-sm font-medium">Cart is empty</p>
                        <p class="text-xs mt-1">Select products to add them here.</p>
                    </div>
                </div>

                <!-- Totals & Checkout -->
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
                    <div class="flex items-center justify-between text-sm group relative">
                        <span class="text-gray-600 dark:text-gray-400 font-medium">{{ t('pos.discount') }}</span>
                        <span class="font-semibold text-red-600 dark:text-red-400">-${{ discount.toFixed(2) }}</span>
                    </div>
                    
                    <div class="pt-3 pb-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-end mt-2">
                        <span class="text-base font-bold text-gray-800 dark:text-gray-100">{{ t('pos.total') }}</span>
                        <span class="text-3xl font-extrabold text-emerald-700 dark:text-emerald-400">${{ grandTotal.toFixed(2) }}</span>
                    </div>

                    <button @click="checkout" :disabled="cart.length === 0 || form.processing"
                        class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-md text-base font-bold text-white transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
                        :class="cart.length > 0 ? 'bg-emerald-700 hover:bg-emerald-800 shadow-emerald-500/30 shadow-lg hover:shadow-emerald-500/40 hover:-translate-y-0.5' : 'bg-gray-400 dark:bg-gray-600'">
                        <CreditCard class="w-5 h-5" />
                        {{ t('pos.pay') }}
                    </button>
                    <InputError class="mt-2 text-center" :message="form.errors.error" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Hide scrollbar but keep scroll working */
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.scrollbar-hide::-webkit-scrollbar { display: none; }
</style>
