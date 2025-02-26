<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // Thêm phương thức này để ghi đè hành vi mặc định
    protected function attemptLogin(Request $request)
    {
        // Thử đăng nhập với thông tin từ form
        $credentials = $this->credentials($request);
        $remember = $request->filled('remember');

        // Hiển thị thông tin debug (xóa sau khi khắc phục)
        // \Log::info('Login attempt', ['email' => $credentials['email']]);

        return $this->guard()->attempt(
            $credentials, $remember
        );
    }

    // Tùy chỉnh thông báo lỗi
    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => [trans('auth.failed')],
            ]);
    }
}