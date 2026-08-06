<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { Package, Plus, Pencil, Trash2, ChevronLeft, ChevronRight, X, Layers, ImageOff, UploadCloud, AlertTriangle, Search } from '@lucide/vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const { t } = useI18n();

const showModal = ref(false);
const showDeleteModal = ref(false);
const search = ref(props.filters?.search || '');
const category_id = ref(props.filters?.category_id || '');

let searchTimeout = null;
watch([search, category_id], ([newSearch, newCategory]) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const query = {};
        if (newSearch) query.search = newSearch;
        if (newCategory) query.category_id = newCategory;

        router.get(route('products.index'), query, {
            preserveState: true,
            replace: true,
            preserveScroll: true,
            onSuccess: () => {
                window.history.replaceState(window.history.state, '', window.location.pathname);
            }
        });
    }, 300);
});
const isEditing = ref(false);
const editingProduct = ref(null);
const deletingProduct = ref(null);
const imagePreview = ref(null);

const form = useForm({
    category_id: '',
    name: '',
    barcode: '',
    cost_price: '',
    selling_price: '',
    stock: 0,
    image: null,
    description: '',
});

const onImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const openCreate = () => {
    isEditing.value = false;
    editingProduct.value = null;
    form.reset();
    form.clearErrors();
    imagePreview.value = null;
    showModal.value = true;
};

const openEdit = (product) => {
    isEditing.value = true;
    editingProduct.value = product;
    form.category_id = product.category_id;
    form.name = product.name;
    form.barcode = product.barcode || '';
    form.cost_price = product.cost_price;
    form.selling_price = product.selling_price;
    form.stock = product.stock;
    form.image = null;
    form.description = product.description || '';
    form.clearErrors();
    imagePreview.value = product.image ? '/storage/' + product.image : null;
    showModal.value = true;
};

const openDelete = (product) => {
    deletingProduct.value = product;
    showDeleteModal.value = true;
};

const submit = () => {
    if (isEditing.value) {
        form.post(route('products.update', editingProduct.value.id), {
            forceFormData: true,
            preserveScroll: true,
            headers: { 'X-HTTP-Method-Override': 'PUT' },
            onSuccess: () => {
                form.reset();
                imagePreview.value = null;
                showModal.value = false;
            },
        });
    } else {
        form.post(route('products.store'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
                imagePreview.value = null;
                showModal.value = false;
            },
        });
    }
};

const confirmDelete = () => {
    router.delete(route('products.destroy', deletingProduct.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            deletingProduct.value = null;
        },
    });
};

const goToPage = (url) => {
    if (url) router.visit(url, { preserveScroll: true });
};
</script>

