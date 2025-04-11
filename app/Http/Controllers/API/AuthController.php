<?php

namespace App\Http\Controllers\API;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->ErrorResponse('Unauthorized', 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->SuccessResponse(['token' => $token, 'user' => $user], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return $this->SuccessResponse([], 'Logged out successfully');
    }

    public function profile(Request $request)
    {
        return $this->SuccessResponse(['user' => $request->user()], 'User profile retrieved successfully');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->ErrorResponse('Email not found.', 404);
        }

        $plainToken = Str::random(60);
        $hashedToken = Hash::make($plainToken);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => $hashedToken,
                'created_at' => Carbon::now()
            ]
        );

        // Kirim email reset password
        Mail::to($user->email)->send(new ResetPasswordMail($plainToken, $user->email));

        return $this->SuccessResponse([
            'message' => 'Reset link has been sent to your email!'
        ], 'Email sent!');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $tokenData = DB::table('password_resets')->where('email', $request->email)->first();

        if (!$tokenData || !Hash::check($request->token, $tokenData->token)) {
            return $this->ErrorResponse('Invalid token.', 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where('email', $request->email)->delete();

        return $this->SuccessResponse([], 'Password has been reset successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = $request->user(); // User yang sedang login

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->ErrorResponse('Password lama salah.', 400, ['current_password' => ['Password lama salah.']]);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return $this->SuccessResponse([], 'Password berhasil diubah.');
    }

    public function listAdmin()
    {
        $admin = User::where('role_id', 1)
            ->get()
            ->makeHidden(['profile_images'])
            ->makeVisible(['profile_image']);

        return $this->SuccessResponse($admin, 'Daftar admin berhasil diambil.');
    }

    public function updateProfileImage(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
        ]);

        try {
            $path = $this->uploadBase64Image($request->image, 'images/profiles');
            $user = Auth::user();
            $user->profile_image = $path;
            $user->save();

            return response()->json([
                'message' => 'Foto profil berhasil diperbarui.',
                'image_url' => asset($path),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    private function uploadBase64Image(string $base64Image, string $folder = 'images'): string
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            throw new \Exception('Format gambar tidak valid.');
        }

        $imageType = strtolower($type[1]);
        $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
        $base64Image = base64_decode($base64Image);

        if ($base64Image === false) {
            throw new \Exception('Base64 decode gagal.');
        }

        $filename = uniqid('profile_', true) . '.' . $imageType;
        $filePath = "{$folder}/{$filename}";

        Storage::disk('public')->put($filePath, $base64Image);

        return "storage/{$filePath}";
    }

}
