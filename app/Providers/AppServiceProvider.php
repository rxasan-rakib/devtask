<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Implicitly grant 'super-admin' role or designated super admin emails all permissions
        Gate::before(function ($user, $ability) {
            return ($user->hasRole('super-admin') || in_array(strtolower($user->email ?? ''), ['support.fastit@gmail.com', 'admin@workflowpro.com'])) ? true : null;
        });

        // Expose all defined module permissions as explicit Gate abilities.
        // Spatie authorizes dynamically via Gate::before, but IDE tooling and extensions
        // (e.g. VS Code Laravel / Laraphense) inspect Gate::abilities() or static definitions
        // and otherwise report false-positive "Policy [...] not found" diagnostics.
        try {
            // 1. Static registration from the module permission service catalog
            if (class_exists(\App\Services\ModulePermissionService::class)) {
                foreach (\App\Services\ModulePermissionService::getAllPermissionNames() as $permName) {
                    if (! Gate::has($permName)) {
                        Gate::define($permName, fn ($user) => method_exists($user, 'checkPermissionTo') && $user->checkPermissionTo($permName));
                    }
                }
            }

            // 2. Dynamic registration from database permissions table (for any custom or runtime additions)
            if (Schema::hasTable(config('permission.table_names.permissions', 'permissions'))) {
                foreach (app(PermissionRegistrar::class)->getPermissions() as $permission) {
                    $name = $permission->name;
                    if (! Gate::has($name)) {
                        Gate::define($name, fn ($user) => method_exists($user, 'checkPermissionTo') && $user->checkPermissionTo($name));
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore if DB is unavailable during early bootstrap
        }

        try {
            if (Schema::hasTable('settings')) {
                // Apply app branding & regional settings
                $appName     = Setting::get('app_name', 'WorkFlow Pro');
                $companyName = Setting::get('company_name', 'Work Management System');
                $timezone    = Setting::get('timezone', 'Asia/Dhaka');

                if ($appName) {
                    Config::set('app.name', $appName);
                }

                if ($timezone) {
                    Config::set('app.timezone', $timezone);
                    date_default_timezone_set($timezone);
                }

                // Apply dynamic SMTP config
                $mailer     = Setting::get('mail_mailer');
                $host       = Setting::get('mail_host');
                $port       = Setting::get('mail_port');
                $username   = Setting::get('mail_username');
                $password   = Setting::get('mail_password');
                $encryption = Setting::get('mail_encryption');
                $fromEmail  = Setting::get('mail_from_address');
                $fromName   = Setting::get('mail_from_name', $appName);

                if ($mailer) {
                    Config::set('mail.default', $mailer);
                }
                if ($host) {
                    Config::set('mail.mailers.smtp.host', $host);
                }
                if ($port) {
                    Config::set('mail.mailers.smtp.port', (int) $port);
                }
                if ($username) {
                    Config::set('mail.mailers.smtp.username', $username);
                }
                if ($password) {
                    Config::set('mail.mailers.smtp.password', $password);
                }
                if ($encryption) {
                    Config::set('mail.mailers.smtp.encryption', $encryption === 'null' ? null : $encryption);
                }
                Config::set('mail.mailers.smtp.stream', [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                    ],
                ]);
                if ($fromEmail) {
                    Config::set('mail.from.address', $fromEmail);
                    Config::set('mail.from.name', $fromName ?: $appName);
                }

                // Share globally across all Blade views
                View::share('appName', $appName);
                View::share('companyName', $companyName);
                View::share('companyEmail', Setting::get('company_email', 'support@workflowpro.com'));
                View::share('companyPhone', Setting::get('company_phone', ''));
                View::share('companyAddress', Setting::get('company_address', ''));
                View::share('currencySymbol', Setting::get('currency_symbol', '$'));
                View::share('dateFormat', Setting::get('date_format', 'd M, Y'));
                View::share('appLogoUrl', Setting::getLogoUrl());
                View::share('appFaviconUrl', Setting::getFaviconUrl());
            }
        } catch (\Throwable $e) {
            // Silently ignore if DB connection is unavailable during early bootstrap
        }
    }
}

