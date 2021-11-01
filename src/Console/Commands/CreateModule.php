<?php

namespace Wolf1848\Module\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Config\Repository;

class CreateModule extends Command
{
    const MODULE_DIR = [
        'Controllers',
        'Files',
        'Migration',
        'Model',
        'Routes',
    ];
    protected $configModulesArray;
    protected $moduleName;
    protected $modulesPath;
    protected $moduleFolder;


    public function saveConfig(){
        $content = "<?php return [\n";
        foreach($this->configModulesArray as $v){
            $content .= "\t'".$v."',\n";
        }
        $content .= "];";
        file_put_contents(config_path('module.php'),$content);
    }

    public function addRoute(){
        $content = "<?php";
        $content .= "\n" . "use Illuminate\Support\Facades\Route";
        $content .= "\n" . "use App\Modules\\".$this->moduleName."\Controllers;" . "\n";
        $content .= "\n";
        $content .= "Route::group(['prefix' => 'api','middleware' => [/*'через запятую','сколько надо'*/]], function () {" ."\n";
        $content .= "\t"."Route::post('/post', Controllers\ClassController::class . '@funcController');" ."\n";
        $content .= "\t"."Route::get('/get', function(){return response()->json([1,2])})"."\n";
        $content .= "});";
        file_put_contents($this->moduleFolder,$content);
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:create
                            {ModuleName : Указать название модуля (обязательный)}
                            {--reset : Переустановить модуль}
                            {--remove : Удалить модуль}';

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

    protected function remove(){
        if(in_array($this->moduleName,$this->configModulesArray) && is_dir($this->moduleFolder)){
            File::deleteDirectories($this->moduleFolder);
            File::deleteDirectory($this->moduleFolder);
            unset($this->configModulesArray[array_search($this->moduleName,$this->configModulesArray)]);
            $this->configModulesArray = array_values($this->configModulesArray);
            $this->saveConfig();
            $this->info('Модуль '.$this->moduleName.' удален');
        }else
            $this->info('Модуль '.$this->moduleName.' не найден');
    }

    protected function create(){
        if(in_array($this->moduleName,$this->configModulesArray)){
            $this->error('Модуль '.$this->moduleName.' существует!');
        }else {

            $this->modulesPath = app_path('Modules');
            File::ensureDirectoryExists($this->modulesPath);

            $configModules = config_path('module.php');
            if(!File::exists($configModules))
                $this->call('vendor:publish',['--provider' => "Wolf1848\Module\Providers\ModulesServiceProvider"]);

            $this->moduleFolder = $this->modulesPath.'/'.$this->moduleName;
            File::ensureDirectoryExists($this->moduleFolder);

            foreach (self::MODULE_DIR as $dir){
                File::ensureDirectoryExists($this->moduleFolder.'/'.$dir);
            }
            $this->configModulesArray[] = $this->moduleName;

            $this->saveConfig();
            $this->addRoute();

            $this->info('Модуль '.$this->moduleName.' успешно создан');
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() : int
    {
        $this->configModulesArray = config('module');
        $this->moduleName = $this->argument('ModuleName');
        $this->modulesPath = app_path('Modules');
        $this->moduleFolder = $this->modulesPath.'/'.$this->moduleName;

        if($this->option('remove')){
            $this->remove();
        }elseif($this->option('reset')){
            $this->remove();
            $this->create();
        }else{
            $this->create();
        }

        return 0;
    }
}
