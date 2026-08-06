<script setup>
import { ref, computed, onMounted } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Sidebar from '@/Components/Sidebar.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Moon, Sun, LogOut, Bell, Globe, User, UserCheck, UserX, Users } from '@lucide/vue';

const { t, locale } = useI18n();

const showingNavigationDropdown = ref(false);
const page = usePage();

const userRole = computed(() => {
    return page.props.auth.user?.role?.name || 'Cashier';
});

const pendingRegistrations = computed(() => page.props.pendingRegistrations || []);

const approvePendingUser = (user) => {
    if (confirm(`Approve cashier account for "${user.name}"?`)) {
        router.patch(route('users.toggle-status', user.id), {}, { preserveScroll: true });
    }
};

const rejectPendingUser = (user) => {
    if (confirm(`Reject and delete account request for "${user.name}"?`)) {
        router.delete(route('users.destroy', user.id), { preserveScroll: true });
    }
};

// Dark mode state
const isDark = ref(false);

const initDarkMode = () => {
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
};

const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('color-theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('color-theme', 'light');
    }
};

initDarkMode();

// Language State
const activeLanguage = ref(locale.value);
const setLanguage = (lang) => {
    activeLanguage.value = lang;
    locale.value = lang;
    localStorage.setItem('app-language', lang);
};
</script>

<template>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900 overflow-hidden font-sans transition-colors duration-200">
        <!-- Sidebar -->
        <Sidebar class="hidden md:flex shrink-0" />

        <!-- Mobile Sidebar Backdrop -->
        <div v-if="showingNavigationDropdown" @click="showingNavigationDropdown = false" class="fixed inset-0 z-20 bg-black/50 md:hidden"></div>

        <!-- Mobile Sidebar -->
        <div :class="[showingNavigationDropdown ? 'translate-x-0' : '-translate-x-full', 'fixed inset-y-0 left-0 z-30 transition duration-300 transform md:hidden']">
            <Sidebar />
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Mobile Header -->
            <header class="flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 md:hidden transition-colors duration-200 relative z-40">
                <div class="flex items-center gap-3">
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="text-gray-500 focus:outline-none">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 6H20M4 12H20M4 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="flex items-center gap-2">
                        <img v-if="$page.props.settings?.store_logo_url" :src="$page.props.settings.store_logo_url" class="w-7 h-7 object-contain rounded" />
                        <span class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $page.props.settings?.store_name || 'POSSys' }}</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    <!-- Language Switcher Dropdown (Mobile) -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-bold text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-xs">
                                <Globe class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                <span>{{ activeLanguage === 'EN' ? '🇬🇧 EN' : '🇰🇭 KH' }}</span>
                            </button>
                        </template>
                        <template #content>
                            <div class="py-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden">
                                <button @click="setLanguage('EN')" class="w-full text-left flex items-center justify-between px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">
                                    <span>🇬🇧 English</span>
                                    <span v-if="activeLanguage === 'EN'" class="text-xs font-bold text-emerald-600 dark:text-emerald-400">✓</span>
                                </button>
                                <button @click="setLanguage('KH')" class="w-full text-left flex items-center justify-between px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">
                                    <span>🇰🇭 ភាសាខ្មែរ (Khmer)</span>
                                    <span v-if="activeLanguage === 'KH'" class="text-xs font-bold text-emerald-600 dark:text-emerald-400">✓</span>
                                </button>
                            </div>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Page Heading (Optional) -->
            <header class="bg-white dark:bg-gray-900 shadow-sm z-40 hidden md:flex items-center justify-between transition-colors duration-200 relative" v-if="$slots.header">
                <div class="px-8 py-6">
                    <slot name="header" />
                </div>
                
                <!-- Language & Notifications on right side of Page Header -->
                <div class="px-8 flex items-center gap-4">
                    <!-- Language Switcher Dropdown -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white transition-all bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-emerald-500 shadow-xs">
                                <Globe class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                <span>{{ activeLanguage === 'EN' ? 'English' : 'ភាសាខ្មែរ' }}</span>
                                <svg class="w-3.5 h-3.5 opacity-60 ml-0.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </template>
                        <template #content>
                            <div class="py-1 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden">
                                <button @click="setLanguage('EN')" class="w-full text-left flex items-center justify-between px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">
                                    <span class="flex items-center gap-2">English</span>
                                    <span v-if="activeLanguage === 'EN'" class="text-xs font-bold text-emerald-600 dark:text-emerald-400">✓</span>
                                </button>
                                <button @click="setLanguage('KH')" class="w-full text-left flex items-center justify-between px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-700 dark:hover:text-emerald-400 transition-colors">
                                    <span class="flex items-center gap-2">ភាសាខ្មែរ (Khmer)</span>
                                    <span v-if="activeLanguage === 'KH'" class="text-xs font-bold text-emerald-600 dark:text-emerald-400">✓</span>
                                </button>
                            </div>
                        </template>
                    </Dropdown>

                    <!-- Notifications Dropdown -->
                    <Dropdown align="right" width="80">
                        <template #trigger>
                            <button class="relative p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-full transition-colors focus:outline-none" title="Notifications">
                                <Bell class="w-5 h-5" />
                                <!-- Dynamic Badge -->
                                <span v-if="pendingRegistrations.length > 0" class="absolute -top-0.5 -right-0.5 flex h-4 min-w-[16px] px-1 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-gray-900 animate-pulse">
                                    {{ pendingRegistrations.length }}
                                </span>
                            </button>
                        </template>
                        <template #content="{ close: dropdownClose }">
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-t-md flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ t('header.notifications') }}</h3>
                                <span v-if="pendingRegistrations.length > 0" class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300">
                                    {{ pendingRegistrations.length }} Pending
                                </span>
                            </div>
                            
                            <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                <div v-for="user in pendingRegistrations" :key="user.id" class="p-3 hover:bg-amber-50/50 dark:hover:bg-amber-900/10 transition-colors">
                                    <div class="flex items-start gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                            <Users class="w-4 h-4" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{{ user.name }}</p>
                                            <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ user.email }}</p>
                                            <p class="text-[10px] text-amber-600 dark:text-amber-400 font-medium mt-0.5">New cashier registration pending approval</p>
                                            
                                            <!-- Quick Action Buttons -->
                                            <div class="flex items-center gap-1.5 mt-2">
                                                <button @click="approvePendingUser(user)" class="px-2 py-1 text-[10px] font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded transition-colors flex items-center gap-1">
                                                    <UserCheck class="w-3 h-3" /> Approve
                                                </button>
                                                <button @click="rejectPendingUser(user)" class="px-2 py-1 text-[10px] font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 hover:bg-red-100 rounded transition-colors flex items-center gap-1">
                                                    <UserX class="w-3 h-3" /> Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="pendingRegistrations.length === 0" class="p-6 text-center text-gray-400">
                                    <Bell class="w-6 h-6 mx-auto mb-1.5 text-gray-300 dark:text-gray-600" />
                                    <p class="text-xs">No pending account notifications.</p>
                                </div>
                            </div>

                            <div v-if="userRole === 'Admin'" class="p-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 rounded-b-md text-center">
                                <Link :href="route('users.index')" @click="dropdownClose" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                                    Manage User Accounts &rarr;
                                </Link>
                            </div>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-950 p-6 md:p-8 transition-colors duration-200">
                <slot />
            </main>
        </div>
    </div>
</template>
