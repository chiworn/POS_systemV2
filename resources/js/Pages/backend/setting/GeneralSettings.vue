<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { 
    Sliders, 
    Store, 
    Mail, 
    Phone, 
    MapPin, 
    Upload, 
    Save, 
    CheckCircle2, 
    Image as ImageIcon,
    Building2
} from '@lucide/vue';

const props = defineProps({
    settings: Object,
});

const { t } = useI18n();
const page = usePage();

const logoPreview = ref(props.settings?.store_logo_url || null);
const logoInput = ref(null);
const successMessage = ref(null);

const form = useForm({
    store_name: props.settings?.store_name || 'Lucky Mart',
    store_phone: props.settings?.store_phone || '012345678',
    store_email: props.settings?.store_email || 'info@luckymart.com',
    store_address: props.settings?.store_address || 'Phnom Penh',
    store_logo: null,
});

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.store_logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const triggerFileInput = () => {
    logoInput.value?.click();
};

const submit = () => {
    form.post(route('settings.general.update'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            successMessage.value = t('settings.success_msg');
            setTimeout(() => {
                successMessage.value = null;
            }, 4000);
        },
    });
};
</script>

<template>
    <Head :title="t('settings.title')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 shadow-sm">
                    <Sliders class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-xl font-bold leading-tight text-gray-800 dark:text-gray-100">
                        {{ t('settings.title') }}
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                        {{ t('settings.subtitle') }}
                    </p>
                </div>
            </div>
        </template>

        <div class="flex flex-col h-full px-4 sm:px-[30px] py-6 gap-6 overflow-y-auto scrollbar-hide">
            
            <!-- Success Alert Banner -->
            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform -translate-y-2 opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="transform translate-y-0 opacity-100"
                leave-to-class="transform -translate-y-2 opacity-0"
            >
                <div v-if="successMessage || page.props.flash?.success" class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 shadow-sm">
                    <CheckCircle2 class="w-5 h-5 shrink-0 text-emerald-600 dark:text-emerald-400" />
                    <span class="text-sm font-medium">{{ successMessage || page.props.flash?.success }}</span>
                </div>
            </transition>

            <!-- Main Form Card (Full Screen / Full Width) -->
            <div class="w-full flex flex-col flex-1 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/80 overflow-hidden">
                <!-- Card Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700/80 bg-gray-50/60 dark:bg-gray-800/60 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <Store class="w-5 h-5 text-emerald-700 dark:text-emerald-400" />
                        <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">
                            {{ t('settings.store_info_section') }}
                        </h3>
                    </div>
                </div>

                <!-- Form Content -->
                <form @submit.prevent="submit" class="p-6 md:p-8 flex-1 flex flex-col justify-between space-y-6">
                    
                    <!-- Store Logo Section -->
                    <div class="flex flex-col sm:flex-row sm:items-center gap-6 p-4 rounded-lg bg-gray-50/70 dark:bg-gray-900/40 border border-gray-200/60 dark:border-gray-700/50">
                        <div class="relative group shrink-0">
                            <div class="w-24 h-24 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 flex items-center justify-center overflow-hidden shadow-inner">
                                <img v-if="logoPreview" :src="logoPreview" alt="Store Logo" class="w-full h-full object-contain p-1" />
                                <div v-else class="flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                    <Building2 class="w-8 h-8 stroke-1" />
                                    <span class="text-[10px] mt-1">No Logo</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 space-y-2">
                            <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200">
                                {{ t('settings.logo') }}
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ t('settings.logo_help') }}
                            </p>
                            
                            <input 
                                ref="logoInput" 
                                type="file" 
                                accept="image/*" 
                                class="hidden" 
                                @change="handleFileChange"
                            />

                            <div class="pt-1">
                                <button
                                    type="button"
                                    @click="triggerFileInput"
                                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-emerald-800 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 rounded-lg transition-colors border border-emerald-200 dark:border-emerald-800"
                                >
                                    <Upload class="w-3.5 h-3.5" />
                                    {{ logoPreview ? t('settings.change_logo') : t('settings.upload_logo') }}
                                </button>
                            </div>
                            <InputError :message="form.errors.store_logo" class="mt-1" />
                        </div>
                    </div>

                    <!-- Input Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Store Name -->
                        <div class="lg:col-span-2">
                            <label for="store_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ t('settings.store_name') }} <span class="text-red-500 dark:text-red-400">*</span>
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                                    <Store class="w-4 h-4" />
                                </div>
                                <input
                                    id="store_name"
                                    type="text"
                                    v-model="form.store_name"
                                    required
                                    :placeholder="t('settings.store_name_placeholder')"
                                    class="block w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700 transition-colors placeholder:text-gray-400"
                                />
                            </div>
                            <InputError :message="form.errors.store_name" class="mt-1" />
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="store_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ t('settings.phone') }}
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                                    <Phone class="w-4 h-4" />
                                </div>
                                <input
                                    id="store_phone"
                                    type="text"
                                    v-model="form.store_phone"
                                    :placeholder="t('settings.phone_placeholder')"
                                    class="block w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700 transition-colors placeholder:text-gray-400"
                                />
                            </div>
                            <InputError :message="form.errors.store_phone" class="mt-1" />
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="store_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ t('settings.email') }}
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                                    <Mail class="w-4 h-4" />
                                </div>
                                <input
                                    id="store_email"
                                    type="email"
                                    v-model="form.store_email"
                                    :placeholder="t('settings.email_placeholder')"
                                    class="block w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700 transition-colors placeholder:text-gray-400"
                                />
                            </div>
                            <InputError :message="form.errors.store_email" class="mt-1" />
                        </div>

                        <!-- Address -->
                        <div class="lg:col-span-2">
                            <label for="store_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ t('settings.address') }}
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute top-3 left-3.5 flex items-start pointer-events-none text-gray-400 dark:text-gray-500">
                                    <MapPin class="w-4 h-4" />
                                </div>
                                <textarea
                                    id="store_address"
                                    v-model="form.store_address"
                                    rows="4"
                                    :placeholder="t('settings.address_placeholder')"
                                    class="block w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700 transition-colors placeholder:text-gray-400 resize-none"
                                ></textarea>
                            </div>
                            <InputError :message="form.errors.store_address" class="mt-1" />
                        </div>

                    </div>

                    <!-- Submit Footer -->
                    <div class="flex items-center justify-end pt-4 border-t border-gray-100 dark:border-gray-700/80">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-emerald-700 hover:bg-emerald-800 rounded-lg shadow-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 disabled:opacity-50"
                        >
                            <Save class="w-4 h-4" />
                            <span>{{ form.processing ? t('settings.saving') : t('settings.save') }}</span>
                        </button>
                    </div>

                </form>
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
