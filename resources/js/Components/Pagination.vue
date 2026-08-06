<script setup>
import { router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from '@lucide/vue';

defineProps({
    data: {
        type: Object,
        required: true
    }
});

const goToPage = (url) => {
    if (url) {
        router.visit(url, { preserveScroll: true, preserveState: true });
    }
};
</script>

<template>
    <div v-if="data && data.total > 0" class="flex flex-col sm:flex-row items-center justify-between px-6 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 gap-4 shrink-0 transition-colors">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Showing {{ data.from ?? 0 }} to {{ data.to ?? 0 }} of {{ data.total }} results
        </p>
        <div class="flex items-center gap-1 flex-wrap">
            <template v-for="(link, p) in data.links" :key="p">
                <!-- Previous Button -->
                <button v-if="link.label.includes('Previous')"
                    @click="goToPage(link.url)" 
                    :disabled="!link.url"
                    class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-md border text-gray-600 dark:text-gray-300 hover:bg-emerald-700 hover:text-white dark:hover:bg-emerald-600 dark:hover:text-white border-gray-200 dark:border-gray-600 hover:border-emerald-700 dark:hover:border-emerald-600 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                    <ChevronLeft class="w-3 h-3" /> Prev
                </button>

                <!-- Next Button -->
                <button v-else-if="link.label.includes('Next')"
                    @click="goToPage(link.url)" 
                    :disabled="!link.url"
                    class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-md border text-gray-600 dark:text-gray-300 hover:bg-emerald-700 hover:text-white dark:hover:bg-emerald-600 dark:hover:text-white border-gray-200 dark:border-gray-600 hover:border-emerald-700 dark:hover:border-emerald-600 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                    Next <ChevronRight class="w-3 h-3" />
                </button>

                <!-- Page Numbers -->
                <button v-else
                    @click="goToPage(link.url)"
                    :disabled="!link.url"
                    :class="[
                        'px-3 py-1.5 text-xs font-semibold rounded-md border transition-colors',
                        link.active 
                            ? 'bg-emerald-700 text-white border-emerald-700 dark:bg-emerald-600 dark:border-emerald-600' 
                            : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-700 dark:hover:text-emerald-400 hover:border-emerald-200 dark:hover:border-emerald-800/50',
                        !link.url && !link.active ? 'opacity-40 cursor-not-allowed' : ''
                    ]"
                    v-html="link.label"
                ></button>
            </template>
        </div>
    </div>
</template>
