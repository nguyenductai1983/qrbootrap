<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SidebarNavigation extends Component
{
    public $menu = null;

    public function mount()
    {
        // Lấy đường dẫn URL hiện tại (ví dụ: "users/create" hoặc "dashboard")
        // request()->path() sẽ trả về đường dẫn không bao gồm domain và dấu '/' đầu tiên
        $currentPath = request()->path();
        $this->menu = $currentPath;
        // Xác định giá trị cho biến $menu dựa trên đường dẫn URL hiện tại

        // Thêm các điều kiện elseif cho các nhóm menu cha khác nếu có
        // Ví dụ:
        // elseif (Str::startsWith($currentPath, 'reports')) {
        //     $this->menu = '4.0'; // Nhóm báo cáo
        // }
    }
    public function render()
    {
        return view('livewire.layout.sidebar-navigation');
    }
}
