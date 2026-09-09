<?php

namespace App\Http\Middleware;

use App\Support\Navigation;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * The navigation tree and business identity are needed by the shell on
     * every page, so they are shared rather than repeated in each controller.
     * Both are resolved lazily so that guest and Blade-only requests do not
     * pay for building them.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),

            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => trim("{$user->first_name} {$user->last_name}") ?: $user->username,
                    'email' => $user->email,
                    'initials' => self::initials($user->first_name, $user->last_name, $user->username),
                ] : null,
            ],

            'business' => fn () => $user ? [
                'name' => session('business.name'),
                // Fall back to the initial badge rather than a broken image
                // when the record names a logo that is no longer on disk.
                'logo' => self::logoUrl(session('business.logo')),
                'theme_color' => session('business.theme_color') ?: 'primary',
            ] : null,

            'currency' => fn () => $user ? [
                'code' => session('currency.code'),
                'symbol' => session('currency.symbol'),
                'thousand_separator' => session('currency.thousand_separator'),
                'decimal_separator' => session('currency.decimal_separator'),
                'symbol_placement' => session('business.currency_symbol_placement', 'before'),
                'precision' => (int) session('business.currency_precision', 2),
            ] : null,

            'nav' => fn () => $user ? Navigation::forCurrentUser() : [],
            'settingsNav' => fn () => $user ? Navigation::settingsForCurrentUser() : [],

            // The app is commonly served from a subdirectory, so the shell
            // must never hardcode paths.
            'routes' => [
                'home' => route('home'),
                'logout' => route('logout'),
            ],

            'flash' => fn () => [
                'success' => $request->session()->get('status.success')
                    ? $request->session()->get('status.msg')
                    : null,
                'error' => $request->session()->get('status.success') === 0
                    ? $request->session()->get('status.msg')
                    : null,
            ],
        ];
    }

    private static function logoUrl(?string $logo): ?string
    {
        if (empty($logo) || ! is_file(public_path('uploads/business_logos/'.$logo))) {
            return null;
        }

        return asset('uploads/business_logos/'.$logo);
    }

    /**
     * Two-letter avatar fallback, used until a user uploads a picture.
     */
    private static function initials(?string $first, ?string $last, ?string $fallback): string
    {
        $letters = mb_substr((string) $first, 0, 1).mb_substr((string) $last, 0, 1);

        return mb_strtoupper($letters !== '' ? $letters : mb_substr((string) $fallback, 0, 2));
    }
}
