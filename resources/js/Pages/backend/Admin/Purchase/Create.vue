<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { computed, ref } from 'vue';
import { ShoppingCart, Plus, Trash2, ArrowLeft, Search, Package } from '@lucide/vue';

const props = defineProps({
    suppliers: Array,
    products:  Array,
});

// ── Form ──────────────────────────────────────────────────────
const form = useForm({
    supplier_id:   '',
    purchase_date: new Date().toISOString().split('T')[0],
    note:          '',
    total:         0,
    items:         [],
});

// ── Product search / picker ───────────────────────────────────
const productSearch = ref('');
const filteredProducts = computed(() => {
    const q = productSearch.value.toLowerCase();
    if (!q) return props.products;
    return props.products.filter(p =>
        p.name.toLowerCase().includes(q) ||
        (p.barcode && p.barcode.toLowerCase().includes(q))
    );
});

const addProduct = (product) => {
    const existing = form.items.find(i => i.product_id === product.id);
    if (existing) {
        existing.quantity += 1;
        existing.subtotal = +(existing.quantity * existing.cost).toFixed(2);
    } else {
        form.items.push({
            product_id:   product.id,
            product_name: product.name,
            quantity:     1,
            cost:         parseFloat(product.cost_price) || 0,
            subtotal:     parseFloat(product.cost_price) || 0,
        });
    }
    recalcTotal();
    productSearch.value = '';
};

const removeItem = (idx) => {
    form.items.splice(idx, 1);
    recalcTotal();
};

const onItemChange = (item) => {
    item.quantity = Math.max(1, parseInt(item.quantity) || 1);
    item.cost     = Math.max(0, parseFloat(item.cost)     || 0);
    item.subtotal = +(item.quantity * item.cost).toFixed(2);
    recalcTotal();
};

const recalcTotal = () => {
    form.total = +form.items.reduce((s, i) => s + i.subtotal, 0).toFixed(2);
};

