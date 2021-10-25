<?php namespace Wolf1848\Module\Providers;

use Wolf1848\Module\Console\Commands\CreateModule;

//https://web-programming.com.ua/realizaciya-modulnoj-struktury-v-laravel/

/** * Сервис провайдер для подключения модулей */
class ModulesServiceProvider extends \Illuminate\Support\ServiceProvider {
    public function boot() {


        //Добавляем консольные команды
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateModule::class,
            ]);
        }
        //Публикуются конфиги
        $this->publishes([
            __DIR__.'/../config/module.php' => config_path('module.php'),
        ]);

        //получаем список модулей, которые надо подгрузить
/*        $modules = config("module.modules");
        if($modules) {
            foreach ($modules as $module){
                //Подключаем роуты для модуля
                if(file_exists(__DIR__.'/'.$module.'/Routes/routes.php')) {
                    $this->loadRoutesFrom(__DIR__.'/'.$module.'/Routes/routes.php');
                }
                //Загружаем View
                if(is_dir(__DIR__.'/'.$module.'/Views')) {
                    $this->loadViewsFrom(__DIR__.'/'.$module.'/Views', $module);
                }
                //Подгружаем миграции
                //if(is_dir(__DIR__.'/'.$module.'/Migration')) {
                    //$this->loadMigrationsFrom(__DIR__.'/'.$module.'/Migration');
                //}
                //Подгружаем переводы
                if(is_dir(__DIR__.'/'.$module.'/Lang')) {
                    $this->loadTranslationsFrom(__DIR__.'/'.$module.'/Lang', $module);
                }
            }
        }*/
    }

    public function register(){

    }
}
