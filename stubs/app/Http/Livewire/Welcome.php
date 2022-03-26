<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Route;
use Livewire\Component;

class Welcome extends Component
{
    public function route()
    {
        return Route::get('/')
            ->name('welcome');
    }

    public function render()
    {
        return <<<'BLADE'
            @section('title', __('Welcome'))

            <div class="card vstack col-lg-4 mx-auto">
                <div class="card-header">
                    @yield('title')
                </div>
                <div class="card-body">
                    {{ __('Welcome to :app_name!', ['app_name' => config('app.name')]) }}
                </div>
            </div>
        BLADE;
    }
}
