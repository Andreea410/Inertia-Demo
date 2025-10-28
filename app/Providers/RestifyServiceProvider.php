<?php

namespace App\Providers;

use Binaryk\LaravelRestify\RestifyApplicationServiceProvider;

class RestifyServiceProvider extends RestifyApplicationServiceProvider
{

    public function boot(): void
    {
        $this->authorization();
    }


    protected function repositories(): void
    {
    }


    protected function routes(): void
    {
    }


    protected function singleton(): void
    {
    }
}
