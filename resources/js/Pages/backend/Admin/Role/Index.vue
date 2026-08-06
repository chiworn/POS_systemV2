<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { 
    Shield, 
    Plus, 
    Edit, 
    Trash2, 
    AlertTriangle,
    ShieldCheck
} from '@lucide/vue';

const { t } = useI18n();

const props = defineProps({
    roles: Array,
});

// Modal state
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const showDelModal = ref(false);
const deleteTarget = ref(null);

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

const openEdit = (role) => {
    isEditing.value = true;
    editingId.value = role.id;
    form.name = role.name;
    form.description = role.description ?? '';
    form.clearErrors();
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('roles.update', editingId.value), {
            onSuccess: () => {
                showModal.value = false;
            }
        });
    } else {
        form.post(route('roles.store'), {
            onSuccess: () => {
                showModal.value = false;
            }
        });
    }
};

const confirmDelete = (role) => {
    deleteTarget.value = role;
    showDelModal.value = true;
};

const deleteRole = () => {
    if (deleteTarget.value) {
        router.delete(route('roles.destroy', deleteTarget.value.id), {
            onSuccess: () => {
                showDelModal.value = false;
                deleteTarget.value = null;
            }
        });
    }
};
</script>

<template>
    <Head :title="t('role.title')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                        <Shield class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">{{ t('role.title') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('role.subtitle') }}</p>
                    </div>
                </div>
            </div>
        </template>

        <div class="px-[30px] py-6">
            <div class="flex justify-end mb-6">
                <button @click="openCreate" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition-colors bg-emerald-700 rounded-md hover:bg-emerald-800 shadow-sm">
                    <Plus class="w-4 h-4" />
                    {{ t('role.add_role') }}
                </button>
            </div>
            <!-- Card View -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="role in roles" :key="role.id" class="bg-white dark:bg-gray-800 rounded-md p-6 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow relative group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center justify-center w-12 h-12 rounded-md bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                            <ShieldCheck class="w-6 h-6" />
                        </div>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="openEdit(role)" class="p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-md transition-colors" :title="t('common.edit')">
                                <Edit class="w-4 h-4" />
                            </button>
                            <button @click="confirmDelete(role)" class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-md transition-colors" :title="t('common.delete')">
                                <Trash2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">{{ role.name }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm line-clamp-2 min-h-[40px]">
                        {{ role.description || '—' }}
                    </p>
                    <div class="mt-4 pt-4 border-t border-gray-50 dark:border-gray-700 flex items-center justify-between">
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ t('role.title') }}</span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            {{ role.users_count || 0 }} {{ t('user.title') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Modal (Create/Edit) -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-sm" @click="showModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-md shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100" id="modal-title">
                            {{ isEditing ? t('role.edit_role') : t('role.add_role') }}
                        </h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('role.role_name') }}</label>
                                <input v-model="form.name" type="text" 
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors"
                                    placeholder="e.g. Admin" required>
                                <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('common.description') }}</label>
                                <textarea v-model="form.description" rows="3"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-md focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors resize-none"
                                    placeholder="..."></textarea>
                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-500">{{ form.errors.description }}</p>
                            </div>
                        </div>
                        <div class="mt-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" :disabled="form.processing"
                                class="inline-flex justify-center w-full px-4 py-2.5 text-sm font-medium text-white bg-emerald-700 border border-transparent rounded-md shadow-sm hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto disabled:opacity-50">
                                {{ form.processing ? t('common.loading') : t('common.save') }}
                            </button>
                            <button type="button" @click="showModal = false"
                                class="inline-flex justify-center w-full px-4 py-2.5 mt-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:ml-3 sm:w-auto transition-colors">
                                {{ t('common.cancel') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDelModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-sm" @click="showDelModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-md shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <AlertTriangle class="w-6 h-6 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100" id="modal-title">{{ t('common.delete_confirm_title') }}</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ t('common.delete_confirm_msg') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="deleteRole"
                            class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto transition-colors">
                            {{ t('common.delete') }}
                        </button>
                        <button type="button" @click="showDelModal = false"
                            class="inline-flex justify-center w-full px-4 py-2 mt-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:w-auto transition-colors">
                            {{ t('common.cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
