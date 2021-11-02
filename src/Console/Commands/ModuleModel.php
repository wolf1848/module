<?php

namespace Wolf1848\Module\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModuleModel extends Command
{

    protected $moduleName;
    protected $modelsPath;
    protected $modelName;

    public function addModel(){
        $content = File::get(__DIR__.'/stubs/model.stub');
        $tableArr = explode('',$this->modelName);
        $tableName = '';
        foreach ($tableArr as $el){
            $tableName .= ctype_upper($el) ? '_'.strtolower($el) :  $el;
        }

        $replace = [
          '{{ namespace }}' => "App\Modules\\".$this->moduleName.'\Model;',
          '{{ class }}' => $this->modelName,
          '{{ table }}' => 'l_'.strtolower($this->moduleName).$tableName,
        ];

        foreach ($replace as $search => $item) {
            $content = str_replace(
                $search,
                $item,
                $content
            );
        }

        File::put($this->modelsPath.'/'.$this->modelName.'.php',$content);
        $this->info('Модель '.$this->modelName.' создана');
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:model
                            {ModuleName : Указать название модуля (обязательный)}
                            {ModelName : Указать название модели (обязательный)}';

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
        $this->modelName = $this->argument('ModelName');
        $this->modelsPath = app_path('Modules').'/'.$this->moduleName.'/Model';

        $this->addModel();

        return 0;
    }
}
