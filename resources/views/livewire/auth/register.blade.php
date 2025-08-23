<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $age_verification = false;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'age_verification' => ['required', 'accepted'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Krijoni një llogari')" :description="__('Futçi detajet tuaja më poshtë për me kriju llogarinë tuaj')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('Emri')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Emri i plotë')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Adresa e email-it')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Fjalëkalimi')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Fjalëkalimi')"
            viewable
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Konfirmoni fjalëkalimin')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Konfirmoni fjalëkalimin')"
            viewable
        />

        <!-- Age Verification -->
        <div class="flex items-center space-x-2">
            <flux:checkbox wire:model="age_verification" required />
            <label class="text-sm text-gray-600">
                {{ __('arcade.age_verification') }}
            </label>
        </div>

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Krijoni llogarinë') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600">
        <span>{{ __('Keni tashë llogari?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Hyni') }}</flux:link>
    </div>
</div>
