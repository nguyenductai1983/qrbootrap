<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Đăng ký người dùng mới.
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed', // 'confirmed' yêu cầu trường password_confirmation
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Lỗi xác thực dữ liệu đầu vào.',
                'errors' => $e->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Tạo token cho người dùng vừa đăng ký (tùy chọn)
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng ký thành công!',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Đăng nhập người dùng.
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Lỗi xác thực dữ liệu đầu vào.',
                'errors' => $e->errors()
            ], 422);
        }

        // Thử xác thực bằng email và password
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email hoac mat khau khong dung.',
            ], 401);
        }

        // Lấy người dùng đã xác thực
        $user = $request->user();

        // Xóa tất cả các token hiện có của người dùng này (tùy chọn, để đảm bảo mỗi lần đăng nhập là một token mới)
        // $user->tokens()->delete();

        // Tạo token mới cho người dùng
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Đăng xuất người dùng (thu hồi token hiện tại).
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request...
        // $request->user()->currentAccessToken()->delete(); // Thu hồi token hiện tại

        // Revoke ALL tokens for the current user
        // $request->user()->tokens()->delete();
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Đăng xuất thành công!',
        ], 200);
    }
public function logoutAll(Request $request)
    {
        // Thu hồi tất cả các token của người dùng
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Đăng xuất khỏi tất cả thiết bị thành công.']);
    }
    /**
     * Lấy thông tin người dùng đã xác thực.
     */
    public function user(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ], 200);
    }
}
