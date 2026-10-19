
# LogicException - Internal Server Error

TENANT_BASE_DOMAIN must be a valid DNS domain.

PHP 8.4.26
Laravel 12.70.0
100.123.173.99

## Stack Trace

0 - app/Domains/School/Services/SchoolService.php:222
1 - app/Domains/School/Services/SchoolService.php:35
2 - vendor/laravel/framework/src/Illuminate/Database/Concerns/ManagesTransactions.php:35
3 - vendor/laravel/framework/src/Illuminate/Database/DatabaseManager.php:491
4 - vendor/laravel/framework/src/Illuminate/Support/Facades/Facade.php:363
5 - app/Domains/School/Services/SchoolService.php:32
6 - app/Filament/Resources/SuperAdminSchoolResource/Pages/CreateSuperAdminSchool.php:21
7 - vendor/filament/filament/src/Resources/Pages/CreateRecord.php:119
8 - vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php:36
9 - vendor/laravel/framework/src/Illuminate/Container/Util.php:43
10 - vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php:96
11 - vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php:35
12 - vendor/livewire/livewire/src/Wrapped.php:23
13 - vendor/livewire/livewire/src/Mechanisms/HandleComponents/HandleComponents.php:583
14 - vendor/livewire/livewire/src/Mechanisms/HandleComponents/HandleComponents.php:224
15 - vendor/livewire/livewire/src/LivewireManager.php:132
16 - vendor/livewire/livewire/src/Mechanisms/HandleRequests/HandleRequests.php:220
17 - vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php:46
18 - vendor/laravel/framework/src/Illuminate/Routing/Route.php:265
19 - vendor/laravel/framework/src/Illuminate/Routing/Route.php:211
20 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:822
21 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
22 - vendor/livewire/livewire/src/Mechanisms/HandleRequests/RequireLivewireHeaders.php:19
23 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
24 - vendor/laravel/framework/src/Illuminate/Routing/Middleware/SubstituteBindings.php:50
25 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
26 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/VerifyCsrfToken.php:87
27 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
28 - vendor/laravel/framework/src/Illuminate/View/Middleware/ShareErrorsFromSession.php:48
29 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
30 - vendor/filament/notifications/src/Http/Middleware/InitializeNotificationMailbox.php:18
31 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
32 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:120
33 - vendor/laravel/framework/src/Illuminate/Session/Middleware/StartSession.php:63
34 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
35 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/AddQueuedCookiesToResponse.php:36
36 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
37 - vendor/laravel/framework/src/Illuminate/Cookie/Middleware/EncryptCookies.php:74
38 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
39 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
40 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:821
41 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:800
42 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:764
43 - vendor/laravel/framework/src/Illuminate/Routing/Router.php:753
44 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:200
45 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:180
46 - vendor/livewire/livewire/src/Features/SupportDisablingBackButtonCache/DisableBackButtonCacheMiddleware.php:19
47 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
48 - app/Http/Middleware/ValidateTrustedHost.php:29
49 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
50 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/ConvertEmptyStringsToNull.php:27
51 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
52 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/TrimStrings.php:47
53 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
54 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePostSize.php:27
55 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
56 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/PreventRequestsDuringMaintenance.php:109
57 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
58 - vendor/laravel/framework/src/Illuminate/Http/Middleware/HandleCors.php:61
59 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
60 - vendor/laravel/framework/src/Illuminate/Http/Middleware/TrustProxies.php:58
61 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
62 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Middleware/InvokeDeferredCallbacks.php:22
63 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
64 - vendor/laravel/framework/src/Illuminate/Http/Middleware/ValidatePathEncoding.php:26
65 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
66 - vendor/filament/notifications/src/Http/Middleware/PublishNotifications.php:16
67 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:219
68 - vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php:137
69 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:175
70 - vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php:144
71 - vendor/laravel/framework/src/Illuminate/Foundation/Application.php:1220
72 - public/index.php:20

## Request

POST /livewire-eb7af37f/update

## Headers

