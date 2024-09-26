<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra xem người dùng đã đăng nhập hay chưa
        if (Auth::check()) {
            $user = Auth::user();
            
            // Kiểm tra nếu người dùng có role là 'admin'
            if ($user->role === User::ADMIN) {
                return $next($request); // Tiếp tục xử lý request
            }

            // Nếu role không phải admin, chuyển hướng tới trang chủ với thông báo lỗi
            return redirect()->route('home')->with('error', 'You do not have admin access.');
        }

        // Nếu không đăng nhập, chuyển hướng tới trang đăng nhập
        return redirect('/login')->with('error', 'Please login to access this page.');
    }
}