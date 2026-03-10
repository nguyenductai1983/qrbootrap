<?php

namespace App\Providers;

// use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider ;
use App\Models\User; // Import User Model của bạn

class GateServiceProvider extends AuthServiceProvider
{
    protected $policies = [
        // Các Policy của bạn sẽ được đăng ký ở đây
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
                //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
         Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
         });
         Gate::define('user', function (User $user) {
            if ($user->role === 'admin') {
                return true; // Admin có thể xem tất cả các loại báo cáo
            }
            return $user->role === 'user'; // Editor chỉ xem báo cáo sales
        });

         Gate::before(function (User $user, string $ability) {
            if ($user->role === 'sadmin') {
                return true; // Người dùng 'super-admin' có toàn quyền
            }
            return null; // Tiếp tục kiểm tra quyền cụ thể
        });
        //
    }
}