* **host**: 100.123.173.99
* **connection**: keep-alive
* **content-length**: 1864
* **user-agent**: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36
* **dnt**: 1
* **content-type**: application/json
* **x-livewire**: 1
* **accept**: */*
* **origin**: http://100.123.173.99
* **referer**: http://100.123.173.99/super-admin/super-admin-schools/create
* **accept-encoding**: gzip, deflate
* **accept-language**: en-US,en;q=0.9,es-ES;q=0.8,es-US;q=0.7,es;q=0.6,fr-FR;q=0.5,fr;q=0.4
* **cookie**: XSRF-TOKEN=eyJpdiI6IjloaEhjY0lhVyt4YlFDOC9LSi9zemc9PSIsInZhbHVlIjoiK0tKVTg2SDNISy9sdTROdlo4RW1Cekhnd0dwZW0xYjZkK1p5MWFUN280V2Y2NWVub1hFdTRiR1V5R3ZKNXB4MEc5eVZiS0xmSXd3V08rOFBQVlcwUWhGbk4vWTRuYzF4amJlMFVjQzkxUFcydjhzQVlvOGxaeDlaVUw0RG5Cb1MiLCJtYWMiOiI3MWZlZjViMDk1Y2UwZjA4MTI3NTNmMjI4ODY1MGRkYzI1ZTk5ZjYzZGFlNGY5ZDQxYTY0OWY2NWQ1OGUwZTM2IiwidGFnIjoiIn0%3D; nexus-school-session=eyJpdiI6IkVJZVUzeVdKMEtEZGw1Ky9FQVBmMlE9PSIsInZhbHVlIjoiVjRCQy9WQ0dKRThmN3dSNzIrSzhES0pQd2k3THRkT3pHcXowd3pKQ3N3SkZOelo3dldRMWwwNjY2K2tpN0E0Tm1LMUNKRWNZTXhPdCsyTUIwbHozcDl6bFJMSHR3TlZHVy82MENWMVFhK3dFck1QNTFOMzFkK2orTzFBSjB0T3UiLCJtYWMiOiI5NGFiNzcwMjhhNWI2YTE5NjZjYTc2Mzg4MzViZDViNDc1MzUxYWVhN2MzMmIzZDMyZTAwMTE0YzMzOGVhYTQxIiwidGFnIjoiIn0%3D

## Route Context

controller: Livewire\Mechanisms\HandleRequests\HandleRequests@handleUpdate
route name: default-livewire.update
middleware: web, Livewire\Mechanisms\HandleRequests\RequireLivewireHeaders, Filament\Notifications\Http\Middleware\InitializeNotificationMailbox

## Route Parameters

No route parameter data available.

## Database Queries

* mysql - select * from `sessions` where `id` = 'Uk3RN0WNio3CfHeXdcCHOImUWXdnUJwkhB2D4CYz' limit 1 (1.53 ms)
* mysql - select * from `cache` where `key` in ('nexus-school-cache-livewire-checksum-failures:100.93.88.111') (0.58 ms)
* mysql - select * from `users` where `id` = 1 and `users`.`deleted_at` is null limit 1 (0.69 ms)
* mysql - select `roles`.*, `model_has_roles`.`model_id` as `pivot_model_id`, `model_has_roles`.`role_id` as `pivot_role_id`, `model_has_roles`.`model_type` as `pivot_model_type` from `roles` inner join `model_has_roles` on `roles`.`id` = `model_has_roles`.`role_id` where `model_has_roles`.`model_id` in (1) and `model_has_roles`.`model_type` = 'App\Models\User' (0.79 ms)
* mysql - select * from `cache` where `key` in ('nexus-school-cache-spatie.permission.cache') (0.56 ms)









vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ sed -n '180,245p' app/Domains/School/Services/SchoolService.php
        }

        if (isset($filters['country'])) {
            $query->where('country', $filters['country']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    private function canonicalSlug(string $value): string
    {
        $slug = Str::lower(Str::slug($value));

        if ($slug === '' || strlen($slug) > 63 || ! preg_match('/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', $slug)) {
            throw ValidationException::withMessages([
                'slug' => 'The school slug must produce a valid DNS subdomain (letters, numbers, and internal hyphens only).',
            ]);
        }

        if (in_array($slug, config('tenancy.reserved_subdomains', []), true)) {
            throw ValidationException::withMessages([
                'slug' => "The subdomain '{$slug}' is reserved by the platform.",
            ]);
        }

        return $slug;
    }

    private function canonicalDomain(string $slug): string
    {
        $baseDomain = Str::lower(trim((string) config('tenancy.tenant_base_domain')));

        if (! preg_match('/^(?=.{1,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,63}$/', $baseDomain)) {
            throw new \LogicException('TENANT_BASE_DOMAIN must be a valid DNS domain.');
        }

        return "{$slug}.{$baseDomain}";
    }

    private function isUniqueConstraintViolation(QueryException $exception): bool
    {
        return in_array((string) $exception->getCode(), ['23000', '23505', '19'], true);
    }
}
vboxuser@debianserver:/var/www/html/NexusSchool-version-1.0-main$ 
