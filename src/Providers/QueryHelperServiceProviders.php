<?php

namespace SOS\QueryHelper\Providers;

use Illuminate\Support\ServiceProvider;
use SOS\QueryHelper\Classes\QueryHelper;
use SOS\QueryHelper\Commands\InstallCommand;

/**
 * Class QueryHelperServiceProviders
 *
 * @author karam mustafa
 * @package SOS\QueryHelper\Providers
 */
class QueryHelperServiceProviders extends ServiceProvider
{
    /**
     *
     *
     * @author karam mustafa
     */
    public function boot()
    {
        $this->registerFacades();
        $this->publishesPackages();
        $this->resolveCommands();
    }

    /**
     *
     *
     * @author karam mustafa
     */
    public function register()
    {
    }

    /**
     *
     */
    protected function registerFacades()
    {
        $this->app->singleton("QueryHelperFacade", function ($app) {
            return new QueryHelper();
        });
    }

    /**
     * @desc publish files
     * @author karam mustafa
     */
    protected function publishesPackages()
    {
        $this->publishes([
            __DIR__."/../Config/query_helper.php" => config_path("query_helper.php"),
        ], "query-helper-config");
    }

    /**
     *
     *
     * @author karam mustafa
     */
    private function resolveCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}
