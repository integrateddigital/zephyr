<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class VerificationVerify extends Component
{
    public $verified = false;

    public function route()
    {
        return Route::get('/verify-email/{id}/{hash}')
            ->name('verification.verify')
            ->middleware('signed');
    }

    public function mount($id, $hash)
    {
        $user = User::findOrFail($id);
     
        if (!$user->hasVerifiedEmail() && hash_equals($hash, sha1($user->getEmailForVerification()))) {
            $user->markEmailAsVerified();

            event(new Verified($user));

            $this->verified = true;
        }
    }

    public function render()
    {
        return <<<'BLADE'
            @section('title', __('Verify Email'))

            <div class="card vstack col-lg-4 mx-auto">
                <div class="card-header">
                    @yield('title')
                </div>
                <div class="card-body">
                    @if($verified)
                        <p class="mb-0">{{ __('Thanks for verifying your email address with us! Your account is now verified.') }}</p>
                    @else
                        <p class="mb-0">{{ __('Sorry, this email verification link has either expired or is invalid.') }}</p>
                    @endif
                </div>
                <div class="card-footer hstack justify-content-end">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-link">{{ __('Login') }}</a>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-link">{{ __('Home') }}</a>
                    @endguest
                </div>
            </div>
        BLADE;
    }
}
