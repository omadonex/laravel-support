<?php

namespace Omadonex\LaravelSupport\Commands\Module;

use Illuminate\Console\Command;

class Make extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omx:module:make {name} {--p|plain} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Making module';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pathRoot = realpath(__DIR__.'/../../..');
        if (!file_exists(config_path('modules.php'))) {
            $this->warn('Configuration file for modules not found!');
            $this->info('Copying file from vendor folder...');
            copy("$pathRoot/config/modules.php", config_path('modules.php'));
            $this->info('Configuration file has been copied. Please run this command again...');

            return ;
        }

        $moduleName = $this->argument('name');
        $studlyName = ucfirst($moduleName);
        $this->call('module:make', [
            'name' => [$moduleName],
            '--plain' => $this->option('plain'),
            '--force' => $this->option('force'),
        ]);

        $path = base_path("modules/$studlyName");
        rename("$path/Interfaces/IModuleMailer.php", "$path/Interfaces/I{$studlyName}Mailer.php");
        rename("$path/Services/ModuleMailer.php", "$path/Services/{$studlyName}Mailer.php");
        rename("$path/Providers/{$studlyName}ServiceProvider.php", "$path/Providers/ModuleServiceProvider.php");
        rename("$path/Database/Seeders/{$studlyName}DatabaseSeeder.php", "$path/Database/Seeders/DatabaseSeeder.php");

        mkdir("$path/Resources/assets/vue/Components", 0755, true);
        mkdir("$path/Resources/assets/vue/Page", 0755, true);
    }
}
