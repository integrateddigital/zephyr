<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Lukeraymonddowning\Honey\Traits\WithHoney;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Register extends Component
{
    use WithHoney;

    public $name;
    public $email;
    public $password;
    public $password_confirmation;

    public function route()
    {
        return Route::get('/register')
            ->name('register')
            ->middleware('guest');
    }

    public function render()
    {
        return <<<'BLADE'
            @section('title', __('Register'))

            <form class="card vstack col-lg-4 mx-auto" wire:submit.prevent="register">
                <div class="card-header">
                    @yield('title')
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Name') }}</label>
                        <input type="text" inputmode="text" id="name" class="form-control @error('name') is-invalid @enderror" wire:model.defer="name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

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

                    <div>
                        <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                        <input type="password" inputmode="text" id="password_confirmation" class="form-control" wire:model.defer="password_confirmation">
                    </div>

                    <x-honey/>
                </div>
                <div class="card-footer hstack justify-content-end gap-2">
                    <a href="{{ route('login') }}" class="btn btn-link">{{ __('Already registered?') }}</a>

                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" wire:loading wire:target="register"></span> {{ __('Register') }}
                    </button>
                </div>
            </form>
        BLADE;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed'],
        ];
    }

    public function register()
    {
        $this->validate();
        $this->ensureHoneyPasses();
        $this->registerUser();

        return redirect()->to(RouteServiceProvider::HOME);
    }

    public function ensureHoneyPasses()
    {
        if (!$this->honeyPasses()) {
            throw new HttpException(403);
        }
    }

    public function registerUser()
    {
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        event(new Registered($user));

        Auth::login($user);
    }
}
