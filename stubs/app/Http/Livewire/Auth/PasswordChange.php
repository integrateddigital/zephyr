<?php

namespace App\Http\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class PasswordChange extends Component
{
    public $current;
    public $password;
    public $password_confirmation;

    public function route()
    {
        return Route::get('/change-password')
            ->name('password.change')
            ->middleware('auth');
    }

    public function render()
    {
        return <<<'BLADE'
            @section('title', __('Change Password'))

            <form class="card vstack col-lg-4 mx-auto" wire:submit.prevent="changePassword">
                <div class="card-header">
                    @yield('title')
                </div>
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <div class="mb-3">
                        <label for="current" class="form-label">{{ __('Current Password') }}</label>
                        <input type="password" inputmode="text" id="current" class="form-control @error('current') is-invalid @enderror" wire:model.defer="current">
                        @error('current') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                        <span class="spinner-border spinner-border-sm" wire:loading wire:target="changePassword"></span> {{ __('Change Password') }}
                    </button>
                </div>
            </form>
        BLADE;
    }

    public function rules()
    {
        return [
            'current' => ['required', 'current_password'],
            'password' => ['required', 'confirmed'],
        ];
    }

    public function changePassword()
    {
        $this->validate();

        Auth::user()->update(['password' => Hash::make($this->password)]);

        session()->flash('status', __('Your password has been changed!'));

        $this->reset();
    }
}
