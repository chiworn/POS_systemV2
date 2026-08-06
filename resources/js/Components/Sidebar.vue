<script setup>
import { computed, ref, watchEffect } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { 
    LayoutDashboard, 
    MonitorSmartphone, 
    Package, 
    Tags, 
    Users, 
    Settings, 
    ShoppingCart, 
    ClipboardList,
    Truck,
    Shield,
    LogOut,
    Moon,
    Sun,
    ChevronDown,
    ChevronRight,
    Store,
    Globe,
    Receipt,
    Calculator,
    Bell,
    Lock,
    Database,
    Info,
    Sliders
} from '@lucide/vue';

const page = usePage();
const { t } = useI18n();

const userRole = computed(() => {
    return page.props.auth.user?.role?.name || 'Cashier'; // Fallback to Cashier
});

const menuItems = computed(() => {
    const role = userRole.value;
    let items = [
        { name: t('sidebar.dashboard'), icon: LayoutDashboard, href: route('dashboard'), active: route().current('dashboard') },
    ];

    if (role === 'Admin') {
        items.push(
            { name: t('sidebar.pos_screen'), icon: MonitorSmartphone, href: route('pos.index'), active: route().current('pos.index') },
            { name: t('sidebar.product'), icon: Package, href: route('products.index'), active: route().current('products.*') },
            { name: t('sidebar.category'), icon: Tags, href: route('categories.index'), active: route().current('categories.*') },
            { name: t('sidebar.supplier'), icon: Truck, href: route('suppliers.index'), active: route().current('suppliers.*') },
            { name: t('sidebar.purchase'), icon: ShoppingCart, href: route('purchases.index'), active: route().current('purchases.*') },
            { name: t('sidebar.sales_history'), icon: ClipboardList, href: route('pos.history'), active: route().current('pos.history') },
            { name: t('sidebar.reports'), icon: ClipboardList, href: route('reports.index'), active: route().current('reports.*') },
            { 
                name: t('sidebar.user_management'), 
                icon: Users, 
                href: '#', 
                active: route().current('roles.*') || route().current('users.*'),
                subItems: [
                    { name: t('sidebar.manage_users'), icon: Users, href: route('users.index'), active: route().current('users.*') },
                    { name: t('sidebar.manage_roles'), icon: Shield, href: route('roles.index'), active: route().current('roles.*') }
                ]
            },
            { 
                name: t('sidebar.settings'), 
                icon: Settings, 
                href: '#', 
                active: route().current('settings.*'),
                subItems: [
                    { name: t('sidebar.general_settings'), icon: Sliders, href: route('settings.general'), active: route().current('settings.general') },
                    { name: t('sidebar.tax_settings'), icon: Calculator, href: route('settings.tax'), active: route().current('settings.tax') },
                    { name: t('sidebar.backup_restore'), icon: Database, href: '#', active: false },
                    { name: t('sidebar.about_system'), icon: Info, href: route('settings.about'), active: route().current('settings.about') }
                ]
            }
        );
    } else if (role === 'Manager') {
        items.push(
            { name: t('sidebar.pos_screen'), icon: MonitorSmartphone, href: route('pos.index'), active: route().current('pos.index') },
            { name: t('sidebar.product'), icon: Package, href: route('products.index'), active: route().current('products.*') },
            { name: t('sidebar.category'), icon: Tags, href: route('categories.index'), active: route().current('categories.*') },
            { name: t('sidebar.supplier'), icon: Truck, href: route('suppliers.index'), active: route().current('suppliers.*') },
            { name: t('sidebar.purchase'), icon: ShoppingCart, href: route('purchases.index'), active: route().current('purchases.*') },
            { name: t('sidebar.sales_history'), icon: ClipboardList, href: route('pos.history'), active: route().current('pos.history') },
            { name: t('sidebar.reports'), icon: ClipboardList, href: route('reports.index'), active: route().current('reports.*') }
        );
    } else if (role === 'Cashier') {
        items.push(
            { name: t('sidebar.pos_screen'), icon: MonitorSmartphone, href: route('pos.index'), active: route().current('pos.index') },
            { name: t('sidebar.sales_history'), icon: ClipboardList, href: route('pos.history'), active: route().current('pos.history') }
        );
    }

    return items;
});

// Dark mode state
const isDark = ref(false);

