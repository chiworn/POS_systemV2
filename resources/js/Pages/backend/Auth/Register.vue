<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Mail, Lock, User } from '@lucide/vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-gray-900">Create Account</h2>
            <p class="text-sm text-gray-500 mt-1">Register to access the system</p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <User class="h-4 w-4 text-gray-400" />
                    </div>
                    <input id="name" type="text" v-model="form.name" required autofocus autocomplete="name"
                        class="block w-full pl-10 pr-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="John Doe" />
                </div>
                <InputError class="mt-1" :message="form.errors.name" />
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Mail class="h-4 w-4 text-gray-400" />
                    </div>
                    <input id="email" type="email" v-model="form.email" required autocomplete="username"
                        class="block w-full pl-10 pr-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="admin@example.com" />
                </div>
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Lock class="h-4 w-4 text-gray-400" />
                    </div>
                    <input id="password" type="password" v-model="form.password" required autocomplete="new-password"
                        class="block w-full pl-10 pr-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="••••••••" />
                </div>
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Lock class="h-4 w-4 text-gray-400" />
                    </div>
                    <input id="password_confirmation" type="password" v-model="form.password_confirmation" required autocomplete="new-password"
                        class="block w-full pl-10 pr-3 py-2 bg-white border border-gray-300 rounded-md text-sm shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="••••••••" />
                </div>
                <InputError class="mt-1" :message="form.errors.password_confirmation" />
            </div>

            <div class="pt-2 space-y-3">
                <button type="submit" :disabled="form.processing"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-700 hover:bg-emerald-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600 transition-colors disabled:opacity-50">
                    Register
                </button>

                <div class="relative flex py-1 items-center">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink mx-3 text-xs text-gray-400 uppercase tracking-wider font-semibold">Or sign up with</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                <a :href="route('auth.google')"
                    class="w-full flex items-center justify-center gap-3 py-2.5 px-4 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Sign up with Google</span>
                </a>
            </div>

            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">
                    Already registered?
                    <Link :href="route('login')" class="font-medium text-emerald-700 hover:text-emerald-800">
                        Sign in
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
