<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration (Username Only)
     */
    public function register(Request $request)
    {
        $key = 'register-' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'username' => "محاولات كثيرة. يرجى المحاولة مرة أخرى بعد $seconds ثواني."
            ]);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{Arabic}a-zA-Z\s]+$/u'
            ],
            'username' => [
                'required',
                'string',
                'max:50',
                'min:3',
                'unique:users,username',
                'regex:/^[a-zA-Z0-9_]+$/',
                'not_regex:/[<>"\']/'
            ],
            'password' => [
                'required',
                'confirmed',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ], [
            'name.required' => 'الاسم مطلوب.',
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.min' => 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل.',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.confirmed' => 'كلمة المرور غير متطابقة.',
        ]);

        RateLimiter::hit($key, 300);

        try {
            $user = User::create([
                'name' => strip_tags($validated['name']),
                'username' => strtolower(trim($validated['username'])),
                'email' => null,
                'password' => Hash::make($validated['password']),
            ]);

            Auth::login($user);
            $request->session()->regenerate();
            RateLimiter::clear($key);

            return redirect()->route('dashboard')
                ->with('success', 'تم إنشاء الحساب بنجاح! مرحباً بك ' . $user->name);
        } catch (\Exception $e) {
            Log::error('Registration error', ['error' => $e->getMessage()]);
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['error' => 'حدث خطأ أثناء إنشاء الحساب.']);
        }
    }

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle user login (Username Only)
     */
    public function login(Request $request)
    {
        $key = 'login-' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'username' => "محاولات كثيرة. يرجى المحاولة مرة أخرى بعد $seconds ثواني."
            ]);
        }

        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'اسم المستخدم مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ]);

        $credentials['username'] = strtolower(trim($credentials['username']));

        // Attempt authentication
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            Log::info('User logged in', [
                'user_id' => Auth::id(),
                'username' => Auth::user()->username,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'مرحباً بعودتك ' . Auth::user()->name . '!');
        }

        RateLimiter::hit($key, 60);

        Log::warning('Failed login attempt', [
            'username' => $credentials['username'],
            'ip' => $request->ip(),
        ]);

        throw ValidationException::withMessages([
            'username' => ['اسم المستخدم أو كلمة المرور غير صحيحة.'],
        ]);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        $username = Auth::user()->username ?? 'Unknown';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', ['username' => $username]);

        return redirect()->route('index')
            ->with('success', 'تم تسجيل الخروج بنجاح.');
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            Log::error('Google Redirect Error', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('login')
                ->withErrors(['error' => 'حدث خطأ في الاتصال بـ Google.']);
        }
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            Log::info('=== Google OAuth Callback ===', [
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
            ]);

            $email = strtolower(trim($googleUser->getEmail()));

            if (empty($email)) {
                throw new \Exception('لم يتم الحصول على البريد الإلكتروني من Google');
            }

            // Check if user exists by email OR google_id
            $user = User::where('email', $email)
                ->orWhere('google_id', $googleUser->getId())
                ->first();

            if ($user) {
                Log::info('Existing user found', ['user_id' => $user->id]);

                // Update Google data if not set
                if (empty($user->google_id)) {
                    $user->google_id = $googleUser->getId();
                    $user->email = $email;
                    $user->avatar = $googleUser->getAvatar();
                    $user->email_verified_at = now();
                    $user->save();

                    Log::info('Updated user with Google data');
                }
            } else {
                Log::info('Creating new user from Google');

                // ✅ Generate username from Google name (not email)
                $baseUsername = $this->generateUsernameFromName($googleUser->getName());
                $username = $this->ensureUniqueUsername($baseUsername);

                // Create user with Google data
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'username' => $username,  // ✅ From Google name
                    'email' => $email,
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(),
                    'password' => null,
                ]);

                Log::info('New Google user created', [
                    'user_id' => $user->id,
                    'name' => $googleUser->getName(),
                    'username' => $username,
                    'email' => $email
                ]);
            }

            // Log in with remember me
            Auth::login($user, true);
            $request->session()->regenerate();

            Log::info('=== Google Login Successful ===', ['user_id' => $user->id]);

            return redirect()->route('dashboard')
                ->with('success', 'مرحباً بك ' . $user->name . '!');
        } catch (\Exception $e) {
            Log::error('=== Google OAuth Error ===', [
                'error' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $request->session()->forget('state');

            return redirect()->route('login')
                ->withErrors(['error' => 'حدث خطأ أثناء تسجيل الدخول عبر Google. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * ✅ Generate username from Google full name (First + Last name)
     * Example: "John Doe" → "john_doe"
     *          "محمد أحمد" → "mohamed_ahmed" (transliterated)
     */
    private function generateUsernameFromName(string $fullName): string
    {
        // Convert to lowercase and replace spaces with underscores
        $username = strtolower(trim($fullName));
        $username = preg_replace('/\s+/', '_', $username);

        // Remove any characters that aren't letters, numbers, or underscores
        $username = preg_replace('/[^a-z0-9_]/', '', $username);

        // If username is too short or empty, add random string
        if (strlen($username) < 3) {
            $username .= substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 5);
        }

        // Limit to 50 characters
        $username = substr($username, 0, 50);

        return $username;
    }

    /**
     * Ensure username is unique by adding numbers if needed
     */
    private function ensureUniqueUsername(string $baseUsername): string
    {
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }
}
