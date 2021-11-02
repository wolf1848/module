<?php

namespace Wolf1848\Module\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModuleController extends Command
{

    protected $moduleName;
    protected $controllersPath;
    protected $controllerName;

    public function addController(){
        $content = File::get(__DIR__.'/stubs/controller.stub');


        $replace = [
            '{{ namespace }}' => "App\Modules\\".$this->moduleName.'\Controllers;',
            '{{ model }}' => "App\Modules\\".$this->moduleName.'\Model',
            '{{ class }}' => $this->controllerName.'Controller',
        ];

        foreach ($replace as $search => $item) {
            $content = str_replace(
                $search,
                $item,
                $content
            );
        }

        File::put($this->modelsPath.'/'.$this->controllerName.'Controller.php',$content);
        $this->info('Контроллер '.$this->controllerName.' создан');

    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:controller
                            {ModuleName : Указать название модуля (обязательный)}
                            {ControllerName : Указать название контроллера (обязательный)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        $this->moduleName = $this->argument('ModuleName');
        $this->controllerName = $this->argument('ControllerName');
        $this->controllersPath = app_path('Modules').'/'.$this->moduleName.'/Controllers';

        $this->addController();

        return 0;
    }
}
