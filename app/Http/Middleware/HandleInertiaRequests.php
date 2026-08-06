<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $pendingRegistrations = [];
        if ($request->user() && $request->user()->role && in_array($request->user()->role->name, ['Admin', 'Manager'])) {
            $pendingRegistrations = \App\Models\User::with('role')
                ->where('status', false)
                ->latest()
                ->take(10)
                ->get();
        }

        $settings = \App\Models\Setting::getSettings();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? $request->user()->load('role') : null,
            ],
            'pendingRegistrations' => $pendingRegistrations,
            'settings' => $settings,
        ];
    }
}
