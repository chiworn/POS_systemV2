<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { ShoppingCart, Plus, Trash2, Eye, ChevronLeft, ChevronRight, Package } from '@lucide/vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    purchases: Object,
});

const { t } = useI18n();

const showDelModal  = ref(false);
const deleteTarget  = ref(null);

const confirmDelete = (p) => { deleteTarget.value = p; showDelModal.value = true; };
const doDelete = () => {
    router.delete(route('purchases.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => { showDelModal.value = false; deleteTarget.value = null; },
    });
};

// goToPage removed as Pagination component handles routing

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-GB') : '—';
const formatCurrency = (n) => '$' + parseFloat(n).toFixed(2);
</script>

<template>
    <Head :title="t('purchase.title')" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                    <ShoppingCart class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">{{ t('purchase.title') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('purchase.subtitle') }}</p>
                </div>
            </div>
        </template>

        <div class="flex flex-col h-full px-[30px] py-5 gap-4 overflow-hidden">

            <!-- Toolbar -->
            <div class="flex items-center justify-between shrink-0">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ t('purchase.total') }} <span class="font-semibold text-gray-700 dark:text-gray-300">{{ purchases.total }}</span> {{ t('purchase.purchases_count') }}
                </p>
                <Link :href="route('purchases.create')"
                    class="inline-flex items-center gap-2 rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800">
                    <Plus class="w-4 h-4" />
                    {{ t('purchase.add_purchase') }}
                </Link>
            </div>

            <!-- Table Card -->
            <div class="flex flex-col flex-1 rounded-md bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden min-h-[740px]">
                <div class="flex-1 overflow-y-auto scrollbar-hide">
                    <table class="w-full text-sm text-left">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-emerald-700 text-white">
                                <th class="px-6 py-4 font-semibold w-12">#</th>
                                <th class="px-6 py-4 font-semibold">{{ t('purchase.supplier') }}</th>
                                <th class="px-6 py-4 font-semibold">{{ t('purchase.purchase_date') }}</th>
                                <th class="px-6 py-4 font-semibold">{{ t('purchase.items') }}</th>
                                <th class="px-6 py-4 font-semibold">{{ t('user.title') }}</th>
                                <th class="px-6 py-4 font-semibold">{{ t('common.total') }}</th>
                                <th class="px-6 py-4 font-semibold text-right">{{ t('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="(purchase, idx) in purchases.data" :key="purchase.id"
                                class="hover:bg-emerald-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 text-gray-400 dark:text-gray-500 font-mono text-xs">
                                    {{ (purchases.current_page - 1) * purchases.per_page + idx + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex items-center justify-center w-8 h-8 rounded-md bg-emerald-100 dark:bg-emerald-900/30 shrink-0">
                                            <ShoppingCart class="w-4 h-4 text-emerald-700 dark:text-emerald-400" />
                                        </span>
                                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ purchase.supplier?.company_name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ formatDate(purchase.purchase_date) }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                        <Package class="w-3 h-3" />
                                        {{ purchase.items_count }} {{ t('purchase.items') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ purchase.user?.name ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-emerald-700 dark:text-emerald-400">{{ formatCurrency(purchase.total) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="route('purchases.show', purchase.id)"
                                            class="p-1.5 rounded-md text-gray-400 dark:text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors" :title="t('dashboard.view_all')">
                                            <Eye class="w-4 h-4" />
                                        </Link>
                                        <button @click="confirmDelete(purchase)"
                                            class="p-1.5 rounded-md text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" :title="t('common.delete')">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="purchases.data.length === 0">
                                <td colspan="7" class="px-6 py-16 text-center text-gray-400 dark:text-gray-500">
                                    <ShoppingCart class="w-10 h-10 mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                                    <p>{{ t('purchase.no_purchases') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination :data="purchases" />
            </div>
        </div>

        <!-- Delete Confirm Modal -->
        <Teleport to="body">
            <transition name="modal">
                <div v-if="showDelModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showDelModal = false"></div>
                    <div class="relative w-full max-w-sm rounded-md bg-white dark:bg-gray-800 shadow-2xl p-6 text-center">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 mx-auto mb-4">
                            <Trash2 class="w-6 h-6 text-red-600 dark:text-red-400" />
                        </div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-1">{{ t('common.delete_confirm_title') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            {{ t('common.delete_confirm_msg') }}
                        </p>
                        <div class="flex gap-3">
                            <button @click="showDelModal = false"
                                class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                                {{ t('common.cancel') }}
                            </button>
                            <button @click="doDelete"
                                class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-md hover:bg-red-700">
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
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.scrollbar-hide::-webkit-scrollbar { display: none; }
</style>
