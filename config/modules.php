<?php

use Nwidart\Modules\Activators\FileActivator;

return [

    /*
    |--------------------------------------------------------------------------
    | Module Namespace
    |--------------------------------------------------------------------------
    |
    | Default module namespace.
    |
    */

    'namespace' => 'Modules',

    /*
    |--------------------------------------------------------------------------
    | Module Stubs
    |--------------------------------------------------------------------------
    |
    | Default module stubs.
    |
    */

    'stubs' => [
        'enabled' => true,
        'path' => base_path() . '/vendor/omadonex/laravel-support/config/modules/stubs',
        'files' => [
            'routes/api_public' => 'Routes/api_public.php',
            'routes/api_secure' => 'Routes/api_secure.php',
            'routes/web' => 'Routes/web.php',
            'scaffold/config' => 'Config/config.php',
            'scaffold/providerBinding' => 'Providers/BindingServiceProvider.php',
            'scaffold/providerEvent' => 'Providers/EventServiceProvider.php',
            'mailer/mailerInterface' => 'Interfaces/IModuleMailer.php',
            'mailer/mailerService' => 'Services/ModuleMailer.php',
            'seeder/seederUnsafe' => 'Database/Seeders/UnsafeDatabaseSeeder.php',
            'layout/layout' => 'Resources/views/layouts/module.blade.php',
            'test/routes' => 'Tests/Feature/Routes/RoutesTest.php',
            'test/routesConfig' => 'Tests/Feature/Routes/config.php',
            'composer' => 'composer.json',
        ],
        'replacements' => [
            'routes/api_public' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'routes/api_secure' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'routes/web' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'json' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'views/index' => ['LOWER_NAME'],
            'views/master' => ['STUDLY_NAME'],
            'scaffold/config' => ['STUDLY_NAME'],
            'scaffold/providerBinding' => ['STUDLY_NAME', 'MODULE_NAMESPACE'],
            'scaffold/providerEvent' => ['STUDLY_NAME', 'MODULE_NAMESPACE'],
            'mailer/mailerInterface' => ['STUDLY_NAME', 'MODULE_NAMESPACE'],
            'mailer/mailerService' => ['STUDLY_NAME', 'MODULE_NAMESPACE'],
            'seeder/seederUnsafe' => ['STUDLY_NAME', 'MODULE_NAMESPACE'],
            'test/routes' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE'],
            'composer' => [
                'LOWER_NAME',
                'STUDLY_NAME',
                'VENDOR',
                'AUTHOR_NAME',
                'AUTHOR_EMAIL',
                'MODULE_NAMESPACE',
            ],
        ],
        'gitkeep' => true,
    ],
    'paths' => [
        /*
        |--------------------------------------------------------------------------
        | Modules path
        |--------------------------------------------------------------------------
        |
        | This path used for save the generated module. This path also will be added
        | automatically to list of scanned folders.
        |
        */

        'modules' => base_path('modules'),
        /*
        |--------------------------------------------------------------------------
        | Modules assets path
        |--------------------------------------------------------------------------
        |
        | Here you may update the modules assets path.
        |
        */

        'assets' => public_path('modules'),
        /*
        |--------------------------------------------------------------------------
        | The migrations path
        |--------------------------------------------------------------------------
        |
        | Where you run 'module:publish-migration' command, where do you publish the
        | the migration files?
        |
        */

        'migration' => base_path('database/migrations'),
        /*
        |--------------------------------------------------------------------------
        | Generator path
        |--------------------------------------------------------------------------
        | Customise the paths where the folders will be generated.
        | Set the generate key to false to not generate that folder
        */
        'generator' => [
            'assets' => ['path' => 'Resources/assets', 'generate' => true],
            'assetsModels' => ['path' => 'Resources/assets/classes/models', 'generate' => true],
            'config' => ['path' => 'Config', 'generate' => true],
            'controller' => ['path' => 'Http/Controllers', 'generate' => true],
            'controllerApi' => ['path' => 'Http/Controllers/Api', 'generate' => true],
            'controllerApiModels' => ['path' => 'Http/Controllers/Api/Models', 'generate' => true],
            'command' => ['path' => 'Console', 'generate' => true],
            'emails' => ['path' => 'Emails', 'generate' => false],
            'event' => ['path' => 'Events', 'generate' => false],
            'factory' => ['path' => 'Database/Factories', 'generate' => true],
            'filter' => ['path' => 'Http/Middleware', 'generate' => true],
            'interfaces' => ['path' => 'Interfaces', 'generate' => true],
            'interfacesMR' => ['path' => 'Interfaces/Models/Repositories', 'generate' => true],
            'interfacesMS' => ['path' => 'Interfaces/Models/Services', 'generate' => true],
            'jobs' => ['path' => 'Jobs', 'generate' => false],
            'lang' => ['path' => 'Resources/lang', 'generate' => true],
            'listener' => ['path' => 'Listeners', 'generate' => false],
            'migration' => ['path' => 'Database/Migrations', 'generate' => true],
            'model' => ['path' => 'Models', 'generate' => true],
            'notifications' => ['path' => 'Notifications', 'generate' => false],
            'policies' => ['path' => 'Policies', 'generate' => false],
            'provider' => ['path' => 'Providers', 'generate' => true],
            'repository' => ['path' => 'Repositories', 'generate' => false],
            'resource' => ['path' => 'Transformers', 'generate' => true],
            'request' => ['path' => 'Http/Requests', 'generate' => true],
            'routes' => ['path' => 'Routes', 'generate' => true],
            'rules' => ['path' => 'Rules', 'generate' => false],
            'seeder' => ['path' => 'Database/Seeders', 'generate' => true],
            'seederCustom' => ['path' => 'Database/Seeders/Custom', 'generate' => true],
            'services' => ['path' => 'Services', 'generate' => true],
            'servicesMR' => ['path' => 'Services/Models/Repositories', 'generate' => true],
            'servicesMS' => ['path' => 'Services/Models/Services', 'generate' => true],
            'test' => ['path' => 'Tests', 'generate' => true],
            'testFeature' => ['path' => 'Tests/Feature', 'generate' => true],
            'testUnit' => ['path' => 'Tests/Unit', 'generate' => true],
            'views' => ['path' => 'Resources/views', 'generate' => true],
            'viewsLayouts' => ['path' => 'Resources/views/layouts', 'generate' => true],
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Scan Path
    |--------------------------------------------------------------------------
    |
    | Here you define which folder will be scanned. By default will scan vendor
    | directory. This is useful if you host the package in packagist website.
    |
    */

    'scan' => [
        'enabled' => false,
        'paths' => [
            base_path('vendor/*/*'),
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Composer File Template
    |--------------------------------------------------------------------------
    |
    | Here is the config for composer.json file, generated by this package
    |
    */

    'composer' => [
        'vendor' => 'omadonex',
        'author' => [
            'name' => 'Yuriy Arkhipov',
            'email' => 'omadonex@yandex.ru',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Here is the config for setting up caching feature.
    |
    */
    'cache' => [
        'enabled' => false,
        'key' => 'laravel-modules',
        'lifetime' => 60,
    ],
    /*
    |--------------------------------------------------------------------------
    | Choose what laravel-modules will register as custom namespaces.
    | Setting one to false will require you to register that part
    | in your own Service Provider class.
    |--------------------------------------------------------------------------
    */
    'register' => [
        'translations' => true,
        /**
         * load files on boot or register method
         *
         * Note: boot not compatible with asgardcms
         *
         * @example boot|register
         */
        'files' => 'register',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activators
    |--------------------------------------------------------------------------
    |
    | You can define new types of activators here, file, database etc. The only
    | required parameter is 'class'.
    | The file activator will store the activation status in storage/installed_modules
    */
    'activators' => [
        'file' => [
            'class' => FileActivator ::class,
            'statuses-file' => base_path('modules_statuses.json'),
            'cache-key' => 'activator.installed',
            'cache-lifetime' => 604800,
        ],
    ],

    'activator' => 'file',
];
