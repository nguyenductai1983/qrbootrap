<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Livewire\Actions\Logout as LogoutAction; // Alias the Action class
class LogoutButton extends Component
{
    public function LogoutAction(LogoutAction $logout)
    {
        $logout(); // Gọi action
        return $this->redirect('/', navigate: true); // Chuyển hướng đến trang chủ và dùng Livewire's navigate nếu muốn
    }
    public function render()
    {
        return view('livewire.auth.logout-button');
    }
}
