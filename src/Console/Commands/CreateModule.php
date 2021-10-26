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

    public static function saveConfig($arr){
        $content = "<?php return [\n";
        foreach($arr as $v){
            $content .= "'".$v."',\n";
        }
        $content .= "];";
        file_put_contents(config_path('module.php'),$content);
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:create
                            {ModuleName : Указать название модуля (обязательный)}';

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
        $configModulesArray = config('module');
        if(in_array($this->argument('ModuleName'),$configModulesArray)){
            $this->error('Модуль с таким именем существует!');
        }else {

            $modulesPath = app_path('Modules');
            File::ensureDirectoryExists($modulesPath);

            $configModules = config_path('module.php');
            if(!File::exists($configModules))
                $this->call('vendor:publish',['--provider' => "Wolf1848\Module\Providers\ModulesServiceProvider"]);

            $moduleFolder = $modulesPath.'/'.$this->argument('ModuleName');
            File::ensureDirectoryExists($moduleFolder);

            foreach (self::MODULE_DIR as $dir){
                File::ensureDirectoryExists($moduleFolder.'/'.$dir);
            }
            $configModulesArray[] = $this->argument('ModuleName');

            self::saveConfig($configModulesArray);
        }






//        if(!File::exists($modulesPath.'/ModulesServiceProvider.php'))
//            copy(__DIR__.'/source/ModulesServiceProvider.copy',$modulesPath.'/ModulesServiceProvider.php');

//        $appConfig = file_get_contents(config_path('app.php'));
//        $pos = strripos($appConfig,"providers");
//        ;
//        $this->info(mb_substr($appConfig,$pos));
        //$appConfig = config('app.providers');
        //$appConfig[] = 'App\Modules\ModulesServiceProvider::class';
            //config(['app.providers' => $appConfig]);
            //config(['app.providers.module' => 'App\Modules\ModulesServiceProvider::class']);
        //$appConfig = config('app.providers');

//        $modulePath = $modulesPath.'/'.$this->argument('ModuleName');
//        File::ensureDirectoryExists($modulePath);



        //$this->info(file_exists($modulePath.'/ModulesServiceProvider.php'));
        //$message = $modulesPath."\n";
//        /Config::set('app.providers.module','App\Modules\ModulesServiceProvider::class');
        //$message .= print_r($appConfig,1);



        $this->info('Succesfull create module!');
//        $this->info(!File::exists(__DIR__.'/ModulesServiceProvider.copy'));
//        $this->info(!File::exists($modulesPath.'/ModulesServiceProvider.copy'));
        //$this->info($message);
        return 0;
    }
}
