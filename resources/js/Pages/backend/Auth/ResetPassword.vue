<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Mail, Lock, ShieldCheck } from '@lucide/vue';

const props = defineProps({
    email: {
        type: String,
        default: '',
    },
    token: {
        type: String,
        default: '',
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <div class="mb-6 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 mb-3">
                <ShieldCheck class="w-6 h-6" />
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Set New Password</h2>
            <p class="text-xs text-gray-500 mt-1">Please enter your email and your new security password below.</p>
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

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Lock class="h-4 w-4 text-gray-400" />
                    </div>
                    <input id="password" type="password" v-model="form.password" required autocomplete="new-password"
                        class="block w-full pl-10 pr-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="••••••••" />
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Lock class="h-4 w-4 text-gray-400" />
                    </div>
                    <input id="password_confirmation" type="password" v-model="form.password_confirmation" required autocomplete="new-password"
                        class="block w-full pl-10 pr-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="••••••••" />
                </div>
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="pt-2">
                <button type="submit" :disabled="form.processing"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-700 hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 transition-colors disabled:opacity-50">
                    Reset Password
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
