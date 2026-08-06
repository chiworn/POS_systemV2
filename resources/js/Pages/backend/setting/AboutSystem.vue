<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { 
    Info, 
    Code2, 
    Cpu, 
    Layers, 
    UserCheck, 
    ShieldCheck, 
    Server, 
    Database, 
    Sparkles, 
    CheckCircle2, 
    Store,
    Calendar,
    Globe
} from '@lucide/vue';

const props = defineProps({
    systemVersion: String,
    laravelVersion: String,
    phpVersion: String,
    vueVersion: String,
    developer: String,
    settings: Object,
});

const { t } = useI18n();
</script>

<template>
    <Head :title="t('about.title')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 shadow-sm">
                    <Info class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">
                        {{ t('about.title') }}
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                        {{ t('about.subtitle') }}
                    </p>
                </div>
            </div>
        </template>

        <div class="flex flex-col h-full px-4 sm:px-[30px] py-6 gap-6 overflow-y-auto scrollbar-hide">
            
            <!-- Hero Branding Banner -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-900 via-emerald-800 to-emerald-950 p-6 sm:p-8 text-white shadow-lg">
                <!-- Background Glow Effect -->
                <div class="absolute -top-10 -right-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col sm:flex-row items-center sm:items-start gap-6">
                    <!-- Logo Card -->
                    <div class="w-24 h-24 rounded-2xl bg-white p-2 flex items-center justify-center shadow-xl shrink-0 overflow-hidden border-2 border-emerald-400/30">
                        <img v-if="props.settings?.store_logo_url" :src="props.settings.store_logo_url" :alt="props.settings.store_name" class="w-full h-full object-contain rounded-xl" />
                        <Store v-else class="w-12 h-12 text-emerald-800" />
                    </div>

                    <div class="flex-1 text-center sm:text-left space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-700/60 border border-emerald-500/40 text-emerald-200 text-xs font-semibold backdrop-blur-md">
                            <Sparkles class="w-3.5 h-3.5" />
                            <span>Official Release</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                            {{ props.settings?.store_name || 'SYSTEM POS JOR' }}
                        </h1>
                        <p class="text-xs sm:text-sm text-emerald-100/80 max-w-2xl">
                            Next-generation Point of Sale & Inventory Management System built for modern retail businesses.
                        </p>
                    </div>

                    <div class="shrink-0 flex flex-col items-center sm:items-end gap-1 bg-emerald-950/50 p-4 rounded-xl border border-emerald-700/50 backdrop-blur-sm">
                        <span class="text-xs text-emerald-300 font-medium">Status</span>
                        <div class="flex items-center gap-1.5 text-emerald-400 font-bold text-sm">
                            <CheckCircle2 class="w-4 h-4" />
                            <span>Active & Updated</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4 Main Requested Feature Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- System Version Card -->
                <div class="group p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/80 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <Layers class="w-6 h-6" />
                        </div>
                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300">
                            Current
                        </span>
                    </div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ t('about.system_version') }}
                    </p>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-gray-100 mt-1">
                        {{ props.systemVersion }}
                    </h3>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-2 flex items-center gap-1 font-medium">
                        <CheckCircle2 class="w-3.5 h-3.5" /> Stable Build
                    </p>
                </div>

                <!-- Laravel Version Card -->
                <div class="group p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/80 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <Server class="w-6 h-6" />
                        </div>
                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300">
                            Backend
                        </span>
                    </div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ t('about.laravel_version') }}
                    </p>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-gray-100 mt-1">
                        v{{ props.laravelVersion }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 font-medium">
                        Laravel Framework
                    </p>
                </div>

                <!-- Vue Version Card -->
                <div class="group p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/80 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-teal-50 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <Code2 class="w-6 h-6" />
                        </div>
                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-teal-100 dark:bg-teal-900/40 text-teal-700 dark:text-teal-300">
                            Frontend
                        </span>
                    </div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ t('about.vue_version') }}
                    </p>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-gray-100 mt-1">
                        v{{ props.vueVersion }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 font-medium">
                        Vue 3 + Inertia.js
                    </p>
                </div>

                <!-- Developer Card -->
                <div class="group p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/80 shadow-sm hover:shadow-md transition-all duration-200 hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <UserCheck class="w-6 h-6" />
                        </div>
                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300">
                            Author
                        </span>
                    </div>
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ t('about.developer') }}
                    </p>
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 mt-1 truncate" :title="props.developer">
                        {{ props.developer }}
                    </h3>
                    <p class="text-xs text-purple-600 dark:text-purple-400 mt-2 font-medium">
                        Lead Development Team
                    </p>
                </div>

            </div>

            <!-- Detailed Specifications Card -->
            <div class="rounded-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/80 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-4 border-b border-gray-100 dark:border-gray-700/80">
                    <Cpu class="w-5 h-5 text-emerald-700 dark:text-emerald-400" />
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100">
                        Technical Environment Details
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/50 space-y-1">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">PHP Runtime</span>
                        <p class="text-base font-bold text-gray-800 dark:text-gray-200">PHP {{ props.phpVersion }}</p>
                    </div>

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/50 space-y-1">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Database Engine</span>
                        <p class="text-base font-bold text-gray-800 dark:text-gray-200">MySQL / MariaDB</p>
                    </div>

                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-700/50 space-y-1">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Environment</span>
                        <p class="text-base font-bold text-emerald-700 dark:text-emerald-400">Production Ready</p>
                    </div>
                </div>
            </div>

            <!-- Footer Copyright -->
            <div class="text-center py-4 text-xs text-gray-400 dark:text-gray-500 space-y-1 border-t border-gray-100 dark:border-gray-800">
                <p>&copy; {{ new Date().getFullYear() }} {{ props.settings?.store_name || 'SYSTEM POS JOR' }}. {{ t('about.all_rights_reserved') }}</p>
                <p class="text-[11px] text-gray-400 dark:text-gray-600">Designed & Developed by {{ props.developer }}</p>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
</style>
