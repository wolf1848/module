<?php

namespace Wolf1848\Module\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModuleMigration extends Command
{

    protected $moduleName;
    protected $migrationPath;
    protected $migrationName;

    public function addMigration(){
        if($this->option('update'))
            $content = File::get(__DIR__.'/stubs/migration_update.stub');
        else
            $content = File::get(__DIR__.'/stubs/migration.stub');

        $tableArr = str_split($this->migrationName,1);
        $tableName = '';
        foreach ($tableArr as $el){
            $tableName .= ctype_upper($el) ? '_'.strtolower($el) :  $el;
        }

        $replace = [
            '{{ class }}' => $this->migrationName,
            '{{ table }}' => 'l_'.strtolower($this->moduleName).$tableName,
        ];

        foreach ($replace as $search => $item) {
            $content = str_replace(
                $search,
                $item,
                $content
            );
        }

        File::put($this->migrationPath.'/'.Carbon::now()->format('d_m_Y_H_i_s').'_'.strtolower($this->migrationName).'.php',$content);
        $this->info('Модель '.$this->migrationName.' создана');
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:migration
                            {ModuleName : Указать название модуля (обязательный)}
                            {MigrationName : Указать название миграции (обязательный)}
                            {--update : Обновить таблицу}';

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
        $this->migrationName = $this->argument('MigrationName');
        $this->migrationPath = app_path('Modules').'/'.$this->moduleName.'/Migration';

        $this->addMigration();

        return 0;
    }
}
