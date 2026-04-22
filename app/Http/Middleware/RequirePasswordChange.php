<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RequirePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $expireDays = config('auth.password_expire_days', 90);
            
            $needsChange = false;
            
            // Ép buộc đổi mật khẩu
            if ($user->force_password_change) {
                $needsChange = true;
            } 
            // Hết hạn mật khẩu
            elseif ($expireDays > 0) {
                $lastChanged = $user->password_changed_at;
                if (!$lastChanged || $lastChanged->copy()->addDays($expireDays)->isPast()) {
                    $needsChange = true;
                }
            }

            if ($needsChange) {
                $allowedRoutes = [
                    'password.force-change', 
                    'password.update-force', 
                    'logout'
                ];
                
                $isLivewireUpdateFromForceChange = $request->routeIs('livewire.update') 
                    && str_contains(parse_url($request->headers->get('referer'), PHP_URL_PATH), '/thay-doi-mat-khau-bat-buoc');

                if (!$request->routeIs($allowedRoutes) && !$isLivewireUpdateFromForceChange) {
                    return redirect()->route('password.force-change');
                }
            }
        }

        return $next($request);
    }
}
