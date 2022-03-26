<?php

namespace App\Http\Livewire\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class VerificationNotice extends Component
{
    public function route()
    {
        return Route::get('/verify-email')
            ->name('verification.notice')
            ->middleware('auth');
    }

    public function mount()
    {
        $this->ensureEmailIsUnverified();
    }

    public function render()
    {
        return <<<'BLADE'
            @section('title', __('Verify Email'))

            <form class="card vstack col-lg-4 mx-auto" wire:submit.prevent="resendEmail">
                <div class="card-header">
                    @yield('title')
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ __('Thanks for signing up! Could you verify your email address by clicking on the link we just emailed to you?') }}</p>

                    @if(session('status'))
                        <div class="alert alert-success mt-3 mb-0">{{ session('status') }}</div>
                    @endif

                    @error('email')
                        <div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>
                    @enderror
                </div>
                <div class="card-footer hstack justify-content-end gap-2">
                    <a href="{{ route('logout') }}" class="btn btn-link">{{ __('Logout') }}</a>

                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" wire:loading wire:target="resendEmail"></span> {{ __('Resend Verification Email') }}
                    </button>
                </div>
            </form>
        BLADE;
    }

    public function resendEmail()
    {
        $this->ensureEmailIsUnverified();
        $this->ensureIsNotRateLimited();

        Auth::user()->sendEmailVerificationNotification();

        session()->flash('status', __('A new verification link has been sent!'));
    }

    public function ensureEmailIsUnverified()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }
    }

    public function ensureIsNotRateLimited()
    {
        $throttleKey = Auth::user()->getEmailForVerification() . '|verify';

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => __('Too many email resend requests. Please try again in :seconds seconds.', [
                    'seconds' => RateLimiter::availableIn($throttleKey),
                ]),
            ]);
        }

        RateLimiter::hit($throttleKey);
    }
}
