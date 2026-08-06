<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { 
    Calculator, 
    Percent, 
    Save, 
    CheckCircle, 
    AlertCircle, 
    Receipt, 
    FileText, 
    ShieldAlert, 
    Sparkles 
} from '@lucide/vue';

const props = defineProps({
    settings: Object,
});

const { t } = useI18n();

const form = useForm({
    enable_tax: Boolean(props.settings?.enable_tax ?? false),
    tax_rate: props.settings?.tax_rate ?? 10.00,
    tax_name: props.settings?.tax_name || 'VAT',
    tax_number: props.settings?.tax_number || '',
});

const successMessage = ref(null);

const submit = () => {
    form.post(route('settings.tax.update'), {
        preserveScroll: true,
        onSuccess: () => {
            successMessage.value = t('tax_settings.success_msg') || 'Tax settings updated successfully!';
            setTimeout(() => {
                successMessage.value = null;
            }, 4000);
        },
    });
};
</script>

<template>
    <Head :title="t('sidebar.tax_settings')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 shadow-sm">
                    <Calculator class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">
                        {{ t('tax_settings.title') || 'Tax Settings' }}
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                        {{ t('tax_settings.subtitle') || 'Configure tax calculation rules, rates, and identification numbers for POS orders' }}
                    </p>
                </div>
            </div>
        </template>

        <div class="flex flex-col h-full px-4 sm:px-[30px] py-6 gap-6 overflow-y-auto scrollbar-hide">
            
            <!-- Success Alert -->
            <div 
                v-if="successMessage" 
                class="flex items-center gap-3 p-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80 shadow-sm transition-all"
            >
                <CheckCircle class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
                <span class="font-medium">{{ successMessage }}</span>
            </div>

            <!-- Tax Configuration Form -->
            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Main Controls (2 Cols) -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Toggle Tax Enable Card -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/80 shadow-sm">
                        <div class="flex items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">
                                        {{ t('tax_settings.enable_tax') || 'Enable Tax' }}
                                    </h3>
                                    <span 
                                        :class="[
                                            'px-2.5 py-0.5 text-xs font-bold rounded-full transition-colors',
                                            form.enable_tax 
                                                ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300' 
                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'
                                        ]"
                                    >
                                        {{ form.enable_tax ? 'Active' : 'Disabled' }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    When enabled, tax will automatically be applied to checkout transactions and printed on receipts.
                                </p>
                            </div>

                            <!-- Modern Switch Toggle -->
                            <button
                                type="button"
                                @click="form.enable_tax = !form.enable_tax"
                                :class="[
                                    'relative inline-flex h-7 w-13 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                                    form.enable_tax ? 'bg-emerald-600' : 'bg-gray-300 dark:bg-gray-600'
                                ]"
                            >
                                <span
                                    :class="[
                                        'pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out',
                                        form.enable_tax ? 'translate-x-6' : 'translate-x-0'
                                    ]"
                                />
                            </button>
                        </div>
                    </div>

                    <!-- Tax Inputs Section -->
                    <div class="p-6 rounded-2xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/80 shadow-sm space-y-6">
                        <div class="flex items-center gap-2 pb-3 border-b border-gray-100 dark:border-gray-700">
                            <Percent class="w-5 h-5 text-emerald-700 dark:text-emerald-400" />
                            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">
                                Tax Parameters
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            
                            <!-- Tax Name / Label -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                                    {{ t('tax_settings.tax_name') || 'Tax Name / Label' }} *
                                </label>
                                <input 
                                    v-model="form.tax_name"
                                    type="text"
                                    required
                                    placeholder="VAT, GST, Sales Tax..."
                                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 dark:focus:border-emerald-500 transition-all"
                                />
                                <p v-if="form.errors.tax_name" class="mt-1 text-xs text-red-500">{{ form.errors.tax_name }}</p>
                            </div>

                            <!-- Tax Rate (%) -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                                    {{ t('tax_settings.tax_rate') || 'Tax Rate (%)' }} *
                                </label>
                                <div class="relative">
                                    <input 
                                        v-model.number="form.tax_rate"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        required
                                        placeholder="10.00"
                                        class="w-full pl-4 pr-10 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 dark:focus:border-emerald-500 transition-all font-semibold"
                                    />
                                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-gray-400 font-bold text-sm">
                                        %
                                    </div>
                                </div>
                                <p v-if="form.errors.tax_rate" class="mt-1 text-xs text-red-500">{{ form.errors.tax_rate }}</p>
                            </div>

                        </div>

                        <!-- Tax Identification Number (TIN/VAT Number) -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2">
                                {{ t('tax_settings.tax_number') || 'Tax Identification Number (TIN / VAT No.)' }}
                            </label>
                            <input 
                                v-model="form.tax_number"
                                type="text"
                                placeholder="e.g. K001-90218921"
                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 dark:focus:border-emerald-500 transition-all"
                            />
                            <p class="mt-1.5 text-[11px] text-gray-500 dark:text-gray-400">
                                Optionally printed on official customer receipts for tax audit compliance.
                            </p>
                            <p v-if="form.errors.tax_number" class="mt-1 text-xs text-red-500">{{ form.errors.tax_number }}</p>
                        </div>

                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-bold text-sm shadow-md hover:shadow-lg transition-all duration-200 disabled:opacity-50"
                        >
                            <Save class="w-4 h-4" />
                            <span>{{ form.processing ? 'Saving...' : 'Save Tax Settings' }}</span>
                        </button>
                    </div>

                </div>

                <!-- Right Live Preview Card -->
                <div class="space-y-6">
                    <div class="p-6 rounded-2xl bg-gradient-to-br from-gray-900 to-gray-800 text-white shadow-xl space-y-5 border border-gray-700/80">
                        <div class="flex items-center gap-2 text-emerald-400 pb-3 border-b border-gray-700/80">
                            <Receipt class="w-5 h-5" />
                            <h4 class="font-bold text-sm uppercase tracking-wider">Receipt Tax Calculation Preview</h4>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between text-gray-300">
                                <span>Subtotal</span>
                                <span class="font-mono">$100.00</span>
                            </div>

                            <div class="flex justify-between items-center text-emerald-400 font-semibold pt-2 border-t border-gray-700/50">
                                <span>{{ form.tax_name || 'Tax' }} ({{ form.enable_tax ? (form.tax_rate || 0) : 0 }}%)</span>
                                <span class="font-mono">
                                    +${{ form.enable_tax ? ((100 * (form.tax_rate || 0)) / 100).toFixed(2) : '0.00' }}
                                </span>
                            </div>

                            <div class="flex justify-between text-base font-black text-white pt-3 border-t border-gray-700">
                                <span>Total Amount</span>
                                <span class="font-mono text-emerald-400">
                                    ${{ form.enable_tax ? (100 + (100 * (form.tax_rate || 0)) / 100).toFixed(2) : '100.00' }}
                                </span>
                            </div>
                        </div>

                        <div v-if="form.tax_number" class="p-3 rounded-lg bg-gray-800/80 border border-gray-700 text-[11px] text-gray-400">
                            <span class="block text-gray-500 font-medium">TIN / VAT No:</span>
                            <span class="font-mono font-bold text-gray-200">{{ form.tax_number }}</span>
                        </div>

                        <p class="text-[11px] text-gray-400 text-center italic">
                            This preview demonstrates how taxes will be calculated on POS checkout receipts.
                        </p>
                    </div>
                </div>

            </form>

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
