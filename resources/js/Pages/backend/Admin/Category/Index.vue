<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Tags, Plus, Pencil, Trash2, ChevronLeft, ChevronRight, X } from '@lucide/vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    categories: Object,
});

const { t } = useI18n();

// ── Modal state ──────────────────────────────────────────────
const showModal    = ref(false);
const isEditing    = ref(false);
const editingId    = ref(null);
const showDelModal = ref(false);
const deleteTarget = ref(null);

// ── Form ──────────────────────────────────────────────────────
const form = useForm({
    name: '',
    description: '',
});

const openCreate = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEdit = (cat) => {
    isEditing.value  = true;
    editingId.value  = cat.id;
    form.name        = cat.name;
    form.description = cat.description ?? '';
    form.clearErrors();
    showModal.value  = true;
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('categories.update', editingId.value), {
            preserveScroll: true,
            onSuccess: () => { form.reset(); showModal.value = false; },
        });
    } else {
        form.post(route('categories.store'), {
            preserveScroll: true,
            onSuccess: () => { form.reset(); showModal.value = false; },
        });
    }
};

// ── Delete ────────────────────────────────────────────────────
const confirmDelete = (cat) => { deleteTarget.value = cat; showDelModal.value = true; };
const doDelete = () => {
    router.delete(route('categories.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onSuccess: () => { showDelModal.value = false; deleteTarget.value = null; },
    });
};

// ── Pagination ────────────────────────────────────────────────
// Handled by Pagination component
</script>

<template>
    <Head :title="t('category.title')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                    <Tags class="w-6 h-6" />
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">{{ t('category.title') }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('category.subtitle') }}</p>
                </div>
            </div>
        </template>

        <!-- Full-height flex column, no outer scroll -->
        <div class="flex flex-col h-full px-[30px] py-5 gap-4 overflow-hidden">

            <!-- Toolbar -->
            <div class="flex items-center justify-between shrink-0">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ t('category.total') }} <span class="font-semibold text-gray-700 dark:text-gray-300">{{ categories.total }}</span> {{ t('category.categories_count') }}
                </p>
                <button
                    @click="openCreate"
                    class="inline-flex items-center gap-2 rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2"
                >
                    <Plus class="w-4 h-4" />
                    {{ t('category.add_category') }}
                </button>
            </div>

            <!-- Table card: flex column, fills remaining space -->
            <div class="flex flex-col flex-1 rounded-md bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden min-h-[730px]">
                <!-- Scrollable table area -->
                <div class="flex-1 overflow-y-auto scrollbar-hide ">
                    <table class="w-full text-sm text-left">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-emerald-700 text-white">
                                <th class="px-6 py-4 font-semibold w-12">#</th>
                                <th class="px-6 py-4 font-semibold">{{ t('category.category_name') }}</th>
                                <th class="px-6 py-4 font-semibold">{{ t('common.description') }}</th>
                                <th class="px-6 py-4 font-semibold text-right">{{ t('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="(category, idx) in categories.data" :key="category.id"
                                class="hover:bg-emerald-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 text-gray-400 dark:text-gray-500 font-mono text-xs">
                                    {{ (categories.current_page - 1) * categories.per_page + idx + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex items-center justify-center w-8 h-8 rounded-md bg-emerald-100 dark:bg-emerald-900/30 shrink-0">
                                            <Tags class="w-4 h-4 text-emerald-700 dark:text-emerald-400" />
                                        </span>
                                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ category.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                    {{ category.description || '—' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openEdit(category)"
                                            class="p-1.5 rounded-md text-gray-400 dark:text-gray-500 hover:text-emerald-700 dark:hover:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors" :title="t('common.edit')">
                                            <Pencil class="w-4 h-4" />
                                        </button>
                                        <button @click="confirmDelete(category)"
                                            class="p-1.5 rounded-md text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors" :title="t('common.delete')">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="categories.data.length === 0">
                                <td colspan="4" class="px-6 py-16 text-center text-gray-400 dark:text-gray-500">
                                    <Tags class="w-10 h-10 mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                                    <p>{{ t('category.no_categories') }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination :data="categories" />
            </div>
        </div>

        <!-- ── Add / Edit Category Modal ── -->
        <Teleport to="body">
            <transition name="modal">
                <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>
                    <div class="relative w-full max-w-md rounded-md bg-white dark:bg-gray-800 shadow-2xl">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-2">
                                <span class="flex items-center justify-center w-8 h-8 rounded-md bg-emerald-100 dark:bg-emerald-900/30">
                                    <Tags class="w-4 h-4 text-emerald-700 dark:text-emerald-400" />
                                </span>
                                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                                    {{ isEditing ? t('category.edit_category') : t('category.add_category') }}
                                </h3>
                            </div>
                            <button @click="showModal = false" class="p-1.5 rounded-md text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <X class="w-4 h-4" />
                            </button>
                        </div>

                        <form @submit.prevent="submit" class="px-6 py-5 space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ t('category.category_name') }} <span class="text-red-500 dark:text-red-400">*</span>
                                </label>
                                <input
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    placeholder="e.g. Beverages"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm text-sm focus:border-emerald-700 focus:ring-emerald-700"
                                />
                                <InputError class="mt-1" :message="form.errors.name" />
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('common.description') }}</label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    placeholder="..."
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-100 shadow-sm text-sm focus:border-emerald-700 focus:ring-emerald-700"
                                ></textarea>
                                <InputError class="mt-1" :message="form.errors.description" />
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-2">
                                <button
                                    type="button"
                                    @click="showModal = false"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                >
                                    {{ t('common.cancel') }}
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-emerald-700 rounded-md hover:bg-emerald-800 transition-colors disabled:opacity-50"
                                >
                                    <Plus class="w-4 h-4" />
                                    {{ isEditing ? t('common.update') : t('common.save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </transition>
        </Teleport>

        <!-- ── Delete Confirmation Modal ── -->
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

/* Hide scrollbar but keep scroll working */
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.scrollbar-hide::-webkit-scrollbar { display: none; }
</style>
