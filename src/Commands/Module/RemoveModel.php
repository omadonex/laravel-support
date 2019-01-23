<?php

namespace Omadonex\LaravelSupport\Commands\Module;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class RemoveModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omx:module:remove-model {class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove model scaffold';

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
        $module = Module::findOrFail(Module::getUsed());
        $modulePath = $module->getPath();

        $className = ucfirst($this->argument('class'));
        UtilsCustom::unlinkIf("{$modulePath}/Models/{$className}.php");
        UtilsCustom::unlinkIf("{$modulePath}/Interfaces/Models/Repositories/I{$className}Repository.php");
        UtilsCustom::unlinkIf("{$modulePath}/Interfaces/Models/Services/I{$className}Service.php");
        UtilsCustom::unlinkIf("{$modulePath}/Services/Models/Repositories/{$className}Repository.php");
        UtilsCustom::unlinkIf("{$modulePath}/Services/Models/Services/{$className}Service.php");
        UtilsCustom::unlinkIf("{$modulePath}/Transformers/{$className}Resource.php");
    }
}
