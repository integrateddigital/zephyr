<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Route;
use Livewire\Component;

class Home extends Component
{
    public function route()
    {
        return Route::get('/home')
            ->name('home')
            ->middleware('auth');
    }

    public function render()
    {
        return <<<'BLADE'
            @section('title', __('Home'))

            <div class="card vstack col-lg-4 mx-auto">
                <div class="card-header">
                    @yield('title')
                </div>
                <div class="card-body">
                    {{ __('You are logged in!') }}
                </div>
            </div>
        BLADE;
    }
}
