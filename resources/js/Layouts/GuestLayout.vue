<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-gray-50 dark:bg-gray-900 pt-6 sm:justify-center sm:pt-0 transition-colors duration-200">
        <div class="flex flex-col items-center gap-2">
            <img v-if="$page.props.settings?.store_logo_url" :src="$page.props.settings.store_logo_url" class="w-16 h-16 object-contain rounded-xl shadow-sm mb-1" />
            <Link href="/" class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">
                {{ $page.props.settings?.store_name || 'SYSTEMPOS JOR' }}
            </Link>
        </div>

        <div class="mt-6 w-full overflow-hidden bg-white dark:bg-gray-800 px-8 py-8 shadow-sm sm:max-w-md rounded-xl border border-gray-200 dark:border-gray-700">
            <slot />
        </div>
    </div>
</template>
