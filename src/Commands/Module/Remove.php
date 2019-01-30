<?php

namespace Omadonex\LaravelSupport\Commands\Module;

use Illuminate\Console\Command;
use Omadonex\LaravelSupport\Classes\Utils\UtilsCustom;

class Remove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'omx:module:remove {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove module';

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
        if ($this->confirm('Are you sure for removing module? WARNING: IT REMOVES ALL FILES IN MODULE FOLDER!!!')) {
            $moduleName = $this->argument('name');
            $studlyName = ucfirst($moduleName);
            UtilsCustom::removeDir(base_path("modules/{$studlyName}"));
            $this->updatePhpUnitXml($studlyName);
        }
    }

    private function updatePhpUnitXml($studlyName)
    {
        $path = base_path('phpunit.xml');
        $xml = new \SimpleXMLElement(file_get_contents($path));
        foreach ($xml->testsuites->testsuite as $testsuite) {
            $testsType = (string)$testsuite->attributes()['name'];
            $index = 0;
            $found = false;
            foreach ($testsuite->children() as $child) {
                if ((string)$child === "./modules/{$studlyName}/Tests/{$testsType}") {
                    $found = true;
                    break;
                }
                $index++;
            }

            if ($found) {
                unset($testsuite->directory[$index]);
            }
        }
        $xml->saveXML($path);
    }
}
