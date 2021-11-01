<?php

namespace Wolf1848\Module\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class ModuleMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:migrate
                            {ModuleName : Указать название модуля (обязательный)}
                            {--reset : Переустановить таблицы}
                            {--remove : Удалить таблицы включая память миграций}
                            {--back= : Откатить миграцию на n шагов}';

    protected $table_name;
    protected $moduleName;
    protected $migrationPath;
    protected $moduleFolder;


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migration module "ModuleName"';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    protected function downMigration(){
        if(Schema::hasTable($this->table_name)){

            $collection = DB::table($this->table_name)
                ->select('id','migration')
                ->orderBy('id','desc')
                ->get();
            foreach ($collection as $el){
                $classes = get_declared_classes();
                include $this->migrationPath.'/'.$el->migration;
                $diff = array_diff(get_declared_classes(), $classes);
                foreach ($diff as $class){
                    if(false === strripos($class,'\\')) {
                        $obj = new $class();
                        $obj->down();
                    }
                }
                $this->info($el->migration);
            }

            Schema::dropIfExists($this->table_name);

            $this->info('Удаление таблиц модуля '.$this->moduleName.' выполнено!');
        }else
            $this->info('В модуле '.$this->moduleName.' таблиц для удаления не найдено!');
    }

    protected function upMigration(){
        if(!Schema::hasTable($this->table_name)){
            Schema::create($this->table_name, function (Blueprint $table) {
                $table->id();
                $table->string('migration');
                $table->integer('batch')->unsigned();
            });
        }
        $batch = 1;
        $collection = DB::table($this->table_name)->get();
        $arrFilesData = ['.','..'];
        foreach ($collection as $el){
            if($el->batch >= $batch)
                $batch = $el->batch+1;
            $arrFilesData[] = $el->migration;
        }


        $files = array_diff(scandir($this->migrationPath),$arrFilesData);
        $migrationFile = [];
        foreach ($files as $file) {

            $info = new \SplFileInfo($this->migrationPath.'/'.$file);
            if($info->getExtension() == 'php') {
                $migrationFile[] = $file;
                $classes = get_declared_classes();
                include $this->migrationPath . '/' . $file;
                $diff = array_diff(get_declared_classes(), $classes);
                foreach ($diff as $class){
                    if(false === strripos($class,'\\')) {
                        $obj = new $class();
                        $obj->up();
                        DB::table($this->table_name)->insert(['migration' => $file, 'batch' => $batch]);
                        $this->info($file);
                    }
                }

            }
        }
        if(empty($migrationFile))
            $this->info('Миграций для модуля '.$this->moduleName.' не найдено!');
        else
            $this->info('Миграции для модуля '.$this->moduleName.' выполнены!');
    }

    protected function backMigration(){
        $back = (int)$this->option('back');
        while($back > 0){

            $tableMigration = DB::table($this->table_name);
            $batch = $tableMigration->max('batch');
            $collection = DB::table($this->table_name)
                ->select('id','migration')
                ->where('batch',$batch)
                ->get()
                ->each(function($item,$key){
                    $classes = get_declared_classes();
                    include $this->migrationPath.'/'.$item->migration;
                    $diff = array_diff(get_declared_classes(), $classes);
                    foreach ($diff as $class){
                        if(false === strripos($class,'\\')) {
                            $fileClasses[$item->migration] = $class;
                            $obj = new $class();
                            $obj->down();
                        }
                    }
                    DB::table($this->table_name)->delete($item->id);
                    $this->info("Выполнен откат : ".$item->migration);
                });
            $back--;
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->moduleName = $this->argument('ModuleName');
        $this->migrationPath = app_path('Modules').'/'.$this->moduleName.'/Migration';
        $this->table_name = strtolower('l_'.$this->moduleName.'_migration');

        if($this->option('remove'))
            $this->downMigration();
        elseif($this->option('reset')){
            $this->downMigration();
            $this->upMigration();
        }elseif($this->option('back') > 0)
            $this->backMigration();
        else
            $this->upMigration();

        return 0;
    }
}
