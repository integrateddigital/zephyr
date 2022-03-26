<?php

namespace App\Http\Livewire\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class PasswordConfirm extends Component
{
    public $password;

    public function route()
    {
        return Route::get('/confirm-password')
            ->name('password.confirm')
            ->middleware('auth');
    }

    public function render()
    {
        return <<<'BLADE'
            @section('title', __('Confirm Password'))

            <form class="card vstack col-lg-4 mx-auto" wire:submit.prevent="confirmPassword">
                <div class="card-header">
                    @yield('title')
                </div>
                <div class="card-body">
                    <p>{{ __('This is a secure area of the application. Please confirm your current password before continuing.') }}</p>

                    <div>
                        <label for="password" class="form-label">{{ __('Current Password') }}</label>
                        <input type="password" inputmode="text" id="password" class="form-control @error('password') is-invalid @enderror" wire:model.defer="password">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="card-footer hstack justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" wire:loading wire:target="confirmPassword"></span> {{ __('Confirm Password') }}
                    </button>
                </div>
            </form>
        BLADE;
    }

    public function rules()
    {
        return [
            'password' => ['required', 'current_password'],
        ];
    }

    public function confirmPassword()
    {
        $this->validate();

        session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
