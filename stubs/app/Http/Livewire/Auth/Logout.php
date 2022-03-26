<?php

namespace App\Http\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class Logout extends Component
{
    public function route()
    {
        return Route::get('/logout')
            ->name('logout')
            ->middleware('auth');
    }

    public function mount()
    {
        Auth::guard('web')->logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->to('/');
    }
}
