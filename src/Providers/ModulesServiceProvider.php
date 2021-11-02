<?php namespace Wolf1848\Module\Providers;

use Wolf1848\Module\Console\Commands;

//https://web-programming.com.ua/realizaciya-modulnoj-struktury-v-laravel/

/** * Сервис провайдер для подключения модулей */
class ModulesServiceProvider extends \Illuminate\Support\ServiceProvider {
    public function boot() {


        //Добавляем консольные команды
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\CreateModule::class,
                Commands\ModuleMigrate::class,
                Commands\ModuleModel::class,
                Commands\ModuleMigration::class,
                Commands\ModuleController::class,
            ]);
        }
        //Публикуются конфиги
        $this->publishes([
            __DIR__.'/../config/module.php' => config_path('module.php'),
        ]);

        //получаем список модулей, которые надо подгрузить
        $modules = config("module");
        $modulesPath = app_path('Modules');
        if($modules) {
            foreach ($modules as $module) {
                $dir = $modulesPath . '/' . $module;
                //Подключаем роуты для модуля
                if (file_exists($dir . '/Routes/routes.php')) {
                    $this->loadRoutesFrom($dir . '/Routes/routes.php');
                }
                //Загружаем View
                if (is_dir($dir . '/Views')) {
                    $this->loadViewsFrom($dir . '/Views', $module);
                }
                //Подгружаем миграции
                //if(is_dir($dir.'/Migration')) {
                //$this->loadMigrationsFrom($dir.'/Migration');
                //}
                //Подгружаем переводы
                if (is_dir($dir . '/Lang')) {
                    $this->loadTranslationsFrom($dir . '/Lang', $module);
                }
            }
        }
    }

    public function register(){

    }
}
