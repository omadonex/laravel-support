<?php

namespace Omadonex\LaravelSupport\Commands\Module;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;

class MakeModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omx:module:make-model {class} {--m|migration} {--s|scaffold}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Making a model scaffold for current module';

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
        $moduleNs = config('modules.namespace') . '\\' . $module->getStudlyName();
        $modulePath = $module->getPath();

        $className = ucfirst($this->argument('class'));
        $tableName = $this->getTableName($className, $module->getLowerName());

        if (file_exists("{$modulePath}/Models/{$className}.php")) {
            $this->error('Model already exists. Abort!');
            return ;
        }

        $pathRoot = realpath(__DIR__.'/../../..');
        $pathScaffold = "{$pathRoot}/config/modules/stubs/model";

        $fullData = [
            'moduleNs' => $moduleNs,
            'modulePath' => $modulePath,
            'pathScaffold' => $pathScaffold,
            'className' => $className,
            'tableName' => $tableName,
        ];

        $files = [
            'model' => [
                'path' => "{$fullData['modulePath']}/Models",
                'name' => "{$fullData['className']}.php",
            ],
        ];

        if ($this->option('scaffold') === true) {
            $files = array_merge($files, [
                'irepository' => [
                    'path' => "{$fullData['modulePath']}/Interfaces/Models/Repositories",
                    'name' => "I{$fullData['className']}Repository.php",
                ],
                'iservice' => [
                    'path' => "{$fullData['modulePath']}/Interfaces/Models/Services",
                    'name' => "I{$fullData['className']}Service.php",
                ],
                'resource' => [
                    'path' => "{$fullData['modulePath']}/Transformers",
                    'name' => "{$fullData['className']}Resource.php",
                ],
                'repository' => [
                    'path' => "{$fullData['modulePath']}/Services/Models/Repositories",
                    'name' => "{$fullData['className']}Repository.php",
                ],
                'service' => [
                    'path' => "{$fullData['modulePath']}/Services/Models/Services",
                    'name' => "{$fullData['className']}Service.php",
                ],
            ]);
        }

        $this->makeFiles($files, $fullData);

        if ($this->option('migration') === true) {
            $migrationName = "create_{$tableName}_table";
            $this->call('module:make-migration', [
                'name' => $migrationName,
                'module' => $module->getStudlyName(),
            ]);
        }
    }

    private function makeFiles($files, $fullData)
    {
        foreach ($files as $file => $paths) {
            $contents = file_get_contents("{$fullData['pathScaffold']}/{$file}.stub");
            $contents = str_replace('$NAMESPACE$', $fullData['moduleNs'], $contents);
            $contents = str_replace('$CLASS$', $fullData['className'], $contents);
            $contents = str_replace('$TABLE_NAME$', $fullData['tableName'], $contents);
            file_put_contents($paths['path'].'/'.$paths['name'], $contents);
        }
    }

    private function getTableName($className, $moduleLowerName)
    {
        $pieces = preg_split('/(?=[A-Z])/', $className, -1, PREG_SPLIT_NO_EMPTY);

        $string = '';
        foreach ($pieces as $i => $piece) {
            if ($i+1 < count($pieces)) {
                $string .= strtolower($piece) . '_';
            } else {
                $string .= Str::plural(strtolower($piece));
            }
        }

        return "{$moduleLowerName}_{$string}";
    }
}
