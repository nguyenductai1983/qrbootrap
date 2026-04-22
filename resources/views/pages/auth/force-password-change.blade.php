<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ], [
                'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
                'current_password.current_password' => 'Mật khẩu hiện tại không đúng.',
                'password.required' => 'Vui lòng nhập mật khẩu mới.',
                'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            ]);
        } catch (ValidationException $e) {
            $this->reset('password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
            'force_password_change' => false,
            'password_changed_at' => now(),
        ]);

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        $this->redirect('/');
    }
}; ?>

<x-guest-layout>
    <div class="mb-6 text-sm text-red-700 font-medium bg-red-50 p-4 rounded-lg border border-red-200">
        {{ __('Vì lý do bảo mật hoặc mật khẩu của bạn đã quá hạn, vui lòng cập nhật mật khẩu mới trước khi tiếp tục truy cập hệ thống.') }}
    </div>

    <form wire:submit="updatePassword" class="space-y-6">
        <div>
            <x-input-label for="current_password" :value="__('Mật khẩu hiện tại')" />
            <x-text-input wire:model="current_password" id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Mật khẩu mới')" />
            <x-text-input wire:model="password" id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Xác nhận mật khẩu mới')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <button type="button" wire:click="logout" class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 underline">
                Đăng xuất
            </button>
            <x-primary-button>
                {{ __('Cập nhật mật khẩu') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
