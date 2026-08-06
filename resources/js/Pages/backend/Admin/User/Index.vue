<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { 
    Users, 
    Plus, 
    Edit, 
    Key,
    UserCheck,
    UserX,
    ShieldCheck,
    User,
    Search,
    Trash2,
    Clock,
    CheckCircle2,
    AlertCircle,
    X
} from '@lucide/vue';

const { t } = useI18n();

const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
    roles: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const currentUser = computed(() => page.props.auth.user);

// Search & Filter State
const searchQuery = ref('');
const activeTab = ref('all'); // 'all', 'pending', 'active', 'inactive'

// KPI Metrics
const totalCount = computed(() => props.users.length);
const activeCount = computed(() => props.users.filter(u => u.status).length);
const pendingCount = computed(() => props.users.filter(u => !u.status).length);
const cashierCount = computed(() => props.users.filter(u => u.role?.name === 'Cashier').length);

const filteredUsers = computed(() => {
    return props.users.filter(u => {
        const matchesSearch = u.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                              u.email.toLowerCase().includes(searchQuery.value.toLowerCase());
        if (!matchesSearch) return false;

        if (activeTab.value === 'pending') return !u.status;
        if (activeTab.value === 'active') return u.status;
        if (activeTab.value === 'inactive') return !u.status;
        return true;
    });
});

// Modal states
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);
const showResetModal = ref(false);
const resetTarget = ref(null);

const form = useForm({
    name: '',
    email: '',
    role_id: '',
    password: '',
    password_confirmation: '',
});

const resetForm = useForm({
    password: '',
    password_confirmation: '',
});

const openCreate = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

const openEdit = (user) => {
    isEditing.value = true;
    editingId.value = user.id;
    form.name = user.name;
    form.email = user.email;
    form.role_id = user.role_id;
    form.password = '';
    form.password_confirmation = '';
    form.clearErrors();
    showModal.value = true;
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('users.update', editingId.value), {
            onSuccess: () => {
                showModal.value = false;
            }
        });
    } else {
        form.post(route('users.store'), {
            onSuccess: () => {
                showModal.value = false;
            }
        });
    }
};

const openResetPassword = (user) => {
    resetTarget.value = user;
    resetForm.reset();
    resetForm.clearErrors();
    showResetModal.value = true;
};

const submitResetPassword = () => {
    resetForm.patch(route('users.reset-password', resetTarget.value.id), {
        onSuccess: () => {
            showResetModal.value = false;
        }
    });
};

const approveUser = (user) => {
    router.patch(route('users.toggle-status', user.id), {}, {
        preserveScroll: true,
    });
};

const toggleStatus = (user) => {
    if (confirm(`Are you sure you want to ${user.status ? 'deactivate' : 'activate'} "${user.name}"?`)) {
        router.patch(route('users.toggle-status', user.id), {}, {
            preserveScroll: true,
        });
    }
};

