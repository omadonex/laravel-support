<?php

namespace Omadonex\LaravelSupport\Commands\Database;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use Omadonex\LaravelSupport\Classes\Utils\UtilsApp;
use Omadonex\LaravelSupport\Traits\UnsafeSeedingTrait;

class UnsafeSeeding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omx:db:seed-unsafe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes unsafe database seeding';

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
        if (!file_exists(database_path('seeders/UnsafeDatabaseSeeder.php'))) {
            $this->warn('UnsafeDatabaseSeeder.php not found. Nothing to do');

            return ;
        }

        //clear all protected
        $modules = \Nwidart\Modules\Facades\Module::all();
        $models = UtilsApp::getAllModels($modules);
        foreach ($models as $model) {
            if (in_array(UnsafeSeedingTrait::class, class_uses($model))) {
                $instance = new $model;
                $qb = $instance->query()->unsafeSeeding();
                if (in_array(SoftDeletes::class, class_uses($model))) {
                    $qb->forceDelete();
                } else {
                    $qb->delete();
                }
                $instance->clearUnsafePivot();
            }
        }

        $this->call('db:seed', [
            '--class' => 'UnsafeDatabaseSeeder',
        ]);
    }
}
