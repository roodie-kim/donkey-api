<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Output\ConsoleOutput;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('APP_ENV') === 'local') {

            $queryCount = collect([]);

            DB::listen(function ($query) use ($queryCount) {
                $queryCount->push($query);
                $output = new ConsoleOutput();
                $output->writeln($queryCount->count());
            });
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
