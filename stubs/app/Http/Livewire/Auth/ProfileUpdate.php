<?php

namespace App\Http\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProfileUpdate extends Component
{
    public $name;
    public $email;

    public function route()
    {
        return Route::get('/update-profile')
            ->name('profile.update')
            ->middleware('auth');
    }

    public function mount()
    {
        $this->fill(Auth::user()->toArray());
    }

    public function render()
    {
        return <<<'BLADE'
            @section('title', __('Update Profile'))

            <form class="card vstack col-lg-4 mx-auto" wire:submit.prevent="updateProfile">
                <div class="card-header">
                    @yield('title')
                </div>
                <div class="card-body">
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Name') }}</label>
                        <input type="text" inputmode="text" id="name" class="form-control @error('name') is-invalid @enderror" wire:model.defer="name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <input type="email" inputmode="email" id="email" class="form-control @error('email') is-invalid @enderror" wire:model.defer="email">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="card-footer hstack justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <span class="spinner-border spinner-border-sm" wire:loading wire:target="updateProfile"></span> {{ __('Update Profile') }}
                    </button>
                </div>
            </form>
        BLADE;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignoreModel(Auth::user())],
        ];
    }

    public function updateProfile()
    {
        $this->validate();

        Auth::user()->update($this->all());

        session()->flash('status', __('Your profile has been updated!'));

        $this->emitTo('layouts.navbar', '$refresh');
    }
}
