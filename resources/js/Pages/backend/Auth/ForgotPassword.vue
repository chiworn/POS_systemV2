<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Mail, ArrowLeft, KeyRound } from '@lucide/vue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="mb-6 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 mb-3">
                <KeyRound class="w-6 h-6" />
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Forgot Password?</h2>
            <p class="text-xs text-gray-500 mt-1">No problem. Enter your email address below and we will send you a password reset link.</p>
        </div>

        <div v-if="status" class="mb-5 text-sm font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 p-3 rounded-md">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Mail class="h-4 w-4 text-gray-400" />
                    </div>
                    <input id="email" type="email" v-model="form.email" required autofocus autocomplete="username"
                        class="block w-full pl-10 pr-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="admin@example.com" />
                </div>
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="pt-2">
                <button type="submit" :disabled="form.processing"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-700 hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 transition-colors disabled:opacity-50">
                    {{ form.processing ? 'Processing...' : 'Reset New Password' }}
                </button>
            </div>

            <div class="pt-2 text-center">
                <Link :href="route('login')" class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700 hover:text-emerald-800 transition-colors">
                    <ArrowLeft class="w-4 h-4" />
                    <span>Back to Sign In</span>
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