const openMenus = ref({});

// Automatically open menus that contain the active item
watchEffect(() => {
    menuItems.value.forEach(item => {
        if (item.subItems && item.active && openMenus.value[item.name] === undefined) {
            openMenus.value[item.name] = true;
        }
    });
});

const toggleSubMenu = (menuName) => {
    openMenus.value[menuName] = !openMenus.value[menuName];
};

// Initialize dark mode from localStorage or system preference
const initDarkMode = () => {
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
};

// Toggle dark mode
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

// Call init on component setup
initDarkMode();

</script>

<template>
    <aside class="flex flex-col w-64 h-full px-3 py-6 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-colors duration-200">
        <!-- Logo & Branding Header -->
        <div class="flex flex-col items-center px-2 mb-3">
            <div v-if="$page.props.settings?.store_logo_url" class="w-32 h-20 mb-1.5 rounded-xl border border-gray-200 dark:border-gray-700/80 bg-white dark:bg-white p-1.5 flex items-center justify-center shadow-xs overflow-hidden transition-transform hover:scale-105">
                <img :src="$page.props.settings.store_logo_url" :alt="$page.props.settings.store_name || 'Store Logo'" class="w-full h-full object-contain rounded-lg" />
            </div>
            <div v-else class="w-32 h-20 mb-1.5 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-extrabold text-2xl shadow-xs">
                <Store class="w-10 h-10" />
            </div>
            <h2 class="text-2xl font-black text-center text-emerald-800 dark:text-emerald-400 tracking-tight leading-tight">
               {{ $page.props.settings?.store_name || t('sidebar.system_pos') }}
            </h2>
            <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5 text-center font-bold leading-tight">{{ t('sidebar.management_products') }}</p>
            <p class="text-[11px] text-gray-500 dark:text-gray-400 text-center mt-0.5 font-semibold">{{ t('sidebar.smart_business') }}</p>
        </div>

        <!-- Nav -->
        <nav class="flex-1 overflow-y-auto space-y-1.5 px-2 mt-4 scrollbar-hide">
            <template v-for="item in menuItems" :key="item.name">
                <div v-if="item.subItems">
                    <!-- Parent Menu Item -->
                    <button
                        @click="toggleSubMenu(item.name)"
                        :class="[
                            'w-full group flex items-center justify-between px-3 py-2 rounded-md text-md transition-all',
                            item.active || openMenus[item.name]
                                ? 'text-emerald-800 dark:text-emerald-400 bg-gray-200 dark:bg-gray-800 font-medium hover:bg-gray-100 dark:hover:bg-gray-700'
                                : 'text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-gray-200'
                        ]"
                    >
                        <div class="flex items-center gap-3">
                            <span
                                :class="[
                                    'flex items-center justify-center w-8 h-8 rounded-md transition-colors',
                                    (item.active || openMenus[item.name]) ? 'bg-white/20 dark:bg-white/10' : ''
                                ]"
                            >
                                <component
                                    :is="item.icon"
                                    class="w-4 h-4 transition-colors"
                                    :class="(item.active || openMenus[item.name]) ? 'text-emerald-800 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200'"
                                />
                            </span>
                            <span>{{ item.name }}</span>
                        </div>
                        <ChevronDown v-if="openMenus[item.name]" class="w-4 h-4 transition-colors" :class="(item.active || openMenus[item.name]) ? 'text-emerald-800 dark:text-emerald-400' : 'text-gray-500'" />
                        <ChevronRight v-else class="w-4 h-4 transition-colors" :class="item.active ? 'text-emerald-800 dark:text-emerald-400' : 'text-gray-500 group-hover:text-gray-800 dark:group-hover:text-gray-200'" />
                    </button>
                    <!-- Sub Items -->
                    <div v-show="openMenus[item.name]" class="mt-1.5  ml-2 space-y-1.5">
                        <Link
                            v-for="subItem in item.subItems"
                            :key="subItem.name"
                            :href="subItem.href"
                            :class="[
                                'group flex items-center gap-3 pl-3 pr-4 py-2 text-[14.5px] transition-colors rounded-md',
                                subItem.active
                                    ? 'text-emerald-800 dark:text-emerald-400 bg-gray-200 dark:bg-gray-800 font-medium hover:bg-gray-100 dark:hover:bg-gray-700'
                                    : 'text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-gray-200'
                            ]"
                        >
                            <span
                                :class="[
                                    'flex items-center justify-center w-8 h-8 rounded-md transition-colors',
                                    subItem.active ? 'bg-gray-100 dark:bg-white/10' : ''
                                ]"
                            >
                                <component
                                    :is="subItem.icon"
                                    class="w-4 h-4 transition-colors bg-gray-200  dark:bg-gray-700"
                                :class="subItem.active ? 'text-gray-800  dark:text-gray-200' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-800 dark:group-hover:text-gray-200'"
                                />
                            </span>
                            <span>{{ subItem.name }}</span>
                        </Link>
                    </div>
                </div>
                <!-- Normal Menu Item -->
                <Link
                    v-else
                    :href="item.href"
                    :class="[
                        'group flex items-center gap-3 px-3 py-2 rounded-md text-md transition-all',
                        item.active
                            ? 'text-emerald-800 dark:text-emerald-400 bg-gray-200 dark:bg-gray-800 font-medium hover:bg-gray-100 dark:hover:bg-gray-700'
                            : 'text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-800 dark:hover:text-gray-200'
                    ]"
                >
                    <span
                        :class="[
                            'flex items-center justify-center w-8 h-8 rounded-md transition-colors',
                            item.active ? 'bg-white/20 dark:bg-white/10' : ''
                        ]"
                    >
                        <component
                            :is="item.icon"
                            class="w-4 h-4 transition-colors"
                            :class="item.active ? 'text-emerald-800 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-800 dark:group-hover:text-gray-200'"
                        />
                    </span>
                    <span>{{ item.name}}</span>
                </Link>
            </template>
        </nav>

        <!-- Receipt-tear divider -->
        <div class="relative h-4 my-2 select-none" aria-hidden="true">
            <svg viewBox="0 0 256 16" preserveAspectRatio="none" class="w-full h-full text-gray-300 dark:text-gray-700">
                <path
                    d="M0,8 
                       Q 4,0 8,8 T 16,8 T 24,8 T 32,8 T 40,8 T 48,8 T 56,8 T 64,8 
                       T 72,8 T 80,8 T 88,8 T 96,8 T 104,8 T 112,8 T 120,8 T 128,8
                       T 136,8 T 144,8 T 152,8 T 160,8 T 168,8 T 176,8 T 184,8 T 192,8
                       T 200,8 T 208,8 T 216,8 T 224,8 T 232,8 T 240,8 T 248,8 T 256,8"
                    fill="none" stroke="currentColor" stroke-width="1.5"
                />
            </svg>
        </div>
        

        <!-- Account -->
        <div class="px-1">
            <!-- Dark Mode Toggle -->
            <button
                @click="toggleDarkMode"
                class="w-full mb-3 text-md font-medium flex items-center justify-between px-3 py-2.5 rounded-md bg-gray-100 dark:bg-gray-800 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors"
            >
                <div class="flex items-center gap-2">
                    <Moon v-if="!isDark" class="w-4 h-4 ml-2" />
                    <Sun v-else class="w-4 h-4 ml-2" />
                    <span>{{ isDark ? t('sidebar.light_mode') : t('sidebar.dark_mode') }}</span>
                </div>
            </button>

            <div class="flex items-center px-2 py-2 rounded-md bg-gray-100 dark:bg-gray-800">
                <div class="relative shrink-0">
                    <img
                        class="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-800"
                        :src="`https://ui-avatars.com/api/?name=${$page.props.auth.user.name}&background=random`"
                        alt="Avatar"
                    />
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-gray-100 dark:ring-gray-800" />
                </div>
                <div class="ml-3 min-w-0">
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $page.props.auth.user.name }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ userRole }}</p>
                </div>
            </div>

            <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="w-full mt-1 text-md font-medium flex items-center gap-2 px-3 py-2.5 rounded-md bg-gray-100 dark:bg-gray-800 text-sm text-gray-500 dark:text-gray-400 hover:text-red-900 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
            >
                <LogOut class="w-4 h-4 ml-2" />
                {{ t('sidebar.log_out') }}
            </Link>
        </div>
    </aside>
</template>

<style scoped>
/* Hide scrollbar for Chrome, Safari and Opera */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.scrollbar-hide {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>