// ── Submit ────────────────────────────────────────────────────
const submit = () => {
    form.post(route('purchases.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="New Purchase" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                    <ShoppingCart class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">New Purchase</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Create a new purchase order and update stock</p>
                </div>
            </div>
        </template>

        <div class="px-[30px] py-5">
            <div class="space-y-6">

                <!-- Back link -->
                <Link :href="route('purchases.index')"
                    class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">
                    <ArrowLeft class="w-4 h-4" /> Back to Purchases
                </Link>

                <form @submit.prevent="submit" class="space-y-5">

                    <!-- ── Section 1: Header Info ── -->
                    <div class="bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Purchase Info</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Supplier <span class="text-red-500 dark:text-red-400">*</span>
                                </label>
                                <select v-model="form.supplier_id" required
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm text-sm focus:border-emerald-700 focus:ring-emerald-700">
                                    <option value="" disabled>Select supplier...</option>
                                    <option v-for="s in suppliers" :key="s.id" :value="s.id">
                                        {{ s.company_name }}
                                    </option>
                                </select>
                                <InputError class="mt-1" :message="form.errors.supplier_id" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Purchase Date <span class="text-red-500 dark:text-red-400">*</span>
                                </label>
                                <input type="date" v-model="form.purchase_date" required
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm text-sm focus:border-emerald-700 focus:ring-emerald-700" />
                                <InputError class="mt-1" :message="form.errors.purchase_date" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Note</label>
                                <textarea v-model="form.note" rows="2" placeholder="Optional note..."
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm text-sm focus:border-emerald-700 focus:ring-emerald-700"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ── Section 2: Product Picker ── -->
                    <div class="bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Add Products</h3>
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500" />
                            <input
                                v-model="productSearch"
                                type="text"
                                placeholder="Search product by name or barcode..."
                                class="block w-full rounded-md border-gray-300 dark:border-gray-700 pl-9 shadow-sm bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 text-sm focus:border-emerald-700 focus:ring-emerald-700"
                            />
                        </div>

                        <!-- Product grid (shows when searching) -->
                        <div v-if="productSearch" class="mt-3 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 max-h-52 overflow-y-auto scrollbar-hide">
                            <button
                                v-for="p in filteredProducts" :key="p.id"
                                type="button"
                                @click="addProduct(p)"
                                class="flex flex-col items-start p-3 rounded-md border border-gray-200 dark:border-gray-700 hover:border-emerald-500 dark:hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-gray-700/50 transition-colors text-left"
                            >
                                <span class="flex items-center justify-center w-8 h-8 rounded bg-emerald-100 dark:bg-emerald-900/30 mb-2">
                                    <Package class="w-4 h-4 text-emerald-700 dark:text-emerald-400" />
                                </span>
                                <span class="text-xs font-medium text-gray-800 dark:text-gray-200 leading-tight">{{ p.name }}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Stock: {{ p.stock }}</span>
                                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 mt-1">${{ p.cost_price }}</span>
                            </button>
                            <p v-if="filteredProducts.length === 0" class="col-span-full text-sm text-gray-400 dark:text-gray-500 text-center py-4">
                                No products match "{{ productSearch }}"
                            </p>
                        </div>
                        <InputError class="mt-2" :message="form.errors.items" />
                    </div>

                    <!-- ── Section 3: Purchase Items Table ── -->
                    <div class="bg-white dark:bg-gray-800 rounded-md shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Order Items</h3>
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ form.items.length }} item(s)</span>
                        </div>

                        <!-- Empty state -->
                        <div v-if="form.items.length === 0" class="py-12 text-center text-gray-400 dark:text-gray-500">
                            <Package class="w-10 h-10 mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                            <p class="text-sm">Search and click a product above to add it here.</p>
                        </div>

                        <!-- Items table -->
                        <table v-else class="w-full text-sm text-left">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                                    <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">#</th>
                                    <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Product</th>
                                    <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300 w-28">Qty</th>
                                    <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300 w-32">Cost ($)</th>
                                    <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300 w-32">Subtotal</th>
                                    <th class="px-6 py-3 w-12"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr v-for="(item, idx) in form.items" :key="item.product_id"
                                    class="hover:bg-emerald-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-3 text-gray-400 dark:text-gray-500 font-mono text-xs">{{ idx + 1 }}</td>
                                    <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-200">{{ item.product_name }}</td>
                                    <td class="px-6 py-3">
                                        <input type="number" v-model.number="item.quantity" min="1"
                                            @input="onItemChange(item)"
                                            class="w-24 rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 text-sm focus:border-emerald-700 focus:ring-emerald-700" />
                                    </td>
                                    <td class="px-6 py-3">
                                        <input type="number" v-model.number="item.cost" min="0" step="0.01"
                                            @input="onItemChange(item)"
                                            class="w-28 rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 text-sm focus:border-emerald-700 focus:ring-emerald-700" />
                                    </td>
                                    <td class="px-6 py-3 font-semibold text-emerald-700 dark:text-emerald-400">
                                        ${{ item.subtotal.toFixed(2) }}
                                    </td>
                                    <td class="px-6 py-3">
                                        <button type="button" @click="removeItem(idx)"
                                            class="p-1 rounded text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Total row -->
                        <div v-if="form.items.length > 0"
                            class="flex items-center justify-end gap-6 px-6 py-4 bg-emerald-700 text-white">
                            <span class="text-sm font-medium opacity-80">Grand Total</span>
                            <span class="text-xl font-bold">${{ form.total.toFixed(2) }}</span>
                        </div>
                    </div>

                    <!-- ── Submit ── -->
                    <div class="flex items-center justify-end gap-3">
                        <Link :href="route('purchases.index')"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </Link>
                        <button type="submit"
                            :disabled="form.processing || form.items.length === 0 || !form.supplier_id"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-emerald-700 rounded-md hover:bg-emerald-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <Plus class="w-4 h-4" />
                            Save Purchase & Update Stock
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.scrollbar-hide::-webkit-scrollbar { display: none; }
</style>