<template>
    <Head :title="t('product.title')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                    <Package class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">{{ t('product.title') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('product.subtitle') }}</p>
                </div>
            </div>
        </template>

        <div class="flex flex-col h-full px-[30px] py-4 gap-4 overflow-hidden">
            <!-- Toolbar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <Search class="h-4 w-4 text-gray-400 dark:text-gray-500" />
                        </div>
                        <input type="text" v-model="search" :placeholder="t('product.search_placeholder')"
                            class="block w-[400px] pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm" />
                    </div>
                    <select v-model="category_id"
                        class="block w-48 py-2 px-3 border border-gray-300 dark:border-gray-700 rounded-md text-sm focus:ring-emerald-500 focus:border-emerald-500 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm">
                        <option value="">{{ t('common.all_categories') }}</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>
                
                <div class="flex items-center gap-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ t('common.total') }} <span class="font-semibold text-gray-700 dark:text-gray-300">{{ products.total }}</span>
                    </p>
                    <button @click="openCreate"
                        class="inline-flex items-center gap-2 rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2">
                        <Plus class="w-4 h-4" />
                        {{ t('product.add_product') }}
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="flex flex-col flex-1 rounded-md bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden min-h-[746px]">
                <div class="flex-1 overflow-y-auto scrollbar-hide">
                    <table class="w-full text-sm text-left">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-emerald-700 text-white">
                                <th class="px-6 py-4 font-semibold w-12">#</th>
                                <th class="px-6 py-4 font-semibold w-16">{{ t('product.image') }}</th>
                                <th class="px-6 py-4 font-semibold">{{ t('product.product') }}</th>
                                <th class="px-6 py-4 font-semibold">{{ t('product.category') }}</th>
                                <th class="px-6 py-4 font-semibold">{{ t('product.barcode') }}</th>
                                <th class="px-6 py-4 font-semibold">{{ t('product.cost') }}</th>
                                <th class="px-6 py-4 font-semibold">{{ t('product.price') }}</th>
                                <th class="px-6 py-4 font-semibold">{{ t('product.stock') }}</th>
                                <th class="px-6 py-4 font-semibold text-right">{{ t('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="(product, idx) in products.data" :key="product.id"
                                class="hover:bg-emerald-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 text-gray-400 dark:text-gray-500 font-mono text-xs">
                                    {{ (products.current_page - 1) * products.per_page + idx + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <img v-if="product.image" :src="'/storage/' + product.image" :alt="product.name"
                                        class="w-10 h-10 rounded-md object-cover border border-gray-200 dark:border-gray-700" />
                                    <span v-else class="flex items-center justify-center w-10 h-10 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500">
                                        <ImageOff class="w-4 h-4" />
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ product.name }}</p>
                                    <p v-if="product.description" class="text-xs text-gray-400 dark:text-gray-500 truncate max-w-[160px]">{{ product.description }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-medium border border-emerald-200 dark:border-emerald-800">
                                        <Layers class="w-3 h-3" />
                                        {{ product.category?.name ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ product.barcode || '—' }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">${{ Number(product.cost_price).toFixed(2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-emerald-700 dark:text-emerald-400">${{ Number(product.selling_price).toFixed(2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="['inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold',
                                        product.stock < 10 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300']">
                                        {{ product.stock }} {{ t('common.units') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openEdit(product)" class="p-1.5 rounded-md text-gray-400 dark:text-gray-500 hover:text-emerald-700 dark:hover:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors" :title="t('common.edit')">
                                            <Pencil class="w-4 h-4" />
                                        </button>
                                        <button @click="openDelete(product)" class="p-1.5 rounded-md text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" :title="t('common.delete')">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="products.data.length === 0">
                                <td colspan="9" class="px-6 py-16 text-center text-gray-400 dark:text-gray-500">
                                    <Package class="w-10 h-10 mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                                    <p>{{ t('product.no_products') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination :data="products" />
            </div>
        </div>

        <!-- Create / Edit Product Modal -->
        <Teleport to="body">
            <transition name="modal">
                <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>
                    <div class="relative w-full max-w-2xl rounded-md bg-white dark:bg-gray-800 shadow-2xl max-h-[90vh] overflow-y-auto">
                        <div class="sticky top-0 bg-white dark:bg-gray-800 flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700 z-10">
                            <div class="flex items-center gap-2">
                                <span class="flex items-center justify-center w-8 h-8 rounded-md bg-emerald-100 dark:bg-emerald-900/30">
                                    <Package class="w-4 h-4 text-emerald-700 dark:text-emerald-400" />
                                </span>
                                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">{{ isEditing ? t('product.edit_product') : t('product.add_product') }}</h3>
                            </div>
                            <button @click="showModal = false" class="p-1.5 rounded-md text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <X class="w-4 h-4" />
                            </button>
                        </div>

                        <form @submit.prevent="submit" class="px-6 py-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('product.product_name') }} <span class="text-red-500 dark:text-red-400">*</span></label>
                                    <input type="text" v-model="form.name" required autofocus placeholder="e.g. Coca-Cola 500ml"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm text-sm focus:border-emerald-700 focus:ring-emerald-700" />
                                    <InputError class="mt-1" :message="form.errors.name" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('product.category') }} <span class="text-red-500 dark:text-red-400">*</span></label>
                                    <select v-model="form.category_id" required
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm text-sm focus:border-emerald-700 focus:ring-emerald-700">
                                        <option value="" disabled>{{ t('product.select_category') }}</option>
                                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                    </select>
                                    <InputError class="mt-1" :message="form.errors.category_id" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('product.barcode') }}</label>
                                    <input type="text" :value="form.barcode || t('product.auto_generated')" disabled readonly
                                        class="block w-full rounded-md border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400 font-mono text-sm shadow-sm cursor-not-allowed select-none" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('product.cost_price') }} <span class="text-red-500 dark:text-red-400">*</span></label>
                                    <input type="number" step="0.01" min="0" v-model="form.cost_price" required placeholder="0.00"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm text-sm focus:border-emerald-700 focus:ring-emerald-700" />
                                    <InputError class="mt-1" :message="form.errors.cost_price" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('product.selling_price') }} <span class="text-red-500 dark:text-red-400">*</span></label>
                                    <input type="number" step="0.01" min="0" v-model="form.selling_price" required placeholder="0.00"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm text-sm focus:border-emerald-700 focus:ring-emerald-700" />
                                    <InputError class="mt-1" :message="form.errors.selling_price" />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('product.stock') }} <span class="text-red-500 dark:text-red-400">*</span></label>
                                    <input type="number" min="0" v-model="form.stock" required placeholder="0"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm text-sm focus:border-emerald-700 focus:ring-emerald-700" />
                                    <InputError class="mt-1" :message="form.errors.stock" />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('product.product_image') }}</label>
                                    <div class="mt-1 flex flex-col items-center justify-center rounded-md border-2 border-dashed border-gray-300 dark:border-gray-700 p-5 text-center cursor-pointer hover:border-emerald-500 dark:hover:border-emerald-500 transition-colors"
                                        @click="$refs.imageInput.click()">
                                        <img v-if="imagePreview" :src="imagePreview" class="mb-2 h-24 w-auto rounded-md object-cover shadow" />
                                        <UploadCloud v-else class="w-8 h-8 text-gray-400 dark:text-gray-500 mb-2" />
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ imagePreview ? t('product.click_to_change') : t('product.click_to_upload') }}</p>
                                        <input ref="imageInput" type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="onImageChange" />
                                    </div>
                                    <InputError class="mt-1" :message="form.errors.image" />
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('common.description') }}</label>
                                    <textarea v-model="form.description" rows="2" placeholder="..."
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm text-sm focus:border-emerald-700 focus:ring-emerald-700"></textarea>
                                    <InputError class="mt-1" :message="form.errors.description" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-5 mt-2 border-t border-gray-100 dark:border-gray-700">
                                <button type="button" @click="showModal = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    {{ t('common.cancel') }}
                                </button>
                                <button type="submit" :disabled="form.processing"
                                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-emerald-700 rounded-md hover:bg-emerald-800 transition-colors disabled:opacity-50">
                                    <Plus v-if="!isEditing" class="w-4 h-4" />
                                    <Pencil v-else class="w-4 h-4" />
                                    {{ isEditing ? t('common.update') : t('common.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </transition>
        </Teleport>

        <!-- Delete Confirmation Modal -->
        <Teleport to="body">
            <transition name="modal">
                <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>
                    <div class="relative w-full max-w-md rounded-md bg-white dark:bg-gray-800 shadow-2xl overflow-hidden">
                        <div class="px-6 py-6 text-center">
                            <div class="mx-auto flex items-center justify-center w-14 h-14 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                                <AlertTriangle class="w-7 h-7 text-red-600 dark:text-red-400" />
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">{{ t('common.delete_confirm_title') }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ t('common.delete_confirm_msg') }}
                            </p>
                        </div>
                        <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            <button @click="showDeleteModal = false"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                {{ t('common.cancel') }}
                            </button>
                            <button @click="confirmDelete"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors">
                                <Trash2 class="w-4 h-4" />
                                {{ t('common.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </transition>
        </Teleport>
    </AuthenticatedLayout>
</template>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }

/* Hide scrollbar but keep scroll working */
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.scrollbar-hide::-webkit-scrollbar { display: none; }
</style>
