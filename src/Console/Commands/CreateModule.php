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



//        $modulesPath = app_path('Modules');
//        File::ensureDirectoryExists($modulesPath);

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
