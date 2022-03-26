<?php

namespace App\Http\Livewire\Layouts;

use Livewire\Component;

class Navbar extends Component
{
    protected $listeners = ['$refresh'];

    public function render()
    {
        return <<<'BLADE'
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container">
                    <a href="{{ url('/') }}" class="navbar-brand d-flex">
                        <img src="{{ mix('images/logo.png') }}" alt="{{ config('app.name') }}" height="24">
                    </a>

                    <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div id="nav" class="collapse navbar-collapse">
                        <div class="navbar-nav ms-auto">
                            @guest
                                @if(Route::has('login'))
                                    <a href="{{ route('login') }}" class="nav-link @if(Route::is('login')) active @endif">{{ __('Login') }}</a>
                                @endif

                                @if(Route::has('register'))
                                    <a href="{{ route('register') }}" class="nav-link @if(Route::is('register')) active @endif">{{ __('Register') }}</a>
                                @endif
                            @else
                                <a href="{{ route('home') }}" class="nav-link @if(Route::is('home')) active @endif">{{ __('Home') }}</a>

                                <div class="nav-item dropdown">
                                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                        {{ Auth::user()->name }}
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a href="{{ route('profile.update') }}" class="dropdown-item @if(Route::is('profile.update')) active @endif">
                                            {{ __('Update Profile') }}
                                        </a>

                                        <a href="{{ route('password.change') }}" class="dropdown-item @if(Route::is('password.change')) active @endif">
                                            {{ __('Change Password') }}
                                        </a>

                                        <a href="{{ route('logout') }}" class="dropdown-item">{{ __('Logout') }}</a>
                                    </div>
                                </div>
                            @endguest
                        </div>
                    </div>
                </div>
            </nav>
        BLADE;
    }
}