const rejectUser = (user) => {
    if (confirm(`Are you sure you want to reject and delete the user account for "${user.name}"?`)) {
        router.delete(route('users.destroy', user.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head :title="t('user.title')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 shrink-0">
                        <Users class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">{{ t('user.title') }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('user.subtitle') }}</p>
                    </div>
                </div>

            </div>
        </template>

        <div class="space-y-6">
            <!-- Summary Stats KPI Header -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ t('user.title') }}</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ totalCount }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <Users class="w-5 h-5" />
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ t('common.active') }}</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ activeCount }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <CheckCircle2 class="w-5 h-5" />
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between relative overflow-hidden" :class="{'ring-2 ring-amber-400/50': pendingCount > 0}">
                    <div>
                        <p class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">{{ t('dashboard.pending') }}</p>
                        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ pendingCount }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center" :class="{'animate-bounce': pendingCount > 0}">
                        <AlertCircle class="w-5 h-5" />
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-100 dark:border-gray-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ t('history.cashier') }}</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ cashierCount }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <ShieldCheck class="w-5 h-5" />
                    </div>
                </div>
            </div>

            <!-- Controls: Search & Tabs & Add Button -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                <!-- Tabs -->
                <div class="flex items-center gap-1.5 p-1 bg-gray-100 dark:bg-gray-900 rounded-lg w-full md:w-auto overflow-x-auto">
                    <button @click="activeTab = 'all'" :class="['px-3.5 py-1.5 text-xs font-semibold rounded-md transition-all whitespace-nowrap', activeTab === 'all' ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700']">
                        {{ t('dashboard.view_all') }} ({{ totalCount }})
                    </button>
                    <button @click="activeTab = 'pending'" :class="['px-3.5 py-1.5 text-xs font-semibold rounded-md transition-all flex items-center gap-1.5 whitespace-nowrap', activeTab === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20']">
                        {{ t('dashboard.pending') }} ({{ pendingCount }})
                        <span v-if="pendingCount > 0" class="w-2 h-2 rounded-full bg-amber-300 animate-ping"></span>
                    </button>
                    <button @click="activeTab = 'active'" :class="['px-3.5 py-1.5 text-xs font-semibold rounded-md transition-all whitespace-nowrap', activeTab === 'active' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700']">
                        {{ t('common.active') }} ({{ activeCount }})
                    </button>
                </div>

                <!-- Right Actions: Search Input & Add Button -->
                <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                    <div class="relative w-full md:w-64">
                        <Search class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" />
                        <input v-model="searchQuery" type="text" :placeholder="t('user.search_placeholder')" class="w-full pl-9 pr-4 py-2 text-xs bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 text-gray-800 dark:text-gray-200 transition-colors">
                    </div>

                    <button @click="openCreate" class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white transition-all bg-emerald-600 rounded-lg hover:bg-emerald-700 shadow-sm active:scale-95 shrink-0">
                        <Plus class="w-4 h-4" />
                        <span>{{ t('user.add_user') }}</span>
                    </button>
                </div>
            </div>

            <!-- Detailed User Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <div v-for="user in filteredUsers" :key="user.id" 
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 border shadow-sm hover:shadow-md transition-all relative group flex flex-col justify-between"
                    :class="!user.status ? 'border-amber-200 dark:border-amber-900/60 bg-amber-50/20 dark:bg-amber-950/10' : 'border-gray-100 dark:border-gray-700'">
                    
                    <div>
                        <!-- Header / User Info -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="relative shrink-0 flex items-center justify-center w-12 h-12 rounded-full font-bold text-lg"
                                    :class="!user.status ? 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300' : 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'">
                                    <User class="w-6 h-6" />
                                    <span :class="['absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full ring-2 ring-white dark:ring-gray-800', user.status ? 'bg-emerald-500' : 'bg-amber-500 animate-pulse']"></span>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 truncate leading-tight">{{ user.name }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ user.email }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Badges & Role -->
                        <div class="space-y-2.5 mb-5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ t('user.role') }}:</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    <ShieldCheck class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" />
                                    {{ user.role?.name || 'Cashier' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ t('common.status') }}:</span>
                                <span v-if="user.status" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                    <UserCheck class="w-3.5 h-3.5" /> {{ t('common.active') }}
                                </span>
                                <span v-else class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 animate-pulse">
                                    <Clock class="w-3.5 h-3.5" /> {{ t('dashboard.pending') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer & Quick Actions -->
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between gap-2">
                        <!-- Pending User Quick Approval Buttons -->
                        <template v-if="!user.status">
                            <button type="button" @click="approveUser(user)" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors shadow-sm cursor-pointer">
                                <UserCheck class="w-3.5 h-3.5" /> {{ t('dashboard.approve') }}
                            </button>
                            <button type="button" @click="rejectUser(user)" class="inline-flex items-center justify-center gap-1 px-3 py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 rounded-lg transition-colors border border-red-200 dark:border-red-800/50 cursor-pointer">
                                <UserX class="w-3.5 h-3.5" /> {{ t('common.delete') }}
                            </button>
                        </template>

                        <!-- Active User Control Buttons -->
                        <template v-else>
                            <button @click="openResetPassword(user)" class="text-xs font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 flex items-center gap-1 px-2 py-1 rounded-md hover:bg-yellow-50 dark:hover:bg-yellow-900/30 transition-colors">
                                <Key class="w-3.5 h-3.5" /> Reset Pass
                            </button>
                            
                            <div class="flex items-center gap-1">
                                <button @click="openEdit(user)" class="p-1.5 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors" :title="t('common.edit')">
                                    <Edit class="w-4 h-4" />
                                </button>
                                <button v-if="currentUser.id !== user.id" @click="toggleStatus(user)" class="p-1.5 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-lg transition-colors" title="Deactivate">
                                    <UserX class="w-4 h-4" />
                                </button>
                                <button v-if="currentUser.id !== user.id" @click="rejectUser(user)" class="p-1.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" :title="t('common.delete')">
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div v-if="filteredUsers.length === 0" class="col-span-full py-16 text-center text-gray-400 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm">
                    <Users class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600" />
                    <p class="text-sm font-medium">{{ t('user.no_users') }}</p>
                </div>
            </div>
        </div>

        <!-- Form Modal (Create/Edit) -->
        <Teleport to="body">
            <transition name="modal">
                <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showModal = false"></div>
                    <div class="relative w-full max-w-lg rounded-xl bg-white dark:bg-gray-800 shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                                {{ isEditing ? t('user.edit_user') : t('user.add_user') }}
                            </h3>
                            <button @click="showModal = false" class="p-1 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 rounded-lg transition-colors">
                                <X class="w-5 h-5" />
                            </button>
                        </div>

                        <form @submit.prevent="submitForm" class="p-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('user.name') }}</label>
                                    <input v-model="form.name" type="text" 
                                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors"
                                        required>
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('user.email') }}</label>
                                    <input v-model="form.email" type="email" 
                                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors"
                                        required>
                                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ t('user.role') }}</label>
                                    <select v-model="form.role_id" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors">
                                        <option value="" disabled>Select a role...</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                                    </select>
                                    <p v-if="form.errors.role_id" class="mt-1 text-sm text-red-500">{{ form.errors.role_id }}</p>
                                </div>
                                
                                <template v-if="!isEditing">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                                        <input v-model="form.password" type="password" 
                                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors"
                                            required>
                                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                                        <input v-model="form.password_confirmation" type="password" 
                                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors"
                                            required>
                                    </div>
                                </template>
                            </div>
                            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <button type="button" @click="showModal = false"
                                    class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    {{ t('common.cancel') }}
                                </button>
                                <button type="submit" :disabled="form.processing"
                                    class="inline-flex justify-center px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-lg shadow-sm hover:bg-emerald-700 focus:outline-none disabled:opacity-50 transition-colors">
                                    {{ form.processing ? t('common.loading') : (isEditing ? t('common.update') : t('common.save')) }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </transition>
        </Teleport>

        <!-- Reset Password Modal -->
        <Teleport to="body">
            <transition name="modal">
                <div v-if="showResetModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showResetModal = false"></div>
                    <div class="relative w-full max-w-md rounded-xl bg-white dark:bg-gray-800 shadow-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">
                                Reset Password: <span class="text-emerald-600 dark:text-emerald-400">{{ resetTarget?.name }}</span>
                            </h3>
                            <button @click="showResetModal = false" class="p-1 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 rounded-lg transition-colors">
                                <X class="w-5 h-5" />
                            </button>
                        </div>

                        <form @submit.prevent="submitResetPassword" class="p-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                                    <input v-model="resetForm.password" type="password" 
                                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors"
                                        required>
                                    <p v-if="resetForm.errors.password" class="mt-1 text-sm text-red-500">{{ resetForm.errors.password }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                                    <input v-model="resetForm.password_confirmation" type="password" 
                                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors"
                                        required>
                                </div>
                            </div>
                            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <button type="button" @click="showResetModal = false"
                                    class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" :disabled="resetForm.processing"
                                    class="inline-flex justify-center px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-lg shadow-sm hover:bg-emerald-700 focus:outline-none disabled:opacity-50 transition-colors">
                                    {{ resetForm.processing ? 'Resetting...' : 'Reset Password' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </transition>
        </Teleport>
    </AuthenticatedLayout>
</template>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
