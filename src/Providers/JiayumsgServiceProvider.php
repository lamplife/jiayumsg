<?php
/**
 * Author: 狂奔的螞蟻 <www.firstphp.com>
 * Date: 2018/3/21
 * Time: 下午4:41
 */

namespace Firstphp\Jiayumsg\Providers;

use Illuminate\Support\ServiceProvider;
use Firstphp\Jiayumsg\Services\JiayumsgService;

class JiayumsgServiceProvider extends ServiceProvider
{

    protected $defer = false;

    protected $migrations = [
        'CreateNotificationSystem' => '2018_03_21_172810_create_notification_system',
        'CreateNotificationSystemMark' => '2018_03_21_172845_create_notification_system_mark',
        'CreateNotificationSystemRead' => '2018_03_21_172848_create_notification_system_read',
    ];


    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->migration();
    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('JiayumsgService', function () {
            return new JiayumsgService();
        });

    }


    /**
     * Publish migration files.
     *
     * @return void
     */
    protected function migration()
    {
        foreach ($this->migrations as $class => $file) {
            if (! class_exists($class)) {
                $this->publishMigration($file);
            }
        }
    }


    /**
     * Publish a single migration file.
     *
     * @param string $filename
     * @return void
     */
    protected function publishMigration($filename)
    {
        $extension = '.php';
        $filename = trim($filename, $extension).$extension;
        $stub = __DIR__.'/../migrations/'.$filename;
        $target = $this->getMigrationFilepath($filename);
        $this->publishes([$stub => $target], 'migrations');
    }


    /**
     * Get the migration file path.
     *
     * @param string $filename
     * @return string
     */
    protected function getMigrationFilepath($filename)
    {
        if (function_exists('database_path')) {
            return database_path('/migrations/'.$filename);
        }
        return base_path('/database/migrations/'.$filename); // @codeCoverageIgnore
    }


}
