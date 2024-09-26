<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckBuyerOrGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Kiểm tra xem người dùng đã đăng nhập hay chưa
        if (Auth::check()) {
            $user = Auth::user();
            
            // Kiểm tra nếu người dùng có role là 'admin'
            if ($user->role === User::ADMIN) {
                return redirect()->route('admin.dashboard'); // Chuyển hướng tới trang admin
            }

            // Kiểm tra nếu người dùng có role là 'buyer'
            if ($user->role === User::BUYER) {
                return $next($request); // Tiếp tục xử lý request
            }

            // Nếu role không phải admin hoặc buyer, có thể trả về trang lỗi hoặc chuyển hướng khác
            abort(404);
        }

        // Nếu không đăng nhập, tiếp tục cho phép truy cập với vai trò khách
        return $next($request);
    }
}
