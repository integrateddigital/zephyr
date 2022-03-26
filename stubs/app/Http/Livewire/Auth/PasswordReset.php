<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PasswordReset extends Component
{
    public $token;
    public $email;
    public $password;
    public $password_confirmation;

    public function route()
    {
        return Route::get('/reset-password/{token}/{email}')
            ->name('password.reset')
            ->middleware('guest');
    }

    public function mount($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function render()
    {
        return <<<'BLADE'
            @section('title', __('Reset Password'))

            <form class="card vstack col-lg-4 mx-auto" wire:submit.prevent="resetPassword">
                <div class="card-header">
                    @yield('title')
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <input type="email" inputmode="email" id="email" class="form-control @error('email') is-invalid @enderror" wire:model.defer="email">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('New Password') }}</label>
                        <input type="password" inputmode="text" id="password" class="form-control @error('password') is-invalid @enderror" wire:model.defer="password">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                        <input type="password" inputmode="text" id="password_confirmation" class="form-control" wire:model.defer="password_confirmation">
                    </div>
                </div>
                <div class="card-footer hstack justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" wire:loading wire:target="resetPassword"></span> {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        BLADE;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed'],
        ];
    }

    public function resetPassword()
    {
        $this->validate();

        $status = Password::reset($this->all(), function (User $user) {
            $user->update([
                'password' => Hash::make($this->password),
                'remember_token' => Str::random(60),
            ]);

            event(new PasswordReset($user));
        });

        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }
        
        return redirect()->route('login')->with('status', __($status));
    }
}
