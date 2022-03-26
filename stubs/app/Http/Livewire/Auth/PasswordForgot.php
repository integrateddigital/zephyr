<?php

namespace App\Http\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PasswordForgot extends Component
{
    public $email;

    public function route()
    {
        return Route::get('/forgot-password')
            ->name('password.forgot')
            ->middleware('guest');
    }

    public function render()
    {
        return <<<'BLADE'
            @section('title', __('Forgot Password'))

            <form class="card vstack col-lg-4 mx-auto" wire:submit.prevent="emailLink">
                <div class="card-header">
                    @yield('title')
                </div>
                <div class="card-body">
                    <p>{{ __('Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</p>

                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <div>
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <input type="email" inputmode="email" id="email" class="form-control @error('email') is-invalid @enderror" wire:model.defer="email">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="card-footer hstack justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" wire:loading wire:target="emailLink"></span> {{ __('Email Password Reset Link') }}
                    </button>
                </div>
            </form>
        BLADE;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public function emailLink()
    {
        $this->validate();

        $status = Password::sendResetLink($this->all());

        if ($status != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        session()->flash('status', __($status));
    }
}
