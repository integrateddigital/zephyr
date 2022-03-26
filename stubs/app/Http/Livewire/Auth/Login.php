<?php

namespace App\Http\Livewire\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{
    public $email;
    public $password;
    public $remember = false;

    public function route()
    {
        return Route::get('/login')
            ->name('login')
            ->middleware('guest');
    }

    public function render()
    {
        return <<<'BLADE'
            @section('title', __('Login'))

            <form class="card vstack col-lg-4 mx-auto" wire:submit.prevent="login">
                <div class="card-header">
                    @yield('title')
                </div>
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <input type="email" inputmode="email" id="email" class="form-control @error('email') is-invalid @enderror" wire:model.defer="email">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Password') }}</label>
                        <input type="password" inputmode="text" id="password" class="form-control @error('password') is-invalid @enderror" wire:model.defer="password">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="remember" class="form-check-input" wire:model.defer="remember">
                        <label for="remember" class="form-check-label">{{ __('Remember me') }}</label>
                    </div>
                </div>
                <div class="card-footer hstack justify-content-end gap-2">
                    @if(Route::has('password.forgot'))
                        <a href="{{ route('password.forgot') }}" class="btn btn-link">{{ __('Forgot password?') }}</a>
                    @endif

                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" wire:loading wire:target="login"></span> {{ __('Login') }}
                    </button>
                </div>
            </form>
        BLADE;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function login()
    {
        $this->validate();
        $this->ensureIsNotRateLimited();
        $this->authenticate();

        session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function ensureIsNotRateLimited()
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            event(new Lockout(request()));

            throw ValidationException::withMessages([
                'email' => __('auth.throttle', [
                    'seconds' => RateLimiter::availableIn($this->throttleKey()),
                ]),
            ]);
        }
    }

    public function authenticate()
    {
        if (!Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function throttleKey()
    {
        return strtolower($this->email) . '|' . request()->ip();
    }
}
